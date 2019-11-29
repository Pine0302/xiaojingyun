<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>云店奖励－编辑店主</title>
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
    <script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
    <style type="text/css">
        .at-main{margin:30px 0 0 30px;}
        .at-main li{font-size:14px;color:#666;margin:15px 0;}
        .at-main .ipt{border-radius:2px;border:solid 1px #ddd;box-sizing:border-box;padding:0 10px;height:28px;line-height:28px;}
        .at-main .at-lable{display:inline-block;vertical-align:middle;margin:0 50px 0 0;}
        .at-main .at-lable input{vertical-align:middle;margin:-2px 3px 0 3px;}
        .at-main .at-tips{position:relative;}
        .at-tips .tips-img{position:absolute;width:12px;right:-15px;top:0;cursor:pointer;}
        .at-btn-content{margin:20px 60px 20px 150px;}
        .at-btn-content .hold-btn{float:none;}
    </style>
</head>
<body>
    <!--内容框架开始-->
    <div class="WSY_content" id="WSY_content_height">
        <!--列表内容大框开始-->
        <div class="WSY_columnbox"> 
            <div class="WSY_column_header">
                <div class="WSY_columnnav">
                    <a class="white1">店主信息编辑</a>
                </div>              
            </div>
            <!--产品管理代码开始-->
            <div class="WSY_data">
                <div class="at-btn-content">
                        <button id="btn" class="WSY_button hold-btn">保存</button>
                        <button onclick="window.history.go(-1);" class="WSY_button hold-btn">返回</button>
                </div>
            </div>
            <!--产品管理代码结束-->
        </div>
    </div>
    <!--内容框架结束-->
    <div class="layui-layer layui-anim layui-layer-tips " id="layui-layer" type="tips" showtime="3000" contype="object" style="z-index: 19891023; position: absolute; width: 210px; top: 260px;display: none"><div class="layui-layer-content"><i class="layui-layer-TipsG layui-layer-TipsR"></i></div><span class="layui-layer-setwin"></span></div>
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript">
//-------------------------------获取需要编辑的店主信息START-----------------------------------------
var profit_shop_bak = 0; //保存初始值
var self_reware_bak = 0; //保存初始值
var user_id = <?php echo $temp['user_id'];?>;
$.get('/mshop/admin/index.php?m=yundian&a=edit_yundian_shopkeepers',{
    is_ajax:1,
    user_id:user_id
},function(data){
    if(data.errcode != 0) {
        var html = "";

        html += '<ul class="at-main">';
        html += '<li>用户ID：<input disabled type="text" id="user_id" name="user_id" class="ipt" style="min-width:150px;" value="'+data.data.keeper_msg.user_id+'"/></li>';
        html += '<li>店主身份：';
        html += '<select id="tequan_id">';
        html += '<option value ="'+data.data.keeper_msg.tequan_id+'">'+data.data.keeper_msg.name+'</option>';

        for (var i = 0; i < data.data.yundian_identity.length; i++) {
            if(data.data["yundian_identity"][i]["id"] == data.data['keeper_msg']['tequan_id']) continue;
            html += '<option value ="'+data.data.yundian_identity[i].id+'">'+data.data.yundian_identity[i].name+'</option>';
        }

        html += '</select></li>';
        html += '<li>店主店铺名称：<input type="text" id="store_name" placeholder="最多不超过15个字符" name="store_name" class="ipt" style="min-width:210px;" maxlength="15" value="'+data.data.keeper_msg.store_name+'"></li>'
        html += '<li>到期时间：<input class="ipt" type="text" id="expire_time" name="expire_time" value="'+data.data.keeper_msg.expire_time+'" onClick="WdatePicker({dateFmt:\'yyyy-MM-dd\'});" style="min-width:120px" /></li>';

        if(!data.data['keeper_msg']['profit_shop']) {
            html += '<li>店主身份奖励比例： <input id="profit_shop" type="text" name="profit_shop" onblur="checknan(this)" oninput=\'checkint(this)\' value="-1" class="ipt">(0-1之间)</li>';
        } else {
            html += '<li>店主身份奖励比例： <input id="profit_shop" type="text" name="profit_shop" onblur="checknan(this)" oninput=\'checkint(this)\' value="'+data.data.keeper_msg.profit_shop+'" class="ipt">(0-1之间)</li>';
        }

        if(!data.data['keeper_msg']['self_reware']) {
            html += '<li>自营产品抽成： <input id="self_reware" type="text" name="self_reware" onblur="checknan2(this)" oninput=\'checkint2(this)\' value="-1" class="ipt">(0-1之间)</li>';
        } else {
            html += '<li>自营产品抽成： <input id="self_reware" type="text" name="self_reware" onblur="checknan2(this)" oninput=\'checkint2(this)\' value="'+data.data.keeper_msg.self_reware+'" class="ipt">(0-1之间)</li></ul>';
        }

        profit_shop_bak = data.data.keeper_msg.profit_shop;
        self_reware_bak = data.data.keeper_msg.self_reware;

        $(".WSY_data").prepend(html);

    } else {
        console.log(data.errmsg);
    }
},'json');



//-------------------------------获取需要编辑的店主信息END-----------------------------------------
    $('.tips-img').on('click', function(){
        var text = $(this).data('tips');
        var val  = $(this).data('val');
        if (val == 1) {
            $('#layui-layer').css('left',254.031);
        } else {
            $('#layui-layer').css('left',339.031);
        }
        $('.layui-layer-content').html(text);
        $('#layui-layer').css('display','block');
        // layer.tips(text,this);
    });

    $('.tips-img').on('mouseleave',function(){
        $('#layui-layer').css('display','none');
    });

    function checkint(obj){
        obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
        obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
        obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
        obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
        obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d\d\d).*$/,'$1$2.$3'); //只能输入五个小数
    }

    function checkint2(obj){
        obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
        obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
        obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
        obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
        obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d\d\d).*$/,'$1$2.$3'); //只能输入两个小数
    }    

    function checknan(obj){
        if(isNaN(obj.value) == true) {
            alert("请正确设置店主身份奖励比例！");
            obj.value = profit_shop_bak;
            return false;
        }
    }

    function checknan2(obj){
        if(isNaN(obj.value) == true) {
            alert("请正确设置自营产品抽成！");
            obj.value = self_reware_bak;
            return false;
        }
    }    
    
    function add (obj) {
        if (obj < 10) return "0" + obj; else return obj;
    }

    //保存
    $('#btn').click(function(){
        var user_id                = $('#user_id').val();
        var tequan_id              = $('#tequan_id').val();
        var store_name             = $('#store_name').val();
        var expire_time            = $('#expire_time').val();
        var profit_shop            = $('#profit_shop').val();
        var self_reware            = $('#self_reware').val();
        if (!status) {
            var status             = $('input:radio[name="status"]').val();
        }
        var date                  = new Date();
        //var time = date.getFullYear() + '-' + add( (date.getMonth()+1) ) + '-' + add( date.getDate() ) + ' ' + add( date.getHours() ) + ':' + add( date.getMinutes() ) + ':' + add( date.getSeconds() );
 

        if($.trim(expire_time) == ""){
            alert("请正确设置到期时间");
            return;
        }

        // if(expire_time<time){
        //     alert("到期时间有误，到期时间不能比当前时间早！");
        //     return;
        // }

        if($.trim(profit_shop) == "") {
            alert("请正确设置店主身份奖励比例！");
            $('#profit_shop').val(profit_shop_bak);
            return false;
        } else {
            var temp = $.trim(profit_shop);
            if(temp < 0 || temp >1) {
                alert("店主身份奖励比例不能大于1或者小于0！");
                $('#profit_shop').val(profit_shop_bak);
                return false;
            }
        }

        if($.trim(self_reware) == "") {
            alert("请正确设置自营产品抽成！");
            $('#self_reware').val(self_reware_bak);
            return false;
        } else {
            var temp = $.trim(self_reware);
            if(temp < 0 || temp >1) {
                alert("自营产品抽成不能大于1或者小于0！");
                $('#self_reware').val(self_reware_bak);
                return false;
            }
        }

        $.ajax({
            url: '/mshop/admin/index.php?m=yundian&a=save_shopkeeper_datas',
            dataType: 'json',
            type: 'post',
            data: {
                user_id:user_id,
                tequan_id:tequan_id,
                store_name:store_name,
                expire_time:expire_time,
                profit_shop:profit_shop,
                self_reware:self_reware,
            },
            success: function(res){
                console.log(res);
                if( res.errcode == '1' ){
                    alert(res.errmsg);
                    window.location = "/mshop/admin/index.php?m=yundian&a=yundian_shopkeeper_list";
                }else{
                    alert(res.errmsg);
                }
            }
        });
    });
</script>   
</html>