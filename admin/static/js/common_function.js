/******* 公共函数 *******/

/* 公共ajax加载数据
 * @param string requestUrl 	请求链接
 * @param object requestData 	请求参数
 * @param bit 	async  			TRUE 异步，FALSE 同步
 * @param func 	callbackfunc 	回调方法
 **/
function ajaxGetData( requestUrl, requestData, async, callbackfunc ) {
    // alert("in ajax function");
    $.ajax({
        url : requestUrl,
        type : "POST",
        dataType : "JSON",
        data : requestData,
        async : async,
        success : function(res) {
            // console.log(res); //统一加个输出，方便调试
            //$('body').animate({ scrollTop: 0 }, 'fast');//带动画
            if( callbackfunc ) {

                callbackfunc(res);

            }
        },
        error : function(err) {
            console.log(err); //统一加个输出，方便调试
            //alert(err.statusText);
        }
    })
}

/* 正整数 */
function clearInt(obj){
    if(obj.value.length==1){obj.value=obj.value.replace(/[^1-9]/g,'')}else{obj.value=obj.value.replace(/\D/g,'')}
}

/* 两位小数 */
function clearFloat2(obj){
    obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
    obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}
/* 两位小数 + 不能是0*/
/*function clearFloat2AndNotZero(obj){
	obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
	obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
	obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d[1-9]{0,1}).*$/,'$1$2.$3'); //只能输入两个小数
}*/

/* 首位不能为空格 */
function clearNoEmpty (obj) {
    if ( (/^\s+/).test($(obj).val()) ) {
        obj.value=obj.value.replace(/^\s+/,'');
    }
}

/* 只能输入英文、数字 */
function clearCharANum (obj) {
    /*var _val = obj.value,
        res = '';

    for ( var i = 0; i < _val.length; i++ ) {
        var _char = _val.substr(i, 1);
        if ( (/^[A-Za-z0-9]+$/).test(_char) ) {
            res = res + _char;
        }
    }

    obj.value = res;*/
    obj.value = obj.value.replace(/[\W]/g,'');
}

/* 过滤Emoji */
function clearEmoji(obj) {
    var pattern = /\uD83C[\uDF00-\uDFFF]|\uD83D[\uDC00-\uDE4F]/g;
    obj.value = obj.value.replace(pattern, '');
    if ( (pattern).test(obj.value) ) {
        obj.value = obj.value.replace(/\s+$/g,'');
    }

}

/* 过滤特殊字符 */
function clearTSZF(obj){
    obj.value = stripscript(obj.value);
}
function stripscript(s)
{
    var pattern = new RegExp("[`~!%@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]");
    var rs = "";
    for (var i = 0; i < s.length; i++) {
        rs = rs+s.substr(i, 1).replace(pattern, '');
    }
    return rs;
}


/* 公共异步获取数据
 *
 * 1.使用方法：先定义一个对象tableobj
  var tableobj = {
 		url:'/addons/index.php/voice_online/Applyroom/Applyroom_list',			//异步请求链接
 		countUrl:'/addons/index.php/voice_online/Applyroom/Applyroom_list',			//异步统计请求链接
 		is_count:1,					//是否统计数量
 		pageNum:20,					//每页数据数量
 		clearData:function(){		//清除数据函数
 			$('.apply_room_info').remove();
 		},
 		searchParam:searchParam ,	//搜索参数数组 searchParam['search_room_name'] = [1,''];//[type , value]	type：类型，1：输入框（text）2：选择框（select）
 		callbackfunc:callbackfunc,	//回调函数
  }

  $(function(){ tableinit.main(); })	//页面初始化执行函数

 *
 * 2.搜索方法使用：
  tableinit.searchForm();
  <input style="padding-right:0" type="button" class="search_btn"  onclick="tableinit.searchForm();" value="搜索">

 *3.遍历赋值到html页面
 *	说明：
 *	 var obj = new Array();
 *	 obj[key] = [0,1,2,3]
 *	 @key为class或者ID,
 *	 @0：1 text，2 attr ，3 替换html
 *	 @1 input和替换html 的数据
 *	 @2 attr 的 key
 *	 @3 attr 的 值

	例子：
	var obj = new Array();
	obj['.n1'] = [1,name]						//把.n1元素的文本内容替换为name
	obj['.bg_imgage'] = [2,,'src',bg_imgage]; 	//把.bg_imgage的元素的'src'内容替换为bg_imgage
	obj['.div'] = [3,html]; 					//把.div元素下的html替换为html
	tableinit.show_html(obj);
*
*
*
*4.单一条件查询，对象形式
	searchParam['extend'] = [{'topic_id':topic_id}];						//适用于查询某一条数据
*
*
*
*
*5.使用自定义参数，并合并到原本的参数，最后调用自定义函数，数据返回的处理函数还是使用callbackfunc,但是要对自定义参数进行处理
	a)自定义参数默认值：searchParam['extend'] = [{'type':1}];
	b)主动修改自定义参数：
		var obj = {'type':2};
		searchParam['extend'] = [obj];
		tableinit.searchForm();
	c)callbackfunc:callbackfunc,				//回调函数，结合tableinit._extend处理业务----数据返回的情况
	d)extend_reflsh:extend_reflsh,				//刷新后的回调函数，主要对自定义参数第一次处理----数据还没返回的情况
	e)tableinit._extend;						//修改后的自定义参数，做为回调函数的判断值

 *
 *
 *
 **/
