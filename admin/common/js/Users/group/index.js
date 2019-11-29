$(function(){
    count_red();
    count_coupon();
    count_red_day();
    count_coupon_day();
    count_currency();
    count_currency_day();
    noGroup_crewNum();
    if( nMS >= 0 ){
        GetRTime(surplus_time);
    }

    //分组重命名
    $("#dd_rename_group").click(function(){
        $('#div_red').hide();
        $('#div_coupon').hide();
        $('#div_red2').hide();
        var dd_gname = $("#dd_group_name dl");
        $('#sure_rename_group').show();
        dd_gname.html("<input type='text' id='txt_group_name' value='' style='width:80px;border: solid 1px #ccc;height: 24px;border-radius: 2px;padding-left: 5px;' />");
    });

    //确认分组重命名
    $('#sure_rename_group').click(function(){
        var dd_gname = $("#dd_group_name dl");
        var new_name = $("#txt_group_name").val();
        var length = strlen(new_name);
        console.log(length);
        if(length>10) {layer.alert('不能超过10个中文字，数字，字母!');return;}//不超过10个字符
        layer.confirm('操作后无法恢复，确定操作？', {
            title:'分组重命名',
            btn: ['确定','取消']
        }, function(){
            $.getJSON("group.class.php?customer_id="+customer_id_en,{name:new_name,id:group_id,op:"modify_group_name"},function(json){
                if(json.code == 0){
                    layer.alert(json.msg);
                }else{
                    // dd_gname.html(new_name);
                    // $('#f_gname_'+group_id).html(new_name);
                    location.href='index.php?group_id='+group_id;
                }
            });
        }, function(json) {
            layer.msg('已取消', {
                time: 4000,
                btn: ['确认'],
                icon: 1
            });
        });
    });

    //删除分组
    $("#dd_delete_group").click(function(){
        $('#div_red').hide();
        $("#all_red").attr("disabled", false);
        $('#div_red2').hide();
        $("#all_red2").attr("disabled", false);
        $('#div_coupon').hide();
        $("#all_coupon").attr("disabled", false);
        $("#btn_savegroup").attr("disabled", true);
        /* if(confirm("删除后当前分组下的组员全部归为未分组，是否确定删除？")){
         $.getJSON("group.class.php?customer_id="+customer_id_en,{id:group_id,op:"delete_group"},function(json){
         if(json.code == 0){
         layer.alert(json.msg);
         }else{
         location.href='index.php?group_id=-1';
         }
         });
         } */
        layer.confirm('删除后当前分组下的组员全部归为未分组，是否确定删除？', {
            title:'分组删除',
            btn: ['确定删除','取消']
        }, function(){
            $.getJSON("group.class.php?customer_id="+customer_id_en,{id:group_id,op:"delete_group"},function(json){
                if(json.code == 0){
                    layer.alert(json.msg);
                }else{
                    location.href='index.php?group_id=-1';
                }
            });
        }, function(json){
            layer.msg('已取消删除', {
                time: 4000,
                btn: ['确认'],
                icon:1
            });
        });
    });

    $('#select_send').change(function(ob){
        var _this = $('#select_send').val();
        console.log(_this);
        if(_this == 'batch_coupons'){
            $('#yhq').show();
            $('#open_info').css('visibility','hidden');
        }else if(_this == 'show_red'){
            $('#yhq').hide();
            $('#open_info').css('visibility','visible');
        }else if(_this == 'pay_currency'){
            $('#yhq').hide();
            $('#open_info').css('visibility','hidden');
        }
    });

    $('#sure_send').click(function (ob) {
        var _this = $('#select_send').val();
        console.log(_this);
        var checklist = document.getElementsByName ("tid");
        var str = '';
        for(var i=0;i<checklist.length;i++)
        {
            if(checklist[i].checked == 1){
                if(str==""){str=str+checklist[i].value;}
                else{
                    str=str+","+checklist[i].value;
                    //  alert(str);
                }
            }
        }
        if(str==""){layer.alert("至少选中一个");return;}
        if(_this == 'show_red'){ 					//红包
            var my_ob = $('#all_red');
            show_red(my_ob);
        }else if(_this == 'batch_coupons'){			//优惠券
            var my_ob = $('#all_coupon');
            batch_coupons(my_ob);
        }else if(_this == 'pay_currency'){			//购物币
            var my_ob = $('#all_gwb');
            show_gwb(my_ob);
        }
    });
});
/*倒计时*/
function GetRTime(surplus_time){
    if( send_time > now_time ){
        $(".send_time").show();
        $("#all_red").attr("disabled", true);
        $("#all_red").css("background","#ebebeb");

        surplus_time = send_time - now_time;
        nMS = surplus_time*1000-runtimes*1000;
        if( nMS < 0 ){
            $(".send_time").hide();
            $("#all_red").attr("disabled", false);
            $("#all_red").css("background","#fff");
        }
        var nD  = Math.floor(nMS/(1000*60*60*24));
        var nH  = Math.floor(nMS/(1000*60*60))%24;
        var nM  = Math.floor(nMS/(1000*60)) % 60;
        var nS  = Math.floor(nMS/1000) % 60;
        var str = "<span id='RemainD'>"+nD+"</span>天"
            +"<span id='RemainH'>"+nH+"</span>时"
            +"<span id='RemainM'>"+nM+"</span>分"
            +"<span id='RemainS'>"+nS+"</span>秒";
        $(".surplus_time").html(str);

        runtimes++;
        t = setTimeout("GetRTime("+surplus_time+")",1000);
    }else{
        $(".send_time").hide();
    }
}
//搜索
function searchAttuser(){
    var search_user_id = $("#search_user_id").val();
    var search_name = document.getElementById("search_name").value;
    var search_type = document.getElementById('sname').value;
    var identity 	= document.getElementById('identity').value;
    location.href='index.php?customer_id='+customer_id_en+'&group_id='+group_id+'&search_user_id='+search_user_id+'&search_name='+search_name+'&search_type='+search_type+'&identity='+identity;
}
//全选
function selectAll(){
    var checklist = document.getElementsByName ("tid");
    if(document.getElementById("controlAll").checked){
        for(var i=0;i<checklist.length;i++){
            checklist[i].checked = 1;
        }
    }else{
        for(var j=0;j<checklist.length;j++){
            checklist[j].checked = 0;
        }
    }
}
//打开修改分组
function toModifyGroup(picId){
    $("#btn_savegroup").removeAttr("disabled");
    var tleft = event.pageX;
    var ttop = event.pageY;
    $("#div_group").css("left",tleft+10+"px");
    $("#div_group").css("top",ttop+10+"px");
    $("#rd_group_"+group_id).attr("checked","checked");
    modifyId = picId;
    $("#div_group").show();
}
//换组
function doModifyGroup(){
    var ckrdo = $("input[name='rd_group']:checked");
    var newid = ckrdo.attr("data-gid");
    var group_num = $("#num_"+newid).val();
    if( group_num >= 1800 && group_id > 0 ){
        layer.alert("改组人员已满！");
        return;
    }
    if(parseInt(newid) == parseInt(group_id)){ //如选择的分组为之前的分组，不发送请求
        $("#div_group").hide();
        return;
    }
    $("#btn_savegroup").attr("disabled", true);
    $.getJSON("group.class.php?customer_id="+customer_id_en,{gid:newid,id:modifyId,op:"modifygroup"},function(json){
        if(json.code == 0){
            layer.alert(json.msg);
        }else{
            $("#dd_pic_"+modifyId).remove();

            var ocount = $("#f_gcount_"+group_id).text();
            $("#f_gcount_"+group_id).text(parseInt(ocount)-1);
            var ncount = $("#f_gcount_"+newid).text();
            $("#f_gcount_"+newid).text(parseInt(ncount)+1);
        }
        $("#div_group").hide();
        $("#btn_savegroup").attr("disabled", false);
    });


}

