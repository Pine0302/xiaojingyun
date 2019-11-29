<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/common/common_ext.php');

_mysql_query("SET NAMES UTF8");

$head=9;		

$keyid    = i2get("keyid",-1);
$op       = i2get("op","");
$deal_arr = i2get("deal_arr","");
$pagenum  = i2get("pagenum",1);
$start    = ($pagenum-1) * 8;
$end      = 8;

if(!empty($op)){
	if($op == "show"){             //显示底部标签
	    $sql = "update bottom_label_setting_t set display=1";
	}elseif($op == "hidden"){      //隐藏底部标签
		$sql = "update bottom_label_setting_t set display=0";
	}elseif($op == "del"){         //删除底部标签
		$sql = "update bottom_label_setting_t set isvalid=0";
	}
	
    if($keyid > 0){
	   $sql .= " where isvalid=true and id=".$keyid." and customer_id=".$customer_id;
	   _mysql_query($sql) or die('sql failed: ' . mysql_error());
	}    	
	
	/*批量操作*/
	if(!empty($deal_arr)){
		if($op == "del_all"){         //批量删除
			$sql1 = "update bottom_label_setting_t set isvalid=0 where isvalid=true and id in (".$deal_arr.") and customer_id=".$customer_id;            
		}elseif($op == "hidden_all"){ //批量隐藏
			$sql1 = "update bottom_label_setting_t set display=0 where isvalid=true and id in (".$deal_arr.") and customer_id=".$customer_id; 						
		}elseif($op == "show_all"){  //批量显示
			$sql1 = "update bottom_label_setting_t set display=1 where isvalid=true and id in (".$deal_arr.") and customer_id=".$customer_id; 
		}
		_mysql_query($sql1) or die('sql1 failed: ' . mysql_error()); 
	}	
}

$query="select id,name,icon_url,icon_url_selected,page_url,column_id,`sort`,display from bottom_label_setting_t where isvalid=true and customer_id=".$customer_id." order by `sort` desc limit ".$start.",".$end;

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$query_num = "select count(1) as rcount from bottom_label_setting_t where isvalid=true and customer_id=".$customer_id;
$result_num = _mysql_query($query_num) or die('Query failed_num: ' . mysql_error());
while ($row = mysql_fetch_object($result_num)) {
    $rcount_num =$row->rcount;
}
$page=ceil($rcount_num/$end);

$num = mysql_num_rows($result);   // 底部标签总数

$query2 = "select id from bottom_label_setting_t where isvalid=true and display=true and customer_id=".$customer_id;
$result2 = _mysql_query($query2) or die('Query2 failed: ' . mysql_error());
$show_num = mysql_num_rows($result2); // 显示的底部标签总数

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>底部标签设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<style>
#caozuo a img{
	width: 18px;
    height: 18px;
    vertical-align: baseline;
    display: inline-block;
    float: none;	
}
#caozuo{
	height: 80px;
	padding-top: 20px !important;
    padding-bottom: 20px !important;
	
}
#caozuo a{
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
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
		?>		
			<div class="WSY_data">

              <div class="WSY_list" id="WSY_list" >
                    <div class="WSY_left" >
                        <a><font style="color:red">提示：标签数量建议在2-5个，全部上传ICON图片总长限制720PX以内，高度限制98PX以内</font></a>
                    </div>
                    
                    <ul class="WSY_righticon">
						<li><a href="javascript:void(0);" onclick = "deal_all('del_all')">批量删除</a></li>
						<li><a href="javascript:void(0);" onclick = "deal_all('hidden_all')">批量隐藏</a></li>
						<li><a href="javascript:void(0);" onclick = "deal_all('show_all')">批量显示</a></li>
<!--                        <li><a href="javascript:showLabel('./label_release.php?customer_id=--><?php //echo $customer_id_en; ?><!--')">发布</a></li>-->
                        <li><a href="new_label_release_index.php?customer_id=<?php echo $customer_id_en; ?>">发布</a></li>
                        <li><a href="label_edit.php?customer_id=<?php echo $customer_id_en; ?>">添加</a></li>
                    </ul>
                    <br class="WSY_clearfloat">

			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			  <thead class="WSY_table_header">
				<th width="3%"><input id="s" onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()" type="checkbox">全选</th>
				<th width="5%">ID</th>
				<th width="10%">名称</th>
				<th width="10%">选中前ICON</th>
				<th width="10%">选中后ICON</th>
				<th width="3%">移位</th>			   
				<th width="8%">操作</th>
			  </thead>
