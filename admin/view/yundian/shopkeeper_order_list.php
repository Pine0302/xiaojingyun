<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>云店奖励－店主商品列表</title>
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
                <?php $keyContent = '店主商品列表'; ?>
                <?php include 'cloud_shop_switching.php'; ?>
            </div>
        <!--产品管理代码开始-->
        <div class="WSY_data">
            <div class="WSY_agentsbox">

                <ul class="WSY_search_q" style="width:30%">
                    <li><input <?php if($_GET['type'] == 0 || $_GET['type'] == 1){ ?> style="background-color:red!important;" <?php }?> type="submit" id="platform" onclick="jump_url('1');" class="WSY-skin-bg form-btn"  value="全部商品(<?php echo $result2['all']?$result2['all']:'0'; ?>)" ></li>
                    <li><input <?php if($_GET['type'] == 2){ ?> style="background-color:red!important;" <?php }?> type="submit" id="my" onclick="jump_url('2');" class="WSY-skin-bg form-btn"  value="上架中(<?php echo $result2['on']?$result2['on']:'0'; ?>)" ></li>
                    <li><input <?php if($_GET['type'] == 3){ ?> style="background-color:red!important;" <?php }?> type="submit" id="my" onclick="jump_url('3');" class="WSY-skin-bg form-btn"  value="下架(<?php echo $result2['out']?$result2['out']:'0'; ?>)" ></li>
                </ul>

                <form class="search" id="ac_frm" style="display:block" method="get" action="/mshop/admin/index.php?m=yundian&a=shopkeeper_order_list">
                    <input type="hidden" id="m" name="m" value="yundian">
                    <input type="hidden" id="a" name="a" value="shopkeeper_order_list">
                    <input type="hidden" id="type" name="type" value="<?php if($data['type']){echo $data['type'];}?>">
                    <ul class="WSY_search_q">
                            <li>店主昵称：<input type="text" name="realname" id="realname" value="<?php if($data['realname']){echo $data['realname'];}?>" class="form_input"></li>
                            <li>店主ID：<input type="text" name="user_id" id="user_id" onkeyup='this.value=this.value.replace(/\D/gi,"")' value="<?php if($data['user_id']){echo $data['user_id'];}?>" class="form_input"></li>
                            <li>商品名称：<input type="text" name="name" id="name" value="<?php if($data['name']){echo $data['name'];}?>" class="form_input"></li>
                            <li>店铺名称：<input type="text" name="store_name" id="store_name" value="<?php if($data['store_name']){echo $data['store_name'];}?>" class="form_input" onkeyup="clearTSZF(this) " onafterpaste="clearTSZF(this) "></li>
                            <li><input type="submit" id="search" class="WSY-skin-bg form-btn"  value="搜索" ></li>
                    </ul>
                </form>

                <table width="97%" class="WSY_table" id="WSY_t1">
                    <thead class="WSY_table_header">
                        <th width="5%" nowrap="nowrap"align="center">序号</th>
                        <th width="10%" nowrap="nowrap"align="center">店主昵称</th>
                        <th width="5%" nowrap="nowrap"align="center">店主id</th>
                        <th width="10%" nowrap="nowrap"align="center">店铺名称</th>
                        <th width="10%" nowrap="nowrap"align="center">商品主图</th>
                        <th width="10%" nowrap="nowrap"align="center">商品名称</th>
                        <th width="10%" nowrap="nowrap"align="center">商品所属分类</th>
                        <th width="10%" nowrap="nowrap"align="center">商品价格</th>
                        <th width="5%" nowrap="nowrap"align="center">商品库存</th>
                        <th width="5%" nowrap="nowrap"align="center">销量</th>
                        <th width="10%" nowrap="nowrap"align="center">商品状态</th>
                        <th width="20%" nowrap="nowrap"align="center">操作</th>
                    </thead>
                    <tbody class="tbody-main">
                    <?php foreach($result as $k => $v){ ?>

                            <tr>
                                <td style="text-align:center;"><?php echo $result[$k]['id']; ?></td>
                                <td style="text-align:center;"><?php echo $result[$k]['realname']; ?></td>
                                <td style="text-align:center;"><?php echo $result[$k]['user_id']; ?></td>
                                <?php  if($result[$k]['store_name'] == ''){$result[$k]['store_name'] = $result[$k]['realname'];}?>
                                <td style="text-align:center;"><?php echo $result[$k]['store_name']; ?></td>
                                <td style="text-align:center;"><img src="<?php echo $result[$k]['default_imgurl']; ?>" width="40px" height="40px"></td>
                                <td style="text-align:center;"><?php echo $result[$k]['name']; ?></td>
                                <td style="text-align:center;"><?php echo $result[$k]['fenlei']; ?></td>
                                <td style="text-align:center;"><?php echo $result[$k]['now_price']; ?></td>
                                <td style="text-align:center;"><?php echo $result[$k]['storenum']; ?></td>
                                <td style="text-align:center;"><?php echo $result[$k]['sell_count']; ?></td>
                                <td style="text-align:center;"><?php if($result[$k]['isout']==0){echo "上架";}else if($result[$k]['isout']==1){echo "下架";}; ?></td>
                                <td style="text-align:center;">
                                    
                                        <button class="table-btn WSY-skin-bg" onclick="change_isout('<?php echo $result[$k]['id']; ?>',3)">查看商品详情</button>
                                    <?php if($result[$k]['isout']==0){ ?>
                                        <button class="table-btn WSY-skin-bg" onclick="change_isout('<?php echo $result[$k]['id']; ?>',1)">下架商品</button>
                                    <?php } ?>
                                        <button class="table-btn WSY-skin-bg" onclick="change_isout('<?php echo $result[$k]['id']; ?>',2)">删除</button>
                                </td>
                            </tr>
                            <?php } ?>
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
    var realname   = $("#realname").val();
    var pro_name   = $("#name").val();
    var user_id    = $("#user_id").val();
    var store_name = $("#store_name").val();
    var param = "";
    if(realname!=""){
        param += "&realname="+realname;
    }
    if(user_id!=""){
        param += "&user_id="+user_id;
    }
    if(pro_name!=""){
        param += "&name="+pro_name;
    }
    if(store_name!=""){
        param += "&store_name="+store_name;
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
            var url="/mshop/admin/index.php?m=yundian&a=shopkeeper_order_list&pagenum="+p+param;
            location.href = url;
        }
    });

    function jumppage(){
        var a=parseInt($("#WSY_jump_page").val());
        if((a<1) || (a>count) || isNaN(a)){
            layer.alert('没有下一页了');
            return false;
        }else{
            var url="/mshop/admin/index.php?m=yundian&a=shopkeeper_order_list&pagenum="+a+param;
            location.href = url;
        }
    }
    <!-- 分页 end -->

    function jump_url(type){
        var url = '';
            url = "/mshop/admin/index.php?m=yundian&a=shopkeeper_order_list&type="+type+param;
        location.href = url;
    }

    //上下架商品
    function change_isout(id,type){
        //type类型 1.下架商品 2.上架商品 3.查看产品详情
        var remark = "";
        if(type==1){

            remark="您确定要下架商品吗？";
            layer.confirm(remark, {
                title:'警告',
                btn: ['确认','取消']
            }, function(confirm){
                layer.close(confirm);

                    $.ajax({
                    url: '/mshop/admin/index.php?m=yundian&a=change_isout_get',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        id:id,
                        is_ajax:1,
                        type_out:1,
                    },
                    success: function(res){
                        if( res.errcode == '1' ){
                            layer.alert(res.errmsg);
                            // console.log(res.sql);
                            // console.log(res.type_out);
                            setTimeout("location.reload()",2000);
                            // location.reload();
                        }else{
                            layer.alert(res.errmsg);
                        }
                    }
                });
            });

        }else if(type==2){

            remark="您确定要删除吗？";
            layer.confirm(remark, {
                title:'警告',
                btn: ['确认','取消']
            }, function(confirm){
                layer.close(confirm);

                    $.ajax({
                    url: '/mshop/admin/index.php?m=yundian&a=change_isout_get',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        id:id,
                        is_ajax:1,
                        type_out:4,
                    },
                    success: function(res){
                        if( res.errcode == '1' ){
                            layer.alert(res.errmsg);
                            // console.log(res.sql);
                            // console.log(res.type_out);
                            setTimeout("location.reload()",2000);
                            // location.reload();
                        }else{
                            layer.alert(res.errmsg);
                        }
                    }
                });
            });

        }else if(type==3){
            var url = '';
                url = "/mshop/admin/index.php?m=yundian&a=shopkeeper_order&id="+id;
            location.href = url;
        }
    }
    //* 过滤特殊字符 */
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
</script>
</html>