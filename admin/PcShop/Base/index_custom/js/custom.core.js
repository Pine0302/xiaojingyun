$(function() {
	
    var $contain= $(".WSY_homeleft_middle"), //Diy 内容显示区域
        $contain2= $(".WSY_foot"),//底部菜单显示区域
        $ctrl = $(".WSY_ctrl") //Diy 控制器显示区域
        data_list=new Array();//所有data数组
    /*
     * 添加模块
     * @param data 模块数据
     */
    get_top=function(){//得到顶部距离
    	return $('.WSY_ctrl').offset().top; 
    }
    custom_add = function(data) {
		if( data.type == 4 ){ 	//楼层专区
			var floor_number = 1;
            for( var i = 0; i < data_list.length; i++ ){
                var _type = data_list[i].type;
                if( _type == 4 ){
					floor_number++;
                }
            }
			
			data.content.floor_number = floor_number;	//楼层号
        }
		
        //添加模板内容
        var html_con = doT.template($("#type_con_" + data.type).html())(data), //内容
        	html_conitem=doT.template($('#type_conitem').html())({html:html_con}),
        	$render_conitem = $(html_conitem); //渲染模板
		    data.dom_conitem = $render_conitem; //缓存左侧内容dom
        
        data_list.push(data);
		//绑定编辑模块事件
	    var $actionPanel = $render_conitem.find(".type-conitem-action"),
			$btn_edit = $actionPanel.find(".j-edit"),
            $btn_del = $actionPanel.find(".j-del");
			
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
       	$contain.append($render_conitem);

       	$actionPanel.click();

		var pic 		= new Array();
		var pic_title 	= new Array();
		var link_url 	= new Array();
		//模块图片
		var content_len = data.content.dataset.length;
		for ( var i = 0; i < content_len; i++ ){
			var pic_len = data.content.dataset[i].length;
			
			pic[i] = '';
			pic_title[i] = '';
			link_url[i] = '';
			for ( var j = 0; j < pic_len; j++ ){
				pic[i] 		 += data.content.dataset[i][j].pic+"|";
				pic_title[i] += data.content.dataset[i][j].title+"|";
				link_url[i]  += data.content.dataset[i][j].link+"|";
			}
			pic[i] 		 = pic[i].substring(0,pic[i].length-1);
			pic_title[i] = pic_title[i].substring(0,pic_title[i].length-1);
			link_url[i]  = link_url[i].substring(0,link_url[i].length-1);
		}
		// console.log(pic);
		//是否显示
		var is_show = '';
		if ( data.content.is_show ){
			var is_show_len = data.content.is_show.length;
			for ( var i = 0; i < is_show_len; i++ ){
				is_show += data.content.is_show[i]+'|';
			}
			is_show = is_show.substring(0,is_show.length-1);	//去掉最后一个字符
		}
		
		//导航样式
		var nav_css_type = '';
		if ( data.content.nav_css_type ){
			var is_show_len = data.content.nav_css_type.length;
			for ( var i = 0; i < is_show_len; i++ ){
				nav_css_type += data.content.nav_css_type[i]+'|';
			}
			nav_css_type = nav_css_type.substring(0,nav_css_type.length-1);	//去掉最后一个字符
		}

		data_list = floorSort(data_list);	//楼层号排序

		add_mod(customer_id,diy_temid,data.type,data.id,data.content.title,data.content.title_en,data.content.mod_describe,data.content.padding,data.content.nav_title,is_show,pic,pic_title,nav_css_type,data.content.floor_number,data.content.css_type,data.content.pro_name_show,data.content.pro_num_show,data.content.show_sale,link_url);

    };
    /*
     * 查询数据
     * @param data 模块数据
     */
        custom_query = function(data) {
        //添加手机内容
        var html_con = doT.template($("#type_con_" + data.type).html())(data), //内容
            html_conitem=doT.template($('#type_conitem').html())({html:html_con}),
            $render_conitem = $(html_conitem); //渲染模板

        data.dom_conitem = $render_conitem; //缓存左侧内容dom
        data_list.push(data);
        //绑定编辑模块事件
        var $actionPanel = $render_conitem.find(".type-conitem-action"),
			$btn_edit = $actionPanel.find(".j-edit"),
            $btn_del = $actionPanel.find(".j-del");
			
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
        $contain.append($render_conitem); 
		
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
					
                    for (var i = 0; i < lists_len; i++) {
                        if (lists[i].id == data.id) {
                            lists.splice(i, 1);
                            break;
                        }
                    }
			data_list = floorSort(data_list);	//楼层号排序
            //console.log(data.id);
            // del_mod(customer_id,data.id);	//不在数据库删除模块，只从缓存中删除
            data.dom_conitem.remove();
            $ctrl.find(".type-ctrl-item[data-origin='item']").remove();
            layer.close(index);
        }, function(){
                
        });
    };
    /*
     * 重新计算装修模块的排序
     */
    reCalcPModulesSort = function(tempname,bgcolor,floating_floor,supply_id,custom_type) {
       $.each(data_list,function(){
            this.sort=this.dom_conitem.index();
       }) 
       data_list=bubbleSort(data_list);//排序
	   var data_list_len = data_list.length;
	   var floor_id_arr = new Array();	//楼层id
	   var floor_number_arr = new Array();	//楼层号
	   
	   for ( var i = 0,j = 0; i < data_list_len; i++ ){
		   if ( data_list[i].type == 4 ){
			   floor_id_arr[j] = data_list[i].id;
			   floor_number_arr[j] = data_list[i].content.floor_number;
			   j++;
		   }
		   
	   }
	   save_mod(customer_id,diy_temid,returnId(data_list),tempname,bgcolor,floating_floor,floor_id_arr,floor_number_arr,supply_id,custom_type);
    };
    $('#j-savePage').click(function(){
		var tempname 		= $('input[name=tempname]').val();
		var bgcolor			= $("#colorbg").attr("value");
		var floating_floor	= $("input[name='floating_floor']:checked").val();
		var custom_type	    = $("#custom_type").val();
		if(bgcolor==""){
			bgcolor="#ffffff";
		}
		
		// var is_continue = true;
		// $("input[name='getImg']").each(function(){
			// if($(this).val().indexOf("//")<0){				
				// is_continue = false;
			// }
		// });
		
		//if(is_continue){
			reCalcPModulesSort(tempname,bgcolor,floating_floor,supply_id,custom_type);
		//}else{
		//	alert('部分图片未设置，请检查！');
		//}		
    })
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
            case 1:custom_event_type1(ctrldom, data);break;//品牌推荐
            case 2:custom_event_type2(ctrldom, data);break;//竖型广告
            case 3:custom_event_type3(ctrldom, data);break;//横型广告
            case 4:custom_event_type4(ctrldom, data);break;//楼层分类专区
            case 5:custom_event_type5(ctrldom, data);break;//活动模板块
            case 6:custom_event_type6(ctrldom, data);break;//多分类橱窗
            case 7:custom_event_type7(ctrldom, data);break;//分类产品
            case 8:custom_event_type8(ctrldom, data);break;//轮播图
        }
    };
});

