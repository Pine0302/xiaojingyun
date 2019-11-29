$(function(){
	//增加模块
	$(".j-diy-addModule").click(function() {
		var type=$(this).data('type');
		  //默认数据
		  var moduleDate={
		  		id:getId(),//模块ID 
		  		type:type,//模块类型
				fixed_link:fixedlink,//固定链接
				type_arr:typearr,//产品分类
                brand_arr:brandarr,//品牌供应商
                package_lists:package_lists,//礼包列表
                template_link:template_link,//一级分类页模板
                room_link:room_link,//微视直播系统
                supply_type_arr:supply_type_arr,//品牌供应商产品分类
                o_supply_type_arr:o_supply_type_arr,//品牌供应商分类
		  		sort:0, //排序
		  		supply_id:supply_id, //供应商id
		  		content:null//模块内容
		  };
		   //根据模块类型设置默认值
		   switch(type){
		   		//品牌推荐
		   		case 1:
		   			moduleDate.content={
						title: '品牌推荐',
						title_en: 'BRAND',
						mod_describe: 'CONTEMPORARY ITEMS BEST ONLINE SHOPPING PLACE FOR YOU',
						padding: 0,
						tab: 1,			//导航栏
						nav_title: '|精选品牌|国际品牌|专柜大牌|时尚潮牌',
						is_show: [1,1,1,1,1],
						floor: 1,		//楼层
						dataset:[
		   					[
								{mod_sort:1,link:"",title:"品牌推荐",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''}  
							],
							[
								{mod_sort:1,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"国际品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"专柜大牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"时尚潮牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							]
		   				]
		   			};
					
		   		break;
				//竖型广告	
				case 2:
                moduleDate.content={
		   				padding:0,
						nav_css_type: [1],	//样式
						dataset:[
							[
								{mod_sort:1,link:"",title:"竖型广告",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"竖型广告",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"竖型广告",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"竖型广告",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"竖型广告",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''}
							]							
		   				]
					};
                break;
				//横型广告
				case 3:
                moduleDate.content={
                    	padding: 0,
						dataset:[
							[
								{mod_sort:1,link:"",title:"横型广告",pic:'images/line.jpg',link_type:'',select_value:"",detail_value:''}
							]
		   				]
					};
                break;
				//楼层专区
				case 4:
		   			moduleDate.content={
						title: '图书专区', 			//楼层标题
						floor_number: 1, 			//楼层号
						tab: 0,						//导航栏
						nav_css_type: [1,1,1,1],	//导航栏样式
						padding: 0,
						nav_title: '时尚女装|优质男装|热门推荐|精选品牌',
						is_show: [1,1,1,1],
						dataset:[
		   					[
								{mod_sort:1,link:"",title:"时尚女装",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"时尚女装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"优质男装",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"优质男装",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"热门推荐",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"热门推荐",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"精选品牌",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"精选品牌",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:''}
							]
		   				]
		   			};
                break;
				//活动模板块
		   		case 5:
		   			moduleDate.content={
						title: '活动模板块',
						padding: 0,
						tab: 1,			//导航栏
						nav_title: '今日推荐|新品预售|品牌活动|猜你喜欢',
						is_show: [1,1,1,1],
						dataset:[
		   					[
								{mod_sort:1,link:"",title:"今日推荐",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:'',start_time: '',end_time: ''}  
							],
							[
								{mod_sort:1,link:"",title:"新品预售",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:'',start_time: '',end_time: ''}
							],
							[
								{mod_sort:1,link:"",title:"品牌活动",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:'',start_time: '',end_time: ''}
							],
							[
								{mod_sort:1,link:"",title:"猜你喜欢",pic:'images/img3.jpg',link_type:'',select_value:"",detail_value:'',start_time: '',end_time: ''}
							]
		   				]
		   			};					
		   		break;
				//多分类橱窗
		   		case 6:
		   			moduleDate.content={
						padding: 0,
						tab: 1,			//导航栏	
						nav_css_type: [1,1,1,1,1,1],	//导航栏样式	
						is_show: [0,1,1,1,1,1],						
						dataset:[
							[
								{mod_sort:1,link:"",title:"分类一",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"分类二",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"分类三",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"分类四",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"分类五",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"分类六",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:12,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:13,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:12,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:13,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:12,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:13,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:12,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:13,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:12,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:13,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''}
							],
							[
								{mod_sort:1,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:2,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:3,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:4,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:5,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:6,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:7,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:8,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:9,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:10,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:11,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:12,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''},
								{mod_sort:13,link:"",title:"",pic:'images/img2.jpg',link_type:'',select_value:"",detail_value:''}
							]
		   				]
		   			};
					
		   		break;
				//分类产品
		   		case 7:
		   			moduleDate.content={
                    	css_type:1,
		   				padding:0,
		   				pro_name_show:1,
		   				pro_num_show:4,
		   				show_sale:1,
						dataset:[
		   					[
								{mod_sort:null,link:"",title:'分类产品',pic:'分类产品',link_type:'-1',select_value:"-1",detail_value:''}
							]
		   				]	
		   			}
		   		break;
				//轮播图	
				case 8:
                moduleDate.content={
		   				padding:0,
						nav_css_type: [1],	//样式
						dataset:[
							[
								{mod_sort:1,link:"",title:"轮播图",pic:'images/img1.jpg',link_type:'',select_value:"",detail_value:''}
							]							
		   				]
					};
                break;
		   }
		   custom_add(moduleDate);
		});
/*get ID*/
getId = function() {
        var date = new Date();
        return "" + diy_temid + date.getFullYear() + parseInt(date.getMonth() + 1) + date.getDate() + date.getHours() + date.getMinutes() + date.getSeconds() + date.getMilliseconds();
    };
    $(".WSY_homeleft_middle").sortable({
        	placeholder: "drag-highlight",
        	stop: function(event,ui) {
            custom_repositionCtrl(ui.item, $(".type-ctrl-item[data-origin='item']")); //重置ctrl的位置
			//移动模块后，楼层号排序
			data_list = floorSort(data_list);
			
        }
    	}).disableSelection();
});
//模块排序
function moduleSort(arr) {
	$.each(arr,function(){
        this.sort = this.dom_conitem.index();
    }) 
	
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
//楼层号排序
function floorSort(_list){
	_list = moduleSort(_list);	//排序
	
	var floor_number = 1;
	var floor_number_bak = 1;
	var _type = 0;
	var html = '';
	$('#nav-left-floor').html('');	//清空浮动楼层
    for( var i = 0; i < _list.length; i++ ){
        _type = _list[i].type;	//模块类型
		
        if( _type == 4 ){
			_list[i].content.floor_number = floor_number;		//楼层号
			$('#floor_'+_list[i].id).html(floor_number+'F');	//更新楼层号
			
			floor_number_bak = $('a[name=floor-'+_list[i].id+']').data('floor_number');
			
			html += '<div class="nav-chd-left"><a href="#floor-'+floor_number_bak+'"><span class="normal-span">'+floor_number+'F</span></a><span class="focus-span main-theme-bg">'+floor_number+'F&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="floor_number_'+floor_number_bak+'">'+_list[i].content.title+'</span></span></div>';
			
			floor_number++;
        }
    }
	
	html += '<div class="nav-chd-left"><span class="nav-go-top" id="backToTop-up"><i class="fa fa-angle-up"></i></span></div>';
	$('#nav-left-floor').append(html);	//浮动楼层插入楼层号
	
	return _list;
}
