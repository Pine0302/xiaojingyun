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
        <!--产品管理代码开始-->
        <div class="WSY_data">
            <div class="WSY_agentsbox">
                <table width="97%" class="WSY_table" id="WSY_t1">
                    <thead class="WSY_table_header">
                    <th width="10%" nowrap="nowrap"align="center">模板编号</th>
                    <th width="20%" nowrap="nowrap"align="center">模板名称</th>
                    <th width="30%" nowrap="nowrap"align="center">创建时间</th>
                    <th width="30%" nowrap="nowrap"align="center">操作</th>
                    </thead>
                    <tbody class="tbody-main">
                    <?php foreach ($data as $key => $row) {
                        ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $row['id']?></td>
                            <td style="text-align:center;"><?php echo $row['name']?></td>
                            <td style="text-align:center;"><?php echo date('Y-m-d H:i',strtotime($row['createtime']))?></td>
                            <td style="text-align:center;">
                                <button class="table-btn WSY-skin-bg" onclick="select('<?php echo $row['id']?>');">选择</button>
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

    <!-- 分页 start -->
    var pagenum = <?php echo $pageNum ?>;//当前页
    var count =<?php echo $pageCount ?>;//总页数
    //pageCount：总页数
    //current：当前页
    $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
            var url="/mshop/admin/index.php?m=bottom_label&a=bottom_selector_list&pagenum="+p;
            location.href = url;
        }
    });

    function jumppage(){
        var a=parseInt($("#WSY_jump_page").val());
        if((a<1) || (a>count) || isNaN(a)){
            layer.alert('没有下一页了');
            return false;
        }else{
            var url="/mshop/admin/index.php?m=bottom_label&a=bottom_selector_list&pagenum="+a;
            location.href = url;
        }
    }
    <!-- 分页 end -->

    function select(id){
        var index = parent.layer.getFrameIndex(window.name);
        parent.showBottomSelectorCallback(id);
        parent.layer.close(index);
    }
</script>
</body>
</html>