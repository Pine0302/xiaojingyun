$(function(){
	//增加模块
	$(".j-diy-addModule").click(function() {
		var type=$(this).data('type');
        console.log(type);
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
                o2o_list_arr:o2o_list_arr//o2o店铺列表
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
		   					{mod_sort:1,link:"#",title:"图片",pic:'images/logo1.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					// {mod_sort:2,link:"#",title:"图片",pic:'images/logo.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}                          
		   				]	
		   			};
		   			break;
		   		//图片广告
		   		case 2:
                moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
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
		   					{mod_sort:1,link:"#",title:"图片",pic:'images/img1.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]
                }
                break;
		   		//分类图标
		   		case 3:
		   			moduleDate.content={
                    	css_type:0,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				nav:0,
		   				pro_title_show:1,
		   				pro_pic_show:0,
		   				all_switch:1,
		   				fix_top:0,
		   				show_num:5,
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
		   					{mod_sort:1,link:"#",title:"图标一",pic:'images/icon01.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:2,link:"#",title:"图标二",pic:'images/icon01.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:3,link:"#",title:"图标三",pic:'images/icon01.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:4,link:"#",title:"图标四",pic:'images/icon01.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
							{mod_sort:5,link:"#",title:"图标五",pic:'images/icon01.png',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]
		   			}
		   			break;
		   		//橱窗二图
		   		case 9:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
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
		   					{mod_sort:1,link:"#",title:"图片一",pic:'images/img2.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:2,link:"#",title:"图片二",pic:'images/img3.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   				]
		   			}
		   			break;
		   		//橱窗三图
		   		case 4:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
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
		   					{mod_sort:1,link:"#",title:"图片一",pic:'images/img2.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:2,link:"#",title:"图片二",pic:'images/img3.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:3,link:"#",title:"图片三",pic:'images/img4.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]
		   			}
		   			break;
		   			//橱窗（四图）
		   		case 8:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
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
		   					{mod_sort:1,link:"#",title:"图片一",pic:'images/img4.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:2,link:"#",title:"图片二",pic:'images/img2.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:3,link:"#",title:"图片三",pic:'images/img3.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:4,link:"#",title:"图片四",pic:'images/img4.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]
		   			}
		   			break;
		   		//分类产品
		   		case 5:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:1,
		   				pro_title_twoline:0,
		   				pro_numshow:2,
		   				show_sale:1,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:null,
						rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:0,
		   				shop_type:0,
		   				divide_type:0,
					dataset:[
		   					{mod_sort:null,link:"#",title:null,pic:null,color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
		   				]	
		   			}
		   			break;
		   		//底部菜单
		   		case 6:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:1,
		   				pro_title_twoline:null,
		   				pro_numshow:null,
		   				show_sale:null,
		   				foot_position:1,
		   				video_link:null,
		   				bg_color:null,
						rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:0,
		   				bottom_id:-1,
		   				dataset:[
		   					/*{mod_sort:1,link:"#",title:"菜单一",pic:'images/i1.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:2,link:"#",title:"菜单二",pic:'images/i1.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:3,link:"#",title:"菜单三",pic:'images/i1.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1},
		   					{mod_sort:4,link:"#",title:"菜单四",pic:'images/i1.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}*/
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
		   					{mod_sort:1,link:"#",title:"分割线",pic:"images/line.jpg",color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   					
		   				]
		   			}
		   			break;
		   			//视频
		   		case 10:
		   			moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:1,
		   				pro_title_twoline:null,
		   				pro_numshow:null,
		   				show_sale:null,
		   				foot_position:1,
		   				video_link:null,
		   				bg_color:null,
						rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:0,
		   				threed_link:null,
		   				dataset:[
		   					{mod_sort:null,link:"#",title:null,pic:null,color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
		   				]
		   			}
		   			break;
					//LBS定位
				case 11:
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
		   					{mod_sort:null,link:"#",title:"LBS定位",pic:'',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]	
		   			};
					break;
						//LBS城市广告
				case 12:
				moduleDate.content={
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
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
						city_name:'北京市',
						location_p:'北京',
                    dataset:[
		   					{mod_sort:1,link:"#",title:"LBS城市广告",pic:'images/img5.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]
                };
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
						dataset:[
		   					{mod_sort:1,link:"#",title:"滚动公告栏",pic:'',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]	
		   			};
					break;
						
						//头部引导页
				case 14:
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
		   				rolling_speed:1,
		   				show_time_limit:1,
					dataset:[
		   					{mod_sort:1,link:"#",title:"头部引导页",pic:'',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
		   				]	
		   			};
					break;

		   		case 15:
                moduleDate.content={
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
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
		   				pro_title_show:0,
		   			dataset:[
		   				{mod_sort:1,link:"#",title:"图片",pic:'images/img1.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1,pro_title_show:0}
		   			]
                }
                break;
                //线下商城店铺展示
				case 16:
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
					dataset:[
		   					{mod_sort:1,link:"#",title:"线下商城店铺展示",pic:'',color:'#000',foreign_id:-1,detail_id:'',link_type:'',select_value:'1_16',detail_value:'',detail_name:"",start_time:"",end_time:""}
		   				]	
		   			};
					break;
				//活动橱窗
				case 17:
		   			moduleDate.content={
                    	css_type:1,  //显示样式
		   				padding:0,
		   				nav:0,  //导航
		   				pro_title_show:1,  //标题显示
		   				pro_title_twoline:0,  //标题显示 0：一行 1：两行
		   				pic_type:1,  //   商品图片  0:封面图  1：产品图
		   				show_sale:1,  //是否显示销量
		   				show_cost:1,  //是否显示原价
		   				show_activity:1,  //是否显示活动价
		   				show_backwards:1, //是否显示开始时间倒数
		   				backwards_day:3,  //提前倒数天数
		   				show_carry:0, //是否显示活动进行时间
		   				show_carry_type:2,  //活动时间样式
		   				text_color:"#FFF",  //时间文字颜色
		   				bg_color:"0,0,0",  //时间底图颜色
						activity_id:"",
						activity_title:"",
						last_title:"",
						production_num:"4",
						dataset:[
		   					{mod_sort:1,link:"#",title:"产品一",pic:"images/img-product.jpg",num:"001",round:"",round_pic:"",round_color:"#FFF",color:'#FFF',money:"",foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""},
		   					{mod_sort:2,link:"#",title:"产品二",pic:"images/img-product.jpg",num:"002",round:"",round_pic:"",round_color:"#FFF",color:'#FFF',money:"",foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:""}
		   				]	
		   			}
		   			break;
		   		// 天气插件
                case 18:
                    moduleDate.content = {
                        css_type: 1,
                        padding: 0,
                        margin: null,
                        all_switch: 0, // 默认关闭
                        dataset: []
                    };
                    break;
                // 社区帖子
                case 19:
                    moduleDate.content = {
                        css_type: 1,
                        placeholder: "",
                        padding: 0,
                        margin: null,
                        all_switch: 0,// 默认关闭
                        dataset: []
                    };
                    break;
                // 云店店头
                case 20:
                var kefu_phone=$(this).data('kefu');
                if(kefu_phone == undefined){kefu_phone==null} 
                    moduleDate.content = {
                        css_type: 1,
                        placeholder: "",
                        padding: 0,
                        yun_consult_show: 1,
                        yun_phone_show: 1,
                        yun_phone:kefu_phone,
                        dataset: []
                    };
                    break;
                // 云店店主产品
                case 21:
                    moduleDate.content = {
                        css_type: 1,
                        placeholder: "",
                        padding: 0,
                    	dataset:[
		   					{mod_sort:1,link:"#",title:"店主精选",pic:'images/add_img.jpg',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:"",detail_value:'',detail_name:"",start_time:"",end_time:"",sel_link_type:1}
		   				]
		   			};
                    break;
                //O2O店铺列表
				case 22:
					moduleDate.content = {
                    	css_type:1,
		   				placeholder:"",
		   				padding:0,
		   				margin:null,
		   				pro_title_show:null,
		   				pro_title_twoline:null,
		   				pro_numshow:15,
		   				show_sale:null,
		   				foot_position:null,
		   				video_link:null,
		   				bg_color:null,
		   				rolling_direction:1,
		   				rolling_speed:1,
		   				show_time_limit:1,
		   				sort_type:0,
                        o2o_grade:1,
                        o2o_price:1,
					    dataset:[
		   					{mod_sort:1,link:"#",title:"O2O店铺列表",pic:'',color:'#000',foreign_id:'',detail_id:'',link_type:'',select_value:'0_22',detail_value:'',detail_name:"",start_time:"",end_time:""}
		   				]	
		   			};
					break;
				
		   	}
		   console.log(moduleDate);
		   custom_add(moduleDate);
		});
/*get ID*/
getId = function() {
        var date = new Date();
        return "" + date.getFullYear() + parseInt(date.getMonth() + 1) + date.getDate() + date.getHours() + date.getMinutes() + date.getSeconds() + date.getMilliseconds();
    };
    $(".WSY_homeleft_middle").sortable({
        	placeholder: "drag-highlight",
        	stop: function(event,ui) {
            custom_repositionCtrl(ui.item, $(".type-ctrl-item[data-origin='item']")); //重置ctrl的位置
        }
    	}).disableSelection();
});