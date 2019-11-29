<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>活动管理－创建活动</title>
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
                    <a class="white1">创建活动</a>
                </div>              
            </div>
            <!--产品管理代码开始-->
            <div class="WSY_data">
                <ul class="at-main">
                    <li style="padding-left:108px">
                        <span>活动名称：</span>
                        <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> type="text" id="name" name="name" class="ipt" style="min-width:150px;" value="<?php echo $res['name']; ?>"/></li>
                    <li style="padding-left:108px">
                        <span>活动时间：</span>
                        <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> class="ipt" type="text" id="start_time" name="start_time" value="<?php echo $res['start_time']; ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
                         至
                        <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> class="ipt" type="text" id="end_time" name="end_time" value="<?php echo $res['end_time']; ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px"/>
                    </li>
                    <li style="padding-left:36px">
                        <span>每个用户每天排队限制：</span>
                        <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> id="queue_num" type="text" name="queue_num" oninput='checkint(this)' value="<?php echo $res['queue_num']?$res['queue_num']:-1;?>" class="ipt">
                        <span>次，默认-1不限制，可以输入非0正整数</span>
                    </li>
                    <li>
                        <span>参与排队的个人消费金额限制：</span>
                        <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> id="queue_expenditure" type="text" name="queue_expenditure" oninput='checkint(this)' value="<?php echo $res['queue_expenditure']?$res['queue_expenditure']:-1;?>" class="ipt">
                        <span>，默认-1不限制(只要买活动商品就生成队列订单)，可输入非0正数，可保留小数点后2位</span>
                    </li>
                    <li style="padding-left:12px">
                        <span>成功限制：排队订单数达到　</span>
                        <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> id="success_num" type="text" name="success_num" oninput='checkint(this)' value="<?php echo $res['success_num']?$res['success_num']:-1;?>" class="ipt">
                        <span>，默认-1不限制，可输入非0正整数；订单数达到后，设置数值内的首位可拿奖励，依次类推。</span>
                    </li>
                    <li style="padding-left:84px">
                        <span>排队奖励金额：</span>
                        <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> id="bonus" type="text" name="bonus" oninput='checkint(this)' value="<?php echo $res['bonus']?$res['bonus']:0;?>" class="ipt">
                        <span>，默认0，可输入0或正数，可保留小数点后2位</span>
                    </li>
                    <li style="padding-left:72px">
                        <span>成功后领取限制：</span>
                        <label class="at-lable at-tips" style="margin-top:14px;padding-bottom:10px">
                            <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';} if($res['get_impose'] == '0') { echo ' checked';} if(empty($res['get_impose'])) {echo ' checked';}?> type="radio" name="get_impose" value="0">
                            <span>参与活动后总个人消费达到 </span>
                            <input style="margin-left:63px" <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> id="expenditure" type="text" name="expenditure" oninput='checkint(this)' value="<?php echo $res['expenditure']?$res['expenditure']:0;?>" class="ipt">
                            <span>，默认0不限制，可输入非0正数，可保留小数点后2位</span>
                        </label>
                        <br>
                        <label class="at-lable at-tips" style="padding-left:100px">
                            <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';} if($res['get_impose'] == '1') { echo ' checked';}?> type="radio" name="get_impose" value="1">
                            <span>首次分享促使他人付款成功的人数达到 </span>
                            <input <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> id="promote_num" type="text" name="promote_num" oninput='checkint(this)' value="<?php echo $res['promote_num']?$res['promote_num']:-1;?>" class="ipt">
                            <span>，默认-1不限制，可输入非0正整数</span>
                        </label>
                    </li>
                    <li style="padding-left:72px ">
                        <div class="WSY_remind_main">
                            <dl class="WSY_bulkdl  w90px">
                                <dt>规则说明：</dt>
                                <?php if($res['is_rule']==1){ ?>
                                <ul class="switch-radio" style="background-color: rgb(255, 113, 112);margin-top:2px;">
                                    <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
                                    <li onclick="is_rule(0)" class="WSY_bot" style="left: 0px;"></li>
                                    <span onclick="is_rule(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                                </ul>
                                <?php }else{ ?>
                                <ul class="switch-radio" style="background-color: rgb(203, 210, 216);margin-top:2px;">
                                    <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
                                    <li onclick="is_rule(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                                    <span onclick="is_rule(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                                </ul>
                                <?php } ?>
                            </dl>
                            <input type="hidden" name="is_rule" id="is_rule" value="<?php echo $res['is_rule'];?>">
                        </div>
                    </li>

                    <li style="padding-left:152px;<?php if($res['is_rule']==0){ echo 'display:none'; }?> ">
                        <dl class="WSY_bulkdl">
                            <span style="color:red;font-size: 14px;">（建议控制在10万个字符以内）</span>
                            <dd>
                                <textarea name="rule" id="rule" value="<?php echo $res['rule'];?>" class="briefdesc" <?php if(!empty($res['isout']) && $res['isout'] != '0') { echo 'disabled';}?> maxlength=100000 ;><?php echo $res['rule']?$res['rule']:'当达到可领取排队奖励的条件后，需要推荐一个人购买排队商品，才能领取奖励；';?></textarea>
                            </dd>
                        </dl>
                    </li>
                </ul>
                <div class="at-btn-content">
                    <?php if(empty($res['isout']) || $res['isout'] == '0') {?>
                        <button id="btn" class="WSY_button hold-btn">保存</button>
                        <button onclick="window.history.go(-1);" class="WSY_button hold-btn">返回</button>
                    <?php } else {?>
                        <button onclick="window.history.go(-1);" class="WSY_button hold-btn">返回</button>
                    <?php }?>
                </div>
            </div>
            <input type="hidden" id="id" name="id" value="<?php echo $res['id']; ?>">
            <!--产品管理代码结束-->
        </div>
    </div>
    <!--内容框架结束-->
    <div class="layui-layer layui-anim layui-layer-tips " id="layui-layer" type="tips" showtime="3000" contype="object" style="z-index: 19891023; position: absolute; width: 210px; top: 260px;display: none"><div class="layui-layer-content"><i class="layui-layer-TipsG layui-layer-TipsR"></i></div><span class="layui-layer-setwin"></span></div>
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<?php if($res['isout'] == 0) { ?>
    <script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
<?php } ?>
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
        var pro_name = $('#name').val();
        stu = reg.test(pro_name);
        return stu;
    }
    
    function add (obj) {
        if (obj < 10) return "0" + obj; else return obj;
    }

    function is_rule(obj){
        <?php if($res['isout'] == 0) {?>
        $("#is_rule").val(obj);
        if (obj == 1) {
            $("#is_rule").parent().parent().next().css({"padding-left":"152px","display":"block"}); 
        } else {
            $("#is_rule").parent().parent().next().css({"padding-left":"152px","display":"none"}); 
        }
        <?php }?>
    }

    var _index = true;  //加锁
    //保存
    $('#btn').click(function(){
        var arr = {};
        arr['id']                = $('#id').val();
        arr['name']              = $('#name').val();
        arr['start_time']        = $('#start_time').val();
        arr['end_time']          = $('#end_time').val();
        arr['queue_num']         = $('#queue_num').val();
        arr['queue_expenditure'] = $('#queue_expenditure').val();
        arr['superposition']     = $('#superposition').val();
        arr['success_num']       = $('#success_num').val();
        arr['bonus']             = $('#bonus').val();
        arr['superposition']     = $('#superposition').val();
        arr['expenditure']       = $('#expenditure').val();
        arr['promote_num']       = $('#promote_num').val();
        arr['get_impose']        = $('input:radio[name="get_impose"]:checked').val();
        arr['is_rule']           = $('#is_rule').val();
        arr['rule']              = $('#rule').val();
        var date                  = new Date();
        var time = date.getFullYear() + '-' + add( (date.getMonth()+1) ) + '-' + add( date.getDate() ) + ' ' + add( date.getHours() ) + ':' + add( date.getMinutes() ) + ':' + add( date.getSeconds() );
 
        if($.trim(arr['name']) == "") {
            alert('请正确设置活动名称');
            return false;
        }

        var stu = check_proname();
        if(stu){
            alert('活动名称不能含有特殊字符串，请重新输入！');
            $('#name').val('');
            return;
        }

        if(arr['name'].length > 5) {
            alert('活动名称字符长度不能超过五个');
            return false;
        }
        
        if($.trim(arr['start_time']) == ""){
            alert("请正确设置开始时间");
            return;
        }

        if($.trim(arr['end_time']) == ""){
            alert("请正确设置结束时间");
            return;
        }

        if(arr['end_time']<arr['start_time']){
            alert("活动时间有误，结束时间不能比开始时间早！");
            return;
        }

        if(arr['end_time']<time){
            alert("活动时间有误，结束时间不能比当前时间早！");
            return;
        }

        if($.trim(arr['queue_num']) == "") {
            alert("请正确设置每个用户每天排队限制");
            return false;
        }

        if(isNaN(arr['queue_num']) || (parseFloat(arr['queue_num']) < -1 || (-1< parseFloat(arr['queue_num']) && parseFloat(arr['queue_num']) <0))){
            alert("请正确设置每个用户每天排队限制");
            $('#queue_num').val('-1');
            return false;
        }

        if($.trim(arr['queue_expenditure']) == "") {
            alert("请正确设置参与排队的个人消费金额限制");
            return false;
        }

        if(isNaN(arr['queue_expenditure']) || (parseFloat(arr['queue_expenditure']) < -1 || (-1< parseFloat(arr['queue_expenditure']) && parseFloat(arr['queue_expenditure']) <0))){
            alert("请正确设置参与排队的个人消费金额限制");
            $('#queue_expenditure').val('-1');
            return false;
        }

        if($.trim(arr['success_num']) == "") {
            alert("请正确设置成功限制：排队人数");
            return false;
        }

        if(isNaN(arr['success_num']) || (parseFloat(arr['success_num']) < -1 || (-1< parseFloat(arr['success_num']) && parseFloat(arr['success_num']) <0))){
            alert("请正确设置成功限制：排队人数");
            $('#success_num').val('-1');
            return false;
        }

        if($.trim(arr['success_num']) == "") {
            alert("请正确设置成功限制：排队人数");
            return false;
        }

        if($.trim(arr['bonus']) == "") {
            alert("请正确设置排队奖励金额");
            return false;
        }

        if(isNaN(arr['bonus']) || (parseFloat(arr['bonus']) < 0 || (0< parseFloat(arr['bonus']) && parseFloat(arr['bonus']) <0))){
            alert("请正确设置排队奖励金额");
            $('#bonus').val('0');
            return false;
        }

        if (arr['get_impose'] == 0 || arr['get_impose'] == '0') {
            if($.trim(arr['expenditure']) == "") {
            alert("请正确设置参与活动后总个人消费达到");
            return false;
            }

            if(isNaN(arr['expenditure']) || (parseFloat(arr['expenditure']) < 0 || (0< parseFloat(arr['expenditure']) && parseFloat(arr['expenditure']) <0))){
                alert("请正确设置参与活动后总个人消费达到");
                $('#expenditure').val('0');
                return false;
            }
        } else {
            if($.trim(arr['promote_num']) == "") {
            alert("请正确设置首次分享促使他人付款成功的人数");
            return false;
            }

            if(isNaN(arr['promote_num']) || (parseFloat(arr['promote_num']) < -1 || (-1< parseFloat(arr['promote_num']) && parseFloat(arr['promote_num']) <0))){
                alert("请正确设置首次分享促使他人付款成功的人数");
                $('#promote_num').val('-1');
                return false;
            }
        }
        if (_index = true) 
        {
            _index = false;
            setTimeout(function(){_index = true;},1500); //防止重复点击
            $.ajax({
                url: '/mshop/admin/index.php?m=queue&a=queue_save',
                dataType: 'json',
                type: 'post',
                traditional:true,
                data: {
                    "arr":JSON.stringify(arr)
                },
                success: function(res){
                    if( res.errcode == '1' ){
                        alert(res.errmsg);
                        window.location = "/mshop/admin/index?m=queue&a=queue_activity";
                    }else{
                        alert(res.errmsg);
                    }
                }
            });
        }
    });
</script>   
</html>