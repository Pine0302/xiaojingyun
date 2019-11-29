<?php 
header("Content-type: text/html; charset=utf-8");     
require('../../../weixinpl/config.php');
require('../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

require_once('../../../weixinpl/function_model/systemFeedback.php');

$systemFeedback = new systemFeedback($customer_id);
$header = 0;
/*搜索条件*/
$condition = array(
	'sft.isvalid' => true,
	'sft.customer_id' => $customer_id,
	'wu.isvalid' => true,
	'order_by' => ' ORDER BY sft.createtime DESC '
);
$search_content = '';
if( !empty($_GET['search_content']) ){
	$search_content = $configutil->splash_new($_GET["search_content"]);
	$condition['sft.content'] = " AND sft.content like '%".$search_content."%' ";
}
$search_start_time = '';
$search_end_time = '';
if( !empty($_GET['search_start_time']) ){
	$search_start_time = $configutil->splash_new($_GET["search_start_time"]);
	$condition['start_time'] = " AND UNIX_TIMESTAMP(sft.createtime)>=UNIX_TIMESTAMP('".$search_start_time."')";
}
if( !empty($_GET['search_end_time']) ){
	$search_end_time = $configutil->splash_new($_GET["search_end_time"]);
	$condition['end_time'] = " AND UNIX_TIMESTAMP(sft.createtime)<=UNIX_TIMESTAMP('".$search_end_time."')";
}
/*搜索条件*/
/*获取的字段*/
$filed = " sft.user_id,sft.content,sft.name AS sname,sft.phone,sft.is_anonymous,sft.createtime,wu.name AS wname,wu.weixin_name ";
$filed_count = " count(1) AS scount ";	//统计数量
/*获取的字段*/
$scount = $systemFeedback -> select_system_feedback($condition,$filed_count)['data'][0]['scount'];
if( $scount == '' ){
	$scount = 0;
}

$pagenum = 1;//页码
$pagesize = 20;//每页数据数量

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * $pagesize;
$end = $pagesize;

$condition['limit'] = " LIMIT ".$start.",".$end;
$info = $systemFeedback -> select_system_feedback($condition,$filed)['data'];

$page = ceil($scount/$end);

$datePlaceholder = date('Y-m-d',time());

//用户总数
$query_count_user = "SELECT count(1) AS ucount FROM weixin_users WHERE customer_id=".$customer_id." AND isvalid=true";
$result_count_user = _mysql_query($query_count_user) or die('Query_count_user failed:'.mysql_error());
$ucount = mysql_fetch_assoc($result_count_user)['ucount'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>系统反馈列表</title>
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link href="../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../css/inside.css" media="all">
<script type="text/javascript" src="../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../common/js/inside.js"></script>
<script type="text/javascript" src="../../js/tis.js"></script>
<script type="text/javascript" src="../../js/WdatePicker.js"></script>
</head>
<style>
.operation-btn{padding: 5px 10px;background-color: #06a7e1;color: #fff;border-radius: 2px;cursor:pointer;}
.header-left{float:left;margin-left:30px;}
.header-left input{height:24px;}
.user_img{width: 50px;border-radius: 30px;}
.WSY_table span{display:block;}
.count_div{float:right;margin-right:100px;}
.red_font{color:red;}
</style>
<body>
</div>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">

       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    <?php include_once('../../../weixinpl/back_newshops/SystemFeedback/head.php');?>

    <div class="WSY_data">
    	<div class="WSY_list">
			<div class="header-left">
				<span>关键字搜索：</span><input type="text" class="search-box" id="search-content" value="<?php echo $search_content;?>" placeholder="请输入关键字" />
				<input type="text" class="search-box" id="start_time" value="<?php echo $search_start_time;?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'end_time\')}'});" placeholder="<?php echo $datePlaceholder;?>" readonly />
				至
				<input type="text" class="search-box" id="end_time" value="<?php echo $search_end_time;?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'start_time\')}'});" placeholder="<?php echo $datePlaceholder;?>" readonly />
			</div>
			<ul class="WSY_righticon" style="float:left;margin-left:5%;">
				<li style="margin-right: 20px;"><span class="operation-btn" id="search-button">搜索</span></li> 
				<li style="margin-right: 20px;"><span class="operation-btn" id="export-button" onclick="exportExcel();">导出</span></li>             
			</ul>
			<div class="count_div">
				<span>汇总：反馈总数  <span class="red_font"><?php echo $scount;?></span>  /  用户总数  <span class="red_font"><?php echo $ucount;?></span></span>
			</div>
		</div>
			<table width="97%" class="WSY_table" id="WSY_t1">
				<thead class="WSY_table_header">
					<th width="10%">粉丝编号</th>
					<th width="13%">用户名（昵称）</th>
					<th width="12%">姓名</th>
					<th width="13%">手机</th>
					<th width="35%">反馈内容</th>
					<th width="15%">提交时间</th>
				</thead>
				<?php
					foreach( $info as $k => $v ){
						if( $v['is_anonymous'] ){
							$name = '匿名';
						} else {
							$name = $v['weixin_name'].'（'.$v['wname'].'）';
						}
						
						$phoneArr[0] = substr($v['phone'],0,3);
						$phoneArr[1] = '****';
						$phoneArr[2] = substr($v['phone'],7,10);
						$phone = implode('',$phoneArr);
				?>
				<tr>
					<td><?php echo $v['user_id'];?></td>
					<td><?php echo $name;?></td>
					<td><?php echo $v['sname'];?></td>
					<td><?php echo $phone;?></td>
					<td><?php echo $v['content'];?></td>
					<td><?php echo $v['createtime'];?></td>
				</tr>
				<?php
					}
				?>
			</table>
    	</div>
        <!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
    </div>
</div>
<!--内容框架结束-->
<script type="text/javascript" src="../../common/js_V6.0/content.js"></script>
<script src="../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="../../common/js/layer/layer.js"></script>
<script>
var customer_id = '<?php echo $customer_id;?>';
var customer_id_en = '<?php echo $customer_id_en;?>';
var search_content = '<?php echo $search_content;?>';
var search_start_time = '<?php echo $search_start_time;?>';
var search_end_time = '<?php echo $search_end_time;?>';
var pagenum = <?php echo $pagenum ?>;
var count =<?php echo $page ?>;//总页数
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			var url = "system_feedback_list.php?pagenum="+p+"&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>";
			if( search_content != '' && search_content > 0 ){
				url += '&search_content='+search_content;
			}
			if( search_start_time != '' ){
				url += '&search_start_time='+search_start_time;
			}
			if( search_end_time != '' ){
				url += '&search_end_time='+search_end_time;
			}
			document.location = url;
	   }
    });