//批量发放购物币
function batch_gwb(){
    var money   = $("#txt_gwb").val();
    var user_id = $('#div_red2').data('user_id');
    var all_send = 0;
    if(user_id >0 || !$('#controlAll').attr('checked')){
        //群发
        all_send = 0;
    }else{
        all_send = 1;
    }
    if( money == "" ){
        layer.alert("购物币数额不能为空！");
        return;
    }
    if( isNaN(money) ){
        layer.alert("购物币数额只能为数字！");
        return;
    }
    if( money < 1 ){
        layer.alert("购物币数额不能低于1元！");
        return;
    }

    $("#div_red2").hide();
    var content = '是否发放购物币？';
    if(user_id >0){

    }else {
        var str = "";
        var checklist = document.getElementsByName("tid");
        for (var i = 0; i < checklist.length; i++) {
            if (checklist[i].checked == 1) {
                if (str == "") {
                    str = str + checklist[i].value;
                }
                else {
                    str = str + "," + checklist[i].value;
                    //  alert(str);
                }
            }
        }
        if (str == "") {
            layer.alert("至少选中一个");
            return;
        }
        var user_id = str;
    }
    layer.confirm(content, {
        title:'确定发放？',
        btn: ['确定','取消']
    }, function(confirm){
        layer.close(confirm);
        $(".batchFinish").show();
        $(".wait_div").show();
        $.ajax({
            url: "send_group_pay_currency.php",
            type:"POST",
            data:{'currency':money,'group_id':group_id,'customer_id':customer_id_en,'user_id':user_id,'send':all_send},
            dataType:"json",
            success: function(json){
                layer.alert(json.msg);
                $(".batchFinish").hide();
                $(".wait_div").hide();
                // location.reload();
            },
            error:function(json){
                layer.alert("发送失败，原因：网络异常！");
            }
        });
        $("#all_red").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
    }, function(json){
        $("#all_red").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
        layer.msg('已取消删除', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });
}

//批量发放红包
function batch_red_packets(){
    var money   = $("#txt_money").val();
    var remark  = $("#remark").val();
    var user_id = $('#div_red').data('user_id');
    var group_num = $('#f_gcount_'+group_id).text();
    var all_send = 0;
    if(user_id >0 || !$('#controlAll').attr('checked')){
        //单发
        all_send = 0;
    }else{
        //群发
        all_send = 1;
        if(group_num > 2000){layer.alert("微信红包最高可发人数2000人！");return;}
    }
    if( money == "" ){
        layer.alert("红包金额不能为空！");
        return;
    }
    if( isNaN(money) ){
        layer.alert("红包金额只能为数字！");
        return;
    }
    if( money < 1 ){
        layer.alert("红包金额不能低于1元！");
        return;
    }
    if( money > 200 ){
        layer.alert("红包金额不能高于200元！");
        return;
    }
    // if( remark == "" ){
    //     layer.alert("红包备注不能为空！");
    //     return;
    // }
    var len = getBt(remark);
    // alert(len);
    if( len > 32 ){
        layer.alert("红包名称不能超过32个字符(1个汉字等于3个字符)");
        return;
    }
    $("#div_red").hide();
    var content = '是否发放红包？';
    if(user_id >0){

    }else{
        var str="";
        var checklist = document.getElementsByName ("tid");
        for(var i=0;i<checklist.length;i++)
        {
            if(checklist[i].checked == 1){
                if(str==""){str=str+checklist[i].value;}
                else{
                    str=str+","+checklist[i].value;
                    //  alert(str);
                }
            }
        }
        if(str==""){layer.alert("至少选中一个");return;}
        user_id = str;
    }
    layer.confirm(content, {
        title:'确定发放？',
        btn: ['确定','取消']
    }, function(confirm){
        layer.close(confirm);
        $(".batchFinish").show();
        $(".wait_div").show();

        $.ajax({
            url: "../../../common_shop/jiushop/send_groupRed_pk.php",
            type:"POST",
            data:{'money':money,'group_id':group_id,'remark':remark,'customer_id':customer_id_en,'user_id':user_id,'send':all_send},
            dataType:"json",
            success: function(json){
                if(json.status==0){
                    layer.alert(json.msg);
                }else{
                    layer.alert(json.msg);
                }
                $(".batchFinish").hide();
                $(".wait_div").hide();
            },
            error:function(json){
                layer.alert("发送红包失败，原因："+json.msg);
            }
        });
        $("#all_red").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
    }, function(json){
        $("#all_red").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
        layer.msg('已取消删除', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });
}

//批量发放优惠券
function batch_coupons(){
    $('#div_red').hide();
    $("#all_red").attr("disabled", false);
    $('#div_coupon').hide();
    $("#all_coupon").attr("disabled", false);
    var user_id = $('#div_coupon').data('user_id');
    var all_send = 0;
    if(user_id >0 || !$('#controlAll').attr('checked')){
        //群发
        all_send = 0;
    }else{
        all_send = 1;
    }
    var coupon = $("#all_coupons option:selected").val();
    var str="";
    var checklist = document.getElementsByName ("tid");
    for(var i=0;i<checklist.length;i++)
    {
        if(checklist[i].checked == 1){
            if(str==""){str=str+checklist[i].value;}
            else{
                str=str+","+checklist[i].value;
                //  alert(str);
            }
        }
    }
    if(str==""){layer.alert("至少选中一个");return;}
    layer.confirm('是否确定批量发放优惠券？', {
        title:'批量发放优惠券',
        btn: ['确定发放','取消']
    }, function(confirm){
        layer.close(confirm);
        $(".batchFinish").show();
        $(".wait_div").show();
        $.ajax({
            url: "send_groupCoupon.php",
            type:"POST",
            data:{'coupon':coupon,'group_id':group_id,'customer_id':customer_id_en,'user_id':str,'send':all_send},
            dataType:"json",
            success: function(json){
                if(json.code==0){
                    layer.alert(json.msg);
                }else{
                    layer.alert(json.msg);
                }
                $(".batchFinish").hide();
                $(".wait_div").hide();
            },
            error:function(json){
                layer.alert("网络错误请检查网络");
            }
        });

    }, function(json){
        layer.msg('已取消发放', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });
}

//批量调整分组
function batch_adjustment_team(a){
    $('#div_red').hide();
    $("#all_red").attr("disabled", false);
    $('#div_coupon').hide();
    $("#all_coupon").attr("disabled", false);
    var all_send = 0;
    if(a == -1) all_send = -1;//未分组多调整
    if( $('#controlAll').attr('checked')){
        //群发
        all_send = 1;
        if(a == -1) all_send = -2;//未分组所有调整
    }
    var str="";
    var mygroup = 0;
    var checklist = document.getElementsByName ("tid");
    var mygroup = $('#all_team option:selected').val();
    if(mygroup == 0){
        layer.alert("请选择分组");return;
    }
    for(var i=0;i<checklist.length;i++)
    {
        if(checklist[i].checked == 1){
            if(str==""){str=str+checklist[i].value;}
            else{
                str=str+","+checklist[i].value;
                //  alert(str);
            }
        }
    }
    if(str==""){layer.alert("至少选中一个");return;}
    layer.confirm('是否确定调整分组？操作后无法恢复,确定吗？', {
        title:'调整分组',
        btn: ['确定','取消']
    }, function(confirm){
        layer.close(confirm);
        $(".batchFinish").show();
        $(".wait_div").show();

        $.ajax({
            url: "group.class.php?customer_id="+customer_id_en,
            type:"POST",
            data:{id:str,o_group_id:group_id,group_id:mygroup,op:"batch_adjustment_team",send:all_send},
            dataType:"json",
            success: function(json){
                if(json.code == 0){
                    layer.alert(json.msg);
                }else{
                    layer.alert(json.msg);
                }
                $(".batchFinish").hide();
                $(".wait_div").hide();
                // location.href='index.php?customer_id='+customer_id_en+'&group_id='+group_id;
            },
            error:function(json){
                layer.alert("网络错误请检查网络");
            }
        });

    }, function(json){
        layer.msg('已取消', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });
}

//批量删除
function all_delete(){
    $('#div_red').hide();
    $("#all_red").attr("disabled", false);
    $('#div_coupon').hide();
    $("#all_coupon").attr("disabled", false);
    var all_send = 0;
    if( $('#controlAll').attr('checked')){
        //群发
        all_send = 1;
    }
    //$("#all_delete").attr("disabled", true);
    var str="";
    var checklist = document.getElementsByName ("tid");
    for(var i=0;i<checklist.length;i++)
    {
        if(checklist[i].checked == 1){
            if(str==""){str=str+checklist[i].value;}
            else{
                str=str+","+checklist[i].value;
                //  alert(str);
            }
        }
    }
    if(str==""){layer.alert("至少选中一个");return;}
    /* if(confirm("是否确定批量删除？")){
     $.getJSON("group.class.php?customer_id="+customer_id_en,{id:str,group_id:group_id,op:"all_delete_crew"},function(json){
     if(json.code == 0){
     layer.alert(json.msg);
     }else{
     location.href='index.php?customer_id='+customer_id_en+'&group_id='+group_id;
     }
     });
     } */
    layer.confirm('是否确定批量删除？', {
        title:'组员批量删除',
        btn: ['确定删除','取消']
    }, function(confirm){
        layer.close(confirm);
        $(".batchFinish").show();
        $(".wait_div").show();
        $.getJSON("group.class.php?customer_id="+customer_id_en,{id:str,group_id:group_id,op:"all_delete_crew",send:all_send},function(json){
            $(".batchFinish").hide();
            $(".wait_div").hide();
            if(json.code == 0){
                layer.alert(json.msg);
            }else{
                location.href='index.php?customer_id='+customer_id_en+'&group_id='+group_id;
            }
        });
    }, function(json){
        layer.msg('已取消删除', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });

    $("#all_delete").attr("disabled", false);
}



//删除组员
function doDeleteCrew(crewId){
    /* if(confirm("是否确定将该组员删除？")){
     $.getJSON("group.class.php?customer_id="+customer_id_en,{id:crewId,group_id:group_id,op:"delete_crew"},function(json){
     if(json.code == 0){
     layer.alert(json.msg);
     }else{
     location.href='index.php?customer_id='+customer_id_en+'&group_id='+group_id;
     }
     });
     } */
    layer.confirm('是否确定将该组员删除？', {
        title:'删除组员',
        btn: ['确定删除','取消']
    }, function(){
        $.getJSON("group.class.php?customer_id="+customer_id_en,{id:crewId,group_id:group_id,op:"delete_crew"},function(json){
            if(json.code == 0){
                layer.alert(json.msg);
            }else{
                location.href='index.php?customer_id='+customer_id_en+'&group_id='+group_id;
            }
        });
    }, function(json){
        layer.msg('已取消删除', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });
}
//添加分组
function addgroup(e){
    var ipt = $("<li><input type='text' name='group_name' id='group_name' class='input w150' onblur='savegroup(this)'/></li>");
    //debugger;
    ipt.insertBefore($(e));
}
//保存分组
function savegroup(e){
    var val = e.value;
    if(val && val != ""){
        $.getJSON("group.class.php?customer_id="+customer_id_en,{group_name:val,op:"addgroup"},function(json){
            if(json.code == 0){
                layer.alert(json.msg);
            }else{
                $(e).replaceWith($("<li><a href='index.php?customer_id="+customer_id_en+"&group_id="+json.data+"'>"+val+"(0)</a></li>"));
                var str = '<option value="'+json.data+'">'+val+'</option>';
                $('#all_team').append(str);
            }
        });
    }
}
//取消按钮
function cancelBtn(btn){
    $(btn).parent().hide();
}
function cancelBtn2(btn){
    $(btn).parent().hide();
    $("#all_red").attr("disabled", false);
    $("#all_coupon").attr("disabled", false);
    $("#all_gwb").attr("disabled", false);
    $("#txt_money").html("");
    $("#remark").html("");
}
function cancelBtn3(btn){
    $(btn).parent().hide();
    $("#all_red").attr("disabled", false);
    $("#all_coupon").attr("disabled", false);
    $("#all_gwb").attr("disabled", false);
    $("#txt_gwb").html("");
}

//显示红包金额输入框
function show_red(obj){
    $("#all_red").attr("disabled", true);
    $("#all_coupon").attr("disabled", true);
    var tleft = event.pageX;
    var ttop = event.pageY;
    $("#div_red").css("left",tleft+10+"px");
    $("#div_red").css("top",ttop+10+"px");
    $("#div_red").show();
    if(obj=="" || obj==undefined){
        $("#div_red").removeData('user_id');
    }else{
        $("#div_red").data('user_id',obj);
    }
}

//显示购物币数额输入框
function show_gwb(obj){
    $("#all_red").attr("disabled", true);
    $("#all_coupon").attr("disabled", true);
    $("#all_gwb").attr("disabled", true);
    var tleft = event.pageX;
    var ttop = event.pageY;
    $("#div_red2").css("left",tleft+10+"px");
    $("#div_red2").css("top",ttop+10+"px");
    $("#div_red2").show();
    if(obj=="" || obj==undefined){
        $("#div_red2").removeData('user_id');
    }else{
        $("#div_red2").data('user_id',obj);
    }
}

//显示优惠券选择框
function show_coupon(obj){
    var sel_coupon = $('#sel_coupon').find('option').length;
    if(sel_coupon==0){
        alert('请先设置优惠券');
        var jump_url = '/weixinpl/back_newshops/Users/group/coupon_group_index.php?customer_id='+customer_id_en;
        window.location.href = jump_url;
        return;
    }
    $("#all_coupon").attr("disabled", true);
    $("#all_red").attr("disabled", true);
    var tleft = event.pageX;
    var ttop = event.pageY;
    $("#div_coupon").css("left",tleft+10+"px");
    $("#div_coupon").css("top",ttop+10+"px");
    $("#div_coupon").show();
    if(obj=="" || obj==undefined){
        $("#div_coupon").removeData('user_id');
    }else{
        $("#div_coupon").data('user_id',obj);
    }
}
//计算字符串所占字符数
function getBt(str){
    var char = str.replace(/[^\x00-\xff]/g, '***');
    return char.length;
}

function getBt1(str){
    var len = 0;
    var hanzi = 0;
    var word  = 0;
    for (var i=0; i<str.length; i++) {
        var c = str.charCodeAt(i);
        //单字节加1
        if ((c >= 0x0001 && c <= 0x007e) || (0xff60<=c && c<=0xff9f)) {
            word++;
        }
        else {
            hanzi++;
        }
    }
    len = hanzi + word;
    return len;
}
//发放组红包
function doModifyRed(){
    var money   = $("#txt_money").val();
    var remark  = $("#remark").val();
    var user_id = $("#div_red").data('user_id');
    if( money == "" ){
        layer.alert("红包金额不能为空！");
        return;
    }
    if( isNaN(money) ){
        layer.alert("红包金额只能为数字！");
        return;
    }
    if( money < 1 ){
        layer.alert("红包金额不能低于1元！");
        return;
    }
    if( remark == "" ){
        layer.alert("红包备注不能为空！");
        return;
    }
    var len = getBt(remark);
    //alert(len);
    if( len > 32 ){
        layer.alert("红包备注不能超过32个字符(1个汉字占3个字符)");
        return;
    }
    $("#div_red").hide();
    var content = '是否发放组红包？';
    if(user_id!="" && user_id!=undefined){
        content = '是否给当前用户发放红包？';
    }
    layer.confirm(content, {
        title:'确定发放',
        btn: ['确定','取消']
    }, function(confirm){
        layer.close(confirm);
        $(".batchFinish").show();
        $(".wait_div").show();

        $.ajax({
            url: "../../../common_shop/jiushop/send_groupRed_pk.php",
            type:"POST",
            data:{'money':money,'group_id':group_id,'remark':remark,'customer_id':customer_id_en,'user_id':user_id},
            dataType:"json",
            success: function(json){
                if(json.status==0){
                    layer.alert(json.msg);
                }else{
                    layer.alert(json.msg);
                }
                $(".batchFinish").hide();
                $(".wait_div").hide();
            },
            error:function(json){
                layer.alert("网络错误请检查网络");
            }
        });
        $("#all_red").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
    }, function(json){
        $("#all_red").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
        layer.msg('已取消删除', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });


}
//发放组优惠券
function doModifyCoupon(){
    var coupon = $("#sel_coupon").val();
    var user_id = $("#div_coupon").data('user_id');
    if( coupon < 0 ){
        layer.alert("请选择优惠券！");
        return;
    }
    $("#div_coupon").hide();
    var content = '是否发放组优惠券？';
    if(user_id!="" && user_id!=undefined){
        content = '是否给当前用户发放优惠券？';
    }
    layer.confirm(content, {
        title:'确定发放',
        btn: ['确定','取消']
    }, function(confirm){
        layer.close(confirm);
        $(".batchFinish").show();
        $(".wait_div").show();

        $.ajax({
            url: "send_groupCoupon.php",
            type:"POST",
            data:{'coupon':coupon,'group_id':group_id,'customer_id':customer_id_en,'user_id':user_id},
            dataType:"json",
            success: function(json){
                if(json.code==0){
                    layer.alert(json.msg);
                }else{
                    layer.alert(json.msg);
                    if(json.code == 750){
                        var jump_url = '/weixinpl/back_newshops/Users/group/coupon_group_index.php?customer_id='+customer_id_en;
                        window.location.href = jump_url;
                    }
                }
                $(".batchFinish").hide();
                $(".wait_div").hide();
            },
            error:function(json){
                layer.alert("网络错误请检查网络");
            }
        });
        $("#all_coupon").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
    }, function(json){
        $("#all_coupon").attr("disabled", false);
        $("#txt_money").html("");
        $("#remark").html("");
        layer.msg('已取消发放', {
            time: 4000,
            btn: ['确认'],
            icon:1
        });
    });


}
function count_red(){
    $.ajax({
        url: "asynchronous.php",
        type:"POST",
        data:{'group_id':group_id,'customer_id':customer_id_en,'op':'groupRedNum'},
        dataType:"json",
        success: function(json){
            if(json == '') json = 0;
            $(".count_red").html(json);
        }
    });
}
function count_red_day(){
    $.ajax({
        url: "asynchronous.php",
        type:"POST",
        data:{'group_id':group_id,'customer_id':customer_id_en,'op':'groupRedNum_day'},
        dataType:"json",
        success: function(json){
            if(json == '') json = 0;
            $(".count_red_day").html(json);
        }
    });
}
function count_coupon(){
    $.ajax({
        url: "asynchronous.php",
        type:"POST",
        data:{'group_id':group_id,'customer_id':customer_id_en,'op':'groupCouponNum'},
        dataType:"json",
        success: function(json){
            if(json == '') json = 0;
            $(".count_coupon").html(json);
        }
    });
}
function count_coupon_day(){
    $.ajax({
        url: "asynchronous.php",
        type:"POST",
        data:{'group_id':group_id,'customer_id':customer_id_en,'op':'groupCouponNum_day'},
        dataType:"json",
        success: function(json){
            if(json == '') json = 0;
            $(".count_coupon_day").html(json);
        }
    });
}
function count_currency(){
    $.ajax({
        url: "asynchronous.php",
        type:"POST",
        data:{'group_id':group_id,'customer_id':customer_id_en,'op':'groupCurrencyNum'},
        dataType:"json",
        success: function(json){
            if(json == '') json = 0;
            $(".count_currency").html(json);
        }
    });
}
function count_currency_day(){
    $.ajax({
        url: "asynchronous.php",
        type:"POST",
        data:{'group_id':group_id,'customer_id':customer_id_en,'op':'groupCurrencyNum_day'},
        dataType:"json",
        success: function(json){
            if(json == '') json = 0;
            $(".count_currency_day").html(json);
        }
    });
}
function noGroup_crewNum(){
    $.ajax({
        url: "asynchronous.php",
        type:"POST",
        data:{'group_id':group_id,'customer_id':customer_id_en,'op':'noGroup_crewNum'},
        dataType:"json",
        success: function(json){
            $("#f_gcount_-1").html(json);
        }
    });
}

function strlen(str){
    var len = 0;
    for (var i=0; i<str.length; i++) {
        var c = str.charCodeAt(i);
        //单字节加1
        if ((c >= 0x0001 && c <= 0x007e) || (0xff60<=c && c<=0xff9f)) {
            len++;
        }
        else {
            len++;
        }
    }
    return len;
}




