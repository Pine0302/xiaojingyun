<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>云店奖励－订单管理</title>
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
    <script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
    <script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
    <script>
        layer.config({
            extend: '/extend/layer.ext.js'
        });
    </script>
    <style type="text/css">
        .form-btn{width:auto!important;padding:0 10px!important;cursor:pointer;color:#fff!important;border:0!important;}
        .form-add-btn{display:inline-block;line-height:24px;border-radius:3px;}
        .table-btn{color:#fff;border:0;cursor:pointer;border-radius:3px;height:24px;padding:0 10px;font-size:12px;}

        .WSY-skin-bg{margin-top: 2px;}
        .div_item{float:left;padding:10px;font-size:14px;}
        .div_item label{margin-left:5px;font-size:14px;}
        .div_item input{border:1px solid #ccc; border-radius: 2px;}
        .layui-layer-content button{float: left;margin-top: 56px;margin-bottom: 19px;width: 80px;height: 30px;}
        .xubox_title{background: none!important;}
        .xubox_title em{left: 0!important;text-align: center!important;width: 100%!important;}
        .WSY_search_q li input[type="text"] {
            width: 150px;
        }
    </style>
</head>
<body>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">
    <!--列表内容大框开始-->
    <div class="WSY_columnbox">
        <div class="WSY_column_header">
            <?php $keyContent = '订单管理'; ?>
            <?php include 'cloud_shop_switching.php'; ?>
        </div>
        <!--产品管理代码开始-->
        <div class="WSY_data">
            <div class="WSY_agentsbox">

                <ul class="WSY_search_q" style="width:20%">
                    <li><input <?php if($_GET['type'] == 0){ ?> style="background-color:red!important;" <?php }?> type="submit" id="platform" onclick="jump_url('<?php echo $row['user_id']?>','0');" class="WSY-skin-bg form-btn"  value="平台订单(<?php echo $platform_num?$platform_num:'0'; ?>)" ></li>
                    <li><input <?php if($_GET['type'] == 1){ ?> style="background-color:red!important;" <?php }?> type="submit" id="my" onclick="jump_url('<?php echo $row['user_id']?>',1);" class="WSY-skin-bg form-btn"  value="自营订单(<?php echo $my_num?$my_num:'0'; ?>)" ></li>
                </ul>

                <form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=yundian&a=yundian_order_list">
                    <input type="hidden" id="m" name="m" value="yundian">
                    <input type="hidden" id="a" name="a" value="yundian_order_list">
                    <input type="hidden" id="type" name="type" value="<?php echo $_GET['type']; ?>">
                    <ul class="WSY_search_q">
                        <li>订单状态：
                            <select name="status" id="status" class="form_select">
                                <option value="0" <?php if($_GET['status'] == 0) echo 'selected' ?>>全部</option>
                                <option value="1" <?php if($_GET['status'] == 1) echo 'selected' ?>>待发货</option>
                                <option value="2" <?php if($_GET['status'] == 2) echo 'selected' ?>>待收货</option>
                                <option value="3" <?php if($_GET['status'] == 3) echo 'selected' ?>>待完成</option>
                                <option value="4" <?php if($_GET['status'] == 4) echo 'selected' ?>>交易完成</option>
                                <option value="5" <?php if($_GET['status'] == 5) echo 'selected' ?>>退款</option>
                                <option value="6" <?php if($_GET['status'] == 6) echo 'selected' ?>>退货</option>
                                <option value="7" <?php if($_GET['status'] == 7) echo 'selected' ?>>换货</option>
                            </select>
                        </li>
                        <li>订单号：<input onkeyup="this.value=this.value.replace(/[^\d]/g,'') " onafterpaste="this.value=this.value.replace(/[^\d]/g,'') " type="text" name="batchcode" id="batchcode" value="<?php if($_GET['batchcode']){echo $_GET['batchcode'];}?>" class="form_input"></li>
                        <?php if($_GET['type'] == 1){ ?>
                            <li>店主昵称：<input type="text" name="name" id="name" value="<?php if($_GET['name']!=-1){echo $_GET['name'];}?>" class="form_input"></li>
                            <li>店主id：<input onkeyup="this.value=this.value.replace(/[^\d]/g,'') " onafterpaste="this.value=this.value.replace(/[^\d]/g,'') " type="text" name="user_id" id="user_id" value="<?php if($_GET['user_id']!=""){echo $_GET['user_id'];}?>" class="form_input"></li>
                        <?php } ?>
                        <input type="hidden" name="yun_user_id" value="<?php echo $_GET['yun_user_id']; ?>" >
                        <li><input type="submit" id="search" class="WSY-skin-bg form-btn"  value="搜索" ></li>
                        <?php if($_GET['type'] == 1){ ?>
                        <li><input type="button" id="export" class="WSY-skin-bg form-btn"  value="导出" ></li>
                        <li><input type="button" id="finishs" class="WSY-skin-bg form-btn"  value="一键完成订单" onclick="jump_url('<?php echo $row['batchcode']?>',7,'<?php echo $row['user_id']?>');" ></li>
                        <li><input type="button" id="pays" class="WSY-skin-bg form-btn"  value="批量确定打款" onclick="jump_url('<?php echo $row['batchcode']?>',8);"></li>
                        <?php } ?>
                    </ul>
                </form>

                <table width="97%" class="WSY_table" id="WSY_t1">
                    <thead class="WSY_table_header">
                    <?php if($_GET['type'] == 0){ ?>
                        <!-- <th width="3%" nowrap="nowrap"align="center">
                            <input type="checkbox" name="all_checkbox" onclick="change_box()" class="all_checkbox" >
                            全选
                        </th> -->
                        <th width="5%" nowrap="nowrap"align="center">序号</th>
                        <th width="10%" nowrap="nowrap"align="center">订单号</th>
                        <th width="5%" nowrap="nowrap"align="center">订单金额</th>
                        <th width="10%" nowrap="nowrap"align="center">用户信息</th>
                        <th width="10%" nowrap="nowrap"align="center">订单支付时间</th>
                        <th width="15%" nowrap="nowrap"align="center">支付方式</th>
                        <th width="5%" nowrap="nowrap"align="center">支付金额</th>
                        <th width="5%" nowrap="nowrap"align="center">订单状态</th>
                        <th width="5%" nowrap="nowrap"align="center">操作</th>
                    <?php }elseif($_GET['type'] == 1){ ?>
                        <th width="3%" nowrap="nowrap"align="center">
                            <input type="checkbox" name="all_checkbox" onclick="change_box()" class="all_checkbox" >
                            全选
                        </th>
                        <th width="6%" nowrap="nowrap"align="center">序号</th>
                        <th width="12%" nowrap="nowrap"align="center">订单号</th>
                        <th width="4%" nowrap="nowrap"align="center">订单金额</th>
                        <th width="10%" nowrap="nowrap"align="center">下单用户信息</th>
                        <th width="10%" nowrap="nowrap"align="center">收货地址</th>
                        <th width="7.5%" nowrap="nowrap"align="center">订单支付时间</th>
                        <th width="12%" nowrap="nowrap"align="center">支付方式</th>
                        <th width="4%" nowrap="nowrap"align="center">支付金额</th>
                        <th width="5%" nowrap="nowrap"align="center">订单状态</th>
                        <th width="7.5%" nowrap="nowrap"align="center">买家留言</th>
                        <!-- <th width="5%" nowrap="nowrap"align="center">订单结算状态</th> -->
                        <th width="7.5%" nowrap="nowrap"align="center">云店店主信息</th>
                        <th width="7.5%" nowrap="nowrap"align="center">货款状态</th>
                        <th width="10%" nowrap="nowrap"align="center">操作</th>
                    <?php } ?>
                    </thead>
                    <tbody class="tbody-main">

                    <?php foreach ($order_list['data'][0] as $key => $row) { ?>
                        <?php if($_GET['type'] == 0){ ?>
                            <tr>
                                <!-- <td style="text-align:center;">
                                    <input type="checkbox" name="input_checkbox" class="checkbox" b_id="<?php echo $row['batchcode']; ?>">
                                </td> -->
                                <td style="text-align:center;"><?php echo $row['id']?></td>
                                <td style="text-align:center;"><?php echo $row['batchcode']?></td>
                                <td style="text-align:center;"><?php echo $row['origin_price']?></td>
                                <td style="text-align:center;"><?php echo $row['name_str']; ?></td>
                                <td style="text-align:center;"><?php echo $row['paytime']; ?></td>
                                <td style="text-align:center;"><?php echo $row['paystyle'].$row['pay_info']; ?></td>
                                <td style="text-align:center;"><?php echo $row['price']?></td>
                                <td style="text-align:center;"><?php echo $row['status_str']?></td>
                                <td style="text-align:center;">
                                    <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['batchcode']?>',3,'<?php echo $row['user_id']?>');">查看订单</button>
                                </td>
                            </tr>
                        <?php }elseif($_GET['type'] == 1){ ?>
                            <tr>
                                <td style="text-align:center;">
                                    <input type="checkbox" name="input_checkbox" class="checkbox" b_id="<?php echo $row['id']; ?>">
                                </td>
                                <td style="text-align:center;"><?php echo $row['id']?></td>
                                <td style="text-align:center;"><?php echo $row['batchcode']?></td>
                                <td style="text-align:center;"><?php echo $row['origin_price']?></td>
                                <td style="text-align:center;"><?php echo $row['name_str']; ?></td>
                                <td style="text-align:center;"><?php echo $row['address']; ?></td>
                                <td style="text-align:center;"><?php echo $row['paytime']; ?></td>
                                <td style="text-align:center;"><?php echo $row['paystyle'].$row['pay_info']; ?></td>
                                <td style="text-align:center;"><?php echo $row['price']?></td>
                                <td style="text-align:center;"><?php echo $row['status_str']?></td>
                                <td style="text-align:center;"><?php
                                        if($row['remark']){
                                            $len = strlen($row['remark']);
                                            if($len >100){
                                                echo mb_substr($row['remark'],0,99,'utf-8').'...';
                                            }else{
                                                echo $row['remark'];
                                            }
                                        }
                                    ?></td>
<!--                                <td style="text-align:center;">--><?php //echo $row['balance_str']?><!--</td>-->
                                <td style="text-align:center;"><?php echo $row['yundian_info']?></td>
                                <td style="text-align:center;"><?php echo $row['payment']?></td>
                                <td style="text-align:center;">
                                    <?php if($row['sendstatus'] == 2 && $row['paystatus'] == 1 && $row['status'] != 1 && $row['aftersale_state']==0){ ?>
                                        <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['batchcode']?>',4,'<?php echo $row['price']?>');">完成订单</button>
                                    <?php }?>
                                    <?php if($row['aftersale_type'] == 1 && $row['aftersale_state'] == 1 && $row['status'] != 1 && $row['status'] == 0 && $row['sendstatus'] != 6){?>
                                        <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['batchcode']?>',5,'<?php echo $row['yundian_id']; ?>',1,'<?php echo $row['account']; ?>','<?php echo $row['reason']; ?>',0);">确定打款</button>
                                    <?php }?>
                                    <?php if($row['aftersale_type'] == 2 && $row['paystatus'] == 1 && $row['status'] != 1 && $row['return_type'] == 0 && ($row['aftersale_state'] == 2 || $row['return_status'] == 2) && $row['sendstatus'] != 4 ){?>
                                        <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['batchcode']?>',5,'<?php echo $row['yundian_id']; ?>',2,'<?php echo $row['account']; ?>','<?php echo $row['reason']; ?>',1);">确定打款</button>
                                    <?php }?>
                                    <?php if($row['aftersale_type'] == 2 && $row['return_type'] == 1 && $row['status'] != 1 && $row['paystatus'] == 1  && $row['return_status'] == 6 && $row['sendstatus'] != 4){?>
                                        <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['batchcode']?>',5,'<?php echo $row['yundian_id']; ?>',3,'<?php echo $row['account']; ?>','<?php echo $row['reason']; ?>',1);">确定打款</button>
                                    <?php }?>
                                    <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['batchcode']?>',6,'<?php echo $row['user_id']?>');">查看订单日志</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php }?>
                    </tbody>

                </table>
            </div>
            <!--翻页开始-->
            <div class="WSY_page">

            </div>
            <!--翻页结束-->
        </div>
        <!--产品管理代码结束-->
    </div>
    <div style="width:100%;height:20px;"></div>
</div>
<!--内容框架结束-->
</body>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script src="/mshop/admin/Order/order/percent/jquery.percentageloader.0.2.js"></script>
<script src="/weixinpl/common/js/floatBox.js"></script>
<script type="text/javascript">
    var type = '<?php echo $_GET['type']?>';
    var customer_id_en = '<?php echo $customer_id_en; ?>';
    var customer_id = '<?php echo $customer_id; ?>';
    var status = $("#status").val();
    var batchcode = $("#batchcode").val();
    var name = $("#name").val();
    var user_id = $("#user_id").val();
    var yun_user_id = '<?php echo $_GET['yun_user_id']; ?>';
    var param = "";
    var param1 = '';
    if(name == undefined || name =='undefined'){
        name = '';
    }
    if(user_id == undefined || user_id =='undefined'){
        user_id = '';
    }
    if(batchcode == undefined || batchcode =='undefined'){
        batchcode = '';
    }
    if(status!="" && status!= undefined){
        param += "&status="+parseInt(status);
        param1 += "/status/"+parseInt(status);
    }
    if(batchcode!="" && batchcode!= undefined){
        param += "&batchcode="+batchcode;
        param1 += "/batchcode/"+batchcode;
    }
    if(name!="" && name!= undefined){
        param += "&name="+name;
        param1 += "/name/"+name;
    }
    if(user_id!="" && user_id!= undefined){
        param += "&user_id="+user_id;
        param1 += "/user_id/"+user_id;
    }
    if(yun_user_id!="" && user_id!= undefined){
        param += "&yun_user_id="+yun_user_id;
        param1 += "/yun_user_id/"+yun_user_id;
    }

    <!-- 分页 start -->
    var pagenum = '<?php echo $pageNum ?>';//当前页
    var count ='<?php echo $pageCount ?>';//总页数
    //pageCount：总页数
    //current：当前页
    $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
            var url="/mshop/admin/index.php?m=yundian&a=yundian_order_list&type="+type+"&pagenum="+p+param;
            location.href = url;
        }
    });

    function jumppage(){
        var a=parseInt($("#WSY_jump_page").val());
        if((a<1) || (a>count) || isNaN(a)){
            layer.alert('没有下一页了');
            return false;
        }else{
            var url="/mshop/admin/index.php?m=yundian&a=yundian_order_list&type="+type+"&pagenum="+a+param;
            location.href = url;
        }
    }
    <!-- 分页 end -->

    function jump_url(data,type,more,more2,more3,more4,retype){
        var url = '';
        if(type == 0 || type == 1){
            url = "/mshop/admin/index.php?m=yundian&a=yundian_order_list&type="+type+param;
        }else if(type == 3) {   //跳转商城订单管理
            url = "/weixinpl/back_newshops/Order/order/order.php?customer_id="+customer_id_en+'&search_batchcode='+data;
        }else if(type == 4) {   //完成订单
            url = "/weixinpl/back_newshops/Order/order/order.class.php";
            var remark = '确定完成订单？';
            layer.confirm(remark, {
                title:'注意',
                btn: ['确认','取消']
            }, function(confirm){
                layer.close(confirm);
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        // tid:template_id,
                        batchcode:data,
                        totalprice:more,
                        op:'confirm',
                    },
                    success: function(res){
                        console.log(res);
                        if( res.status == 0 ){
                            layer.alert(res.msg,function(){
                                document.location.reload();
                            });
                        }else{
                            alert(res.msg);
                        }
                    }
                });
            }, function(){

            });
            return;
        }else if(type == 5){    //确定打款
            var remark = '确定要打款吗？一旦确认打款之后无法撤回！<br/>确认退款金额为：'+more3;
            var batchcode = data;
            var status = 1;
            var op = 'goodRefund';
            layer.confirm(remark, {
                title:'注意',
                btn: ['确认','取消']
            }, function(confirm){
                layer.close(confirm);
                $.ajax({
                    url: "/mshop/admin/Order/order/order.class.php",
                    type:"POST",
                    data:{'batchcode':batchcode,'totalprice':more3,'retype':retype,'status':1,'op':op},
                    dataType:"json",
                    async:false,
                    success: function(res2){
                        console.log(res2);
                        if( res2.status == 0 ){
                            layer.alert(res2.msg,function(){
                                document.location.reload();
                            });
                        }else{
                            alert(res2.msg);
                        }
                    },
                    error:function(){
                        alert("网络错误请检查网络");
                    }
                })
            }, function () {
            });
            return;
        }else if(type == 6){    //查看订单日志
            url = "/mshop/admin/index.php?m=yundian&a=yundian_order_log&batchcode="+data+'&user_id='+more;
        }else if(type == 7) {    //一键完成订单
            url = "/weixinpl/back_newshops/Order/order/order.class.php";
            var remark = '确定一键完成所有待完成自营订单么？';
            layer.confirm(remark, {
                title: '注意',
                btn: ['确认', '取消']
            }, function (confirm) {
                layer.close(confirm);
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        op: 'yundian_confirm_all'
                    },
                    success: function(res){
                        console.log(res);
                        if( res.status == 0 ){
                            layer.alert(res.msg,function(){
                                document.location.reload();
                            });
                        }else{
                            alert(res.msg);
                        }
                    }
                });
            }, function () {
            });
            return;
        }else if(type == 8){    //批量打款
            //获取所有选中的而且不包含disabled属性的复选框
            var box = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)');

            if (box.length < 1) {
                layer.alert("亲，请勾选批量打款的复选框对象！");
                return;
            }

            var box_arr = [];   //创建数组
            for (var i = 0; i < box.length; i++) {
                box_arr[i] = [];
                var box_val = $(':checkbox[name="input_checkbox"]:checked:not(:disabled)').eq(i).attr("b_id");
                //复选框附带的数组
                var box_arr1 = eval("(" + box_val + ")");
                box_arr[i] = box_arr1;
            }

            box_arr = JSON.stringify(box_arr);  //数组转json