var tableinit = {
    main:function(){
        var searchDataJson = $.getUrlParam('searchJson');  //URL的参数
        if( searchDataJson != '' ) {
            try{
                searchDataJson = JSON.parse(searchDataJson);
                //console.log(searchDataJson['extend']);
                //console.log(searchParam);

                for( i in tableobj.searchParam ) {

                    //console.log(tableobj.searchParam[i]);
                    if(i == 'extend'){


                        tableobj.searchParam['extend']= searchDataJson['extend'];


                    } else{
                        tableobj.searchParam[i][1] = searchDataJson[i];
                        switch( tableobj.searchParam[i][0] ) {
                            case 1:
                                $('#'+i).val(searchDataJson[i]);
                                break;
                            case 2:
                                $('#'+i).find('option[value='+searchDataJson[i]+']').attr('selected','selected')
                                break;
                        }
                    }


                }
                //console.log(tableobj.searchParam);

            }
            catch(e){

            }
        }
        //console.log(searchDataJson['extend']);

        //url参数优先，重新修改搜索参数
        //console.log(searchDataJson['extend']);
        if(tableobj.extend_reflsh){

            if( typeof(searchDataJson['extend']) !='undefined'){
                tableinit._extend = searchDataJson['extend'];				//把搜索的extend保存到全局给外部使用
                tableobj.searchParam['extend'] = searchDataJson['extend'];	//重新修改搜索默认参数
                //tableobj.extend_reflsh();
            }


        }


        tableinit.getData();		//获取数据


        if( tableobj.is_count ) {
            tableinit.countData();	//统计数据数量
        }

        //监听浏览器后退/前进
        if(history.pushState) {
            window.addEventListener("popstate", function() {
                window.location.reload();
            });
        }
    },

    //异步请求参数
    data:{},

    //重置参数
    clearParam:function(){
        for( i in tableobj.searchParam ){
            if(i != 'extend'){
                tableobj.searchParam[i][1] = '';
            }

        }
        tableinit.totalPage = 1;
        tableinit.currentPage = 1;
    },

    //统计数据数量函数
    countData:function(){
        tableinit.arrangeParam(1);
        ajaxGetData(tableobj.countUrl, tableinit.data, true, function(res){
            res = JSON.parse(res);
            tableinit.totalPage = Math.ceil(parseInt(res) / tableobj.pageNum);

            //渲染分页
            tableinit.unbindPageClick();
            tableinit.createPage();
        });
    },

    //统计数据数量函数
    getData:function(){
        tableinit.arrangeParam(2);
        ajaxGetData(tableobj.url, tableinit.data, true, tableobj.callbackfunc);
    },

    //解绑分页事件，避免绑定多个相同事件
    unbindPageClick:function(){
        $(".WSY_page").off('click');
    },

    //渲染分页
    createPage:function(){
        $(".WSY_page").createPage({
            pageCount : tableinit.totalPage,
            current : tableinit.currentPage,
            backFn : function(p) {
                tableinit.currentPage = p;
                tableinit.arrangeUrlParam();
                tableobj.clearData();
                tableinit.getData();
                //tableinit.insertHistory();
            }
        });
    },

    //搜索
    searchForm:function(){
        tableinit.clearParam();

        for( i in tableobj.searchParam ) {
            if(i != 'extend'){
                tableobj.searchParam[i][1] = $('#'+i).val();
            }

        }

        tableinit.arrangeUrlParam();
        tableobj.clearData();
        tableinit.getData();
        if( tableobj.is_count ) {
            tableinit.countData();
        }

        //tableinit.insertHistory();
    },

    //浏览器插入历史记录
    insertHistory:function(){
        var header = $.getUrlParam('header');	//头部导航参数
        var historyUrl = location.href.split("?")[0]+'?customer_id='+customer_id_en+'&'+tableinit.urlParam+'&currentPage='+tableinit.currentPage;
        if( header !== '' ) {
            historyUrl += '&header='+header;
        }
        history.pushState({}, '', historyUrl);
    },

    //整理参数	type：1统计数据数量，2获取数据
    arrangeParam:function(type){
        tableinit.data['op_type'] = type;
        tableinit.data['page'] = tableinit.currentPage;
        tableinit.data['pageNum'] = tableobj.pageNum;
        tableinit.data['customer_id'] = customer_id_en;

        for( i in tableobj.searchParam ) {
            if(i == 'extend'){
                var extend = tableobj.searchParam[i][0];
                tableinit._extend = extend;					//赋值给全局变量
                for(k in extend){
                    tableinit.data[k] = new Array();
                    tableinit.data[k] = extend[k];
                }
            }else{
                tableinit.data[i] = tableobj.searchParam[i][1];
            }


        }
        //console.log(tableinit.data);
    },

    //重组url参数
    arrangeUrlParam:function(){
        //数组转对象
        var jsons = {};
        for( i in tableobj.searchParam ) {

            jsons[i] = tableobj.searchParam[i];
            if(i == 'extend'){
                jsons[i] = tableobj.searchParam[i];
            }else{
                jsons[i] = tableobj.searchParam[i][1];
            }
        }
        //对象转JSON字符串
        jsons = JSON.stringify(jsons);

        tableinit.urlParam = 'searchJson='+jsons;
    },

    show_html:function(res){			//自动显示数据到html
        for( i in res ){
            var val = res[i];
            if(val[0] == 1){			//1：text
                $(i).text(val[1]);
            }else if(val[0] == 2){		//2：attr
                $(i).attr(val[2],val[3]);
            }else if(val[0] == 3){
                $(i).html(val[1]);    //3.替换html
            }else if(val[0] == 4){
                $(i).val(val[1]);    //4.input val()
            }
        }

    },
    //url参数
    urlParam:'',
    //当前页码
    currentPage:($.getUrlParam('currentPage')!='' && !isNaN($.getUrlParam('currentPage')))?$.getUrlParam('currentPage'):1,
    //总页数
    totalPage:1,
    _extend:[],


}

