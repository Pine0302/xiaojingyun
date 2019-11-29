<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/common/common_ext.php'); 
require('../../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

$keyid =  -1;
$name = "";
$icon_url = "";
$icon_url_selected = "";
$page_url = "";
$column_id = -1;

$keyid  = i2get("keyid",-1);
$pagenum = i2get("pagenum",1);
	
if($keyid > 0){  
	$query="select id,name,icon_url,icon_url_selected,page_url,column_id,`sort`,display,selector_id from bottom_label_setting_t where isvalid=true and customer_id=".$customer_id." and id=".$keyid." limit 1";
	
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	
	 while ($row = mysql_fetch_object($result)) {
	   $keyid =  $row->id ;
	   $name = $row->name;
	   $icon_url = $row->icon_url;
	   $icon_url_selected = $row->icon_url_selected;
	   $page_url = $row->page_url;
	   $column_id = $row->column_id;
	   $selector_id = $row->selector_id;
	 }
    $selector_name = substr($selector_id,(strripos($selector_id,'-')+1));
}
?>
<!doctype html>
<html>	
<head>
<meta charset="utf-8">
<title>底部标签设置</title>
</head>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../../js/tis.js"></script>
<script type="text/javascript" src="../../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../../common/js/layer/layer.js"></script>
<script type="text/javascript" src="../../../../common/js/jscolor.js" ></script>
<script> 
 $(function(){
	 var old_column_id = parseInt("<?php echo $column_id;?>");
	 
	 if(old_column_id > 0){
		$("#page_url").attr("disabled",true);  
		$("#column_id").find("option[data-column='data_"+old_column_id+"']").attr("selected",true);
	 }
	 
	 $("#column_id").change(function(){
	    var column_id = $(this).children('option:selected').val();
		
		if(column_id > 0){  //已选择栏目，URL不可用
			$("#page_url").val("");
			$("#page_url").attr("disabled",true);
		}else{
			$("#page_url").val("<?php echo $page_url;?>");
			$("#page_url").attr("disabled",false);
		}
	 });
 });
 
 function submitV(){
//    var column_id = document.getElementById("column_id").value;
     var selector_id = document.getElementById("selector_id").value;
     var selector_name = document.getElementById("selector_title").value;
	var page_url  = document.getElementById("page_url").value;
	var name_str  = document.getElementById("name").value;
	
    var len = 0; //字符数量
    for (var i=0; i<name_str.length; i++) {  
        if (name_str.charCodeAt(i)>127 || name_str.charCodeAt(i)==94) {  
           len += 2;  
        } else {  
           len ++;  
        }  
    }
    if (len>8){
        alert("名称限制8个字符以内");
        return;
    }
	
//	if((column_id==-1 || column_id==-2) && (page_url=="" || page_url==null)){
//		alert("URL不能为空");
//		return;
//	}

     if((selector_id=='' || selector_name=='') && (page_url=="" || page_url==null)){
		alert("URL不能为空");
		return;
	}
	
	var upfile1 =  $("#upfile1").val();
	var icon_url = $("#icon_url").val();
	
	if($("#upfile1").val()=="" && $("#icon_url").val()==""){
		alert("请先上传选中前图标");
		return;
	}
	
	if($("#upfile2").val()=="" && $("#icon_url_selected").val()==""){
		alert("请先上传选中后图标");
		return;
	}

    document.getElementById("upform").submit();
 }
</script>
<body>
<!--内容框架开始-->
<div class="WSY_content">  
<div class="div_new_content">
<form action="save_label.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data" id="upform" name="upform">
		<input type="hidden" name="keyid" id="keyid" value="<?php echo $keyid; ?>" />
		<input type="hidden" name="pagenum" value="<?php echo $pagenum; ?>" />
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1"><?php if($keyid >0){?>编辑<?php }else{?>添加<?php }?>底部标签</a>
				</div>
			</div>
			<!--列表头部切换结束-->
			<div class="WSY_data">
				<dl class="WSY_member">
					<dt>名称</dt>
					<dd>	
					<span class="input">
			           <input type="text" placeholder="限制8个字符内" style="width: 200px;" name="name" id="name" value="<?php echo $name?>" />
					</span>
					</dd>
				</dl>
				<dl class="WSY_member">
					<dt>URL</dt>
					<dd>	
					<span class="input">
			           <input type="text" style="width: 200px;" name="page_url" id="page_url" value="<?php echo $page_url;?>" />
					</span>
					</dd>
				</dl>
				<dl class="WSY_member">
					<dt>栏目</dt>
<!--					<dd>	-->
<!--						<span class="input">-->
<!--						     --><?php //
//                                $column_type = 2;
//                                include("../../../../../weixinpl/back_newshops/Base/personalization/jurisdiction_check.php");
//                            ?><!--							-->
<!--						</span>-->
<!--					</dd>-->
                    <dd>
                        <input type='text' disabled name="selector_title" id="selector_title" value="<?php echo $selector_name; ?>" />
                    </dd>
                    <dd>
                        <button type="button" class="link-choose" onclick="showSelector(this)">请选择</button>
                        <input type=hidden name="selector_id" id="selector_id" value="<?php echo $selector_id?$selector_id:-1; ?>" />
                    </dd>
				</dl>
				<dl class="WSY_member">
					<dt style="visibility:hidden;">占位符</dt>
					<dd>	
					<span class="input" style="color:red;">
			            URL与栏目都设置，默认跳转栏目页面链接
					</span>
					</dd>
				</dl>
				<!--选中前图标-->
				<dl class="WSY_member">
                    <dt>选中前图标</dt>
                    <div class="WSY_memberimg">
						<?php if($icon_url!=""){?>
                        <img src="<?php echo $icon_url; ?>" style="width:80px;height:80px;">
						<?php }else{ ?>	
						<img src=	"../../../Common/images/Base/personal_center/gift.png" style="width:126px;height:120px;">
						<?php } ?>
                        <span>(建议上传高度限制98px内，jpg/png等）</span>
                        <!--上传文件代码开始-->
                        <div class="uploader white">
                            <input type="text" class="filename" readonly />
                            <input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="upfile1" id="upfile1" type=file value="<?php echo $icon_url ?>">
							<input type=hidden value="<?php echo $icon_url ?>" name="icon_url" id="icon_url" /> 
                        </div>
                        <!--上传文件代码结束-->
                    </div>
                </dl>
				<!--选中后图标-->	
                <dl class="WSY_member">
                    <dt>选中后图标</dt>
                    <div class="WSY_memberimg">
						<?php if($icon_url_selected!=""){?>
                        <img src="<?php echo $icon_url_selected; ?>" style="width:80px;height:80px;">
						<?php }else{ ?>	
						<img src=	"../../../Common/images/Base/personal_center/gift.png" style="width:126px;height:120px;">
						<?php } ?>
                        <span>(建议上传高度限制98px内，jpg/png等）</span>
                        <!--上传文件代码开始-->
                        <div class="uploader white">
                            <input type="text" class="filename" readonly />
                            <input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="upfile2" id="upfile2" type=file value="<?php echo $icon_url_selected ?>">
							<input type=hidden value="<?php echo $icon_url_selected ?>" name="icon_url_selected" id="icon_url_selected" /> 
                        </div>
                        <!--上传文件代码结束-->
                    </div>
                </dl>
				<div class="WSY_text_input01">
				  <div class="WSY_text_input"><input type="button" class="WSY_button"  value="保存" onclick="submitV();" /></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:location.href='./index.php?customer_id=<?php echo $customer_id_en; ?>&pagenum=<?php echo $pagenum;?>';"/></div>
				</div>
                <input type="hidden" name="navigation_id" value="<?php echo $navigation_id ?>" />
                <input type="hidden" name="pagenum" value="<?php echo $pagenum ?>" />
			</div>
		</div>
	</form>	
	<div style="width:100%;height:20px;"></div>
</div>
</div>
<!--内容框架结束-->
<script type="text/javascript" src="../../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<!-- 新选择链接 -->
<script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
<script>
    var that;//标签选择
    var customer_id_en = '<?php echo $customer_id_en; ?>';
    //选择优惠劵
    function showSelector(obj){
        that = obj;
        var selector_id = $(obj).parent().find('#selector_id').val();
        layer.open({
            type: 2,
            area: ['1500px', '720px'],
            fixed: false, //不固定
            maxmin: true,
            resize:true,
            title: '选择链接页面',
            content: '/mshop/admin/index.php?m=plug_link_selector&a=selector_list&customer_id='+customer_id_en+'&selector_id='+selector_id,
        });
    }
    //选择链接回调函数
    //[int] selector_id 链接组成ID [string] selector_title 链接名称
    function showSelectorCallback(selector_id,selector_title){
        console.log(selector_id);
        console.log(selector_title);
        $(that).parents().find("#selector_title").attr("disabled",false);
        $(that).parents().find("#selector_title").val(selector_title);
        $(that).parents().find("#selector_id").val(selector_id);
    }

</script>
<?php

mysql_close($link);
?>
</body>
</html>
