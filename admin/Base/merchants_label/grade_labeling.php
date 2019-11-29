<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');			
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]1
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");	
$head=0;
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $_GET["pagenum"];
}


$start = ($pagenum-1) * 20;
$end = 20;
if(!empty($_GET["type"])){
   $type = $_GET["type"];
}

$query="select id,label_level,level_name,label_image,label_image2,createtime from weixin_cityarea_supply_label where customer_id = ".$customer_id." and isvalid=true";
if(!empty($_GET["search_name"])){
   $search_name = $_GET["search_name"];
   $query_serach = $query_serach." and level_name like '%".$search_name."%'";
}
if(!empty($_GET["pay_begintime"])){
   $pay_begintime = $_GET["pay_begintime"];
   $query_serach = $query_serach." and UNIX_TIMESTAMP(createtime)>=".strtotime($pay_begintime)."";
}
if(!empty($_GET["pay_endtime"])){
   $pay_endtime = $_GET["pay_endtime"];
   $query_serach = $query_serach." and UNIX_TIMESTAMP(createtime)<=".strtotime($pay_endtime)."";
}
$query .=$query_serach." order by id desc limit ".$start." , ".$end."";
// echo $query;exit();

$query_num = 'select count(1) as rcount from weixin_cityarea_supply_label where isvalid=true and customer_id='.$customer_id; 
$query_num .=$query_serach;
$result_num = _mysql_query($query_num) or die('Query failed_num: ' . mysql_error());
while($row = mysql_fetch_object($result_num)) {
	$rcount_num =$row->rcount;
} 
 