function add_mod(customer_id,diy_temid,type,diy_tem_contid,title,title_en,mod_describe,mod_padding,nav_title,is_show,imgurl,pic_title,nav_css_type,floor_number,css_type,pro_name_show,pro_num_show,show_sale,link_url){ //添加模块
	var op = "add_mod";
	$.ajax({
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_temid" : diy_temid,"type" : type,"diy_tem_contid" : diy_tem_contid,"title" : title,"title_en" : title_en,"mod_describe" : mod_describe,"mod_padding" : mod_padding,"nav_title" : nav_title,"is_show" : is_show,"imgurl" : imgurl,"pic_title" : pic_title,"nav_css_type" : nav_css_type,"floor_number" : floor_number,"css_type" : css_type,"pro_name_show" : pro_name_show,"pro_num_show" : pro_num_show,"show_sale" : show_sale,"link" : link_url},
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
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_tem_contid" : diy_tem_contid},
			dataType: "json",		
			success : function(result) {
				console.log(result.msg);
			}
		
		});
	
}

function save_mod(customer_id,diy_temid,content,tempname,bgcolor,floating_floor,floor_id_arr,floor_number_arr,supply_id,custom_type){ //保存模块顺序
	// console.log(supply_id);
	var op="save_mod";
	$.ajax({
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"diy_temid" : diy_temid,"content" : content,"name" : tempname,"bgcolor" : bgcolor,"floating_floor" : floating_floor,"floor_id_arr" : floor_id_arr,"floor_number_arr" : floor_number_arr,"supply_id" : supply_id,"custom_type" : custom_type},
			dataType: "json",		
			success : function(result) {
				if(result.code=="1"){
                    layer.alert("保存成功",{shift:2,time:1500});
				}
			}
		
		});
	
}