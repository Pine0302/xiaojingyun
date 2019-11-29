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
			
			var pic_data 	 = new Array();
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var pic_title 	 = pic_data['pic_title'];		//图片标题
			var link_type 	 = pic_data['link_type'];		//链接类型
			var link     	 = pic_data['link'];		    //外部链接
			var select_value = pic_data['select_value'];	//链接选择的值
			var detail_value = pic_data['detail_value'];	//选择产品的id	
			
			var is_show = nav_is_show(data);	//导航栏显示或隐藏
			update_mod1(customer_id,data.id,data.content.title,data.content.title_en,data.content.mod_describe,data.content.padding,data.content.nav_title,is_show,pic,pic_title,link_type,select_value,detail_value,link,data.supply_id);
			
        }
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var link_type_val = $(this).val();
			var product_type = $('#product_type_2_'+sort);
			var brand_supply = $('#brand_supply_'+sort);
			var template_link = $('#template_link_'+sort);
			var room_link = $('#room_link_'+sort);
			var supply_type = $('#supply_type_'+sort);
			link_type_val = link_type_val.split("_");
			
			if( sort == 50 ){
				data.content.dataset[0][0].link_type = link_type_val[0];
				data.content.dataset[0][0].detail_value = '';
				
				if( link_type_val[0] == 2 ){	//固定链接
					data.content.dataset[0][0].select_value = link_type_val[1];
				}else if( link_type_val[0] == 3 ){	//产品分类
					data.content.dataset[0][0].select_value = product_type.val();
				}else if( link_type_val[0] == 4 ){	//品牌供应商
					data.content.dataset[0][0].select_value = brand_supply.val();
				}else if( link_type_val[0] == 5 ){	//一级分类页
					data.content.dataset[0][0].select_value = template_link.val();
				}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
					data.content.dataset[0][0].select_value = supply_type.val();
				}else if( link_type_val[0] == 7 ){	//微视直播系统
					data.content.dataset[0][0].select_value = room_link.val();
				}else if( link_type_val[0] == 8 ){  //大礼包
                    data.content.dataset[0][0].select_value = template_link.val();
                }
				
			} else {
				data.content.dataset[data.content.tab][sort].link_type = link_type_val[0];
				data.content.dataset[data.content.tab][sort].detail_value = '';
				
				if( link_type_val[0] == 2 ){	//固定链接
					data.content.dataset[data.content.tab][sort].select_value = link_type_val[1];
				}else if( link_type_val[0] == 3 ){	//产品分类
					data.content.dataset[data.content.tab][sort].select_value = product_type.val();
				}else if( link_type_val[0] == 4 ){	//品牌供应商
					data.content.dataset[data.content.tab][sort].select_value = brand_supply.val();
				}else if( link_type_val[0] == 5 ){	//一级分类页
					data.content.dataset[data.content.tab][sort].select_value = template_link.val();
				}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
					data.content.dataset[data.content.tab][sort].select_value = supply_type.val();
				}else if( link_type_val[0] == 7 ){	//微视直播系统
					data.content.dataset[data.content.tab][sort].select_value = room_link.val();
				}
			}
				
            reRender();
			
        });
		//选择分类
		$ctrl.find("select[name='product_type_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择品牌供应商店铺
		$ctrl.find("select[name='brand_supply']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='template_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择直播房间
		$ctrl.find("select[name='room_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择产品
        $ctrl.find("select[name='product_detail_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = $(this).val();
				
			}
            
            reRender();
        });
        //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			if ( sort == 50 ){
				data.content.dataset[0][0].pic = $(this).val();
			} else {
				data.content.dataset[data.content.tab][sort].pic = $(this).val();
			}
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
            // var index=$(this).parents("li.ctrl-item-list-li").index();
			// console.log(index);
            // console.log($("#frm_img"+index));
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
            $("#frm_img"+sort).submit();
        });
        //改变标题
        $ctrl.find("input[name='title']").change(function(){
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.title);
            } else {
				data.content.title = $(this).val();
				
				reRender();
			}
			
        });
		//改变英文标题
        $ctrl.find("input[name='title_en']").change(function(){
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.title_en);
            } else {
				data.content.title_en = $(this).val();
				
				reRender();
			}
			
        });
		//改变模块描述
        $ctrl.find("textarea[name='mod_describe']").change(function(){
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.mod_describe);
            } else {
				data.content.mod_describe = $(this).val();
				
				reRender();
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
		//导航栏标题
		$ctrl.find("input[name='nav_title']").change(function(){
            var patrn = /[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;
			var tab_len = data.content.dataset[data.content.tab].length;
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.dataset[data.content.tab][0].title);
            } else {
				for ( var i = 0; i < tab_len; i++ ){
					data.content.dataset[data.content.tab][i].title = $(this).val();
				}
				sort_nav_title();	//整理导航栏标题

				reRender();
			}
        });
		//整理导航栏标题
		var sort_nav_title = function (){
			var nav_title = '';
			for ( var i = 0; i < 5; i++ ){
				nav_title += data.content.dataset[i][0].title+'|';
			}
			nav_title = nav_title.substring(0,nav_title.length-1);	//去掉最后一个字符
			data.content.nav_title = nav_title;
			//console.log(nav_title);
		}
		//切换导航栏
        $ctrl.find(".pc-brand-tab-data").click(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			var tab = $(this).data('tab');
			if ( data.content.tab != tab ){
				data.content.tab = tab;
				data.content.floor = 1;	//切换导航栏，默认显示楼层一
				reRender();
			}
        });
		//导航栏标题显示或隐藏
		$ctrl.find("input[name='pc_brand_tab_show']").click(function(){
			data.content.is_show[data.content.tab] = $(this).val();
			
			if ( $(this).val() == 0 ){
				for ( var i=1; i < 5; i++ ){
					if ( data.content.is_show[i] == 1 ){
						data.content.tab = i;
						break;
					}
				}
			}
			
			reRender();
        });
		//楼层切换
		$ctrl.find(".select-floor").change(function(){
			var floor = $(this).val();
			data.content.floor = floor;
			reRender();
        });
		//图片上移
        $ctrl.find(".j-moveup").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			if(sort==0) return;//第一张图片不可再向上移动
			//替换缓存数组中的位置
			var tmpdata=data.content.dataset[data.content.tab].slice(sort,sort+1)[0];
			data.content.dataset[data.content.tab].splice(sort,1);
			data.content.dataset[data.content.tab].splice(sort-1,0,tmpdata);
			reRender();//更新视图
        });
        //图片下移
        $ctrl.find(".j-movedown").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var len=data.content.dataset[data.content.tab].length;

			if(sort==len-1) return;//最后一张图片不可再向下移动

			//替换缓存数组中的位置
			var tmpdata=data.content.dataset[data.content.tab].slice(sort,sort+1)[0];
			data.content.dataset[data.content.tab].splice(sort,1);
			data.content.dataset[data.content.tab].splice(sort+1,0,tmpdata);
			reRender();//更新视图
        });

        //添加图片
        $ctrl.find(".ctrl-item-list-add").click(function(){
			var new_sort=data.content.dataset[data.content.tab].length+1;
			var newdata={
					mod_sort: new_sort,
					link: "",
					title: data.content.dataset[data.content.tab][0].title,
					pic: 'images/img3.jpg',
					link_type: '',
					select_value: '',
					detail_value: ''
				};
			if(data.content.dataset[data.content.tab].length<18){
				data.content.dataset[data.content.tab].push(newdata);
				reRender();
			}
			else{
				_alert('请不要超过18张图片');
			}
        });
        //删除图片
        $ctrl.find(".j-del").click(function(){
			if(data.content.dataset[data.content.tab].length>1){
				var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
				data.content.dataset[data.content.tab].splice(sort,1);
				reRender();
			}
			else{
				_alert('再删就没拉');
			}
        });
		//切换链接类型
		$ctrl.find(".link_type").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');		
			if ( sort == 50 ){
				data.content.dataset[0][0].link_type = $(this).val();
			}else{
				data.content.dataset[data.content.tab][sort].link_type = $(this).val();
			}
			reRender();								
        });
		//改变链接网址
		$ctrl.find("input[name='link_address']").change(function(){
            var reg=/^(http:\/\/|https:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
            } else {
				var sort = $(this).parents("li.ctrl-item-list-li").data('sort');		
				if ( sort == 50 ){
					data.content.dataset[0][0].link = $(this).val();
				}else{
					data.content.dataset[data.content.tab][sort].link = $(this).val();
				}					
				reRender();
			}
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
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type2($ctrl,data);
			
			var pic_data 	 = new Array();
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var link_type 	 = pic_data['link_type'];		//链接类型
			var link     	 = pic_data['link'];		    //外部链接
			var select_value = pic_data['select_value'];	//链接选择的值
			var detail_value = pic_data['detail_value'];	//选择产品的id	
			
			var nav_css_type = arrange_nav_css_type(data);
			update_mod2(customer_id,data.id,pic,link_type,select_value,detail_value,data.content.padding,nav_css_type,link,data.supply_id);
			
        }
        //改变显示方式
        /* $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
        }); */
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
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var link_type_val = $(this).val();
			var product_type = $('#product_type_2_'+sort);
			var brand_supply = $('#brand_supply_'+sort);
			var template_link = $('#template_link_'+sort);
			var room_link = $('#room_link_'+sort);
			var supply_type = $('#supply_type_'+sort);
			link_type_val = link_type_val.split("_");
			
			data.content.dataset[0][sort].link_type = link_type_val[0];
			data.content.dataset[0][sort].detail_value = '';
			
			if( link_type_val[0] == 2 ){	//固定链接
				data.content.dataset[0][sort].select_value = link_type_val[1];
			}else if( link_type_val[0] == 3 ){	//产品分类
				data.content.dataset[0][sort].select_value = product_type.val();
			}else if( link_type_val[0] == 4 ){	//品牌供应商
				data.content.dataset[0][sort].select_value = brand_supply.val();
			}else if( link_type_val[0] == 5 ){	//一级分类页
				data.content.dataset[0][sort].select_value = template_link.val();
			}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
				data.content.dataset[0][sort].select_value = supply_type.val();
			}else if( link_type_val[0] == 7 ){	//微视直播系统
				data.content.dataset[0][sort].select_value = room_link.val();
			}else if( link_type_val[0] == 8 ){  //大礼包
                data.content.dataset[0][0].select_value = template_link.val();
            }
			
            reRender();
        });
		//选择分类
		$ctrl.find("select[name='product_type_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商店铺
		$ctrl.find("select[name='brand_supply']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='template_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择直播房间
		$ctrl.find("select[name='room_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择产品
        $ctrl.find("select[name='product_detail_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = $(this).val();
            
            reRender();
        });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[0][index].pic=$(this).val();
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			$("#frm_img"+index).submit();
        });
        //改变标题
        /* $ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].title=$(this).val();
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test(data.content.dataset[index].title)){
                _alert('提示信息：您输入的数据含有非法字符！');
                data.content.dataset[index].title="";
            }
            reRender();
        }); */
		//上移
        $ctrl.find(".j-moveup").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();

            if(index==0) return;//第一个导航不可再向上移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset[0].slice(index,index+1)[0];
            data.content.dataset[0].splice(index,1);
            data.content.dataset[0].splice(index-1,0,tmpdata);

            reRender();//更新视图
        });
        //下移
        $ctrl.find(".j-movedown").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index(),
                len=data.content.dataset[0].length;

            if(index==len-1) return;//最后一个导航不可再向下移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset[0].slice(index,index+1)[0];
            data.content.dataset[0].splice(index,1);
            data.content.dataset[0].splice(index+1,0,tmpdata);
            reRender();//更新视图
        });
		//改变显示方式
        $ctrl.find("input[name='nav_css_type']").change(function(){
            data.content.nav_css_type[0] = $(this).val();
            reRender();
        });
		//切换链接类型
		$ctrl.find(".link_type").click(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();	
			data.content.dataset[0][index].link_type = $(this).val();
			reRender();								
        });
		//改变链接网址
		$ctrl.find("input[name='link_address']").change(function(){
            var reg=/^(http:\/\/|https:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
            } else {
				var index=$(this).parents("li.ctrl-item-list-li").index();	
				data.content.dataset[0][index].link = $(this).val();						
				reRender();
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
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type3($ctrl,data);
			
			var pic_data 	 = new Array();
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var link_type 	 = pic_data['link_type'];		//链接类型
			var link     	 = pic_data['link'];		    //外部链接
			var select_value = pic_data['select_value'];	//链接选择的值
			var detail_value = pic_data['detail_value'];	//选择产品的id	
			
			update_mod3(customer_id,data.id,pic,link_type,select_value,detail_value,data.content.padding,link,data.supply_id);
			
        }
        //模块上下间距
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
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var link_type_val = $(this).val();
			var product_type = $('#product_type_2_'+sort);
			var brand_supply = $('#brand_supply_'+sort);
			var template_link = $('#template_link_'+sort);
			var room_link = $('#room_link_'+sort);
			var supply_type = $('#supply_type_'+sort);
			link_type_val = link_type_val.split("_");
			
			data.content.dataset[0][sort].link_type = link_type_val[0];
			data.content.dataset[0][sort].detail_value = '';
			
			if( link_type_val[0] == 2 ){	//固定链接
				data.content.dataset[0][sort].select_value = link_type_val[1];
			}else if( link_type_val[0] == 3 ){	//产品分类
				data.content.dataset[0][sort].select_value = product_type.val();
			}else if( link_type_val[0] == 4 ){	//品牌供应商
				data.content.dataset[0][sort].select_value = brand_supply.val();
			}else if( link_type_val[0] == 5 ){	//一级分类页
				data.content.dataset[0][sort].select_value = template_link.val();
			}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
				data.content.dataset[0][sort].select_value = supply_type.val();
			}else if( link_type_val[0] == 7 ){	//微视直播系统
				data.content.dataset[0][sort].select_value = room_link.val();
			}else if( link_type_val[0] == 8 ){  //大礼包
                data.content.dataset[0][0].select_value = template_link.val();
            }
			
            reRender();
        });
		//选择分类
		$ctrl.find("select[name='product_type_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商店铺
		$ctrl.find("select[name='brand_supply']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='template_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择直播房间
		$ctrl.find("select[name='room_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择产品
        $ctrl.find("select[name='product_detail_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = $(this).val();
            
            reRender();
        });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            data.content.dataset[0][0].pic = $(this).val();
            reRender();
        });
        //选择图片       
        $ctrl.find("input[name='upfile2']").change(function(){
            $("#frm_img0").submit();
        });
		//切换链接类型
		$ctrl.find(".link_type").click(function(){
			data.content.dataset[0][0].link_type = $(this).val();
			reRender();								
        });
		//改变链接网址
		$ctrl.find("input[name='link_address']").change(function(){
            var reg=/^(http:\/\/|https:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
            } else {		
				data.content.dataset[0][0].link = $(this).val();						
				reRender();
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
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type4($ctrl,data);
			
			var pic_data 	 = new Array();
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var pic_title 	 = pic_data['pic_title'];		//图片标题
			var link_type 	 = pic_data['link_type'];		//链接类型
			var link     	 = pic_data['link'];		    //外部链接
			var select_value = pic_data['select_value'];	//链接选择的值
			var detail_value = pic_data['detail_value'];	//选择产品的id	
			
			var is_show = nav_is_show(data);	//导航栏显示或隐藏
			
			var nav_css_type = arrange_nav_css_type(data);	//每个导航的样式
			// console.log(nav_css_type);
			
			//渲染模块后，楼层号排序
			data_list = floorSort(data_list);
			
			update_mod4(customer_id,data.id,data.content.title,data.content.padding,data.content.nav_title,is_show,pic,pic_title,link_type,select_value,detail_value,nav_css_type,link,data.supply_id);
			
        }
        //模块上下间距
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
		//改变标题
        $ctrl.find("input[name='title']").change(function(){
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.title);
            } else {
				data.content.title = $(this).val();
				//console.log(data.content.floor_number);
				$('.floor_number_'+data.content.floor_number).text($(this).val());
				
				reRender();
			}
			
        });
		//改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var link_type_val = $(this).val();
			var product_type = $('#product_type_2_'+sort);
			var brand_supply = $('#brand_supply_'+sort);
			var template_link = $('#template_link_'+sort);
			var room_link = $('#room_link_'+sort);
			var supply_type = $('#supply_type_'+sort);
			link_type_val = link_type_val.split("_");
			
			data.content.dataset[data.content.tab][sort].link_type = link_type_val[0];
			data.content.dataset[data.content.tab][sort].detail_value = '';
			
			if( link_type_val[0] == 2 ){	//固定链接
				data.content.dataset[data.content.tab][sort].select_value = link_type_val[1];
			}else if( link_type_val[0] == 3 ){	//产品分类
				data.content.dataset[data.content.tab][sort].select_value = product_type.val();
			}else if( link_type_val[0] == 4 ){	//品牌供应商
				data.content.dataset[data.content.tab][sort].select_value = brand_supply.val();
			}else if( link_type_val[0] == 5 ){	//一级分类页
				data.content.dataset[data.content.tab][sort].select_value = template_link.val();
			}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
				data.content.dataset[data.content.tab][sort].select_value = supply_type.val();
			}else if( link_type_val[0] == 7 ){	//微视直播系统
				data.content.dataset[data.content.tab][sort].select_value = room_link.val();
			}else if( link_type_val[0] == 8 ){  //大礼包
                data.content.dataset[0][0].select_value = template_link.val();
            }
			
            reRender();
        });
        //选择分类
		$ctrl.find("select[name='product_type_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商店铺
		$ctrl.find("select[name='brand_supply']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='template_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='room_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择产品
        $ctrl.find("select[name='product_detail_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = $(this).val();
            
            reRender();
        });
		//导航栏标题
		$ctrl.find("input[name='nav_title']").change(function(){
            var patrn = /[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;
			var tab_len = data.content.dataset[data.content.tab].length;
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.dataset[data.content.tab][0].title);
            } else {
				for ( var i = 0; i < tab_len; i++ ){
					data.content.dataset[data.content.tab][i].title = $(this).val();
				}
				sort_nav_title();	//整理导航栏标题

				reRender();
			}
        });
		//整理导航栏标题
		var sort_nav_title = function (){
			var nav_title = '';
			for ( var i = 0; i < 4; i++ ){
				nav_title += data.content.dataset[i][0].title+'|';
			}
			nav_title = nav_title.substring(0,nav_title.length-1);	//去掉最后一个字符
			data.content.nav_title = nav_title;
		}
		//切换导航栏
        $ctrl.find(".pc-brand-tab-data").click(function(){
			var tab = $(this).data('tab');
			if ( data.content.tab != tab ){
				data.content.tab = tab;
				reRender();
			}
        });
		//导航栏标题显示或隐藏
		$ctrl.find("input[name='pc_brand_tab_show']").click(function(){
			data.content.is_show[data.content.tab] = $(this).val();
			
			if ( $(this).val() == 0 ){
				var nav_len = data.content.dataset.length;
				for ( var i=0; i < nav_len; i++ ){
					if ( data.content.is_show[i] == 1 ){
						data.content.tab = i;
						break;
					}
				}
			}
				
			reRender();
        });
		//改变显示方式
        $ctrl.find("input[name='nav_css_type']").change(function(){
            data.content.nav_css_type[data.content.tab] = $(this).val();
            reRender();
        });
        //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			data.content.dataset[data.content.tab][sort].pic = $(this).val();
			
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
            $("#frm_img"+sort).submit();
        });
		//图片上移
        $ctrl.find(".j-moveup").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			if(sort==0) return;//第一张图片不可再向上移动
			//替换缓存数组中的位置
			var tmpdata=data.content.dataset[data.content.tab].slice(sort,sort+1)[0];
			data.content.dataset[data.content.tab].splice(sort,1);
			data.content.dataset[data.content.tab].splice(sort-1,0,tmpdata);
			reRender();//更新视图
        });
        //图片下移
        $ctrl.find(".j-movedown").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var len=data.content.dataset[data.content.tab].length;

			if(sort==len-1) return;//最后一张图片不可再向下移动

			//替换缓存数组中的位置
			var tmpdata=data.content.dataset[data.content.tab].slice(sort,sort+1)[0];
			data.content.dataset[data.content.tab].splice(sort,1);
			data.content.dataset[data.content.tab].splice(sort+1,0,tmpdata);
			reRender();//更新视图
        });
		//切换链接类型
		$ctrl.find(".link_type").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			data.content.dataset[data.content.tab][sort].link_type = $(this).val();
			reRender();								
        });
		//改变链接网址
		$ctrl.find("input[name='link_address']").change(function(){
            var reg=/^(http:\/\/|https:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
            } else {
				var sort=$(this).parents("li.ctrl-item-list-li").data('sort');			
				data.content.dataset[data.content.tab][sort].link = $(this).val();					
				reRender();
			}
        });
    };
    custom_event_type5=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_5").html(),//手机内容模板
            ctrl=$("#type_ctrl_5").html();//控制内容模板
            data.dom_ctrl=ctrldom;
			
			showSelectProduct(data);	//显示产品选择框
			
        //重新渲染数据
        var reRender=function(){
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type5($ctrl,data);
			
			// var start_time = Date.parse(new Date(data.content.dataset[0][0].start_time))/1000;	//开始时间
			// var end_time   = Date.parse(new Date(data.content.dataset[0][0].end_time))/1000;	//结束时间
			// remaining_time(start_time,end_time);
			// remaining_time_id = setInterval(remaining_time(start_time,end_time),1000);
			
			var pic_data 	 = new Array();
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var pic_title 	 = pic_data['pic_title'];		//图片标题
			var link_type 	 = pic_data['link_type'];		//链接类型
			var link     	 = pic_data['link'];		    //外部链接
			var select_value = pic_data['select_value'];	//链接选择的值
			var detail_value = pic_data['detail_value'];	//选择产品的id	
			var start_time 	 = pic_data['start_time'];		//选择产品的id	
			var end_time 	 = pic_data['end_time'];		//选择产品的id	
			
			var is_show = nav_is_show(data);	//导航栏显示或隐藏
			
			update_mod5(customer_id,data.id,data.content.title,data.content.padding,data.content.nav_title,is_show,pic,pic_title,link_type,select_value,detail_value,start_time,end_time,link,data.supply_id);
			
        }
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var link_type_val = $(this).val();
			var product_type = $('#product_type_2_'+sort);
			var brand_supply = $('#brand_supply_'+sort);
			var template_link = $('#template_link_'+sort);
			var room_link = $('#room_link_'+sort);
			var supply_type = $('#supply_type_'+sort);
			link_type_val = link_type_val.split("_");
			
			if( sort == 50 ){
				data.content.dataset[0][0].link_type = link_type_val[0];
				data.content.dataset[0][0].detail_value = '';
				
				if( link_type_val[0] == 2 ){	//固定链接
					data.content.dataset[0][0].select_value = link_type_val[1];
				}else if( link_type_val[0] == 3 ){	//产品分类
					data.content.dataset[0][0].select_value = product_type.val();
				}else if( link_type_val[0] == 4 ){	//品牌供应商
					data.content.dataset[0][0].select_value = brand_supply.val();
				}else if( link_type_val[0] == 5 ){	//一级分类页
					data.content.dataset[0][0].select_value = template_link.val();
				}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
					data.content.dataset[0][0].select_value = supply_type.val();
				}else if( link_type_val[0] == 7 ){	//微视直播系统
					data.content.dataset[0][0].select_value = room_link.val();
				}else if( link_type_val[0] == 8 ){  //大礼包
                    data.content.dataset[0][0].select_value = template_link.val();
                }
				
			} else {
				data.content.dataset[data.content.tab][sort].link_type = link_type_val[0];
				data.content.dataset[data.content.tab][sort].detail_value = '';
				
				if( link_type_val[0] == 2 ){	//固定链接
					data.content.dataset[data.content.tab][sort].select_value = link_type_val[1];
				}else if( link_type_val[0] == 3 ){	//产品分类
					data.content.dataset[data.content.tab][sort].select_value = product_type.val();
				}else if( link_type_val[0] == 4 ){	//品牌供应商
					data.content.dataset[data.content.tab][sort].select_value = brand_supply.val();
				}else if( link_type_val[0] == 5 ){	//一级分类页
					data.content.dataset[data.content.tab][sort].select_value = template_link.val();
				}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
					data.content.dataset[data.content.tab][sort].select_value = supply_type.val();
				}else if( link_type_val[0] == 7 ){	//微视直播系统
					data.content.dataset[data.content.tab][sort].select_value = room_link.val();
				}
			}
				
            reRender();
			
        });
		//选择分类
		$ctrl.find("select[name='product_type_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择品牌供应商店铺
		$ctrl.find("select[name='brand_supply']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='template_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择直播房间
		$ctrl.find("select[name='room_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = '';
				data.content.dataset[0][0].select_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = '';
				data.content.dataset[data.content.tab][sort].select_value = $(this).val();
				
			}
			
            reRender();
			
        });
		//选择产品
        $ctrl.find("select[name='product_detail_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			if( sort == 50 ){
				data.content.dataset[0][0].detail_value = $(this).val();
				
			} else {
				data.content.dataset[data.content.tab][sort].detail_value = $(this).val();
				
			}
            
            reRender();
        });
        //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			if ( sort == 50 ){
				data.content.dataset[0][0].pic = $(this).val();
			} else {
				data.content.dataset[data.content.tab][sort].pic = $(this).val();
			}
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
            $("#frm_img"+sort).submit();
        });
        //改变标题
        $ctrl.find("input[name='title']").change(function(){
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.dataset[0][0].title);
            } else {
				data.content.dataset[0][0].title = $(this).val();
				
				sort_nav_title();	//整理导航栏标题
				
				reRender();
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
		//改变开始时间
        $ctrl.find("input[name='start_time']").focus(function(){
			var endtime=$dp.$('endtime');
			WdatePicker({onpicked:function(){
				// endtime.focus();
				data.content.dataset[0][0].start_time=$(this).val();
				reRender();
				},oncleared:function(){
					data.content.dataset[0][0].start_time=$(this).val();
					reRender();
				},readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'endtime\')}'
			})
        });
		$ctrl.find("input[name='start_time']").change(function(){
			data.content.dataset[0][0].start_time=$(this).val();
			reRender();
		});
		//改变结束时间
        $ctrl.find("input[name='end_time']").click(function(){
			var starttime=$dp.$('starttime');
			WdatePicker({onpicked:function(){
				// endtime.focus();
				data.content.dataset[0][0].end_time=$(this).val();
				reRender();
				},oncleared:function(){
					data.content.dataset[0][0].end_time=$(this).val();
					reRender();
				},readOnly:true,dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'starttime\')}'
			})
        });	
		$ctrl.find("input[name='end_time']").change(function(){
			data.content.dataset[0][0].end_time=$(this).val();
			reRender();
		});
		//计算剩余时间
		var remaining_time = function (start_time,end_time){
			var currtime   = Date.parse(new Date())/1000;			//当前时间
			// var start_time = Date.parse(new Date(start_time))/1000;	//开始时间
			// var end_time   = Date.parse(new Date(end_time))/1000;	//结束时间
			// var start_time_timestamp = start_time - currtime;	//时间差
			// var end_time_timestamp 	 = end_time - currtime;		//时间差
			var day = 24 * 60 * 60;
			var hour = 60 * 60;
			var minute = 60;
			var second = 1;
			var days = 0;
			var hours = 0;
			var minutes = 0;
			var seconds = 0;
			var timestamp = 0;	//时间差
			
			$('.child-nav-time-start_'+data.id).hide();
			$('.child-nav-time-end_'+data.id).hide();
			
			if ( start_time > currtime ){
				$('.child-nav-time-start_'+data.id).show();
				timestamp = start_time - currtime;
				$('.child-time-span-tip_'+data.id).html('距开始仅剩：');
			} else if ( currtime < end_time ){
				$('.child-nav-time-start_'+data.id).show();
				timestamp = end_time - currtime;
				$('.child-time-span-tip_'+data.id).html('距结束仅剩：');
				
				/*if ( timestamp <= 0 ){
					clearInterval(remaining_time_id);
				}*/
			} else if ( currtime >= end_time ){
				$('.child-nav-time-end_'+data.id).show();
			}
			// console.log(timestamp);
			if ( timestamp > 0 ){
				if ( timestamp >= day ){
					days = parseInt(timestamp/day);
					timestamp = timestamp - day * days;
				}
				if ( timestamp >= hour ){
					hours = parseInt(timestamp/hour);
					timestamp = timestamp - hour * hours;
				}
				if ( timestamp >= minute ){
					minutes = parseInt(timestamp/minute);
					timestamp = timestamp - minute * minutes;
				}
				if ( timestamp >= second ){
					seconds = parseInt(timestamp/second);
					timestamp = timestamp - second * seconds;
				}
				$('#day_'+data.id).html(days);
				$('#hour_'+data.id).html(hours);
				$('#minute_'+data.id).html(minutes);
				$('#second_'+data.id).html(seconds);
				
			}
			
		};
		
		var start_time = Date.parse(new Date(data.content.dataset[0][0].start_time))/1000;	//开始时间
		var end_time   = Date.parse(new Date(data.content.dataset[0][0].end_time))/1000;	//结束时间
		remaining_time(start_time,end_time);
		
		//导航栏标题
		$ctrl.find("input[name='nav_title']").change(function(){
            var patrn = /[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
			var tab_len = data.content.dataset[data.content.tab].length;
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.dataset[data.content.tab][0].title);
            } else {
				for ( var i = 0; i < tab_len; i++ ){
					data.content.dataset[data.content.tab][i].title = $(this).val();
				}
				sort_nav_title();	//整理导航栏标题

				reRender();
			}
        });
		//整理导航栏标题
		var sort_nav_title = function (){
			var nav_title = '';
			for ( var i = 0; i < 4; i++ ){
				nav_title += data.content.dataset[i][0].title+'|';
			}
			nav_title = nav_title.substring(0,nav_title.length-1);	//去掉最后一个字符
			data.content.nav_title = nav_title;
		}
		//切换导航栏
        $ctrl.find(".pc-brand-tab-data").click(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();
			var tab = $(this).data('tab');
			if ( data.content.tab != tab ){
				data.content.tab = tab;
				reRender();
			}
        });
		//导航栏标题显示或隐藏
		$ctrl.find("input[name='pc_brand_tab_show']").click(function(){						
			data.content.is_show[data.content.tab] = $(this).val();
			
			if ( $(this).val() == 0 ){
				for ( var i=1; i < 4; i++ ){
					if ( data.content.is_show[i] == 1 ){
						data.content.tab = i;
						break;
					}
				}
			}
			
			reRender();
        });
		//图片上移
        $ctrl.find(".j-moveup").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			if(sort==0) return;//第一张图片不可再向上移动
			//替换缓存数组中的位置
			var tmpdata=data.content.dataset[data.content.tab].slice(sort,sort+1)[0];
			data.content.dataset[data.content.tab].splice(sort,1);
			data.content.dataset[data.content.tab].splice(sort-1,0,tmpdata);
			reRender();//更新视图
        });
        //图片下移
        $ctrl.find(".j-movedown").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var len=data.content.dataset[data.content.tab].length;

			if(sort==len-1) return;//最后一张图片不可再向下移动

			//替换缓存数组中的位置
			var tmpdata=data.content.dataset[data.content.tab].slice(sort,sort+1)[0];
			data.content.dataset[data.content.tab].splice(sort,1);
			data.content.dataset[data.content.tab].splice(sort+1,0,tmpdata);
			reRender();//更新视图
        });

        //添加图片
        $ctrl.find(".ctrl-item-list-add").click(function(){
			var new_sort=data.content.dataset[data.content.tab].length+1;
			var newdata={
					mod_sort: new_sort,
					link: "",
					title: data.content.dataset[data.content.tab][0].title,
					pic: 'images/img3.jpg',
					link_type: '',
					select_value: '',
					detail_value: ''
				};
			if(data.content.dataset[data.content.tab].length<4){
				data.content.dataset[data.content.tab].push(newdata);
				reRender();
			}
			else{
				_alert('请不要超过4张图片');
			}
        });
        //删除图片
        $ctrl.find(".j-del").click(function(){
			if(data.content.dataset[data.content.tab].length>1){
				var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
				data.content.dataset[data.content.tab].splice(sort,1);
				reRender();
			}
			else{
				_alert('再删就没拉');
			}
        });
		//切换链接类型
		$ctrl.find(".link_type").click(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			if ( sort == 50 ){
				data.content.dataset[0][0].link_type = $(this).val();
			} else {
				data.content.dataset[data.content.tab][sort].link_type = $(this).val();
			}
			reRender();								
        });
		//改变链接网址
		$ctrl.find("input[name='link_address']").change(function(){
            var reg=/^(http:\/\/|https:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
            } else {
				var sort=$(this).parents("li.ctrl-item-list-li").data('sort');			
				if ( sort == 50 ){
					data.content.dataset[0][0].link = $(this).val();
				} else {
					data.content.dataset[data.content.tab][sort].link = $(this).val();
				}						
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
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type6($ctrl,data);
			
			var pic_data 	 = new Array();
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var pic_title 	 = pic_data['pic_title'];		//标题图标
			var link_type 	 = pic_data['link_type'];		//链接类型
			var link     	 = pic_data['link'];		    //外部链接
			var select_value = pic_data['select_value'];	//链接选择的值
			var detail_value = pic_data['detail_value'];	//选择产品的id	
			
			var nav_css_type = arrange_nav_css_type(data);	//每个导航的样式
			
			var is_show = nav_is_show(data);	//导航栏显示或隐藏
			// console.log(data.id);
			// console.log(link_type);
			// console.log(link);
			update_mod6(customer_id,data.id,pic,pic_title,link_type,select_value,detail_value,data.content.padding,nav_css_type,is_show,link,data.supply_id);
			
        }
        //模块上下间距
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
		//导航栏标题显示或隐藏
		$ctrl.find("input[name='is_show']").click(function(){
			var show_count = 0;
			for(var k=0;k<6;k++){
				if(data.content.is_show[k]==1){
					show_count++;
				}
			}

			if(show_count==3 && $(this).val()==0){
				_alert('橱窗分类不得少于三个！');
			}else{
				data.content.is_show[data.content.tab - 1] = $(this).val();
				
				if ( $(this).val() == 0 ){
					for ( var i=0; i < 6; i++ ){
						if ( data.content.is_show[i] == 1 ){
							data.content.tab = i+1;
							break;
						}
					}
				}
			}
			
			reRender();
        });
        //改变链接
        $ctrl.find("select[name='type_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var link_type_val = $(this).val();
			var product_type = $('#product_type_2_'+sort);
			var brand_supply = $('#brand_supply_'+sort);
			var template_link = $('#template_link_'+sort);
			var room_link = $('#room_link_'+sort);
			var supply_type = $('#supply_type_'+sort);
			link_type_val = link_type_val.split("_");
			
			data.content.dataset[data.content.tab][sort].link_type = link_type_val[0];
			data.content.dataset[data.content.tab][sort].detail_value = '';
			
			if( link_type_val[0] == 2 ){	//固定链接
				data.content.dataset[data.content.tab][sort].select_value = link_type_val[1];
			}else if( link_type_val[0] == 3 ){	//产品分类
				data.content.dataset[data.content.tab][sort].select_value = product_type.val();
			}else if( link_type_val[0] == 4 ){	//品牌供应商
				data.content.dataset[data.content.tab][sort].select_value = brand_supply.val();
			}else if( link_type_val[0] == 5 ){	//一级分类页
				data.content.dataset[data.content.tab][sort].select_value = template_link.val();
			}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
				data.content.dataset[data.content.tab][sort].select_value = supply_type.val();
			}else if( link_type_val[0] == 7 ){	//微视直播系统
				data.content.dataset[data.content.tab][sort].select_value = room_link.val();
			}else if( link_type_val[0] == 8 ){  //大礼包
                data.content.dataset[0][0].select_value = template_link.val();
            }
			
            reRender();
        });
        //选择分类
		$ctrl.find("select[name='product_type_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商店铺
		$ctrl.find("select[name='brand_supply']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='template_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择直播房间
		$ctrl.find("select[name='room_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = '';
			data.content.dataset[data.content.tab][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择产品
        $ctrl.find("select[name='product_detail_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[data.content.tab][sort].detail_value = $(this).val();
            
            reRender();
        });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
			var data_tab=$(this).parent().parent().data('tab');
			if(data_tab>0){   //标题图标
				data.content.dataset[0][data_tab - 1].pic = $(this).val();
			}else{			  //橱窗内容图片
				var sort=$(this).parents("li.ctrl-item-list-li").data('sort');
				data.content.dataset[data.content.tab][sort].pic = $(this).val();
			}
           
            reRender();
        });
        //选择图片       
        $ctrl.find("input[name='upfile2']").change(function(){
			var data_tab=$(this).parent().parent().parent().data('tab');
			if(data_tab>0){   //标题图标
				$("#frm_imgnav"+data_tab).submit();
			}else{			  //橱窗内容图片
				var sort=$(this).parents("li.ctrl-item-list-li").data('sort');
				$("#frm_img"+sort).submit();
			}       
        });
		//切换导航栏
        $ctrl.find(".pc-brand-tab-data ").click(function(){
			var tab = $(this).data('tab');
			if ( data.content.tab != tab ){
				data.content.tab = tab; 
				reRender();
			}
        });
		//导航栏标题
		$ctrl.find("input[name='nav_title']").change(function(){
            var patrn = /[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
			var tab_len = data.content.dataset[data.content.tab].length;
            if(patrn.test($(this).val())){
                _alert('提示信息：您输入的数据含有非法字符！');
                $(this).val(data.content.dataset[0][data.content.tab-1].title);
            } else {
				data.content.dataset[0][data.content.tab-1].title = $(this).val();

				reRender();
			}
        });
		//改变显示方式
        $ctrl.find("input[name='nav_css_type']").change(function(){
            data.content.nav_css_type[data.content.tab - 1] = $(this).val();
            reRender();
        });
		//切换链接类型
		$ctrl.find(".link_type").click(function(){
			var sort=$(this).parents("li.ctrl-item-list-li").data('sort');
			data.content.dataset[data.content.tab][sort].link_type = $(this).val();
			reRender();								
        });		
		//改变链接网址
		$ctrl.find("input[name='link_address']").change(function(){
            var reg=/^(http:\/\/|https:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
            } else {
				var sort=$(this).parents("li.ctrl-item-list-li").data('sort');
				data.content.dataset[data.content.tab][sort].link = $(this).val();
				reRender();
			}
        });
    };
	
	custom_event_type7=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_7").html(),//手机内容模板
            ctrl=$("#type_ctrl_7").html();//控制内容模板
            data.dom_ctrl=ctrldom;

        //重新渲染数据
        var reRender=function(){
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
			
			if(!(/^(\+|-)?\d+$/.test( data.content.pro_num_show )) || data.content.pro_num_show < 0){
				data.content.pro_num_show=4;
			}
            custom_event_type7($ctrl,data);
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var pic_title 	 = pic_data['pic_title'];		//标题图标
			var link_type 	 = pic_data['link_type'];		//链接类型
			var select_value = pic_data['select_value'];	//链接选择的值
			//console.log(data);
			update_mod7(customer_id,data.id,data.content.css_type,data.content.pro_name_show,data.content.pro_num_show,data.content.show_sale,data.content.padding,pic_title,pic,link_type,select_value);
        }
        //布局方式
        $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type = $(this).val();
            reRender();
        });
        //是否显示标题
        $ctrl.find("input[name='pro_name_show']").change(function(){
            data.content.pro_name_show = $(this).val();
            reRender();
        });
        //商品数量
        $ctrl.find("input[name='pro_num_show']").change(function(){
            data.content.pro_num_show=$(this).val();
			if(!(/^(\+|-)?\d+$/.test( data.content.pro_num_show )) || data.content.pro_num_show < 0 ){
				_alert("输入数量有误，请重新输入");
				data.content.pro_num_show = 4;
			}else if(data.content.pro_num_show > 20){
				_alert("数量不宜大于20");
				data.content.pro_num_show = 20;
			}
            reRender();
        });
        //选择分类
        $ctrl.find("select[name='type_id_2']").change(function(){
            data.content.dataset[0][0].select_value = $(this).val();
            reRender();
        });
        //是否显示销量
        $ctrl.find("input[name='show_sale']").change(function(){
            data.content.show_sale = $(this).val();
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
                data.content.padding = parseInt(ui.value);
				reRender();
            }
        });
    };
	custom_event_type8=function(ctrldom, data){
        var $conitem = data.dom_conitem, //手机内容
            $ctrl = ctrldom;//控制内容
            con=$("#type_con_8").html(),//手机内容模板
            ctrl=$("#type_ctrl_8").html();//控制内容模板
            data.dom_ctrl=ctrldom;
			
			showSelectProduct(data);	//显示产品选择框
			
        //重新渲染数据
        var reRender=function(){
            var $render=$(doT.template(con)(data));
            $conitem.find(".con_display").remove().end().append($render);
            var $render_ctrl=$(doT.template(ctrl)(data));
            $ctrl.empty().append($render_ctrl);
            custom_event_type8($ctrl,data);
			
			var pic_data 	 = new Array();
			
			pic_data = arrange_pic(data);	//整理图片和链接
			
			var pic 		 = pic_data['pic'];				//图片
			var link_type 	 = pic_data['link_type'];		//链接类型
			var link     	 = pic_data['link'];		    //外部链接
			var select_value = pic_data['select_value'];	//链接选择的值
			var detail_value = pic_data['detail_value'];	//选择产品的id	
			
			var nav_css_type = arrange_nav_css_type(data);
			update_mod2(customer_id,data.id,pic,link_type,select_value,detail_value,data.content.padding,nav_css_type,link,data.supply_id);
			
        }
        //改变显示方式
        /* $ctrl.find("input[name='css_type']").change(function(){
            data.content.css_type=$(this).val();
            reRender();
        }); */
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
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			var link_type_val = $(this).val();
			var product_type = $('#product_type_2_'+sort);
			var brand_supply = $('#brand_supply_'+sort);
			var template_link = $('#template_link_'+sort);
			var room_link = $('#room_link_'+sort);
			var supply_type = $('#supply_type_'+sort);
			link_type_val = link_type_val.split("_");
			
			data.content.dataset[0][sort].link_type = link_type_val[0];
			data.content.dataset[0][sort].detail_value = '';
			
			if( link_type_val[0] == 2 ){	//固定链接
				data.content.dataset[0][sort].select_value = link_type_val[1];
			}else if( link_type_val[0] == 3 ){	//产品分类
				data.content.dataset[0][sort].select_value = product_type.val();
			}else if( link_type_val[0] == 4 ){	//品牌供应商
				data.content.dataset[0][sort].select_value = brand_supply.val();
			}else if( link_type_val[0] == 5 ){	//一级分类页
				data.content.dataset[0][sort].select_value = template_link.val();
			}else if( link_type_val[0] == 6 ){	//品牌供应商产品分类
				data.content.dataset[0][sort].select_value = supply_type.val();
			}else if( link_type_val[0] == 7 ){	//微视直播系统
				data.content.dataset[0][sort].select_value = room_link.val();
			}else if( link_type_val[0] == 8 ){  //大礼包
                data.content.dataset[0][0].select_value = template_link.val();
            }
			
            reRender();
        });
		//选择分类
		$ctrl.find("select[name='product_type_2']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商店铺
		$ctrl.find("select[name='brand_supply']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='template_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择一级分类页
		$ctrl.find("select[name='room_link']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择品牌供应商产品分类
		$ctrl.find("select[name='supply_type']").change(function(){
            var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = '';
			data.content.dataset[0][sort].select_value = $(this).val();
			
            reRender();
			
        });
		//选择产品
        $ctrl.find("select[name='product_detail_id_2']").change(function(){
			var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
			
			data.content.dataset[0][sort].detail_value = $(this).val();
            
            reRender();
        });
		//添加图片
        $ctrl.find(".ctrl-item-list-add").click(function(){
			var new_sort=data.content.dataset[0].length+1;
			var newdata={
					mod_sort: new_sort,
					link: "",
					title: '轮播图',
					pic: 'images/img1.jpg',
					link_type: '',
					select_value: '',
					detail_value: ''
				};
			if(data.content.dataset[0].length<6){
				data.content.dataset[0].push(newdata);
				reRender();
			}
			else{
				_alert('请不要超过6张图片');
			}
        });
        //删除图片
        $ctrl.find(".j-del").click(function(){
			if(data.content.dataset[0].length>1){
				var sort = $(this).parents("li.ctrl-item-list-li").data('sort');
				data.content.dataset[0].splice(sort,1);
				reRender();
			}
			else{
				_alert('再删就没拉');
			}
        });
         //改变pic
        $ctrl.find("input[name='getImg']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[0][index].pic=$(this).val();
            reRender();
        });
        //选择图片       
         $ctrl.find("input[name='upfile2']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
			$("#frm_img"+index).submit();
        });
        //改变标题
        /* $ctrl.find("input[name='title']").change(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();
            data.content.dataset[index].title=$(this).val();
            var patrn=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;  
            if(patrn.test(data.content.dataset[index].title)){
                _alert('提示信息：您输入的数据含有非法字符！');
                data.content.dataset[index].title="";
            }
            reRender();
        }); */
		//上移
        $ctrl.find(".j-moveup").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index();

            if(index==0) return;//第一个导航不可再向上移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset[0].slice(index,index+1)[0];
            data.content.dataset[0].splice(index,1);
            data.content.dataset[0].splice(index-1,0,tmpdata);

            reRender();//更新视图
        });
        //下移
        $ctrl.find(".j-movedown").click(function(){
            var index=$(this).parents("li.ctrl-item-list-li").index(),
                len=data.content.dataset[0].length;

            if(index==len-1) return;//最后一个导航不可再向下移动

            //替换缓存数组中的位置
            var tmpdata=data.content.dataset[0].slice(index,index+1)[0];
            data.content.dataset[0].splice(index,1);
            data.content.dataset[0].splice(index+1,0,tmpdata);
            reRender();//更新视图
        });
		//改变显示方式
        $ctrl.find("input[name='nav_css_type']").change(function(){
            data.content.nav_css_type[0] = $(this).val();
            reRender();
        });
		//切换链接类型
		$ctrl.find(".link_type").click(function(){
			var index=$(this).parents("li.ctrl-item-list-li").index();	
			data.content.dataset[0][index].link_type = $(this).val();
			reRender();								
        });
		//改变链接网址
		$ctrl.find("input[name='link_address']").change(function(){
            var reg=/^(http:\/\/|https:\/\/).*$/; 
            if(!reg.test($(this).val())){
                _alert('提示信息：请输入以//或https://开头正确的URL！');
            } else {
				var index=$(this).parents("li.ctrl-item-list-li").index();	
				data.content.dataset[0][index].link = $(this).val();						
				reRender();
			}
        });
    };
});

