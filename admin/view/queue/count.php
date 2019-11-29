<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>活动管理－活动统计</title>
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
    <script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>

    <style type="text/css">
        .form-btn{width:auto!important;padding:0 10px!important;cursor:pointer;color:#fff!important;border:0!important;}
        .form-add-btn{display:inline-block;line-height:24px;border-radius:3px;}
        .table-btn{color:#fff;border:0;cursor:pointer;border-radius:3px;height:24px;padding:0 10px;font-size:12px;}


        .div_item{float:left;padding:10px;font-size:14px;}
        .div_item label{margin-left:5px;font-size:14px;}
        .div_item input{border:1px solid #ccc; border-radius: 2px;}
        .layui-layer-content button{float: left;margin-top: 56px;margin-bottom: 19px;width: 80px;height: 30px;}
        .xubox_title{background: none!important;}
        .xubox_title em{left: 0!important;text-align: center!important;width: 100%!important;}
        .count_item{width:180px;}
    </style>
</head>
<body>
    <!--内容框架开始-->
    <div class="WSY_content" id="WSY_content_height">
        <!--列表内容大框开始-->
        <div class="WSY_columnbox"> 
            <div class="WSY_column_header">
                <!-- <div class="WSY_columnnav">
                    <a class="white1">满赠活动</a>
                </div> -->
                <?php 
                    $head = 1;
                    include("queue_head.php");
                ?>
            </div>
            <!--产品管理代码开始-->
            <div class="WSY_data">
                <div class="WSY_agentsbox">
                    <form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=m=queue&a=queue_count">
                        <input type="hidden" id="m" name="m" value="queue">
                        <input type="hidden" id="a" name="a" value="queue_count">
                        <ul class="WSY_search_q">
                            <li>活动名称：<input type="text" name="activity_name" id="activity_name" value="<?php if($param['activity_name']!=""){echo $param['activity_name'];}?>" class="form_input" style="width:180px"></li>
                            <li>活动编码：<input type="text" name="activity_id" id="activity_id" value="<?php if($param['activity_id']!=-1){echo $param['activity_id'];}?>" class="form_input " style="width:180px" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)" ></li>
                            <li>活动状态：
                                <select name="isout" id="isout">
                                    <option value="1" <?php if($param['isout']==1){?>selected<?php }?>>已启用</option>
                                    <option value="2" <?php if($param['isout']==2){?>selected<?php }?>>已终止</option>
                                    <option value="3" <?php if($param['isout']==3){?>selected<?php }?>>全部</option>
                                </select>
                            </li>
                            <li>订单号：<input type="text" name="batchcode" id="batchcode" value="<?php if($param['batchcode']!=-1){echo $param['batchcode'];}?>" class="form_input " style="width:180px" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)" ></li>
                            <br><br><br>
                            <li>用户名称：<input type="text" name="user_name" id="user_name" value="<?php if($param['user_name']!=""){echo $param['user_name'];}?>" class="form_input" style="width:180px"></li>
                            <li>用户编码：<input type="text" name="user_id" id="user_id" value="<?php if($param['user_id']!=-1){echo $param['user_id'];}?>" class="form_input" style="width:180px" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)" ></li>
                            <li>排队状态：
                                <select name="status" id="status">
                                    <option value="-1">全部</option>
                                    <option value="0" <?php if($param['status']==0){?>selected<?php }?>>待排队</option>    
                                    <option value="1" <?php if($param['status']==1){?>selected<?php }?>>排队中</option>
                                    <option value="2" <?php if($param['status']==2){?>selected<?php }?>>排队完成</option>
                                    <option value="3" <?php if($param['status']==3){?>selected<?php }?>>待领取</option>
                                    <option value="4" <?php if($param['status']==4){?>selected<?php }?>>已领取</option>
                                    <option value="5" <?php if($param['status']==5){?>selected<?php }?>>排队失败</option>
                                    <option value="6" <?php if($param['status']==6){?>selected<?php }?>>领取失败</option>
                                    <option value="7" <?php if($param['status']==7){?>selected<?php }?>>已取消</option>
                                </select>
                            </li>
                            <li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" ></li>
                        </ul> 
                    </form>

                    <table width="97%" class="WSY_table" id="WSY_t1">
                        <thead class="WSY_table_header">
                            <th width="7%" nowrap="nowrap"align="center">队列号</th>
                            <th width="14%" nowrap="nowrap"align="center">订单号</th>
                            <th width="4%" nowrap="nowrap"align="center">排队号</th>
                            <th width="10%" nowrap="nowrap"align="center">用户名称 / 用户编码</th>
                            <th width="8%" nowrap="nowrap"align="center">排队状态</th>
                            <th width="7%" nowrap="nowrap"align="center">排队人数</th>
                            <th width="12%" nowrap="nowrap"align="center">已消费金额 / 分享购物人数</th>
                            <th width="7%" nowrap="nowrap"align="center">离领奖还差</th>
                            <th width="14%" nowrap="nowrap"align="center">活动名称 / 活动编码</th>
                            <th width="8%" nowrap="nowrap"align="center">参与限制</th>
                            <th width="8%" nowrap="nowrap"align="center">成功限制</th>
                            <th width="8%" nowrap="nowrap"align="center">领取限制</th>
                            <th width="8%" nowrap="nowrap"align="center">奖励金额</th>
                            <th width="6%" nowrap="nowrap"align="center">活动状态</th>
                            <th width="7%" nowrap="nowrap"align="center">操作</th>
                        </thead>
                        <tbody class="tbody-main">
                            <?php foreach ($res as $key => $row) {
                                $status = $row['status'] ;
                                switch($status){
                                    case "0":
                                        $status_str = "待排队";
                                        break;
                                    case "1":
                                        $status_str = "排队中";
                                        break;
                                    case "2":
                                        $status_str = "排队完成";
                                        break;
                                    case "3":
                                        $status_str = "待领取";
                                        break;
                                    case "4":
                                        $status_str = "已领取";
                                        break;
                                    case "5":
                                        $status_str = "排队失败";
                                        break;
                                    case "6":
                                        $status_str = "领取失败";
                                        break;
                                    case "7":
                                        $status_str = "已取消";
                                        break;
                                    default:
                                        $status_str = "未知状态";
                                }

                                $isout = $row['isout'] ;
                                switch($isout){
                                    case "0":
                                        $isout_str = "待启用";
                                        break;
                                    case "1":
                                        $isout_str = "已启用";
                                        break;
                                    case "2":
                                        $isout_str = "已终止";
                                        break;
                                    default:
                                        $isout_str = "未知状态";
                                }
                            ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $row['id'];?></td>
                                <td style="text-align:center;"><?php echo $row['batchcode'];?></td>
                                <td style="text-align:center;"><?php echo $row['queue_code'];?></td>
                                <td style="text-align:center;"><?php echo $row['weixin_name'].' / '.$row['weixin_id'];?></td>
                                <td style="text-align:center;"><?php echo $status_str;?></td>
                                <td style="text-align:center;"><?php echo $row['count_num'];?></td>
                                <?php if($row['get_impose']==0){?>
                                    <td style="text-align:center;"><?php echo $row['o_expenditure'];?></td>
                                    <td style="text-align:center;"><?php echo $row['e_num'];?></td>
                                <?php } else {?>
                                    <td style="text-align:center;"><?php echo $row['o_promote_num'];?>人</td>
                                    <td style="text-align:center;"><?php echo $row['p_num'];?>人</td>
                                <?php }?>
                                
                                <td style="text-align:center;"><?php echo $row['queue_name'].' / '.$row['queue_id'];?></td>
                                <td style="text-align:center;"><?php echo $row['queue_expenditure'];?></td>
                                <td style="text-align:center;"><?php echo $row['success_num'];?></td>
                                <?php if($row['get_impose']==0){?>
                                    <td style="text-align:center;"><?php echo $row['q_expenditure'];?></td>
                                <?php } else {?>
                                    <td style="text-align:center;"><?php echo $row['promote_num'];?>人</td>
                                <?php }?>
                                <td style="text-align:center;"><?php echo $row['bonus'];?></td>
                                <td style="text-align:center;"><?php echo $isout_str;?></td>
                                <td style="text-align:center;">
                                <?php if( $isout == 1 && $status != 7 && $status < 4 ){ ?>
                                    <button class="table-btn WSY-skin-bg" onclick="status_del(<?php echo $row['sid'].','.$row['status'].','.$row['user_id'] ?>)">取消</button>
                                <?php }else if( $status == 7){ ?>
                                    <button class="table-btn WSY-skin-bg" onclick="data_del(<?php echo $row['sid'].','.$row['status'].','.$row['user_id'] ?>)">删除</button>
                                <?php } ?>
                                    <?php //if( $row['status'] < 4 ){ ?>
                                        
                                    <?php //}else if( $row['status'] == 7 ){ ?>
                                        <!-- <button class="table-btn WSY-skin-bg" onclick="data_del(<?php echo $row['sid'].','.$row['status'].','.$row['user_id'] ?>)">删除</button> -->
                                    <?php //} ?>

                                </td>
                            </tr>
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
<script type="text/javascript">

var activity_name = $("#activity_name").val();
var activity_id   = $("#activity_id").val();
var isout         = $("#isout").val();
var user_name     = $("#user_name").val();
var user_id       = $("#user_id").val();
var status        = $("#status").val();
var param         = "";
var id            = '<?php echo $id ?>';
if(activity_name!=""){
    param += "&activity_name="+activity_name;
}
if(activity_id!=""){    
    param += "&activity_id="+parseInt(activity_id);
}
if(isout!=-1){
    param += "&isout="+isout;
}
if(user_name!=""){
    param += "&user_name="+user_name;
}
if(user_id!=""){    
    param += "&user_id="+parseInt(user_id);
}
if(status!=-1){
    param += "&status="+status;
}
if(id!='-1'){
    param += "&id="+id;
}

<!-- 分页 start -->
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数   
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
    pageCount:count,
    current:pagenum,
    backFn:function(p){
    var url="/mshop/admin/index.php?m=queue&a=queue_count&pagenum="+p+param;    

    location.href = url;
   }
});