console.log(box_arr);
//            url = "/mshop/web/index?m=yundian&a=yundian_pay_return_agrees";
            url = '/weixinpl/back_newshops/Order/order/order.class.php';
            var remark = '确定要打款吗？一旦确认打款之后无法撤回！<br/>';
            layer.confirm(remark, {
                title:'注意',
                btn: ['确认','取消']
            }, function(confirm){
                layer.close(confirm);
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        box_arr:box_arr,
                        op:'yundian_return_money_all'
                    },
                    success: function(res){
                        console.log(res);
                        if( res.status == 0 ){
                            layer.alert(res.msg,function(){
                                document.location.reload();
                            });
                        }else{
                            alert(res.msg);
                        }
                    }
                });
            }, function(){
            });
            return;
        }
        location.href = url;
    }
;
    $('#export').click(function(){
        var type;
        yundian_excel(type);
    });



    /*全选*/
    function change_box(){
        var all_box = $(".all_checkbox").is(':checked')
        if( all_box ){
            $(".checkbox").prop("checked",true);
        }else{
            $(".checkbox").prop("checked",false);
        }
    }

    function yundian_excel(){
        if(type ==0){
            var excelArray = [
                ["num","序号"],
                ["batchcode","订单号"],
                ["origin_price","订单金额"],
                ["user_id","用户信息"],
                ["paytime","订单支付时间"],
                ["paystyle","支付方式"],
                ["price","支付金额"],//
                ["status","订单状态"]
            ];
        }else{
            var excelArray = [
                ["num","序号"],
                ["batchcode","订单号"],
                ["origin_price","订单金额"],
                ["user_id","下单用户信息"],
                ["address","收货地址"],
                ["paytime","订单支付时间"],
                ["paystyle","支付方式"],
                ["price","支付金额"],//
                ["status","订单状态"],//
                ["words","买家留言"],
                // ["balance","订单结算状态"],
                ["yundian_info","云店店主信息"],
                ["payment","货款状态"]
            ];
        }

        exportBox(excelArray);
        $(".floatbox").css('z-index','999');
        $(".floatbox").show();

        $(".floatinputs").click(function(){
            var str="";
            $("input[name='excel_field[]']:checkbox").each(function(){
                if($(this).is(':checked')){
                    str += $(this).val()+","
                }
            })
            str = str.substring(0,str.length-1);
            var customer_id='<?php echo $data['customer_id']; ?>';
            var status = $("#status").val();//订单状态
            var user_id = '';
            var name = '';
            if(status==""){
                status = '0';
            }
            var batchcode = $("#batchcode").val();//订单号
            if(batchcode==""){
                batchcode = '';
            }
            if(type == 1){
                user_id = $("#user_id").val();
                name = $("#name").val();
                if(user_id==""){
                    user_id = '';
                }
                if(name==""){
                    name = '';
                }
            }
            console.log(str);

            var parm='/customer_id/'+customer_id+'/type/'+type+'/status/'+status+'/batchcode/'+batchcode+'/user_id/'+user_id+'/name/'+name;
            var __s = [];
            parm = parm.substring(1, parm.length-1);
            console.log(parm);
            __s = parm.split('/');
            var _obj = {};
            console.log(__s);
            for (var i in __s) {
                if (parseInt(i)%2 == 0) {
                    _obj[__s[i]] = '';
                } else {
                    _obj[__s[i-1]] = __s[i];
                }
            }

            var obj = JSON.stringify(_obj);
            console.log(obj);

            var excel_fields= str;
            var name='yundian_excel';
            var emails = '';
            var op     = 'iscount';
            $.ajax({type:'post', async:false, url:'/weixinpl/common/explore/jiaoben.php',data:{fields:excel_fields,function_name:name,param_json:obj,customer_id:customer_id,op:op,},
                success:function(data)
                {
                    console.log(data);
                    var res = JSON.parse(data);

                    if(res.status == 2)
                    {
                        layer.msg(res.msg);
                        return;
                    }


                    var eamil_arr     = res.emails.split('#*#');
                    var eamil_address = "";
                    var type          = 2;
                    var op            = 'add_email';
                    var tips          = "导出数据已打包发送到您的邮箱，请注意查收";
                    if(eamil_arr.length>0)
                    {
                        // eamil_address = eamil_arr[0];//不显示默认邮箱2018/3/27
                    }

                    if(res.errcode == 10003)
                    {
                        layer.msg(res.errmsg);
                        return;
                    }
                    else
                    {
                        type = 2;
                        tips = "请留意您的邮箱，导出完成后会发到你的邮箱上！";
                        layer.prompt({title: '请输入您邮箱地址',value:eamil_address, formType: 0}, function(email, prompt){
                            layer.close(prompt);
                            if (checkEmail(email)){
                                emails    = email;
                                $.ajax({type:'post', url:'/weixinpl/common/explore/jiaoben.php', data:{fields:excel_fields,function_name:name,param_json:obj,customer_id:customer_id,email:emails,op:op,type:type,},
                                    success:function(data){
                                        var res           = JSON.parse(data);
                                        if(res.status == 2)
                                        {
                                            layer.msg(res.msg);
                                            return;
                                        }
                                        $.post('/weixinpl/common/explore/jiaoben.php',{'debug':1},function(da){},'json');
                                        layer.msg(tips);
                                    }
                                });

                            }
                            else
                            {
                                layer.msg("邮箱地址填写有误，请填写正确的邮箱地址");
                                return;
                            }
                        })
                    }


                }
            })
            $(".floatbox").hide();
        });
    }

    /*校验邮箱地址*/
    function checkEmail(str){
        var re= /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        return re.test(str);
    }
</script>
</html>