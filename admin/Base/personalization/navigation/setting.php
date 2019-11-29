<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');
require_once('../../../../../weixinpl/common/common_ext.php');
require_once('../../../../../weixinpl/common/utility_setting_function.php');
_mysql_query("SET NAMES UTF8");
$head=8;        

$keyid    = i2get("keyid",-1);
$op       = i2get("op","");
$deal_arr = i2get("deal_arr","");
$pagenum  = i2get("pagenum",1);

$start = ($pagenum-1) * 20;
$end = 20;

//检查是否已经发布过自定义导航
$navigation_sql1 = "select count(1) as is_set_nav from navigation_using where customer_id=".$customer_id;
$is_set_nav = 0;
$navigation_result = _mysql_query($navigation_sql1) or die('navigation_sql1 failed: ' . mysql_error());
while ($row = mysql_fetch_object($navigation_result)) {
   $is_set_nav   = $row->is_set_nav;  
}

if (!$is_set_nav){
    initialization_navigation($customer_id);
}

if(!empty($op)){
    if($op == "del"){
        $query = 'update navigation_setting_t set isvalid=false where id='.(int)$keyid;
        _mysql_query($query) or die('query failed: ' . mysql_error());
    }elseif($op == "show"){
        $query = 'update navigation_setting_t set display=true where id='.(int)$keyid;
        _mysql_query($query) or die('query failed: ' . mysql_error());
    }elseif($op == "hide"){
        $query = 'update navigation_setting_t set display=false where id='.(int)$keyid;
        _mysql_query($query) or die('query failed: ' . mysql_error());
    }
}

/*批量操作*/
if(!empty($deal_arr)){
    
    if($op == "del_all"){         //批量删除
        $sql1 = "update navigation_setting_t set isvalid=0 where isvalid=true and id in (".$deal_arr.") and customer_id=".$customer_id;
        _mysql_query($sql1) or die('sql1 failed: ' . mysql_error());  

    }elseif($op == "hidden_all"){ //批量隐藏
        $sql1 = "update navigation_setting_t set display=0 where isvalid=true and id in (".$deal_arr.") and customer_id=".$customer_id;
        _mysql_query($sql1) or die('sql1 failed: ' . mysql_error());  

    }elseif($op == "show_all"){  //批量显示
        $sql1 = "update navigation_setting_t set display=1 where isvalid=true and id in (".$deal_arr.") and customer_id=".$customer_id;
        _mysql_query($sql1) or die('sql1 failed: ' . mysql_error()); 

    } 
}

$query = "select id,name,icon_url,sort,display from navigation_setting_t where isvalid=true and customer_id=".$customer_id." order by sort desc limit ".$start.",".$end;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$page_num = mysql_num_rows($result);

$query_num = "select count(1) as rcount from navigation_setting_t where isvalid=true and customer_id=".$customer_id; 
$result_num = _mysql_query($query_num) or die('Query failed_num: ' . mysql_error());
while ($row = mysql_fetch_object($result_num)) {
    $rcount_num =$row->rcount;
}
$page=ceil($rcount_num/$end);

$query2 = "select id from navigation_setting_t where isvalid=true and display=true and customer_id=".$customer_id;
$result2 = _mysql_query($query2) or die('Query2 failed: ' . mysql_error());
$show_num = mysql_num_rows($result2);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>导航设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<style>
.caozuo a img{
    width: 18px;
    height: 18px;
    vertical-align: baseline;
    display: inline-block;
    float: none;    
}
.caozuo{
    height: 80px;
    padding-top: 20px !important;
    padding-bottom: 20px !important;
    
}
.caozuo a{
    display: inline-block;
    margin-right: 10px;
}
#WSY_t1 tr td{
	text-align:center;
}
</style>
</head>
<body>

    <div class="WSY_content">

        <div class="WSY_columnbox">

        <?php
            include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/navigation/head.php");
        ?>      
            <div class="WSY_data">

                <div class="WSY_list" id="WSY_list" >
                    <div class="WSY_left" >
                        <a><font style="color:red">提示：导航最多15个</font></a>
                    </div>
                    
                    <ul class="WSY_righticon">
                        <li><a href="javascript:void(0);" onclick = "deal_all('del_all')">批量删除</a></li>
                        <li><a href="javascript:void(0);" onclick = "deal_all('hidden_all')">批量隐藏</a></li>
                        <li><a href="javascript:void(0);" onclick = "deal_all('show_all')">批量显示</a></li>
