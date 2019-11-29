(function($){
    var ms = {
        init:function(obj,args){
            return (function(){
                ms.fillHtml(obj,args);
                ms.bindEvent(obj,args);
            })();
        },
        //填充html
        fillHtml:function(obj,args){
            return (function(){
                args.current = parseInt(args.current)
                args.pageCount = parseInt(args.pageCount)
                obj.empty();
                //obj.append('<ul class="WSY_pageleft">');
                var html='<ul class="WSY_pageleft" style="width: 70%;">';
                var html1="";
                //中间页码
                if(args.current != 1 && args.current >= 4 && args.pageCount != 4){
                    //obj.append('<li class="tcdNumber">'+1+'</li>');
                    console.log(args.current);
                    html+='<li class="tcdNumber">'+1+'</li>';
                }
                if(args.current-2 > 2 && args.current <= args.pageCount && args.pageCount > 5){
                    //obj.append('<span>...</span>');
                    html+='<span>...</span>';
                }
                var start = args.current -2,end = args.current+2;
                if((start > 1 && args.current < 4)||args.current == 1){
                    end++;
                }
                if(args.current > args.pageCount-4 && args.current >= args.pageCount){
                    start--;
                }
                for (;start <= end; start++) {
                    if(start <= args.pageCount && start >= 1){
                        if(start != args.current){
                            //obj.append('<li class="tcdNumber">'+ start +'</li>');
                            html+='<li class="tcdNumber">'+ start +'</li>';
                        }else{
                            //obj.append('<li class="one">'+ start +'</li>');
                            html+='<li class="one">'+ start +'</li>';
                            html1='<div class="WSY_searchbox">';
                            html1+='<input class="WSY_page_search" name="WSY_jump_page" id="WSY_jump_page" value="">';
                            html1+='<input class="WSY_jump" type="button" value="跳转" onclick="jumppage()"></div>';
                        }
                    }
                }

                if(parseInt(args.current) + 2 <= parseInt(args.pageCount) - 1 && parseInt(args.current) >= 1 && parseInt(args.pageCount) > 5){
                    //obj.append('<span>...</span>');
                    html+='<span>...</span>';
                }
                if(args.current != args.pageCount && args.current < parseInt(args.pageCount) -2  && args.pageCount != 4){
                    //obj.append('<li class="tcdNumber">'+args.pageCount+'</li>');
                    html+='<li class="tcdNumber">'+args.pageCount+'</li>';
                }

                html+=html1+'</ul>';
                obj.append(html);
                //上一页
                var html2='<ul class="WSY_pageright">';
                //obj.append('<ul class="WSY_pageright">');
                if(args.current > 1){
                    // obj.append('<li class="WSY_previous"></li>');
                    // html2+='<li class="WSY_previous"></li>';
                    html2+='<li class="WSY_previous" style="width:90px; text-indent:2.8em; background-repeat:no-repeat;background-position: 10% 50%;">上一页</li>';
                }else{
                    obj.remove('.WSY_previous');
                    //obj.append('<span class="disabled">上一页</span>');

                }
                //下一页
                if(args.current < args.pageCount){
                    //obj.append('<li class="WSY_next">下一页</li>');
                    html2+='<li class="WSY_next" style="width:90px;>下一页</li>';
                }else{
                    obj.remove('.WSY_next');
                    //obj.append('<span class="disabled">下一页</span>');
                }
                html2+='</ul>';
                obj.append(html2);
            })();
        },
        //绑定事件
        bindEvent:function(obj,args){
            return (function(){
                obj.on("click","li.tcdNumber",function(){
                    var current = parseInt($(this).text());
                    ms.fillHtml(obj,{"current":current,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current);
                    }
                });
                //上一页
                obj.on("click","li.WSY_previous",function(){
                    var current = parseInt(obj.find('.WSY_pageleft').children("li.one").text());
                    ms.fillHtml(obj,{"current":current-1,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current-1);
                    }
                });
                //下一页
                obj.on("click","li.WSY_next",function(){
                    var current = parseInt(obj.find('.WSY_pageleft').children("li.one").text());
                    ms.fillHtml(obj,{"current":current+1,"pageCount":args.pageCount});
                    if(typeof(args.backFn)=="function"){
                        args.backFn(current+1);
                    }
                });
            })();
        }
    }
    $.fn.createPage = function(options){
        var args = $.extend({
            pageCount : 10,
            current : 1,
            backFn : function(){}
        },options);
        ms.init(this,args);
    }
})(jQuery);