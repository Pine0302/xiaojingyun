var is_have_huanxun = false;

/*全选*/
function change_box(){
    var all_box = $(".all_checkbox").is(':checked');
    if( all_box ){
        $(".checkbox:not(:disabled)").prop("checked",true);
    }else{
        $(".checkbox:not(:disabled)").prop("checked",false);
    }
}
//当取消掉其中一个选项时，也取消全选按钮
/*function cancelAllChoose(){
    $(".all_checkbox").prop("checked", false);

}*/

/*批量通过*/
function batchPass(HostUrl,gathering_name,begin,end) {
    var param = [HostUrl,gathering_name,begin,end];
    sms_check("moneybag_withdraw","go_batchPass",param);
}
function go_batchPass(HostUrl,gathering_name,begin,end) {
    var select = gathering_name.split(',');
    var begin = begin.split(',');
    var end = end.split(',');

    check_bacthPass_type();

    var str_batchPass = '<ul class="layer-ul">';
    str_batchPass += '<li><span>收款项目：</span> <select style="width:100px;" id="batchPass_project">';
    for (var y = 0; y < select.length; y++) {
        str_batchPass += '<option data-begin="' + begin[y] + '" data-end="' + end[y] + '">' + select[y] + '</option>';
    }
    str_batchPass += '</select></li>';
    str_batchPass += '</ul>';

    //判断是否有环迅账户，提示不同的内容
    if(is_have_huanxun) {
        layer.open({
            title: '确认提示',
            content: str_batchPass,
            btn: ['确认', '取消'],
            yes: function (index) {
                batchPass_all(HostUrl);
            },
            btn2: function (index) {
                layer.close(index);
            }
        });
    }else{
        batchPass_all(HostUrl);
    }

}

//检查批量类型，若有环迅账户就弹出选择框
function check_bacthPass_type(){

    var box = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)');
    var box_arr = []; 	//创建数组
    for (var i = 0; i < box.length; i++) {
        box_arr[i] = [];
        var box_val = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)').eq(i).attr("c_data");
        //复选框附带的数组
        var box_arr1 = eval("(" + box_val + ")");
        box_arr[i] = box_arr1;
    }

    for(var j = 0; j < box_arr.length; j++){
        if(box_arr[j].cash_type == '环迅账户'){
            is_have_huanxun = true;
        }
    }
}

function batchPass_all(HostUrl){

    var hxpay_url = HostUrl;
    /*环迅支付需要获取收款项目*/
    var gathering_name = $("#batchPass_project option:selected").text();
    var begin = $('#batchPass_project option:selected').data('begin');
    var end = $('#batchPass_project option:selected').data('end');
    var d = new Date();
    var now = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();

    //获取所有选中的而且不包含disabled属性的复选框
    var box = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)');

    if (box.length < 1) {
        layer.alert("亲，请勾选批量通过的复选框对象！");
        return;
    }

    var box_arr = []; 	//创建数组
    for (var i = 0; i < box.length; i++) {
        box_arr[i] = [];
        var box_val = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)').eq(i).attr("c_data");
        //复选框附带的数组
        var box_arr1 = eval("(" + box_val + ")");
        box_arr[i] = box_arr1;
    }

    box_arr = JSON.stringify(box_arr);  //数组转json


    if(is_have_huanxun) {                               //含有环迅支付的批量提现
        if (gathering_name.length == 1) {
            layer.alert("亲，请选择环迅支付的收款项目！");
        } else if ((now < begin) || (now > end)) {
            layer.open({
                title: '提示',
                content: '您的收款项目已过期,请重新添加!',
                btn: ['去添加', '取消'],
                yes: function (index) {
                    window.location.href = '//' + hxpay_url + '/weixinpl/Base/pay_set/hxpay_set.php';
                    layer.close(index);
                },
                btn2: function (index) {
                    layer.close(index);
                }
            });
        } else {
            $.ajax({
                type: "post",
                url: "save_batch_pass.php",
                data: {'box_arr': box_arr, 'gathering_name': gathering_name},
                dataType: "json",
                success: function (res) {
                    if (res.status == 0) {
                        layer.alert(res.msg);
                        setTimeout(function () {
                            location.href = location;
                        }, 2500);
                    }
                },
                error: function (e) {
                    layer.alert("网络错误请检查网络");
                }
            });
        }
    } else {                                                    //不含有环迅支付的批量提现
        $.ajax({
            type: "post",
            url: "save_batch_pass.php",
            data: {'box_arr': box_arr, 'gathering_name': gathering_name},
            dataType: "json",
            success: function (res) {
                if (res.status == 0) {
                    layer.alert(res.msg);
                    setTimeout(function () {
                        location.href = location;
                    }, 2500);
                }
            },
            error: function (e) {
                layer.alert("网络错误请检查网络");
            }
        });
    }

}

/*批量驳回*/
function batchReject()
{
    var batch_tis_str = prompt("请输入驳回理由（30字符内）","余额不够");

    if(batch_tis_str) {
        if (batch_tis_str.length > 30) {
            alert('请控制字数在30个字符以内');
            return;
        }

        //获取所有选中的而且不包含disabled属性的复选框
        var box = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)');

        if (box.length < 1) {
            layer.alert("亲，请勾选批量驳回的复选框对象！");
            return;
        }

        var box_arr = []; 	//创建数组
        for (var i = 0; i < box.length; i++) {
            box_arr[i] = [];
            var box_val = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)').eq(i).attr("c_data");
            //复选框附带的数组
            var box_arr1 = eval("(" + box_val + ")");
            box_arr[i] = box_arr1;
        }
        box_arr = JSON.stringify(box_arr);  //数组转json
        // console.log(box_arr);

        //判断是否开启短信验证
        $.ajax({
            url: "/wsy_pub/admin/index.php?m=security_sms&a=sms_verification_check",
            dataType: 'json',
            type: 'post',
            data:{'sms_check_type':'moneybag_withdraw'},
            success:function (res){
                // console.log(res);return;
                if(res.is_sms_check == 1 && !res.security_sms_ischeck && res.sms_check_info && !security_sms_key){       //开启短信验证且短信session为false且符合验证内容且安全限制全局参数为false，进行短信验证
                    sms_layer(res.sms_check_phone);
                }else{
                    $.ajax({
                        type: "post",
                        url: "save_batch_reject.php",
                        data: {'box_arr': box_arr,'batch_tis_str':batch_tis_str},
                        dataType: "json",
                        success: function (res) {
                            if (res.status == 0) {
                                layer.alert(res.msg);
                                setTimeout(function () {
                                    window.history.go(0);
                                }, 2500);
                            }
                            //插入操作日志
                            var log_content = box.length+"笔待提现，提现审核驳回；";
                            $.ajax({
                                type: "post",
                                url: "/wsy_pub/admin/index.php?m=security_sms&a=sys_log_insert",
                                data: {'sys_calss': 'shop_system_moneybag','sys_content':log_content},
                                dataType: "json",
                                success: function (res) {
                                    console.log('succrss');
                                },
                                error: function (e) {
                                    console.log('操作日志插入失败')
                                }
                            });
                        },
                        error: function (e) {
                            layer.alert("网络错误请检查网络");
                        }
                    });
                }

            },
            error:function(res){
                console.log(res);
            },
        });
    }

}