function update_mod1(customer_id,diy_tem_contid,title,title_en,mod_describe,mod_padding,nav_title,is_show,imgurl,pic_title,link_type,select_value,detail_value,link,supply_id){  //品牌推荐更新
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"title_en" : title_en,"mod_describe" : mod_describe,"mod_padding" : mod_padding,"nav_title" : nav_title,"is_show" : is_show,"imgurl" : imgurl,"pic_title" : pic_title,"link_type" : link_type,"select_value" : select_value,"detail_value" : detail_value,"link":link,"supply_id" : supply_id},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod2(customer_id,diy_tem_contid,imgurl,link_type,select_value,detail_value,mod_padding,nav_css_type,link,supply_id){  //搜索更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"imgurl" : imgurl,"link_type" : link_type,"select_value" : select_value,"detail_value" : detail_value,"mod_padding" : mod_padding,"nav_css_type" : nav_css_type,"link":link,"supply_id" : supply_id},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod3(customer_id,diy_tem_contid,imgurl,link_type,select_value,detail_value,mod_padding,link,supply_id){  //横型广告更新

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"imgurl" : imgurl,"link_type" : link_type,"select_value" : select_value,"detail_value" : detail_value,"mod_padding" : mod_padding,"link":link,"supply_id" : supply_id},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}