//分页跳转
function jumppage(){
    var a = parseInt($("#WSY_jump_page").val());
    if((a<1) || (a==tableinit.currentPage) || isNaN(a) || a>tableinit.totalPage ){
        return false;
    }else{
        tableinit.currentPage = a;
        tableinit.arrangeUrlParam();
        tableobj.clearData();
        tableinit.getData();
        //tableinit.insertHistory();
        tableinit.unbindPageClick();
        tableinit.createPage();
    }
}

//input框首位不能为空
$(document).on('keyup', 'input', function () {
    clearNoEmpty(this);
})


//输入框按回车键触发搜索
$('#search_form').find('input').on('keydown',function(){
    if( event.keyCode == 13 ){
        tableinit.searchForm();
    }
});


//模拟表单提交
//object:需要创建post数据一对数组 [key:val]
//strurl:跳转链接
function Turn_Post(object, strurl){
    var objform = document.createElement('form');
    document.body.appendChild(objform);

    $.each(object,function(i,value){
        var obj_p = document.createElement("input");
        obj_p.type = "hidden";
        objform.appendChild(obj_p);
        obj_p.value = value['val'];
        obj_p.name = value['key'];
    });

    objform.action = strurl;
    objform.method = "POST"
    objform.submit();

}

/**
 * 检查空数据，空则返回true
 * @param str
 * @returns {boolean}
 */
function check_empty(str) {
    if(str==null || str=="") return true;
    else return false;
}

//刷新当前页面
function refresh(){
    window.location.reload();
}

/******* 公共函数 *******/