<?php
			
  if(!empty($result)){
   while ($row = mysql_fetch_object($result)) {
	   $keyid =  $row->id ;
	   $name = $row->name;
	   $icon_url = $row->icon_url;
	   $icon_url_selected = $row->icon_url_selected;
	   $page_url = $row->page_url;
	   $column_id = $row->column_id;
	   $sort = $row->sort;
	   $display = (int)$row->display;	
?>	
          <tr data-id="<?php echo $keyid; ?>" data-sort="<?php echo $sort; ?>">
            <td><input type="checkbox" id="tid" name="code_Value" value="<?php echo $keyid; ?>" data-display="<?php echo $display; ?>"></td>
			<td align="center"><?php echo $keyid; ?></td>
            <td ><?php echo $name; ?></td>
            <td ><img src="<?php echo $icon_url ?>" style="width:80px;height:80px;" /></td>
			<td ><img src="<?php echo $icon_url_selected ?>" style="width:80px;height:80px;" /></td>
			<td class="caozuo">
				<a href="javascript:void(0)" onClick="up(this)" style="cursor:pointer;" class="WSY_operation" title="前移"><img src="../../../../common/images_V6.0/operating_icon/icon32.png" style="width:18px;height:18px;"></a>
				<a href="javascript:void(0)" onClick="down(this)" style="cursor:pointer;" class="WSY_operation" title="后移"><img src="../../../../common/images_V6.0/operating_icon/icon33.png" style="width:18px;height:18px;"></a>
			</td>         
            <td id="caozuo">
				<a href="label_edit.php?keyid=<?php echo $keyid ?>&customer_id=<?php echo $customer_id_en; ?>&pagenum=<?php echo $pagenum ?>" style="cursor:pointer;" class="WSY_operation" title="编辑"><img src="../../../../common/images_V6.0/operating_icon/icon05.png"></a>
			    <?php if($display == 1){?>			
				   <a href="javascript:void(0)" onClick="hide(<?php echo $keyid ?>)" style="cursor:pointer;" class="WSY_operation" title="隐藏"><img src="../../../../common/images_V6.0/operating_icon/icon25.png"></a>
				<?php }else{?>
				   <a href="javascript:void(0)" onClick="show(<?php echo $keyid ?>)" style="cursor:pointer;" class="WSY_operation" title="显示"><img src="../../../../common/images_V6.0/operating_icon/icon1.png"></a>
				<?php }?>
                   <a href="javascript:void(0)" onClick="del(<?php echo $keyid; ?>)" title="删除"><img src="../../../../common/images_V6.0/operating_icon/icon04.png"></a> 
				
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
        <!--<div style="width:100%;height:20px;padding-left:20px;padding-top:20px;color:#FF0000;">     提示：标签数量建议在2-5个，全部上传ICON图片总长限制720PX以内，高度限制98PX以内
		</div>-->
        </div>
		</div>
		</div>
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
        document.location = "./index.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+p;
    }
});

var page = <?php echo $page ?>;
function jumppage(){
    var a=parseInt($("#WSY_jump_page").val());
    if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
        return false;
    }else{
        document.location = "./index.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+a;
    }
}
</script>
<script>
	var num = parseInt('<?php echo $num;?>');              //已有底部标签数目
	var show_num = parseInt('<?php echo $show_num; ?>');  //已有显示导航数目
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
	}
	// ---------全选效果End
	
	//前移
	function up(obj) {
		var objParentTR = $(obj).parent().parent();
		var obj_id = $(objParentTR).data("id");
		var obj_sort = $(objParentTR).data("sort");

		$.ajax({
			url: 'label_ajax.php',
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
			url: 'label_ajax.php',
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
			G.ui.tips.confirm_t('全部标签隐藏后则不显示底部标签',"./index.php?keyid="+keyid+"&op=hidden&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum);
		}else{
			document.location = "./index.php?keyid="+keyid+"&op=hidden&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum;
		}
		
	}

	//显示
	function show(keyid) {
		if (show_num >= 5){
			alert('显示的标签将要超过5个，操作失败');
			return;
		}
		document.location = "./index.php?keyid="+keyid+"&op=show&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum;
	}
	//删除
	function del(keyid){
		if (num <= 1){
			G.ui.tips.confirm('您确定要删除吗？全部标签删除后则不显示底部标签',"./index.php?keyid="+keyid+"&op=del&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum);
		}else{
			G.ui.tips.confirm('您确定要删除吗？',"./index.php?keyid="+keyid+"&op=del&customer_id=<?php echo $customer_id_en; ?>&pagenum="+pagenum);
		}
	}
	/*批量处理*/
   function deal_all(op){    //del_all：批量删除; hidden_all：批量隐藏  show_all:批量显示
		if(num > 0){
			if(num == 1){
				var code_Values = $('#tid');
			}else{
				var code_Values = document.all['tid'];
			}
		}	

		var deal_arr = new Array();   //要批量处理的ID
		
		var is_selected = $('input[name="code_Value"]:checked').val();
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
			if(num == deal_arr.length){
				msg += "底部标签删除后则不显示!";
			}		
		}else if(op == "hidden_all"){
			msg = "您确定要隐藏已选目标吗？";
			 if(parseInt(show_num) <= deal_arr.length){
				msg += "全部标签隐藏后则不显示底部标签!";
			} 
		}else if(op == "show_all"){
			msg = "您确定要显示已选目标吗？";
			if (show_num+deal_arr.length-check_show > 5){
				alert("显示的底部标签将要超过5个，操作失败");
				return;
			}
		}
		
		if(op == "del_all"){
			G.ui.tips.confirm(msg,'./index.php?customer_id=<?php echo $customer_id_en;?>&deal_arr='+deal_arr+"&op="+op+"&pagenum="+pagenum);
		}else{
			G.ui.tips.confirm_t(msg,'./index.php?customer_id=<?php echo $customer_id_en;?>&deal_arr='+deal_arr+"&op="+op+"&pagenum="+pagenum);
		}

   }
</script>	
<script type="text/javascript" src="../../../../promotion/ZeroClipboard.js"></script>
</body>
</html>
