<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>底部菜单模板列表</title>
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

    </style>
</head>
<body>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">
    <!--列表内容大框开始-->
    <div class="WSY_columnbox">
        <?php
            include_once($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
        ?>
        <!--产品管理代码开始-->
        <div class="WSY_data">
            <div class="WSY_agentsbox">
                <form class="search" id="search" style="display:block" method="get" action="/mshop/admin/index.php">
                    <input type="hidden" id="m" name="m" value="bottom_label">
                    <input type="hidden" id="a" name="a" value="template_list">
                    <input type="hidden" id="customer_id_en" name="customer_id_en" value="<?php echo $customer_id_en; ?>">
                    <ul class="WSY_search_q">
                        <li>模板编号：<input type="text" name="template_id" id="template_id" value="<?php if($condition['id']!='-1'){echo $condition['id'];}?>" class="form_input"></li>
                        <li>模板名称：<input type="text" name="name" id="name" value="<?php if($condition['name']!=""){echo $condition['name'];}?>" class="form_input"></li>
                        <li>创建时间：
                            <input class="form_input" type="text" id="createtime" name="createtime" value="<?php if($condition['createtime']!=-1){echo $condition['createtime'];}?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});" style="min-width:120px" />
                        </li>
                        <li>模板状态：
                            <select name="status" id="status">
                                <option value="-1">--请选择--</option>
                                <option value="1" <?php if($condition['is_shelve']==1){?>selected<?php }?>>上架</option>
                                <option value="0" <?php if($condition['is_shelve']==0){?>selected<?php }?>>下架</option>
                            </select>
                        </li>
                        <li><input type="submit" class="WSY-skin-bg form-btn"  value="搜索" onclick="document.getElementById("search").submit();" ></li>
                        <li><a class="WSY-skin-bg form-btn form-add-btn" onclick="jump_url(0,2);">添加模板</a></li>
                    </ul>
                </form>

                <table width="97%" class="WSY_table" id="WSY_t1">
                    <thead class="WSY_table_header">
                    <th width="10%" nowrap="nowrap"align="center">模板编号</th>
                    <th width="20%" nowrap="nowrap"align="center">模板名称</th>
                    <th width="30%" nowrap="nowrap"align="center">创建时间</th>
                    <th width="10%" nowrap="nowrap"align="center">状态</th>
                    <th width="30%" nowrap="nowrap"align="center">操作</th>
                    </thead>
                    <tbody class="tbody-main">
                    <?php foreach ($data as $key => $row) {
                        $status = $row['is_shelve'] ;
                        switch($status){
                            case "0":
                                $status_str = "下架";
                                break;
                            case "1":
                                $status_str = "上架";
                                break;
                            default:
                                $status_str = "下架";
                        }
                        ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $row['id']?></td>
                            <td style="text-align:center;"><?php echo $row['name']?></td>
                            <td style="text-align:center;"><?php echo date('Y-m-d H:i',strtotime($row['createtime']))?></td>
                            <td style="text-align:center;"><?php echo $status_str;?></td>
                            <td style="text-align:center;">
                                <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['id']?>',1);">编辑名称</button>
                                <button class="table-btn WSY-skin-bg" onclick="jump_url('<?php echo $row['id']?>',3)">设置图标</button>
                                <?php if($status==1){?>
                                <button class="table-btn WSY-skin-bg" onclick="operate('<?php echo $row['id']?>',2)">下架</button>
                                <?php }?>
                                <?php if($status==0){?>
                                    <button class="table-btn WSY-skin-bg" onclick="operate('<?php echo $row['id']?>',1);">上架</button>
                                <?php }?>
                                <?php if($status==0){?>
                                    <button class="table-btn WSY-skin-bg" onclick="operate('<?php echo $row['id']?>',3);">删除</button>
                                <?php }?>
                                <button class="table-btn WSY-skin-bg" onclick="operate('<?php echo $row['id']?>',4);">查看已关联页面</button>
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
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script><!--添加时间插件-->
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
    var template_id = $("#template_id").val();
    var template_name = $("#name").val();
    var createtime = $("#createtime").val();
    var status = $("#status").val();
    var param = "";
    if(template_id!=""){
        param += "&template_id="+parseInt(activity_id);
    }
    if(template_name!=""){
        param += "&name="+template_name;
    }
    if(createtime!=""){
        param += "&createtime="+createtime;
    }
    if(status == 0 || status == 1){
        param += "&status="+status;
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
            var url="/mshop/admin/index.php?m=bottom_label&a=template_list&pagenum="+p+param;
            location.href = url;
        }
    });

    function jumppage(){
        var a=parseInt($("#WSY_jump_page").val());
        if((a<1) || (a>count) || isNaN(a)){
            layer.alert('没有下一页了');
            return false;
        }else{
            var url="/mshop/admin/index.php?m=bottom_label&a=template_list&pagenum="+a+param;
            location.href = url;
        }
    }
    <!-- 分页 end -->

    function operate(template_id,type){
        //type：1模板上架 2模板下架 3模板删除 4查看已关联页面
        var url = "";
        var remark = "";
        if(type==1){
            url = '/mshop/admin/index.php?m=bottom_label&a=template_release';
            remark = "模板上架需关联发布页面，确认后即可关联。";
        }else if(type==2){
            url = '/mshop/admin/index.php?m=bottom_label&a=template_off_shelve';
            remark = "模板下架后底部标签无法显示，确认吗？";
        }else if(type==3){
            url = '/mshop/admin/index.php?m=bottom_label&a=template_del';
            remark = "模板删除后不可恢复，确定吗？";
        }else if(type==4){
            url = '/mshop/admin/index.php?m=bottom_label&a=template_release&do=show';
//            remark = "模板上架后底部标签会在已关联页面显示，确认吗？";
        }
        if(type == 4){
            window.location.href=url+'&tid='+template_id;
        }else{
            layer.confirm(remark, {
                title:'提示',
                btn: ['确认','取消']
            }, function(confirm){
                layer.close(confirm);
                if(type == 1) {
                    window.location.href=url+'&tid='+template_id;
                }else{
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        type: 'post',
                        data: {
                            tid:template_id,
                            type:type
                        },
                        success: function(res){
                            console.log(res);
                            if( res.errcode == 0 ){
                                layer.alert(res.errmsg,function(){
                                    document.location.reload();
                                });
                            }else{
                                alert(res.errmsg);
                            }
                        }
                    });
                }
            }, function(){
                layer.msg('已取消', {
                    time: 4000,
                    btn: ['确认'],
                    icon:1
                });
            });
        }
    }

    function jump_url(template_id,type){
        //type : 1-查看，编辑名称 2-添加  3-设置图标
        var url = "";
        if(type==1){
            url = "/mshop/admin/index.php?m=bottom_label&a=template_edit&id="+template_id;
        }else if(type==2){
            url = "/mshop/admin/index.php?m=bottom_label&a=template_add";
        }else if(type==3){
            url = "/mshop/admin/index.php?m=bottom_label&a=icon_list&id="+template_id;
        }
        location.href = url;
    }
</script>
</body>
</html>