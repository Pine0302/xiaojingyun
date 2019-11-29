<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>关联产品</title>
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
    <script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
    <style type="text/css">
        .form-btn{width:auto!important;padding:0 10px!important;cursor:pointer;color:#fff!important;border:0!important;}
        .form-add-btn{display:inline-block;line-height:30px;border-radius:3px;margin:18px 0 0 18px;min-width:80px;font-size:14px;text-align:center;}
        .table-btn{color:#fff;border:0;cursor:pointer;border-radius:3px;height:24px;padding:0 10px;font-size:12px;}
        .tbody-main td{text-align:center!important;}
        .tbody-img{max-width:100%;max-height:80px;}
        .tbody-main .ipt{border-radius:2px;border:solid 1px #ddd;box-sizing:border-box;padding:0 10px;height:28px;line-height:28px;text-align:center;}
        .at-btn-content{margin:20px 0;text-align:center;}
        .at-btn-content .hold-btn{float:none;}
        .fixed_img{width: 60%;height: 60px;margin-top: 2px;}
        .ellipsis{text-overflow:ellipsis;overflow:hidden;white-space:nowrap;}
    </style>
</head>
<body>
    <!--内容框架开始-->
    <div class="WSY_content" id="WSY_content_height">
        <!--列表内容大框开始-->
        <div class="WSY_columnbox"> 
            <div class="WSY_column_header">
                <div class="WSY_columnnav">
                    <a class="white1">关联产品</a>
                </div>
            </div>
            <!--产品管理代码开始-->
            <div class="WSY_data">
                <div class="WSY_agentsbox">
                    <?php if($arr['status'] != 4) {?>
                    <a href="index.php?m=queue&a=queue_product&activity_id=<?php echo $param['id'];?>" class="WSY-skin-bg form-btn form-add-btn">添加产品</a>
                    <?php }?>
                    <table width="97%" class="WSY_table" id="WSY_t1">
                        <thead class="WSY_table_header">
                            <th width="10%" nowrap="nowrap"align="center">产品图片</th>
                            <th width="8%" nowrap="nowrap"align="center">产品编码</th>
                            <th width="10%" nowrap="nowrap"align="center">产品名称</th>
                            <th width="10%" nowrap="nowrap"align="center">产品分类</th>
                            <th width="8%" nowrap="nowrap"align="center">产品原价</th>
                            <th width="8%" nowrap="nowrap"align="center">产品现价</th>
                            <th width="10%" nowrap="nowrap"align="center">库存</th>
                            <th width="10%" nowrap="nowrap"align="center">购物币抵扣</th>
                            <?php if($arr['status'] != 4) {?>
                            <th width="10%" nowrap="nowrap"align="center">操作</th>
                            <?php }?>
                        </thead>
                        <tbody class="tbody-main">
                            <?php foreach ($data as $key => $row) { ?>
                            <tr id="id_<?php echo $row['id'];?>">
                                <td><img src="<?php echo $row['default_imgurl'];?>" class="tbody-img fixed_img"></td>
                                <td><?php echo $row['pid'];?></td>
                                <td class="str_name ellipsis"><?php echo $row['name'];?></td>
                                <td class="str_type ellipsis"><?php echo $row['typename'];?></td>
                                <td>￥<?php echo $row['orgin_price'];?></td>
                                <td>￥<?php echo $row['now_price'];?></td>
                                <td><?php echo $row['storenum'];?></td>
                                <td><?php echo $row['currency'];?>%</td>
                                <?php if($arr['status'] != 4) {?>
                                <td>
                                    <button onclick="delQueue('<?php echo $row['id'];?>')" class="table-btn WSY-skin-bg">删除</button>
                                </td>
                                <?php }?>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="at-btn-content">
                        <button onclick="offgo();" class="WSY_button hold-btn">返回</button>
                    </div>
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
$("p").mouseleave(function(){
  $("p").css("background-color","#E9E9E4");
});

$(".str_name").each(function(i,n){
    var maxwidth=20;
    if($(n).html().length>maxwidth){
        $(n).html($(n).html().substring(0,maxwidth)+'…');
    }
});

$(".str_type").each(function(i,n){
    var maxwidth=5;
    if($(n).html().length>maxwidth){
        $(n).html($(n).html().substring(0,maxwidth)+'…');
    }
});

<!-- 分页 start -->
var customer_id = "<?php echo $this->customer_id;?>";
var activity_id = "<?php echo $id;?>";
var pagenum = <?php echo $pageNum ?>;//当前页
var count =<?php echo $pageCount ?>;//总页数   
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
    pageCount:count,
    current:pagenum,
    backFn:function(p){
    var url="/mshop/admin/index.php?m=queue&a=queue_shop&id="+activity_id+"&pagenum="+p;    
    location.href = url;
   }
});

function offgo() {
    window.location = "/mshop/admin/index?m=queue&a=queue_activity";
}

function jumppage(){
    var a=parseInt($("#WSY_jump_page").val());
    if((a<1) || (a>count) || isNaN(a)){
        layer.alert('没有下一页了');
        return false;
    }else{
        var url="/mshop/admin/index.php?m=queue&a=queue_shop&id="+activity_id+"&pagenum="+a;    
        location.href = url;
    }
}
<!-- 分页 end -->

function checkint(obj){
    obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
    obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}

function exint1(obj){
    if (obj.value != '') {
        if (obj.value < 0) {
            obj.value = 0;
        }

        if (!isNaN(obj.value) && $.trim(obj.value) != "") {
            obj.value = parseFloat(obj.value);
        }
    }
}

function exint2(obj){
    if (obj.value != '') {
        if (obj.value < -1) {
            obj.value = -1;
        }

        if (!isNaN(obj.value) && $.trim(obj.value) != "") {
            obj.value = parseFloat(obj.value);
        }
    }
}

//删除
function delQueue(i) {
    if(confirm("你确定要删除吗？"))   
    {  
        var activity_id  = "<?php echo $param['id'];?>";
        var pid          = i;
        $.ajax({
            url: '/mshop/admin/index.php?m=queue&a=product_del',
            dataType: 'json',
            type: 'post',
            data: {
                activity_id:activity_id,
                pid:pid
            },
            success: function(res){
                if( res.errcode == '1' ){
                    alert(res.errmsg);
                    window.location = "/mshop/admin/index?m=queue&a=queue_shop&id="+activity_id+"&pagenum=1";
                }else{
                    alert(res.errmsg);
                }
            }
        });
    }  
    
}

$(document).ready(function() { 
    var ceshi = '<?php echo $_GET['pagenum'];?>';

    if (ceshi == '') {
        localStorage.clear();
    }

    var id = localStorage.getItem("add");
    console.log(id);
    if (id) {
        var arr = id.split(',');
        $.each(arr,function(n,value){
            // console.log($("#id_"+value));
            $("#id_"+value).find('input').removeAttr("disabled");
        });
    }
});
</script>   
</html>