<!--                        <li><a href="javascript:showLabel('./navigation_release.php?customer_id=--><?php //echo $customer_id_en; ?><!--')">发布</a></li>-->
                        <li><a href="new_navigation_release_index.php?customer_id=<?php echo $customer_id_en; ?>">发布</a></li>
                        <li><a href="navigation_edit.php?customer_id=<?php echo $customer_id_en; ?>">添加</a></li>
                        <li><a href="../home_decoration/defaultset.php?customer_id=<?php echo $customer_id_en; ?>">返回</a></li>
                    </ul>
                    <br class="WSY_clearfloat">

                    <table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
                        <thead class="WSY_table_header">
                            <th width="3%"><input id="s" onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()" type="checkbox"></th>
                            <th width="5%">序号</th>
                            <th width="10%">名称</th>
                            <th width="10%">图标</th>
                            <th width="8%">修改排序</th>
                            <th width="8%">操作</th>
                        </thead>
<?php
            
    if(!empty($result)){
        while ($row = mysql_fetch_object($result)) {
            $keyid =  $row->id ;
            $name = $row->name;
            $icon_url = $row->icon_url;
            $sort = $row->sort;
            $display = $row->display;
     
?>
        
                        <tr data-id="<?php echo $keyid; ?>" data-sort="<?php echo $sort; ?>">
                            <td ><input type="checkbox" name="tid" id="tid" value="<?php echo $keyid; ?>" data-display="<?php echo $display; ?>"></td>
                            <td align="center"><?php echo $keyid; ?></td>
                            <td ><?php echo $name; ?></td>
                            <td ><img src="<?php echo $icon_url ?>" /></td>
                            <td class="caozuo">
                                <a href="javascript:void(0)" onClick="up(this)" style="cursor:pointer;" class="WSY_operation" title="前移"><img src="../../../../common/images_V6.0/operating_icon/icon32.png"></a>
                                <a href="javascript:void(0)" onClick="down(this)" style="cursor:pointer;" class="WSY_operation" title="后移"><img src="../../../../common/images_V6.0/operating_icon/icon33.png"></a></td>
                            <td class="caozuo">
                                <a href="navigation_edit.php?navigation_id=<?php echo $keyid ?>&customer_id=<?php echo $customer_id_en; ?>&pagenum=<?php echo $pagenum;?>" style="cursor:pointer;" class="WSY_operation" title="编辑"><img src="../../../../common/images_V6.0/operating_icon/icon05.png"></a>
                                <?php if ($display){ ?>
                                    <a href="javascript:void(0)" onClick="hide(<?php echo $keyid ?>)" style="cursor:pointer;" class="WSY_operation" title="隐藏"><img src="../../../../common/images_V6.0/operating_icon/icon25.png"></a>
                                <?php }else{ ?>
                                    <a href="javascript:void(0)" onClick="show(<?php echo $keyid ?>)" style="cursor:pointer;" class="WSY_operation" title="显示"><img src="../../../../common/images_V6.0/operating_icon/icon1.png"></a>
                                <?php } ?>
                                <a href="javascript: G.ui.tips.confirm('您确定删除吗？','setting.php?keyid=<?php echo $keyid ?>&op=del&customer_id=<?php echo $customer_id_en; ?>');" title="删除"><img src="../../../../common/images_V6.0/operating_icon/icon04.png"></a>     
                            </td>
                        </tr>
<?php

        }
    }
    mysql_close($link);
?>
                    </table>
                    <!--表格结束-->
                    <!--翻页开始-->
                    <div class="WSY_page">
                        
                    </div>
                    <!--翻页结束-->
                </div>
            </div>
        </div>
        <div style="width:100%;height:20px;"></div>
    </div>

<script src="../../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="../../../../common/js/layer/layer.js"></script>
<script>
var pagenum = <?php echo $pagenum ?>;
var count =<?php echo $page ?>;//总页数
//pageCount：总页数
//current：当前页
$(".WSY_page").createPage({
    pageCount:count,
    current:pagenum,
    backFn:function(p){
        document.location = "setting.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+p;
    }
});

var page = <?php echo $page ?>;
function jumppage(){
    var a=parseInt($("#WSY_jump_page").val());
    if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
        return false;
    }else{
        document.location = "setting.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+a;
    }
}
</script>
<script>
var num = '<?php echo $rcount_num;?>';  //已有导航数目 
var show_num = parseInt('<?php echo $show_num; ?>');  //已有显示导航数目
var page_num = parseInt('<?php echo $page_num; ?>');  //当前页面导航数目

// ---------全选效果
    function checkAll() {
        var code_Values = document.all['tid'];
        if (code_Values.length) {
            for (var i = 0; i < code_Values.length; i++) {
                code_Values[i].checked = true;
            }
        } else {
            code_Values.checked = true;
        }
        
        var code_Values2 = document.all['tid'];
        if (code_Values2.length) {
            for (var i = 0; i < code_Values2.length; i++) {
                code_Values2[i].checked = true;
            }
        } else {
            code_Values2.checked = true;
        }
        
    }
    function uncheckAll() {
        var code_Values = document.all['tid'];
        if (code_Values.length) {
            for (var i = 0; i < code_Values.length; i++) {
                code_Values[i].checked = false;
            }
        } else {
            code_Values.checked = false;
        }
        var code_Values2 = document.all['tid'];
        if (code_Values2.length) {
            for (var i = 0; i < code_Values2.length; i++) {
                code_Values2[i].checked = false;
            }
        } else {
            code_Values2.checked = false;
        }
    }
