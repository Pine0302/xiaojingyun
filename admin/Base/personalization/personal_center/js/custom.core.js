$(function() {
	
    var $contain= $(".WSY_homeleft_middle"), //Diy 内容显示区域
        $contain2= $(".WSY_foot"),//底部菜单显示区域
        $ctrl = $(".WSY_ctrl"), //Diy 控制器显示区域
		$main=$('.WSY_main'),
        data_list=new Array();//所有data数组
    /*
     * 添加模块
     * @param data 模块数据
     */
    get_top=function(){//得到顶部距离
    	return $('.WSY_ctrl').offset().top; 
    }
    custom_add = function(data) {
        //添加手机内容
        var html_con = doT.template($("#type_con_" + data.type).html())(data), //内容
        	html_conitem=doT.template($('#type_conitem').html())({html:html_con}),
        	$render_conitem = $(html_conitem); //渲染模板
		    data.dom_conitem = $render_conitem; //缓存左侧内容dom
        if(data.type==6){ //只允许创建一个菜单6
            for(i=0;i<data_list.length;i++){
                var _type=data_list[i].type;
                if(_type==6){
                    layer.alert('只允许创建一个底部菜单',{shade: 0});
                    return false;
                }
            }
        }
      if(data.type==1){//只允许创建一个菜单2
        for(i=0;i<data_list.length;i++){
          var _type=data_list[i].type;
          if(_type==1){
                layer.alert('只允许创建一个搜索框',{shade: 0});
                return false;
          }
        }
      }
	  if(data.type==11){//只允许创建一个LBS定位
        for(i=0;i<data_list.length;i++){
          var _type=data_list[i].type;
          if(_type==11){
                layer.alert('只允许创建一个LBS定位',{shade: 0});
                return false;
          }
        }
      }
	  if(data.type==12){//创建LBS城市广告必须先创建LBS定位
	    var can_add=false;
        for(i=0;i<data_list.length;i++){
          var _type=data_list[i].type;
          if(_type==11){
                can_add=true;
          }
        }
		if(!can_add){
			layer.alert('必须先创建LBS定位',{shade: 0});
            return false;
		}
      }
	  if(data.type==14){//只允许创建一个头部引导页
        for(i=0;i<data_list.length;i++){
          var _type=data_list[i].type;
          if(_type==14){
                layer.alert('只允许创建一个头部引导页',{shade: 0});
                return false;
          }
        }
      }
	  if(data.type==17){//只允许创建一个个人中心头部
        for(i=0;i<data_list.length;i++){
          var _type=data_list[i].type;
          if(_type==17){
                layer.alert('只允许创建一个个人中心头部',{shade: 0});
                return false;
          }
        }
      }
        data_list.push(data);
		//绑定编辑模块事件
	    var $actionPanel = $render_conitem.find(".type-conitem-action"),
			$btn_edit = $actionPanel.find(".j-edit"),
            $btn_del = $actionPanel.find(".j-del");
			/*if(data.type == 14){	//头部引导不需要编辑
				$btn_edit.hide();
			}*/
        $actionPanel.click(function() {
            $(".type-conitem-action").removeClass("selected");
            $(this).addClass("selected");
            custom_edit(data);
        });
        //绑定删除事件
        $btn_del.click(function() {
                custom_del(data);
                return false;
            });
        //插入文档
        if(data.type==6){ //底部菜单添加到contain2；
          $contain2.append($render_conitem); 
        }
        else{
       	$contain.append($render_conitem); 
       }
       	$actionPanel.click();
		var title="";
		var pic="";
		var start_time="";
		var end_time="";
		var sel_link_type="";
		var select_value="";
		
		for(var i=0;i<data.content.dataset.length;i++)
		{
			title 		+=data.content.dataset[i].title+"|";
			pic   		+=data.content.dataset[i].pic+"|";
			start_time	+=data.content.dataset[i].start_time+"|";
			end_time	+=data.content.dataset[i].end_time+"|";
			sel_link_type	+=data.content.dataset[i].sel_link_type+"|";
			select_value	+=data.content.dataset[i].select_value+"|";
		}
		
		// ctrl_address(data.areaData,"location_p"+data.id,"location_c"+data.id,"",'北京','北京市','');
		if ( data.type == 13 ) {
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
		}
		add_mod(customer_id,diy_temid,data.type,data.id,data.content.css_type,title,pic,data.content.foot_position,data.content.placeholder,data.content.pro_numshow,data.content.pro_title_show,data.content.pro_title_twoline,data.content.show_sale,data.content.rolling_direction,data.content.rolling_speed,data.content.show_time_limit,data.content.city_name,start_time,end_time,data.content.location_p,sel_link_type,select_value,data.content.icon_pic,data.content.li_title);

    };
    /*
     * 查询数据
     * @param data 模块数据
     */
        custom_query = function(data) {
		
		if(data.type == 21){
			var temp = data.content.dataset;
			
			data.content.dataset0 = 
			[
				{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'待发货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'待收货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset1 = 
			[
				{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'待确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'待使用',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset2 = 
			[
				{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'待发货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'待收货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset3 = 
			[
				{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'待使用',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset4 = 
			[
				{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'待确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'待消费',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset5 = 
			[
				{mod_sort:1,link:"#",title:'未确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'已确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'已取消',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'已支付',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:5,link:"#",title:'未支付',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset6 = 
			[
				{mod_sort:1,link:"#",title:'待接单',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'进行中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset7 = 
			[
				{mod_sort:1,link:"#",title:'优惠买单',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'代金券',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'套餐',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset8 = 
			[
				{mod_sort:1,link:"#",title:'全部',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			data.content.dataset9 = 
			[

			];
			data.content.dataset10 = 
			[
				{mod_sort:1,link:"#",title:'全部',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:2,link:"#",title:'待接单',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:3,link:"#",title:'已接单',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:4,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
				{mod_sort:5,link:"#",title:'退款中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
			];
			
			/*	switch(data.content.select_value){
					case '商城订单':
					case '线下商城-配送订单':
						data.content.dataset0 = temp;
						break;
					case '订餐订单':
						data.content.dataset1 = temp;
						break;
					case '外卖订单':
					case '线下商城-配送订单':
						data.content.dataset2 = temp;
						break;
					case 'KTV订单':
					case '酒店':
						data.content.dataset3 = temp;
						break;
					case '线下商城-自提订单':
						data.content.dataset4 = temp;
						break;
					case '金融订单':
						data.content.dataset5 = temp;
						break;
					case '教练服务订单':
						data.content.dataset6 = temp;
						break;
					case '线下收银订单':
						data.content.dataset7 = temp;
						break;
					case '到店付订单':
						data.content.dataset8 = temp;
						break;
					case '拼团订单':
					case '大礼包订单':
					case '票务订单':
						data.content.dataset9 = temp;
						break;
				} */
		}else if(data.type == 22){
			data.content.dataset1 = [
		   				{mod_sort:1,link:"#",title:"零钱",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"零钱",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   				{mod_sort:2,link:"#",title:"会员卡积分",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"会员卡积分",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   			];
			data.content.dataset2 = [
				{mod_sort:1,link:"#",title:"零钱",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"零钱",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
			]
		}
			
        //添加手机内容
        var html_con = doT.template($("#type_con_" + data.type).html())(data), //内容
            html_conitem=doT.template($('#type_conitem').html())({html:html_con}),
            $render_conitem = $(html_conitem); //渲染模板
          // console.log($("#type_con_" + data.type).html())

        data.dom_conitem = $render_conitem; //缓存左侧内容dom
        data_list.push(data);
        //绑定编辑模块事件
        var $actionPanel = $render_conitem.find(".type-conitem-action"),
			$btn_edit = $actionPanel.find(".j-edit"),
            $btn_del = $actionPanel.find(".j-del");
			/*if(data.type == 14){	//头部引导不需要编辑
				$btn_edit.hide();
			}*/
        $actionPanel.click(function() {
            $(".type-conitem-action").removeClass("selected");
            $(this).addClass("selected");
            custom_edit(data);
        });
        //绑定删除事件
        $btn_del.click(function() {
                custom_del(data);
                return false;
            });
        //插入文档
        if(data.type==6){
          $contain2.append($render_conitem); 
        }
        else{
        $contain.append($render_conitem); 
       }
        $actionPanel.click();
    };
      /*
     * 编辑模块
     * @param data 模块数据
     */
    custom_edit = function(data) {
        //移除之前的模块控制内容
        $ctrl.find(".type-ctrl-item[data-origin='item']").remove();
        //渲染模板
        var html_ctrl_panel=$("#type_ctrl").html(),
            html_ctrl_con=doT.template($("#type_ctrl_" + data.type).html())(data), 
            html_ctrl=doT.template(html_ctrl_panel)({html:html_ctrl_con})
            $render_ctrl = $(html_ctrl);
        $ctrl.append($render_ctrl); //插入dom

        custom_repositionCtrl(data.dom_conitem, $render_ctrl); //设置控制内容的位置

        custom_bindEvents($render_ctrl, data); //绑定各种事件
        $('.diy-ctrl-item-b').hide();//隐藏设置页面
        $render_ctrl.show().siblings(".type-ctrl-item").hide(); //显示控制内容，并隐藏其它
    };
        /*
     * 重设控制内容的位置
     * @param conitem 手机视图dom对象
     * @param ctrl 控制内容dom对象
     */
    custom_repositionCtrl = function(conitem, ctrl) {
        var top_conitem = conitem.offset().top-get_top();
        ctrl.css("marginTop", top_conitem);//设置位置
        $("html,body").scrollTop(top_conitem);//滚动页面
        var index_15 = $("li>div").index($('#con_15').parent());
        if( index_15 == 0 ){
          $('#con_15').show()
        }else{
          $('#con_15').hide()
        }
    };
    /*
     * 删除模块
     * @param data 模块数据
     */
    custom_del = function(data) {
        if (!data) return;
        //提示删除
        layer.confirm('确定要删除吗？', {
            title: false,
            skin:'red-skin',
            shift:6,
            btn: ['删除','取消'] //按钮
        }, function(index){
             //从缓存数组中删除
                    var lists = data_list,
                        lists_len = data_list.length;
					if(data.type==11){
						var can_del = false;
						for(var i = 0; i < lists_len; i++){
							if(lists[i].type == 12) {
								layer.alert('先删除LBS城市广告才能删除LBS定位',{shade: 0});
								return false;
							}
						}
					}
                    for (var i = 0; i < lists_len; i++) {
                        if (lists[i].id == data.id) {
                            lists.splice(i, 1);
                            break;
                        }
                    }
            //console.log(data.id);
            del_mod(customer_id,data.id);
            data.dom_conitem.remove();
            $ctrl.find(".type-ctrl-item[data-origin='item']").remove();
            layer.close(index);
            var index_15 = $("li>div").index($('#con_15').parent());
            if( index_15 == 0 ){
              $('#con_15').show()
            }else{
              $('#con_15').hide()
            }
        }, function(){
                
        });
    };
    /*
     * 重新计算装修模块的排序
     */
    reCalcPModulesSort = function(tempname,bgcolor,default_img) {
       $.each(data_list,function(){
            this.sort=this.dom_conitem.index();
       }) 
       data_list=bubbleSort(data_list);//排序
	   save_mod(customer_id,diy_temid,returnId(data_list),tempname,bgcolor);
    };
    $('#j-savePage').click(function(){
		var tempname 	=$('input[name=tempname]').val();
		var bgcolor		=$("#colorbg").attr("value");
		if(bgcolor==""){
			bgcolor="#ffffff";
		}
		reCalcPModulesSort(tempname,bgcolor);
				
    })
	
	  /*
     * 生成个人中心自定义界面
     * @param data 模块数据
     */
     custom_build = function(data) {
        //添加手机内容
        var html_con = doT.template($("#type_con_" + data.type).html())(data) //内容
            $render_conitem = $(html_con); //渲染模板
        //插入文档
        if(data.type==6){
          $foot.append($render_conitem); 
        }else if(data.type==18){ // 顶部菜单添加到
          $head.append($render_conitem);
        }
        else{
          $main.append($render_conitem); 
        }
        var index_15 = $(".WSY_main>div").index($('#con_15'));
        if( index_15 == 0 ){
          $('#con_15').show()
        }else{
         $('#con_15').hide()
        }
    };
    function bubbleSort(arr) {
        var i = arr.length, j;
        var tempExchangVal;
        while (i > 0) {
            for (j = 0; j < i - 1; j++) {
                if (arr[j].sort > arr[j + 1].sort) {
                    tempExchangVal = arr[j];
                    arr[j] = arr[j + 1];
                    arr[j + 1] = tempExchangVal;
                }
            }
            i--;
        }
        return arr;
    }
    function returnId(arr){
        var str="";
        for(var i=0;i<arr.length;i++)
        {
            str=str+arr[i].id+",";
        }
        return str;
    }
    /*
     * 绑定事件
     */
        custom_bindEvents = function(ctrldom, data) {
        //根据不同类型模块绑定相应事件
        var k=parseInt(data.type);
        switch (k) {
             case 1:custom_event_type1(ctrldom, data);break;//搜索栏
            case 7:custom_event_type7(ctrldom, data);break;//分割线
            case 13:custom_event_type13(ctrldom, data);break;//滚动公告栏
            case 17:custom_event_type17(ctrldom, data);break;//个人中心头部
            case 21:custom_event_type21(ctrldom, data);break;//订单显示一
            case 22:custom_event_type22(ctrldom, data);break;//数据模块 
            case 23:custom_event_type23(ctrldom, data);break;//数据模块 
            case 24:custom_event_type24(ctrldom, data);break;//订单显示二
        }
    };
});


function add_mod(customer_id,diy_temid,type,diy_tem_contid,css_type,title,imgurl,foot_position,placeholder,pro_numshow,pro_title_show,pro_title_twoline,show_sale,rolling_direction,rolling_speed,show_time_limit,city_name,start_time,end_time,province,sel_link_type,select_value,icon_pic,li_title){ //添加模块
	
	var op="add_mod";
	$.ajax({
			type : "POST",  
			url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
			data : {"op" : op,"customer_id" : customer_id,"diy_temid" : diy_temid,"type" : type,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"foot_position" : foot_position,"placeholder" : placeholder,"pro_numshow" : pro_numshow,"pro_title_show" : pro_title_show,"pro_title_twoline" : pro_title_twoline,"show_sale" : show_sale,"rolling_direction" : rolling_direction,"rolling_speed" : rolling_speed,"show_time_limit" : show_time_limit,"city_name" : city_name,"start_time" : start_time,"end_time" : end_time,"province" : province,"sel_link_type" : sel_link_type,"select_value" : select_value,"icon_pic" : icon_pic,"li_title" : li_title},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);	
			}
		
		});
	
}


function del_mod(customer_id,diy_tem_contid){  //删除模板

	var op="del_mod";
	$.ajax({  
			type : "POST",  
			url : "/mshop/admin/index.php?m=personal_center&a=save_template_content",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid},
			dataType: "json",		
			success : function(result) {
				console.log(result.msg);
			}
		
		});
	
}

function save_mod(customer_id,diy_temid,content,tempname,bgcolor){ //保存模块顺序
	
	$.ajax({
			type : "POST",  
			url : "/mshop/admin/index.php?m=personal_center&a=save_template",
			data : {"customer_id" : customer_id,"diy_temid" : diy_temid,"content" : content,"name" : tempname,"bgcolor" : bgcolor},
			dataType: "json",		
			success : function(result) {
				if(result.errcode==0){
                    layer.alert("保存成功",{shift:2,time:1500});
				}
			}
		
		});
	
}