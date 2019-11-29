<?php
header("Content-type: text/html; charset=utf-8"); 
require_once('../../../../weixinpl/config.php');
require_once('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require_once('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/function_model/currency.php');

$currency_head = 3;

$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

$keyid         = -1;
if(!empty($_GET["keyid"])){
   $keyid = (int)$configutil->splash_new($_GET["keyid"]);
}

$keyword 	= trim($_GET["keyword"]);

if(strpos($keyword,"%") !== false){		
	$keyword = str_replace("%","\%",$keyword);								
}  

_mysql_query("SET NAMES UTF8");

if($keyid>0){		
	$query = 'SELECT detail_t.id,detail_t.createtime,detail_t.user_id,detail_t.card_name,key_t.`key` as card_key,us.weixin_name FROM currency_recharge_card_detail_t as detail_t inner join currency_recharge_card_key_t as key_t on key_t.id=detail_t.key_id inner join currency_recharge_card_list_t as list_t on list_t.id=detail_t.recharge_id inner join weixin_users as us on us.id=detail_t.user_id where detail_t.recharge_id='.$keyid." and detail_t.isvalid=true and list_t.isvalid=true";
		
	$query1 = 'SELECT count(1) as wcount FROM currency_recharge_card_detail_t as detail_t inner join currency_recharge_card_key_t as key_t on key_t.id=detail_t.key_id inner join currency_recharge_card_list_t as list_t on list_t.id=detail_t.recharge_id inner join weixin_users as us on us.id=detail_t.user_id where detail_t.recharge_id='.$keyid.' and detail_t.isvalid=true and list_t.isvalid=true';
	
	if(!empty($_GET["keyword"])){
		$query.=" and (detail_t.user_id like '%".$keyword."%' or us.weixin_name like '%".$keyword."%' or key_t.`key` like '%".$keyword."%' or detail_t.createtime like binary '%".$keyword."%' or detail_t.card_name like '%".$keyword."%')"; 
        $query1.=" and (detail_t.user_id like '%".$keyword."%' or us.weixin_name like '%".$keyword."%' or key_t.`key` like '%".$keyword."%' or detail_t.createtime like binary '%".$keyword."%' or detail_t.card_name like '%".$keyword."%')"; 
	}
	$query .= " order by detail_t.createtime desc limit ".$start.",".$end;	

	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	$result1 = _mysql_query($query1) or die('Query1 failed: ' . mysql_error());

	$wcount =0;
	$page=0;
	while ($row1 = mysql_fetch_object($result1)) {
		$wcount =  $row1->wcount;
	}	
	$page=ceil($wcount/$end);
	$rcount_q = mysql_num_rows($result);

	 /*查找充值卡明细*/
	$query2 = "SELECT id,title,money from currency_recharge_card_list_t where id=".$keyid;
	$result2 = _mysql_query($query2) or die('Query1 failed: ' . $query1);	
	while ($row1 = mysql_fetch_object($result2)) {
		$list_id = $row1->id;
		$title = $row1->title;
		$money = $row1->money;
	}
}else{		
	$query = "SELECT detail_t.id,detail_t.createtime,detail_t.user_id,key_t.`key` as card_key,detail_t.card_name,us.weixin_name FROM currency_recharge_card_detail_t as detail_t inner join currency_recharge_card_key_t as key_t on key_t.id=detail_t.key_id inner join currency_recharge_card_list_t as list_t on list_t.id=detail_t.recharge_id inner join weixin_users as us on us.id=detail_t.user_id where detail_t.isvalid=true and detail_t.customer_id=".$customer_id." and list_t.isvalid=true";	
	
	$query1 = "SELECT count(1) as wcount FROM currency_recharge_card_detail_t as detail_t inner join currency_recharge_card_key_t as key_t on key_t.id=detail_t.key_id inner join currency_recharge_card_list_t as list_t on list_t.id=detail_t.recharge_id inner join weixin_users as us on us.id=detail_t.user_id where detail_t.isvalid=true and detail_t.customer_id=".$customer_id." and list_t.isvalid=true";
	
	if(!empty($_GET["keyword"])){
		$query.=" and (detail_t.user_id like '%".$keyword."%' or us.weixin_name like '%".$keyword."%' or key_t.`key` like '%".$keyword."%' or detail_t.createtime like binary '%".$keyword."%' or detail_t.card_name like '%".$keyword."%')"; 
        $query1.=" and (detail_t.user_id like '%".$keyword."%' or us.weixin_name like '%".$keyword."%' or key_t.`key` like '%".$keyword."%' or detail_t.createtime like binary '%".$keyword."%' or detail_t.card_name like '%".$keyword."%')"; 
	}
	   $query .= " order by detail_t.createtime desc limit ".$start.",".$end;
		
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		$result1 = _mysql_query($query1) or die('Query1 failed: ' . mysql_error());

		$wcount =0;
		$page=0;
		while ($row1 = mysql_fetch_object($result1)) {
			$wcount =  $row1->wcount;
		}	
		$page=ceil($wcount/$end);
		$rcount_q = mysql_num_rows($result);
}
if(strpos($keyword,"\%") !== false){		
	$keyword = str_replace("\%","%",$keyword);								
}  
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>充值明细</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" href="../../Order/order/percent/jquery.percentageloader.0.2.css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script type="text/javascript" src="../../Order/order/percent/jquery.percentageloader.0.2.js"></script>
</head>
<style>
/*.white1{background-color: #fff;
border-bottom: solid 2px #06a7e1;}*/
table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
.count{
	_width: 200px;
	height:30px;
	margin-left: 40px;
	margin-top: 40px;
	float: left;
}

.count span{
	font-size: 18px;
	color: #68af27;
	font-weight: bold;
}
</style>

<style>
    /*<!-- 导出字段 -->*/
    .floatbox{position: fixed;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
    .floatbox .tishitext{margin-bottom: 4px;}
    .floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
    .checkboxsdiv input,.quanbuxuan input{display: inline-block;}
    .checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
    .floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
    .floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
    .quanbuxuan{display: inline-block;padding: 5px 0 0 10px;vertical-align: middle;margin-top: 15px;}
    .subdivb{display: inline-block;vertical-align: middle;}
    /*<!-- 导出字段 End -->*/
	
	#topLoader {width: 256px;height: 256px;margin-bottom: 32px;position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100; padding:1px;}
	#per_container {width: 500px;margin-left: auto;margin-right: auto;}
	#BgDiv{background-color:#e3e3e3; position:absolute; z-index:99; left:0; top:0; display:none; width:100%;height:1000px;opacity:0.5;filter: alpha(opacity=50);-moz-opacity: 0.5;}
	#DialogDiv{position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100;background-color:#fff; border:1px #8FA4F5 solid; padding:1px;}
</style>
<body>
    <div id="BgDiv"></div>
    <div id="per_container">
    <div style="display:none" id="topLoader"></div>
    </div>
	<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<div class="WSY_column_header">
				<div class="WSY_columnnav_currency WSY_columnnav">
					<a href="currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">卡列表</a>	
                    <a href="">充值明细</a>						
				</div>
			</div> 
		<!--群发信息开始-->
		<div class="WSY_data">
			<!--列表按钮开始-->
			<div class="WSY_list">
		<?php if($keyid>0){?>
			<div id="WSY_list" class="WSY_list">
				<div class="WSY_left" style="background: none;">
					<div class="search">
						卡编号：<span style="font-weight:bold"><?php echo $list_id;?></span>&nbsp;&nbsp;&nbsp; 卡名称：<span style="font-weight:bold"><?php echo $title;?></span>&nbsp;&nbsp;&nbsp;					
						充值<?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>：<span style="font-weight:bold;font-size:22px;color:red"><?php echo $money;?></span>							
					</div>
				</div>
				<li style="margin: 10px 40px 0 0;float:right;"><a href="currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>

			</div>
		<?php }?>
		<form action="currency_recharge_detail?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>" method="get" id="upform" name="upform" >	 
			<div style="margin-left:40px;margin-top:0px;">
				<input type="hidden" name="keyid" id="keyid" value="<?php echo $keyid;?>" style="width:150px;height:25px;border:1px solid #ccc;border-radius:3px;"  > 
				<span>关键词：</span>
				<input type="text" name="keyword" id="keyword" value="<?php echo $keyword?>" style="width:150px;height:25px;border:1px solid #ccc;border-radius:3px;"  > 
				<input type="submit" class="my_search" id="my_search" value="搜索">
				<input type="button" class="my_search" id="my_excel"  value="导出">
				<?php if($keyid<0){?>
				<ul class="WSY_righticon">			    
					<li style="margin-top: 20px;margin-right: 60px;"><a href="pay_currency.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">返回</a></li>
				</ul>
				<?php }?>	
			</div>		
		</form>
             <br class="WSY_clearfloat";>
			</div>
			<!--列表按钮开始-->
			<!--表格开始-->
			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			 <thead class="WSY_table_header">
				<tr style="border:none">
					<th width="2%" >编号</th>
					<th width="2%" >用户编号</th>
					<th width="4%">用户名称</th>
					<th width="4%">卡名称</th>
					<th width="6%">卡密</th>
                    <th width="6%">充值时间</th>					
				</tr>
			</thead>
				<tbody>
			<?php 
				while ($row = mysql_fetch_object($result)) {
					$key_id = $row->id;
					$createtime = $row->createtime;
					$user_id = $row->user_id;					
					$name = $row->name;
					$card_key = $row->card_key;	
                    $weixin_name = $row->weixin_name;
                    $card_name = $row->card_name;					
			?>
				<tr style="border:1px solid #D8D8D8">
					<td style="text-align: center;"><?php echo $key_id;?></td>
					<td style="text-align: center;"><?php echo $user_id;?></td>
					<td style="text-align: center;"><?php echo $weixin_name;?></td>
					<td style="text-align: center;"><?php echo $card_name;?></td>
					<td style="text-align: center;"><?php echo $card_key;?></td>
                    <td style="text-align: center;"><?php echo $createtime;?></td>					
				</tr>
			<?PHP }?> 
			
			</tbody>
			</table>
			<!--表格结束-->
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
			</div>
			<!--群发信息结束-->
		</div>
				<div class="floatbox">
		    <p class="tishitext">导出字段选择</p>
		    <div class="checkboxsdiv">
		        <div><input type="checkbox" checked name="excel_field" value="id"><p>编号</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="user_id"><p>用户编号</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="weixin_name"><p>用户名称</p></div>
				<div><input type="checkbox" checked name="excel_field" value="card_name"><p>卡名称</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="card_key"><p>卡密</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="createtime"><p>充值时间</p></div>		       
		    </div>
		    <div class="quanbuxuan">
		    	<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
		    </div>
		    <div class="subdivb">
		    	<input type="submit" class="floatinputs" value="确定">
		    	<input type="submit" class="floatinputc" value="取消">
		    </div>
		</div> 
	</div>
<?php mysql_close($link); ?>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
		 var pagenum = <?php echo $pagenum ?>;
		  var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页
			var keyword = "<?php echo $keyword;?>";
			
			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "currency_recharge_detail.php?keyid=<?php echo $keyid;?>&pagenum="+p+"&keyword="+keyword;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "currency_recharge_detail.php?keyid=<?php echo $keyid;?>&pagenum="+a+"&keyword="+keyword;
			}
		  }	
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script>
    $(".WSY_columnnav_currency").find("a").eq(1).addClass('white1');
	// 显示导出界面
	$("#my_excel").click(function(){
	    $(".floatbox").toggle();
	});

	// 导出取消
	$(".floatinputc").click(function(){
	    $(".floatbox").hide();
	}); 

	 // 全选
	$("#allselects").click(function(){    
	    if(this.checked){    
	        $(".checkboxsdiv :checkbox").attr("checked", true);   
	    }else{    
	        $(".checkboxsdiv :checkbox").attr("checked", false); 
	    }    
	});	
	//导出
	$(".floatinputs").click(function(){
		var text = [];
		$('input[name="excel_field"]:checked').each(function(){ 
			text.push($(this).val()); 
		}); 
		if(text.length == 0){
			alert("请至少选择一个字段！");
			return;
		}
		
		var customer_id = <?php echo $customer_id ?>;
		var keyword = $('#keyword').val();
		var keyid = $('#keyid').val();				
		
		if(keyid==""){
			keyid = 0;
		}
		if(keyword==""){
			keyword = 0;
		}		
			
		var url_base='/weixin/plat/app/index.php/Excel/currency_recharge_detail_excel/excel_fields/'+text+'/keyid/'+keyid+'/customer_id/'+customer_id+"/keyword/"+encodeURI(keyword);	
		inti_per();
		ShowDIV('topLoader');	

		if (topLoaderRunning) {
			return;
		}
		topLoaderRunning = true;
		var oFunc = function () {
			url = url_base + '/limit_count/20/limit_p/'+obj_json.page+'/page_count/'+obj_json.page_count+'/count/'+obj_json.count+'/';

			$.ajax({type:'GET', async:false, url:url,
				success:function(data){
					obj_json = eval('('+data+')');
					if(obj_json.page_count<obj_json.page){
						closeDiv('topLoader');
						window.location.href=url+'output/go/';	
						
					}else{ }
				}		
			});
			
			glo_add = glo_add + glo_per;
			$topLoader.percentageLoader({progress: glo_add});
			$topLoader.percentageLoader({value: ('导出中，请勿刷新和关闭页面！')});
			if(glo_add<1){
				setTimeout(oFunc, 200);
			}else{
				topLoaderRunning = false;
			}
		}

		if(obj_json.length==0){
			$topLoader.percentageLoader({progress: glo_add});
			$topLoader.percentageLoader({value: ('导出中，请勿刷新和关闭页面！')});
			url = url_base + '/limit_count/20/limit_p/0/';
			$.ajax({type:'GET', async:false, url:url,
				success:function(data){
					obj_json = eval('('+data+')');
					glo_per = 1 / obj_json.page_count;
					setTimeout(oFunc, 1000);

				}
			});	
		}else{ }			
		 $(".floatbox").hide();		
	});	
	
	//发送动画
    var glo_add;
    var glo_per;//完成百份比
    var obj_json;
    var topLoaderRunning;
    var $topLoader;
    $(function() {
        inti_per();
    });

    function inti_per(){
        glo_add = 0.0;
        glo_per = 0.0;
        obj_json = new Array(); 
        $topLoader = $("#topLoader").percentageLoader({
            width: 256, height: 256, controllable: true, progress: glo_add, onProgressUpdate: function (val) {
              this.setValue(Math.round(val * 100.0) + '%初始化中，请勿刷新！');
            }
        });
        topLoaderRunning = false;   
    }
    
    function ShowDIV(thisObjID) {
        $("#BgDiv").css({ display: "block", height: $(document).height() });
        var yscroll = document.documentElement.scrollTop;
        $("#" + thisObjID).css("top", "100px");
        $("#" + thisObjID).css("display", "block");
        document.documentElement.scrollTop = 0;
    }

    function closeDiv(thisObjID) {
        $("#BgDiv").css("display", "none");
        $("#" + thisObjID).css("display", "none");
    }
</script>

</body>
</html>
