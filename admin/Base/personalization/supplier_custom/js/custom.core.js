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
        if(data.type==6){ //底部菜单添加到contain2；
          $contain2.append($render_conitem); 
        }
        else{
       	$contain.append($render_conitem); 
       }
       	$actionPanel.click();
		var title="";
		var pic="";
		for(var i=0;i<data.content.dataset.length;i++)
		{
			title +=data.content.dataset[i].title+"|";
			pic   +=data.content.dataset[i].pic+"|";
		}
		
		add_mod(customer_id,supplier_id,diy_temid,data.type,data.id,data.content.css_type,title,pic,data.content.foot_position,data.content.placeholder,data.content.pro_numshow,data.content.pro_title_show,data.content.pro_title_twoline,data.content.show_sale);

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
            //console.log(data.id);
            del_mod(customer_id,supplier_id,data.id);
            data.dom_conitem.remove();
            $ctrl.find(".type-ctrl-item[data-origin='item']").remove();
            layer.close(index);
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
	   save_mod(customer_id,supplier_id,diy_temid,returnId(data_list),tempname,bgcolor);
    };
    $('#j-savePage').click(function(){
		var tempname 	=$('input[name=tempname]').val();
		var bgcolor		=$("#colorbg").attr("value");
		if(bgcolor==""){
			bgcolor="#ffffff";
		}
		reCalcPModulesSort(tempname,bgcolor);
				
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
             case 1:custom_event_type1(ctrldom, data);break;//搜索栏
             case 2:custom_event_type2(ctrldom, data);break;//轮播图
             case 3:custom_event_type3(ctrldom, data);break;//分类图标
             case 4:custom_event_type4(ctrldom, data);break;//图片广告
             case 5:custom_event_type5(ctrldom, data);break;//分类产品
            case 6:custom_event_type6(ctrldom, data);break;//底部菜单
            case 7:custom_event_type7(ctrldom, data);break;//底部菜单
            case 8:custom_event_type8(ctrldom, data);break;//橱窗四图
            case 9:custom_event_type9(ctrldom, data);break;//橱窗二图
            case 10:custom_event_type10(ctrldom, data);break;//视频
        }
    };
});


function add_mod(customer_id,supplier_id,diy_temid,type,diy_tem_contid,css_type,title,imgurl,foot_position,placeholder,pro_numshow,pro_title_show,pro_title_twoline,show_sale){ //添加模块
	
	var op="add_mod";
	$.ajax({
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_temid" : diy_temid,"type" : type,"diy_tem_contid" : diy_tem_contid,"css_type" : css_type,"title" : title,"imgurl" : imgurl,"foot_position" : foot_position,"placeholder" : placeholder,"pro_numshow" : pro_numshow,"pro_title_show" : pro_title_show,"pro_title_twoline" : pro_title_twoline,"show_sale" : show_sale},
			dataType: "json",		
			success : function(result) {
			//	console.log(result.msg);	
			}
		
		});
	
}


function del_mod(customer_id,supplier_id,diy_tem_contid){  //删除模板

	var op="del_mod";
	$.ajax({  
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_tem_contid" : diy_tem_contid},
			dataType: "json",		
			success : function(result) {
				console.log(result.msg);
			}
		
		});
	
}

function save_mod(customer_id,supplier_id,diy_temid,content,tempname,bgcolor){ //保存模块顺序
	
	var op="save_mod";
	$.ajax({
			type : "POST",  
			url : "save_model.php",
			data : {"op" : op,"customer_id" : customer_id,"supplier_id" : supplier_id,"diy_temid" : diy_temid,"content" : content,"name" : tempname,"bgcolor" : bgcolor},
			dataType: "json",		
			success : function(result) {
				if(result.code=="1"){
                    layer.alert("保存成功",{shift:2,time:1500});
				}
			}
		
		});
	
}