</script>

<script>
var pagenum = <?php echo $pagenum ?>;
var page = <?php echo $page ?>;
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || isNaN(a)){
		return false;
	}else{
		var url = "system_feedback_list.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;
		if( search_content != '' ){
				url += '&search_content='+search_content;
			}
			if( search_start_time != '' ){
				url += '&search_start_time='+search_start_time;
			}
			if( search_end_time != '' ){
				url += '&search_end_time='+search_end_time;
			}
		document.location = url;
	}
}
</script>
<script>
//导出
function exportExcel(){
	var url='/weixin/plat/app/index.php/Excel/commonshop_excel_system_feedback_list/customer_id/<?php echo passport_decrypt($customer_id); ?>';
	
	if( search_content != '' ){
		url += '/search_content/'+search_content;
	}
	if( search_start_time != '' ){
		url += '/search_start_time/'+search_start_time;
	}
	if( search_end_time != '' ){
		url += '/search_end_time/'+search_end_time;
	}
	
	document.location = url;
}
//搜索
$('#search-button').click(function(){
	var search_content = $('#search-content').val();
	var search_start_time = $('#start_time').val();
	var search_end_time = $('#end_time').val();
	
	var url = "system_feedback_list.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>";
	if( search_content != '' ){
		url += '&search_content='+search_content;
	}
	if( search_start_time != '' ){
		url += '&search_start_time='+search_start_time;
	}
	if( search_end_time != '' ){
		url += '&search_end_time='+search_end_time;
	}
	document.location = url;
});

</script>
<?php
	mysql_close($link);
?>
</body>
</html>