function update_mod4(customer_id,diy_tem_contid,title,mod_padding,nav_title,is_show,imgurl,pic_title,link_type,select_value,detail_value,nav_css_type,link,supply_id){  //楼层专区更新
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"mod_padding" : mod_padding,"nav_title" : nav_title,"is_show" : is_show,"imgurl" : imgurl,"pic_title" : pic_title,"link_type" : link_type,"select_value" : select_value,"detail_value" : detail_value,"nav_css_type" : nav_css_type,"link":link,"supply_id" : supply_id},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod5(customer_id,diy_tem_contid,title,mod_padding,nav_title,is_show,imgurl,pic_title,link_type,select_value,detail_value,start_time,end_time,link,supply_id){  //活动模板块更新
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"title" : title,"mod_padding" : mod_padding,"nav_title" : nav_title,"is_show" : is_show,"imgurl" : imgurl,"pic_title" : pic_title,"link_type" : link_type,"select_value" : select_value,"detail_value" : detail_value,"start_time" : start_time,"end_time" : end_time,"link":link,"supply_id" : supply_id},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod6(customer_id,diy_tem_contid,imgurl,pic_title,link_type,select_value,detail_value,mod_padding,nav_css_type,is_show,link,supply_id){  //活动模板块更新
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"imgurl" : imgurl,"pic_title" : pic_title,"link_type" : link_type,"select_value" : select_value,"detail_value" : detail_value,"mod_padding" : mod_padding,"nav_css_type" : nav_css_type,"is_show" : is_show,"link":link,"supply_id" : supply_id},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod7(customer_id,diy_tem_contid,css_type,pro_name_show,pro_num_show,show_sale,mod_padding,pic_title,imgurl,link_type,select_value){  //分类产品更新
	
	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"pro_name_show" : pro_name_show,"pro_num_show" : pro_num_show,"show_sale" : show_sale,"mod_padding" : mod_padding,"pic_title" : pic_title,"imgurl" : imgurl,"link_type" : link_type,"select_value" : select_value},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function update_mod8(customer_id,diy_tem_contid,imgurl,link_type,select_value,detail_value,mod_padding,nav_css_type,link,supply_id){  //搜索更新模板

	var op="update_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid,"imgurl" : imgurl,"link_type" : link_type,"select_value" : select_value,"detail_value" : detail_value,"mod_padding" : mod_padding,"nav_css_type" : nav_css_type,"link":link,"supply_id" : supply_id},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);
			}
		
		});
}
function _alert(content){
    layer.alert(content,{shade: 0,shift:0});
}