function jumppage(){
    var a=parseInt($("#WSY_jump_page").val());
    if((a<1) || (a>count) || isNaN(a)){
        layer.alert('没有下一页了');
        return false;
    }else{
        var url="/mshop/admin/index.php?m=queue&a=queue_count&pagenum="+a+param;    
        location.href = url;
    }
}
<!-- 分页 end -->

/*只能输入数字*/
function clearNoNum(obj){
    obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"以外的字符
    obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
}

/*状态改为删除*/
function status_del(id,status,user_id) {
        var op_msg = '您确定要取消吗？';
        layer.confirm(op_msg,{btn: ['确认','取消']},
            function(confirm){
                layer.close(confirm);
                $.ajax({
                    url: '/mshop/admin/index.php?m=queue&a=status_del',
                    type: 'post',
                    data: {id:id,status:status,user_id:user_id},
                    dataType: 'json',
                    success: function (res) {
                        // console.log(res)
                        // if (res.errcode == 0) {
                        location.reload();
                        // } else {
                        //     layer.alert(res.errmsg);
                        // }
                    },
                    error:function(res){
                        layer.alert("网络错误请检查网络");
                    }
                });
            },function(){
                layer.msg('已取消', {
                    time: 4000,
                    btn: ['确认'],
                    icon:1
                });
            });
    }
/*删除队列数据*/
function data_del(id,status) {
        var op_msg = '您确定要删除吗？';
        layer.confirm(op_msg,{btn: ['确认','取消']},
            function(confirm){
                layer.close(confirm);
                $.ajax({
                    url: '/mshop/admin/index.php?m=queue&a=data_del',
                    type: 'post',
                    data: {id:id,status:status},
                    dataType: 'json',
                    success: function (res) {
                        console.log(res)
                        // if (res.errcode == 0) {
                        location.reload();
                        // } else {
                        //     layer.alert(res.errmsg);
                        // }
                    },
                    error:function(res){
                        layer.alert("网络错误请检查网络");
                    }
                });
            },function(){
                layer.msg('已取消', {
                    time: 4000,
                    btn: ['确认'],
                    icon:1
                });
            });
    }
</script>   
</html>