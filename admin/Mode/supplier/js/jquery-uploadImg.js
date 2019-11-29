/*2018-3-22 前端-刘武杰*/
(function($){
	var lan_obj=typeof lan_HTML == "undefined"? "": lan_HTML;
	var lan_obj_del=lan_obj.del||"删除";
	var lan_obj_sure=lan_obj.sure||"确认";
	var lan_obj_tips=lan_obj.tips||"提示";
	var can_file_type=lan_obj.can_file_type||"上传文件格式错误，请重新上传！";
	var can_file_size=lan_obj.can_file_size||"上传文件过大，请重新上传！"
	
    $.fn.UpLoadImg = function(opt){
        console.log(opt);
        var that = $(this);
        var defaults = {
            type:/image\/\w+/,
            max:1,
            maxsize:500*1024,
            file_name : opt.name, //增加文件上传默认name属性
            width:$(that).data('width') ? $(that).data('width'):120,
            height:$(that).data('height') ? $(that).data('height'):120,
            success:function(file){},
            delete:function(obj){}
        }
        var opt  = $.extend(defaults,opt);

        function change_file(obj){
            $(obj).on('change',function(){
                var file = this.files[0];
                if(!opt.type.test(file.type)){
                	bootbox.alert({
                		title:'提示',
                		message:"上传文件格式错误，请重新上传！",
                		buttons: {
	                        ok: {  
	                            label: '确认',
	                        }  
                    	}
                	});
                    return false;
                }
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function(e) {
                    result = e.target.result;
                    defaults(result);
                };
            })
        }
        

        var str = '<div class="uploadimg-box" style="width: '+opt.width+'px; height: '+opt.height+'px;">';
        str += '<input class="file-main" type="file" multiple="multiple" name="'+opt.file_name+'"><div class="del-upload">删除</div>';
        str += '<div class="img-default"></div>';
        str += '<div class="img-box" style="width: '+opt.width+'px; height: '+opt.height+'px;"><img class="img-show"></div>';
        str += '</div>';

        var html = '<div class="del-upload">删除</div>';
        html += '<div class="img-default"></div>';
        html += '<div class="img-box" style="width: '+opt.width+'px; height: '+opt.height+'px;"><img class="img-show"></div>';

        that.find('.uploadimg-box').css({width:opt.width,height:opt.height});
        that.find('.uploadimg-box').append(html);

        that.find('.uploadimg-box').each(function(){
            var data_src = $(this).data('src');

            if(data_src){

                $("#is_load_logo").val('1')
                $(this).find('.del-upload').show();
                $(this).find('.img-default').hide();
                $(this).find('.img-show').attr('src',data_src).css('opacity',1);
                $(this).addClass('isload');
            }
        })

        this.each(function(){
            that.on('change',':file',function(e){
                var file = this.files[0];
                if(!file){
                    return false;
                }
                var file_box = $(this).parent();
                if(!opt.type.test(file.type)){
                	bootbox.alert({
                		title:'提示',
                		message:'"上传文件格式错误，请重新上传！"',
                		buttons: {
	                        ok: {  
	                            label: '确认',
	                        }  
                    	}
                	});
//                  bootbox.alert({title:'提示',message:'上传文件格式错误，请重新上传！'});
                    this.value = '';
                    return false;
                }
                if(file.size > opt.maxsize){
                	bootbox.alert({
                		title:'提示',
                		message:"上传文件过大，请重新上传！",
                		buttons: {
	                        ok: {  
	                            label: '确认',
	                        }  
                    	}
                	});
//                  bootbox.alert({title:'提示',message:'上传文件过大，请重新上传！'});
                    this.value = '';
                    return false;
                }
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function(e) {
                    result = e.target.result;
                    file_box.find('.img-show').attr('src',result).css('opacity',1);
                    file_box.find('.del-upload').show();
                    file_box.find('.img-default').hide();
                    file_box.addClass('isload');
                    opt.success(file);
                    if(that.find('.uploadimg-box').length < opt.max && file_box.next().length < 1){
                        that.append(str);
                    }else{
                        return false;
                    }

                };
            });

            that.on('click','.del-upload',function(){
                var del = $(this).parent();
                if(that.attr('division') == 'logo'){
                    $("#del_logo").val('yes');
                    $("#is_load_logo").val('2');
                }

                if(that.attr('division') == 'start_logo'){
                    $("#del_index"+del.index()).val('yes');
                }
                var as = del.attr('del_id');

                if(that.find('.uploadimg-box').length > 1){
                    del.remove();
                    if(that.find('.uploadimg-box').last().hasClass('isload')){
                        that.append(str);
                    }
                }else{

                    del.find('.img-show').attr('src','').css('opacity','0');
                    del.find('.del-upload').hide();
                    del.find('.img-default').show();
                    del.find('.file-main').val('');
                    del.find('.file-main').change();
                }
                opt.delete(del);
            })
        })
    }
})(jQuery)