$page=ceil($rcount_num/$end);


	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>入驻商家管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../css/inside.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jscolor.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script> 
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<style>
.aright {    
    margin-right:5px!important;;
}
.left{
	
    margin-top: 10px;
    padding-left: 20px;
    font-size: 14px;
    color: #2eade8;
    background-image: url(../../../common/images_V6.0/table_icon/icon01.png);
    background-repeat: no-repeat;
    background-position: left 0%;
    margin-left: 20px;
}
#caozuo a img{
	width: 18px;
    height: 18px;
	vertical-align: baseline;	
}
#caozuo{
	height: 80px;
	padding-top: 20px !important;
    padding-bottom: 20px !important;
	
}
.WSY_table a{
	color:#06a7e1;
	
}	
.WSY_list .WSY_righticon li a {
	cursor:pointer;
}
</style>
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">

       <!--列表内容大框开始-->
	<div class="WSY_columnbox">	
	<!--头部导航start-->
	<?php require($_SERVER['DOCUMENT_ROOT'].'/mshop/admin/Base/merchants_label/basic_head.php');?>	
	<!--头部导航end-->	
    <!--产品管理代码开始-->
    <div class="WSY_data">
    <div class="WSY_position1" >
    
    <div class="WSY_agentsbox WSY_list" id="search-enter" style="margin:20px 0 0 0 ">
    <ul class="WSY_righticon" style="float:none;">				
		<li class="WSY_position_date" style="margin:5px 0 10px 0">
			<p>等级名称：<input type="text" name="search_name" id="search_name" value="<?php echo $search_name;?>"/></p>
		</li>
		<li class="WSY_position_date tate001" style="margin-top:5px;">
			<p>创建时间：					
				<span class="time">
					<input class="date_picker time" type="text"name="AccTime_S" id="pay_begintime" value="<?php echo $pay_begintime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'pay_endtime\')}'});">
					<span class="om-calendar-trigger"></span>
				</span>
				-
				<span class="time" >
					<input class="date_picker time" type="text" name="AccTime_B" id="pay_endtime" value="<?php echo $pay_endtime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'pay_begintime\')}'});">
					
				</span>&nbsp;  
			</p>
		</li>
		<li class="WSY_position_text" style="margin:5px 0 10px 0"><a class="WSY_bottonliss"><input type="button" id="btnSubmit" style="width:auto;margin-left:3px" value="搜索" onclick="searchForm();" /></a></li>
		<li style="margin:5px 0 10px 0"><a onclick="del_select();">删除</a></li>
        <li style="margin:5px 0 10px 0"><a href="add_purview.php?customer_id=<?php echo $customer_id_en ;?>&op=add&type=<?php echo $type;?>">添加等级</a></li>
    </ul>
    <!--<ul>
		<li class="WSY_position_date tate001" style="margin-top:5px;">
			<p>创建时间：					
				<span class="time">
					<input class="date_picker time" type="text"name="AccTime_S" id="pay_begintime" value="<?php echo $pay_begintime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'pay_endtime\')}'});">
					<span class="om-calendar-trigger"></span>
				</span>
				-
				<span class="time" >
					<input class="date_picker time" type="text" name="AccTime_B" id="pay_endtime" value="<?php echo $pay_endtime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'pay_begintime\')}'});">
					
				</span>&nbsp;
			</p>
		</li>
	</ul>
	<ul><li class="WSY_position_text" style="margin-left:5px;"><a class="WSY_bottonliss"><input type="button" value="搜索" onclick="searchForm();" /></a></li></ul>-->
    </div>
			
	</div>
    	<div class="WSY_agentsbox WSY_list">
			
			<!--<ul class="WSY_righticon" style="margin-left:30px;margin-top:10px;float:none;">
                <li><a onclick="del_select();">删除</a></li>
                <li><a href="add_purview.php?customer_id=<?php echo $customer_id_en ;?>&op=add&type=<?php echo $type;?>">添加等级</a></li>
                     
            </ul>-->
           <!--  <ul class="WSY_righticon" >
			     <li><a onclick="excel_cityarea_label_manage();">导出</a></li>
            </ul> -->
            <table width="97%" class="WSY_table" id="WSY_t1">
			  <thead class="WSY_table_header">
					<th width="3%"><input id="s" onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()" type="checkbox"></th>
					<th width="5%" nowrap="nowrap">ID</th>
					<th width="10%" nowrap="nowrap">等级</th>
					<th width="12%" nowrap="nowrap">等级名称</th>
                    <th width="13%" nowrap="nowrap">标签图标</th>
					<th width="13%" nowrap="nowrap">标签图片</th>
					<th width="7%" nowrap="nowrap">当前等级商家</th>
					<th width="12%" nowrap="nowrap">创建时间</th>				
					<th width="15%" nowrap="nowrap" class="last">操作</th>
			  </thead>
			  
			<?php       
			    $keyid       = 0;
				$label_level   = '';
				$level_name        = '';
				$label_image       = '';
				$createtime       = '';		
				// echo $query;exit(); 		
			    $result=_mysql_query($query)or die('Query failed'.mysql_error());
                while($row=mysql_fetch_object($result)){
	                $keyid 	                = $row->id;
	                $label_level 	 	    = $row->label_level;
	                $level_name 		 	= $row->level_name;
                    $label_image            = $row->label_image;
	                $label_image2 		 	= $row->label_image2;
	                $createtime 		 	= $row->createtime;
                 if ($type=='o2o') {
	                $query_count = "select count(1) as rcount from now_pay_cust where isvalid=true and custid=".$customer_id." and label_id=".$keyid;
                    $result_count = _mysql_query($query_count) or die('Query failed_num: ' . mysql_error());
	                    while($row = mysql_fetch_object($result_count)) {
		                $label_count =$row->rcount;
		                }
	                }else if($type=='city'){
	                $query_count = "select count(1) as rcount from weixin_cityarea_supply where isvalid=true and customer_id=".$customer_id." and label_id=".$keyid;
                    $result_count = _mysql_query($query_count) or die('Query failed_num: ' . mysql_error());
	                    while($row = mysql_fetch_object($result_count)) {
		                $label_count =$row->rcount;
		                }
	                }
               
			?>
			   <tr>
			        <td><input type="checkbox" name="tid" value="<?php echo $keyid; ?>"></td>
					<td><?php echo $keyid; ?></td>
					<td><?php echo $label_level; ?></td>	
					<td><?php echo $level_name; ?></td>				
					<td>
                        <span style="display:block"><img style="height: 80px;margin-top: 10px; margin-bottom: 10px;" src="<?php echo $label_image ;?>"/></span>                     
                    </td>
                    <td>
						<span style="display:block"><img style="height: 80px;margin-top: 10px; margin-bottom: 10px;" src="<?php echo $label_image2 ;?>"/></span>						
					</td>					
				    <td><?php echo $label_count ;?></td>
				    <td><?php echo $createtime ;?></td>			
					<td id="caozuo">				      
						<a href="add_purview.php?customer_id=<?php echo $customer_id_en ;?>&keyid=<?php echo passport_encrypt((string)$keyid) ;?>&op=detail&level_name=<?php echo $level_name;?>&label_level=<?php echo $label_level;?>&type=<?php echo $type;?>">
						<img src="../../../common/images_V6.0/operating_icon/icon05.png" align="absmiddle" alt="编辑推广员" title="编辑"></a>
						<a href="javascript: G.ui.tips.confirm('您确定删除吗？','save_purview.php?keyid=<?php echo passport_encrypt((string)$keyid) ;?>&op=del&customer_id=<?php echo $customer_id_en;?>&type=<?php echo $type;?>');" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>
					</td>	
				</tr>
                <?php } ?>
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
<?php 