//显示产品选择框
function showSelectProduct(data){	
	var _li = $('.ctrl-item-list-li');
	$.each(_li,function(index, element) {
		var position = $(this).data('position');
		var sort = $(this).data('sort');
		var _sort = $(this).data('sort');
		
		if( sort == 50 ){
			sort = 0;
		}
		
		if( (data.content.dataset[position][sort].link_type == 3 || data.content.dataset[position][sort].link_type == 6) && data.content.dataset[position][sort].select_value > 0 ){
			changeProductType(data.content.dataset[position][sort].select_value,_sort,data.content.dataset[position][sort].detail_value);
		}
		
	});
}

//整理图片和链接
function arrange_pic(data){
	var result		 = new Array();
	var pic 		 = new Array();	//图片
	var pic_title 	 = new Array();	//图片标题
	var link_type 	 = new Array();	//链接类型
	var link     	 = new Array();	//外部网址连接
	var select_value = new Array();	//链接选择的值
	var detail_value = new Array();	//选择产品的id
	var start_time	 = new Array();	//开始时间
	var end_time	 = new Array();	//结束时间
	var dataset_len  = data.content.dataset.length;
	
	//按模块整理图片
	for ( var i = 0; i < dataset_len; i++ ){
		var pic_str 		 = '';
		var pic_title_str 	 = '';
		var link_type_str 	 = '';
		var link_str 	     = '';
		var select_value_str = '';
		var detail_value_str = '';
		var start_time_str 	 = '';
		var end_time_str 	 = '';
		var len = data.content.dataset[i].length;	//模块内图片数量
		//拼接数据
		for ( var j = 0; j < len; j++ ){
			pic_str 		 += data.content.dataset[i][j].pic+'|';
			pic_title_str 	 += data.content.dataset[i][j].title+'|';
			link_type_str 	 += data.content.dataset[i][j].link_type+'|';
			link_str 	     += data.content.dataset[i][j].link+'|';
			select_value_str += data.content.dataset[i][j].select_value+'|';
			detail_value_str += data.content.dataset[i][j].detail_value+'|';
			if ( data.content.dataset[i][j].start_time != '' ){
				start_time_str 	 += data.content.dataset[i][j].start_time+'|';
			}
			if ( data.content.dataset[i][j].end_time != '' ){
				end_time_str 	 += data.content.dataset[i][j].end_time+'|';
			}
			
		}
		//去掉最后一个字符
		pic[i] 			= pic_str.substring(0,pic_str.length-1);
		pic_title[i] 	= pic_title_str.substring(0,pic_title_str.length-1);
		link_type[i] 	= link_type_str.substring(0,link_type_str.length-1);
		link[i] 	    = link_str.substring(0,link_str.length-1);
		select_value[i] = select_value_str.substring(0,select_value_str.length-1);
		detail_value[i] = detail_value_str.substring(0,detail_value_str.length-1);
		if ( start_time_str != '' ){
			start_time[i] 	= start_time_str.substring(0,start_time_str.length-1);
		}
		if ( end_time_str != '' ){
			end_time[i] 	= end_time_str.substring(0,end_time_str.length-1);
		}
	}
	//返回结果
	result['pic'] 			= pic;
	result['pic_title'] 	= pic_title;
	result['link_type'] 	= link_type;
	result['link'] 	        = link;
	result['select_value'] 	= select_value;
	result['detail_value'] 	= detail_value;
	result['start_time'] 	= start_time;
	result['end_time'] 		= end_time;
	return result;
}

//整理导航栏显示或隐藏
function nav_is_show(data){
	var is_show_str = '';
	var is_show_len = data.content.is_show.length;	//导航栏个数
	//拼接数据
	for ( var i = 0; i < is_show_len; i++ ){
		is_show_str += data.content.is_show[i]+'|';
	}
	is_show_str = is_show_str.substring(0,is_show_str.length-1);	//去掉最后一个字符
	
	return is_show_str;
}

//导航样式
function arrange_nav_css_type(data){
	var nav_css_type_str = '';
	var nav_css_type_len = data.content.nav_css_type.length;	//导航个数
	//拼接数据
	for ( var i = 0; i < nav_css_type_len; i++ ){
		nav_css_type_str += data.content.nav_css_type[i]+'|';
	}
	nav_css_type_str = nav_css_type_str.substring(0,nav_css_type_str.length-1);	//去掉最后一个字符
	
	return nav_css_type_str;
}

