$(function(){
	//增加模块
	$(".j-diy-addModule").click(function() {
		var type=$(this).data('type');
		  //默认数据
		  var moduleDate={
		  		id:getId(),//模块ID 
		  		type:type,//模块类型
		  		sort:0, //排序
		  		content:null,//模块内容
		  		fixed_link:fixedlink,//固定分类
		  		package_lists:package_lists,//固定分类
				type_arr:typearr,//产品分类
		  		img_info:imginfo,//图文
				coupon_info:couponinfo,//优惠券
				is_coupon:is_coupon,//优惠券开关
				city_food:cityfood,//城市商圈美食
                city_ktv:cityktv,//城市商圈ktv
                city_hotel:cityhotel,//城市商圈酒店
                city_shop:cityshop,//城市商圈线下商城
				is_cityarea_caterer:is_cityarea_caterer,//城市商圈（美食）
				is_cityarea_ktv:is_cityarea_ktv,//城市商圈（ktv）
				is_cityarea_hotel:is_cityarea_hotel,//城市商圈（酒店）
				is_cityarea_shop:is_cityarea_shop,//城市商圈（线下商城）
				cityarea_industry:cityarea_industry,//商圈行业
				is_cityarea:is_cityarea,//城市商圈，渠道开关
				brand_arr:brandarr,//品牌供应商
				areaData:areaData,//省、市
				room_link:room_link,//微视直播系统
				template_link:template_link,//已启用的模板
				cityarea_shop_protype_arr:cityarea_shop_protype_arr,//线下商城产品分类
				cityarea_shop_type_arr:cityarea_shop_type_arr,//线下商城店铺分类
				cityarea_shop_arr:cityarea_shop_arr,//线下商城店铺
				red_score_member_link:red_score_member_link, //红积分会员卡
				
		  };
		   //根据模块类型设置默认值
		   switch(type){
		   		//搜索栏
		   		case 1:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"请输入搜索关键字",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:1,
		   				pro_title_twoline:null,
		   				pro_numshow:null,
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:"#fff",
						rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:0,
					dataset:[
		   					{mod_sort:1,link:"#",title:"图片",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/logo1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					// {mod_sort:2,link:"#",title:"图片",pic:'images/logo.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}                          
		   				]	
		   			};
		   			break;
		   		
		   		//订单显示
		   		case 21:
		   			moduleDate.content={
                    	css_type:1,
                    	css_show:1, 
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				icon_pic:'/weixinpl/mshop/images/info_image/s_order.png',
		   				li_title:'商城订单',
		   				select_value:'商城订单',
						dataset:[
								{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'待发货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待收货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],
						dataset0:[
								{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'待发货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待收货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],
						dataset1:[
								{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'待确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待使用',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],dataset2:[
								{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'待发货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待收货',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],dataset3:[
								{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'待使用',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],dataset4:[
								{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'待确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待消费',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],dataset4:[
								{mod_sort:1,link:"#",title:'待付款',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'待确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待消费',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],dataset5:[
								{mod_sort:1,link:"#",title:'未确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'已确认',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'已取消',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'已支付',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta4.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:5,link:"#",title:'未支付',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta5.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],dataset6:[
								{mod_sort:1,link:"#",title:'待接单',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'进行中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'待评价',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:4,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}								
						],dataset7:[
								{mod_sort:1,link:"#",title:'优惠买单',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'代金券',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'套餐',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
						],dataset8:[
								{mod_sort:1,link:"#",title:'全部',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:2,link:"#",title:'已完成',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta2.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
								{mod_sort:3,link:"#",title:'售后中',pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/order-sta3.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
						],dataset9:[
						]
		   			}
		   			break;
		   		//数据显示
		   		case 22:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				data_num:2,
		   				pro_title_show:1,
		   				pro_title_twoline:null,
		   				pro_numshow:null,
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:null,
						rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:0,
		   				has_red_score_member:has_red_score_member,    //是否有红积分会员卡
                    dataset:[
		   					{mod_sort:1,link:"#",title:"零钱",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"零钱",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1,member_id:''},
		   					{mod_sort:2,link:"#",title:"会员卡积分",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"会员卡积分",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1,member_id:''}
		   				],
		   			dataset1:[
		   				{mod_sort:1,link:"#",title:"零钱",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"零钱",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1,member_id:''},
		   				{mod_sort:2,link:"#",title:"会员卡积分",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"会员卡积分",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1,member_id:''}
		   			],
		   			dataset2:[
		   				{mod_sort:1,link:"#",title:"零钱",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/fun_icon9.png',color:'#333',color1:'#888',foreign_id:'',detail_id:'',link_type:'',select_value:"零钱",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1,member_id:''},
		   			]
                }
                break;
                //功能模块
		   		case 23:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				data_num:4,
		   				pro_title_show:1,
		   				pro_title_twoline:null,
		   				pro_numshow:null,
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:null,
						rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:0,
		   				dataset:[
		   					{mod_sort:1,link:"#",title:"我的资产",pic:'/weixinpl/back_newshops/Base/personalization/personal_center/images/data-icon.png',color:'#333',foreign_id:'',detail_id:'',link_type:'',select_value:"我的资产",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					
		   				]
		   			}
		   			break;
		   		//订单模块二
		   		case 24:
		   			moduleDate.content={
                    	css_show:1, 
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				dataset:[
		   					{mod_sort:1,link:"#",title:"商城订单",pic:'/weixinpl/mshop/images/info_image/s_order.png',color:'#333',foreign_id:'',detail_id:'',link_type:'',select_value:"商城订单",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					
		   				]
		   			}
		   			break;
					//分割线
		   		case 7:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:null,
		   				padding:0,
		   				margin:null,
		   				pro_title_show:null,
		   				pro_title_twoline:null,
		   				pro_numshow:null, 
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:null,
						rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:0,
		   				dataset:[
		   					{mod_sort:1,link:"#",title:"分割线",pic:"/weixinpl/back_newshops/Base/personalization/personal_center/images/line.jpg",color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"1_我的资产",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   					
		   				]
		   			}
		   			break;
		   			
						
						//滚动公告栏
				case 13:
				moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:null,
		   				pro_title_twoline:null,
		   				pro_numshow:null,
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:"#fff",
		   				rolling_direction:1,
		   				rolling_speed:10,
		   				show_time_limit:1,
                        text_length:'5',
                        title:"滚动公告栏",
						dataset:[
		   					{mod_sort:1,link:"#",title:"滚动公告栏",pic:'',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1,text_length:'5',}
		   				]	
		   			};
					break;
						
				//个人中心头部
				case 17:
					moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:null,
		   				pro_title_twoline:null,
		   				pro_numshow:2,
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:null,
		   				rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:1,
		   				sort_type:0,
						icon_pic:"",
					dataset:[
		   					{mod_sort:1,link:"#",title:"个人中心头部",pic:'',color:'#000',foreign_id:-1,detail_id:'',link_type:'',select_value:'1_16',detail_value:'',detail_name:"",start_time:"",end_time:""}
		   				]	
		   			};
					break;
				
                
                
		   }
		   //console.log(moduleDate);
		   custom_add(moduleDate);
		});
        
 
        
/*get ID*/
getId = function() {
        var date = new Date();
        return "" + date.getFullYear() + parseInt(date.getMonth() + 1) + date.getDate() + date.getHours() + date.getMinutes() + date.getSeconds() + date.getMilliseconds();
    };
//新增模板时，默认新增一个个人中心头部
/*
 if(action == "add"){
    var moduleDate={
		id:getId(),//模块ID 
		type:17,//模块类型
		sort:0, //排序
		content:null//模块内容
	};
	 moduleDate.content={
		css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:null,
		   				pro_title_twoline:null,
		   				pro_numshow:2,
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:null,
		   				rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:1,
		   				sort_type:0,
						icon_pic:"",
					dataset:[
		   					{mod_sort:1,link:"#",title:"个人中心头部",pic:'',color:'#000',foreign_id:-1,detail_id:'',link_type:'',detail_value:'1_17',detail_name:"",start_time:"",end_time:""}
		   				]
	   }
	
	custom_add(moduleDate);
    }*/
    
    $(".WSY_homeleft_middle").sortable({
        	placeholder: "drag-highlight",
        	stop: function(event,ui) {
            custom_repositionCtrl(ui.item, $(".type-ctrl-item[data-origin='item']")); //重置ctrl的位置
        }
    	}).disableSelection();
});
