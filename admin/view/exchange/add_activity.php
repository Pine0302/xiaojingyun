<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>活动管理－添加活动</title>
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
                    <a class="white1">添加活动</a>
                </div>              
            </div>
            <!--产品管理代码开始-->
            <div class="WSY_data">
                <ul class="at-main">
                    <li>活动名称：<input <?php if(!empty($res['status']) && $res['status'] != '1') { echo 'disabled';}?> type="text" id="tit" name="title" class="ipt" style="min-width:150px;" value="<?php echo $res['title']; ?>"/></li>
                    <li>活动时间：<input <?php if(!empty($res['status']) && $res['status'] != '1') { echo 'disabled';}?> class="ipt" type="text" id="starttime" name="starttime" value="<?php echo $res['starttime']; ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
                         至
                        <input <?php if(!empty($res['status']) && $res['status'] != '1') { echo 'disabled';}?> class="ipt" type="text" id="endtime" name="endtime" value="<?php echo $res['endtime']; ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/></li>
                    <li>每笔订单参与门槛，满￥ <input <?php if(!empty($res['status']) && $res['status'] != '1') { echo 'disabled';}?> id="threshold" type="text" name="threshold" oninput='checkint(this)' value="<?php if(empty($res['threshold'])) { echo 59; } else { echo $res['threshold']; } ?>" class="ipt"></li>
                    <li>每笔订单可换赠产品总量 <input <?php if(!empty($res['status']) && $res['status'] != '1') { echo 'disabled';}?> id="exchange_num" type="text" name="exchange_num" oninput='exint(this)' value="<?php if(empty($res['exchange_num'])) { echo -1; } else { echo $res['exchange_num']; } ?>" class="ipt"></li>
                    <li>是否支持叠加活动：
                        <label class="at-lable at-tips">
                            <input <?php if(!empty($res['status']) && $res['status'] != '1') { echo 'disabled';}?> type="radio" name="superposition" <?php if($res['is_superposition'] == '1'){ echo 'checked'; } if(empty($res['is_superposition'])){ echo 'checked'; } ?> value="1">是
                            <img class="tips-img" src="/weixinpl/back_newshops/Common/images/Base/help.png" data-val="1" data-tips="举例：满59元加3元换购，满160元加1元换购；当选择是，用户订单满160元，能换购满160元的1元产品，也能换购满59元的3元产品"/>
                        </label>
                        <label class="at-lable at-tips">
                            <input <?php if(!empty($res['status']) && $res['status'] != '1') { echo 'disabled';}?> type="radio" name="superposition" <?php if($res['is_superposition'] == '0'){ ?>checked<?php } ?> >否
                            <img class="tips-img" src="/weixinpl/back_newshops/Common/images/Base/help.png" data-val="0" data-tips="举例：满59元加3元换购，满160元加1元换购；当选择否，用户订单满160元，只能换购满160元的1元产品"/>
                        </label>
                    </li>
                    <li>是否立即发布：
                        <?php if(empty($res['status']) || $res['status'] == 1) {?>
                            <label class="at-lable"><input checked type="radio" name="status" <?php if($res['status'] == 2){ ?>checked<?php } ?> value="2">是</label>
                            <label class="at-lable"><input type="radio" name="status" <?php if($res['status'] == '1'){ ?>checked<?php } ?> value="1">否</label>
                        <?php } else { ?>
                            <label class="at-lable"><input <?php if(!empty($res['status'])) { echo 'disabled';} ?> checked type="radio" name="status" value="<?php echo $res['status']; ?>">是</label>
                            <label class="at-lable"><input <?php if(!empty($res['status'])) { echo 'disabled';}?> type="radio" name="status" value="<?php echo $res['status']; ?>">否</label>
                        <?php } ?>
                    </li>
                </ul>
                <div class="at-btn-content">
                    <?php if(empty($res['status']) || $res['status'] == '1') {?>
                        <button id="btn" class="WSY_button hold-btn">保存</button>
                    <?php } else {?>
                        <button onclick="window.history.go(-1);" class="WSY_button hold-btn">返回</button>
                    <?php }?>
                </div>
            </div>
            <input type="hidden" id="ex_id" value="<?php echo $res['id']; ?>">
            <!--产品管理代码结束-->
        </div>
    </div>
    <!--内容框架结束-->
    <div class="layui-layer layui-anim layui-layer-tips " id="layui-layer" type="tips" showtime="3000" contype="object" style="z-index: 19891023; position: absolute; width: 210px; top: 260px;display: none"><div class="layui-layer-content"><i class="layui-layer-TipsG layui-layer-TipsR"></i></div><span class="layui-layer-setwin"></span></div>
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script type="text/javascript">
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
        obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
        obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
        obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
    }

    function exint(obj){
    if (obj.value != '') {
        if (obj.value < -1) {
            obj.value = -1;
        }

        if (!isNaN(obj.value) && $.trim(obj.value) != "") {
            obj.value = parseFloat(obj.value);
        }
    }
}

    //检查活动名称是否有单引号双引号
    function check_proname(){
        var stu = false;
        var reg = /['"’”‘“]/g;
        var pro_name = $('#tit').val();
        stu = reg.test(pro_name);
        return stu;
    }
    
    function add (obj) {
        if (obj < 10) return "0" + obj; else return obj;
    }

    //保存
    $('#btn').click(function(){
        var id                 = $('#ex_id').val();
        var title              = $('#tit').val();
        var starttime          = $('#starttime').val();
        var endtime            = $('#endtime').val();
        var threshold          = $('#threshold').val();
        var exchange_num       = $('#exchange_num').val();
        var superposition      = $('input:radio[name="superposition"]:checked').val();
        var status             = $('input:radio[name="status"]:checked').val();
        if (!status) {
            var status             = $('input:radio[name="status"]').val();
        }
        // var exchange_num       = Math.floor(exchange_num);
        var date                  = new Date();
        var time = date.getFullYear() + '-' + add( (date.getMonth()+1) ) + '-' + add( date.getDate() ) + ' ' + add( date.getHours() ) + ':' + add( date.getMinutes() ) + ':' + add( date.getSeconds() );
 
        if($.trim(title) == "") {
            alert('请正确设置活动名称');
            return false;
        }

        var stu = check_proname();
        if(stu){
            alert('活动名称不能含有特殊字符串，请重新输入！');
            $('#name').val('');
            return;
        }

        if(title.length > 5) {
            alert('活动名称字符长度不能超过五个');
            return false;
        }
        
        if($.trim(starttime) == ""){
            alert("请正确设置开始时间");
            return;
        }

        if($.trim(endtime) == ""){
            alert("请正确设置结束时间");
            return;
        }

        if(endtime<starttime){
            alert("活动时间有误，结束时间不能比开始时间早！");
            return;
        }

        if(endtime<time){
            alert("活动时间有误，结束时间不能比当前时间早！");
            return;
        }

        if($.trim(threshold) == '') {
            alert("请正确设置门槛金额");
            return false;
        }

        // if(threshold == "" || !Number(threshold)) {
        //     alert('请正确设置门槛金额');
        //     return false;
        // }

        if(isNaN(threshold) || (parseFloat(threshold) < 0 || (0< parseFloat(threshold) && parseFloat(threshold) <0))){
            alert("请正确设置门槛金额");
            $('#currency_percentage').val('0');
            return false;
        }

        if($.trim(exchange_num) == "") {
            alert("请正确设置产品总量");
            return false;
        }

        if(isNaN(exchange_num) || (parseFloat(exchange_num) < -1 || (-1< parseFloat(exchange_num) && parseFloat(exchange_num) <0))){
            alert("请正确设置产品总量");
            $('#currency_percentage').val('-1');
            return false;
        }
        exchange_num = parseFloat(exchange_num);
        
        $.ajax({
            url: '/mshop/admin/index.php?m=exchange&a=save_exchange',
            dataType: 'json',
            type: 'post',
            data: {
                id:id,
                title:title,
                starttime:starttime,
                endtime:endtime,
                threshold:threshold,
                exchange_num:exchange_num,
                superposition:superposition,
                status:status
            },
            success: function(res){
                if( res.errcode == '1' ){
                    alert(res.errmsg);
                    window.location = "/mshop/admin/index?m=exchange&a=exchange_activity_list";
                }else{
                    alert(res.errmsg);
                }
            }
        });
    });
</script>   
</html>