// ---------全选效果End

//前移
function up(obj) {
    var objParentTR = $(obj).parent().parent();
    var obj_id = $(objParentTR).data("id");
    var obj_sort = $(objParentTR).data("sort");

    $.ajax({
        url: 'navigation_ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {"op": "up","keyid": obj_id,"key_sort": obj_sort},
        success: function(res){
            console.log(res);
            if(res.errcode===0){    
                location.reload();
            }else{
                alert("操作失败:"+res.errmsg);
            }           
        },  
        error:function(){
            alert('网络出错，请刷新页面重试');
        }
    })
}

//后移
function down(obj) {
    var objParentTR = $(obj).parent().parent();
    var obj_id = $(objParentTR).data("id");
    var obj_sort = $(objParentTR).data("sort");

    $.ajax({
        url: 'navigation_ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {"op": "down","keyid": obj_id,"key_sort": obj_sort},
        success: function(res){
            console.log(res);
            if(res.errcode===0){    
                location.reload();
            }else{
                alert("操作失败:"+res.errmsg);
            }           
        },  
        error:function(){
            alert('网络出错，请刷新页面重试');
        }
    })
}

//隐藏
function hide(keyid) {
    if (show_num <= 1){
        G.ui.tips.confirm_t('全部导航图标隐藏后则不显示导航',"setting.php?keyid="+keyid+"&op=hide&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum);
    }else{
        document.location = "setting.php?keyid="+keyid+"&op=hide&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum;
    }
    
}

//显示
function show(keyid) {
    if (show_num >= 15){
        alert('显示的导航将要超过15个，操作失败');
        return;
    }
    document.location = "setting.php?keyid="+keyid+"&op=show&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum;
}

/*批量处理*/
function deal_all(op){    //del_all：批量删除; hidden_all：批量隐藏  show_all:批量显示
    if(parseInt(page_num) >0){
        if(parseInt(page_num) == 1){
            var code_Values = $('#tid');
        }else{
            var code_Values = document.all['tid'];
        }
    }
    var deal_arr = new Array();   //要批量处理的ID
    
    var is_selected = $('input[name="tid"]:checked').val();
    if(is_selected == "" || is_selected == null || is_selected == "undefined"){
        if(op == "del_all"){
            alert("请选择要批量删除的目标");
        }else if(op == "hidden_all"){
            alert("请选择要批量隐藏的目标");
        }else if(op == "show_all"){
            alert("请选择要批量显示的目标");
        }
        return;         
    }
    if (code_Values.length) {
        var check_show = 0; //勾选的选项中已经是显示状态的项目
        for (var i = 0; i < code_Values.length; i++) {
            if(code_Values[i].checked == true){
                var data_display = $(code_Values[i]).data("display");
                if (data_display){
                    check_show++;
                }
                deal_arr.push(code_Values[i].value);
            }           
        }
    }
    
    var msg = "";
    if(op == "del_all"){
        msg = "您确定要删除已选目标吗？";
        if(parseInt(num) == deal_arr.length){
            msg += "全部导航图标删除后则不显示导航!";
        }       
    }else if(op == "hidden_all"){
        msg = "您确定要隐藏已选目标吗？";
        if(parseInt(show_num) <= deal_arr.length){
            msg += "全部导航图标隐藏后则不显示导航!";
        } 
    }else if(op == "show_all"){
        msg = "您确定要显示已选目标吗？";
        if (show_num+deal_arr.length-check_show > 15){
            alert("显示的导航将要超过15个，操作失败");
            return;
        }
    }
	if(op == "del_all"){
	    G.ui.tips.confirm(msg,'./setting.php?customer_id=<?php echo $customer_id_en;?>&deal_arr='+deal_arr+"&op="+op+"&pagenum="+pagenum);
	}else{
		G.ui.tips.confirm_t(msg,'./setting.php?customer_id=<?php echo $customer_id_en;?>&deal_arr='+deal_arr+"&op="+op+"&pagenum="+pagenum);
	}
}

/*发布底部标签*/
function showLabel(url){
    i = $.layer({
        type : 2,
        shadeClose: true,
        offset : ['200px' , '500px'],
        time : 0,
        iframe : {
            src:url
        },
        title : "发布选择",
        //fix : true,
        zIndex : 2,
        border : [5 , 0.3 , '#437799', true],
        area : ['500px','400px'],
        closeBtn : [0,true],
        success : function(){ //层加载成功后进行的回调
            //layer.shift('right-bottom',1000); //浏览器右下角弹出s
        },
        end : function(){ //层彻底关闭后执行的回调

        }
    });
}
</script>   
</body>
</html>