mysql_close($link);
?>
<!--内容框架结束-->
<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
    $('#search-enter').bind('keypress', function (event) {
        if (event.keyCode == "13") {
            $("#btnSubmit").click();
        }
    });
  var pagenum = <?php echo $pagenum ?>;
  var count =<?php echo $page ?>;//总页数
  var customer_id =<?php echo $customer_id ?>;
  var type ='<?php echo $type ?>';
  var page = count;
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 document.location= "grade_labeling.php?customer_id=<?php echo $customer_id_en; ?>&pagenum="+p+"&type="+type;
	   }
    });


  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "grade_labeling.php?customer_id=<?php echo $customer_id_en?>&pagenum="+a+"&type="+type;
	}
  }
  function searchForm(){

    var search_name = document.getElementById("search_name").value;
  	var pay_begintime = document.getElementById("pay_begintime").value;
  	var pay_endtime = document.getElementById("pay_endtime").value;

	document.location="grade_labeling.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&search_name="+search_name+"&pay_begintime="+pay_begintime+"&pay_endtime="+pay_endtime+"&pagenum="+pagenum+"&type="+type;

  }
  //导出
// function excel_cityarea_label_manage(){
//     var search_name = document.getElementById("search_name").value;
//   	var pay_begintime = document.getElementById("pay_begintime").value;
//   	var pay_endtime = document.getElementById("pay_endtime").value;
//   	if (pay_endtime==''||pay_endtime==null) {
//   		pay_endtime = -1;
//   	}
//   	if (pay_begintime==''||pay_begintime==null) {
//   		pay_begintime = -1;
//   	}
//   	if (search_name==''||search_name==null) {
//   		search_name = -1;
//   	}

// 	var url='/weixin/plat/app/index.php/ExcelCityarea/excel_cityarea_label_manage/customer_id/'+customer_id+'/search_name/'+search_name+'/pay_begintime/'+pay_begintime+'/pay_endtime/'+pay_endtime+'/';
// 	console.log(url);
// 	document.location=url; 
// }
//导出End
	// ---------全选效果
	function checkAll() {
		var code_Values = document.all['tid'];
		if (code_Values.length) {
			for (var i = 0; i < code_Values.length; i++) {
				code_Values[i].checked = true;
				console.log(code_Values[i].value);
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
//批量删除
function del_select(){
	  var type ='<?php echo $type ?>';
	var ckIds =$("input[name=tid]:checked");
	 var idsStr = "";
            ckIds.each(function(i,n){
                //console.log(" i : "+i+" n.value : "+n.value);
                if(i > 0){
                    idsStr += ",";
                }
                idsStr = idsStr + n.value;
            });
    if (idsStr=="" ||idsStr==null) {
    	alert('请选择标签');return;
    }
	if (confirm("您确定删除所选中标签吗？删除后无法恢复！！！")) {
	 
	$.ajax({
            type: "POST",
            url: "save_purview.php",
            data: {'idsStr':idsStr,'op':'batchdel','type':type},
            dataType: "json",
            success: function(data){
                //alert('success_del');
                document.location='grade_labeling.php?customer_id='+customer_id+'&type='+type;
            }
        });  
	}else{
		return;
	}
	// alert("您确定删除所选中商家吗？删除后无法恢复！！！");
	 

}
//批量删除End
</script>

</body>
</html>