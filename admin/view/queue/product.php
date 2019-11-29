<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加关联产品</title>
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
    <script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
    <style type="text/css">
        .form-btn{width:auto!important;padding:0 10px!important;cursor:pointer;color:#fff!important;border:0!important;}
        .form-add-btn{display:inline-block;line-height:24px;border-radius:3px;}
        .table-btn{color:#fff;border:0;cursor:pointer;border-radius:3px;height:24px;padding:0 10px;font-size:12px;}
        .at-btn-content{margin:20px 0;text-align:center;}
        .at-btn-content .hold-btn{float:none;}
        .tbody-main td{text-align:center!important;}
        .tbody-img{max-width:100%;height: 60px;margin-top: 2px;}
        .selected-product{font-size:16px;margin:15px 0 15px 18px;line-height:28px;}
        .ellipsis{text-overflow:ellipsis;overflow:hidden;white-space:nowrap;}
        .tbody-name{max-width:240px;text-align:center;}
        #str_name{max-height:110px;overflow:hidden;}
        #str_name span{display:inline-block;margin:0 10px 0 0;background-color:#ddd;border-radius:10px;padding:0 10px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
        .hide-spot{display:none;}
    </style>
</head>
<body>
    <!--内容框架开始-->
    <div class="WSY_content" id="WSY_content_height">
        <!--列表内容大框开始-->
        <div class="WSY_columnbox"> 
            <div class="WSY_column_header">
                <div class="WSY_columnnav">
                    <a class="white1">添加关联产品</a>
                </div>
            </div>
            <!--产品管理代码开始-->
            <div class="WSY_data">
                <div class="WSY_agentsbox">
                    <form class="/mshop/admin/index.php?m=queue&a=queue_product&activity_id=<?php echo $param['activity_id'];?>&pagenum=<?php echo $pageNum;?>" style="display:block" method="post" action="" enctype="multipart/form-data">
                        <ul class="WSY_search_q">
                            <li>产品名称：<input type="text" name="product_name" value="<?php if($param['product_name']!=""){echo $param['product_name'];}?>" class="form_input"></li>
                            <li>产品编码：<input type="text" name="product_id" value="<?php if($param['product_id']!=-1){echo $param['product_id'];}?>" class="form_input" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" onchange="clearNoNum(this)" ></li>
                            <li>产品分类：
                                <select name="product_type" id="search_type_id">
                                    <option value="">全部</option>
                                    <?php echo $type;?>
                                </select>
                            </li>
                            <li><input type="submit" class="WSY-skin-bg form-btn" value="搜索" ></li>
                            <input type="hidden" name="pagenum" value="1">
                        </ul> 
                    </form>

                    <div class="selected-product">
                        <p>已选产品：</p>
                        <div id="str_name"></div>
                        <div class="hide-spot">······</div>
                    </div>

                    <table width="97%" class="WSY_table" id="WSY_t1">
                        <thead class="WSY_table_header">
                            <th width="5%" nowrap="nowrap"align="center"><input type="checkbox" id="choice"/></th>
                            <th width="5%" nowrap="nowrap"align="center">序号</th>
                            <th width="12%" nowrap="nowrap"align="center">产品图片</th>
                            <th width="10%" nowrap="nowrap"align="center">产品编码</th>
                            <th width="20%" nowrap="nowrap"align="center">产品名称</th>
                            <th width="12%" nowrap="nowrap"align="center">产品分类</th>
                            <th width="12%" nowrap="nowrap"align="center">产品原价</th>
                            <th width="12%" nowrap="nowrap"align="center">产品现价</th>
                            <th width="12%" nowrap="nowrap"align="center">产品库存</th>
                            <th width="12%" nowrap="nowrap"align="center">购物币抵扣</th>
                        </thead>
                        <tbody class="tbody-main">
                            <?php foreach ($data as $key => $row) { ?>
                            <tr>
                                <td><input type="checkbox" name="choice" value="<?php echo $row['id'];?>"/></td>
                                <td><?php echo $key+1+(($pageNum-1)*20);?></td>
                                <td><img src="<?php echo $row['default_imgurl'];?>" class="tbody-img"/></td>
                                <td><?php echo $row['id'];?></td>
                                <td><div class="tbody-name ellipsis"><?php echo $row['name'];?></div></td>
                                <td><div class="tbody-typename ellipsis"><?php echo $row['typename'];?></div></td>
                                <td>￥<?php echo $row['orgin_price'];?></td>
                                <td>￥<?php echo $row['now_price'];?></td>
                                <td><?php echo $row['storenum'];?></td>
                                <td><?php echo $row['currency'];?>%</td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="at-btn-content">
                        <button id="btn" class="WSY_button hold-btn">保存</button>
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
    </div>
    <!--内容框架结束-->
</body>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
    function offgo(){
        sessionStorage.clear();

        window.location = "/mshop/admin/index?m=queue&a=queue_shop&id="+activity_id+"&pagenum=1";
    }

    var _index = true;  //加锁
    //保存
    $('#btn').click(function(){
        if (_index = true) 
        {
            _index = false;
            setTimeout(function(){_index = true;},1500); //防止重复点击
            var activity_id  = "<?php echo $param['activity_id'];?>";
            var ids = 'id<?php echo $param['activity_id'];?>';
            $.ajax({
                url: '/mshop/admin/index.php?m=queue&a=product_add',
                dataType: 'json',
                type: 'post',
                data: {
                    idsStr:sessionStorage.getItem(ids),
                    activity_id:activity_id
                },
                success: function(res){
                    if( res.errcode == '1' ){
                        sessionStorage.clear();
                        localStorage.clear();
                        localStorage.add = res.add;
                        window.location = "/mshop/admin/index?m=queue&a=queue_shop&id="+activity_id+"&pagenum=1";
                    }else{
                        alert(res.errmsg);
                    }
                }
            });
        }
        
    });

    $('#choice').on('change',function(){
        var c = $(this).prop('checked');
        var ids = 'id<?php echo $param['activity_id'];?>';
        var names = 'name<?php echo $param['activity_id'];?>';
        $('input[name="choice"]').prop('checked',c);

        if ( !sessionStorage.getItem(ids)) {
            var str  = '';
            var name = '';
        } else {
            var str  = sessionStorage.getItem(ids);
            var name = sessionStorage.getItem(names);
        }

        $("input[name='choice']").each(function(i,n){
            var par  = $(n).parent().next().next().next().next().text();
            var id   = $(n).val();

            id_name = '<span>' + id + '：' + par + "；</span>";
            
            obj=$(n).val()+',';
            
            sessionStorage.setItem(ids,str.replace(obj,''));
            sessionStorage.setItem(names,name.replace(id_name,''));
            str=sessionStorage.getItem(ids);
            name=sessionStorage.getItem(names);
            
            if($(n).prop('checked') === true){
                str = str + n.value + ','; 
                name = name + '<span>' + id + '：' + par + "；</span>";
            }
        });
        sessionStorage.setItem(ids,str);
        sessionStorage.setItem(names,name);
        $('#str_name').html(sessionStorage.getItem(names));
        if (sessionStorage.getItem(names)) {
            judge();
        } else {
            $('.hide-spot').hide();
        }
    });

    $('input[name="choice"]').on('change',function(){
        var ids = 'id<?php echo $param['activity_id'];?>';
        var names = 'name<?php echo $param['activity_id'];?>';

        var par  = $(this).parent().next().next().next().next().text();
        var id   = $(this).val();

        if($(this).prop('checked') === false){
            var str =sessionStorage.getItem(ids);
            var name=sessionStorage.getItem(names);
            var obj=$(this).val()+',';
            var id_name = '<span>' + id + '：' + par + "；</span>";
            sessionStorage.setItem(ids,str.replace(obj,''));
            sessionStorage.setItem(names,name.replace(id_name,''));
            $('#str_name').html(localStorage[names]);
        } else{
            if (sessionStorage.getItem(ids)) {
                sessionStorage.setItem(ids,sessionStorage.getItem(ids) + $(this).val() + ',');
            } else {
                sessionStorage.setItem(ids,$(this).val() + ',');
            }

            if (sessionStorage.getItem(names)) {
                sessionStorage.setItem(names,sessionStorage.getItem(names) + '<span>' + id + '：' + par + "；</span>");
            } else {
                sessionStorage.setItem(names,'<span>' + id + '：' + par + "；</span>");
            }
        }

        $('#str_name').html(sessionStorage.getItem(names));
        if (sessionStorage.getItem(names)) {
            judge();
        } else {
            $('.hide-spot').hide();
        }
    });

    function judge(){
    	$('#str_name span').each(function(item,obj){
    		var re = (item+1)%6;
    		if(item >= 5 && re == 0){
    			$(obj).after('<br/>');
    		}
    		if(item >= 17){
    			$('.hide-spot').show();
    		}
    	});
    }

    <!-- 分页 start -->
    var activity_id  = "<?php echo $param['activity_id'];?>";
    var product_id   = "<?php echo $param['product_id'];?>";
    var product_name = "<?php echo $param['product_name'];?>";
    var product_type = "<?php echo $param['product_type'];?>";
    var pagenum = <?php echo $pageNum ?>;//当前页
    var count =<?php echo $pageCount ?>;//总页数   
    //pageCount：总页数
    //current：当前页
    $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
            var url="/mshop/admin/index.php?m=queue&a=queue_product&activity_id="+activity_id+"&pagenum="+p;    
            location.href = url;
       }
    });

    function jumppage(){
        var a=parseInt($("#WSY_jump_page").val());
        if((a<1) || (a>count) || isNaN(a)){
            layer.alert('没有下一页了');
            return false;
        }else{
            var url="/mshop/admin/index.php?m=queue&a=queue_product&activity_id="+activity_id+"&pagenum="+a;    
            location.href = url;
        }
    }
    <!-- 分页 end -->

    $(document).ready(function() { 
        var ids = 'id<?php echo $param['activity_id'];?>';
        var names = 'name<?php echo $param['activity_id'];?>';
        var ceshi = '<?php echo $_GET['pagenum'];?>';

        if (ceshi == '') {
            sessionStorage.clear();
        }

        if(sessionStorage.getItem(ids)) {
            var arr = new Array(); 
            arr = sessionStorage.getItem(ids).split(","); 
            $("input[name='choice']").each(function(i,n){
                if ($.inArray(n.value, arr) != -1) {
                    n.checked=true;
                }  
            });
            if ($("input[name='choice']:checked").length == $("input[name='choice']").length) {
                $('#choice').prop('checked',true);
            }
        }
        $('#str_name').html(sessionStorage.getItem(names));
        if (sessionStorage.getItem(names)) {
            judge();
        } else {
            $('.hide-spot').hide();
        }
    });

    /*只能输入数字*/
    function clearNoNum(obj){
        obj.value = obj.value.replace(/[^\d]/g,""); //清除"数字"以外的字符
        obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
    }
    
</script>   
</html>