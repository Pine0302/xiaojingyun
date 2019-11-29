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

$status =0;
$status 	= $_GET['status'];
$keyword 	= trim($_GET['keyword']);

if(!empty($_GET["keyid"])){
   $keyid = $configutil->splash_new($_GET["keyid"]);
}
//分页---start
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

if(strpos($keyword,"%") !== false){		
	$keyword = str_replace("%","\%",$keyword);								
}  
if($keyid>0){	
	$query = 'SELECT id,`status`,`key` as card_key,code_url,account,money,starttime,endtime FROM currency_recharge_card_key_t where recharge_id='.$keyid;
	$query1 = 'SELECT count(1) as wcount FROM currency_recharge_card_key_t where recharge_id='.$keyid;
	
	if(!empty($status)){
		$query .= " and `status`=".$status;
		$query1 .= " and `status`=".$status;
	}
	if(!empty($keyword)){
		$query .= " and (account like'%".$keyword."%' or money like '%".$keyword."%' or `key` like '%".$keyword."%' or id like '%".$keyword."%' or starttime like binary '%".$keyword."%' or endtime like binary '%".$keyword."%')";
		$query1 .= " and (account like'%".$keyword."%' or money like '%".$keyword."%' or `key` like '%".$keyword."%' or id like '%".$keyword."%' or starttime like binary '%".$keyword."%' or endtime like binary '%".$keyword."%')";
	}
	$query .= " order by id desc limit ".$start.",".$end;
	
	
	$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
	$wcount =0;
	$page=0;
	while ($row1 = mysql_fetch_object($result1)) {
		$wcount =  $row1->wcount ;
	}	

	$page=ceil($wcount/$end);
	
    $result = _mysql_query($query) or die('Query failed: ' . mysql_error());	
}
if(strpos($keyword,"\%") !== false){		
	$keyword = str_replace("\%","%",$keyword);								
}

require_once (ROOT_DIR . "/wsy_pub/admin/model/security_sms.php");  //短信验证
$security_sms = new \model_security_sms($customer_id);
$check_result = $security_sms->sms_verification_check('currency_camilo');
if ($check_result["errcode"] != 0){
    echo"<script>alert('短信验证错误');history.go(-1);</script>";
    return;
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>卡密明细</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" href="../../Order/order/percent/jquery.percentageloader.0.2.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script type="text/javascript" src="../../Common/js/Product/product/qrcode.js"></script>
<script type="text/javascript" src="../../Order/order/percent/jquery.percentageloader.0.2.js"></script>
<script type="text/javascript" src="../../Common/js/Product/product/security_code.js"></script>
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
.single_qr{
	position:absolute;
	display:none;
	top:0px;
	left:65px;
	width:50px;
	height:50px;
	z-index:10;
	text-align:center;
}
.single_qr .loading{
	margin-top:40px;
}
.qr_wrap,.qr_img{
	width:100%;
	height:100%
}

.order dl dd b{width:85px;}
.shui{width: 15px;height: 15px;color: #ffffff;background: #ec2935;padding: 2px;line-height: 15px;display: inline-block;border-radius: 3px;font-size: 15px;text-align: center;vertical-align: bottom;}
.div_item{float:left;padding:15px;font-size:14px;}
.div_item label{margin-left:5px;font-size:14px;}
.div_item input{border:1px solid #ccc; border-radius: 2px;}
.layui-layer-content button{float: left;margin-top: 56px;margin-bottom: 19px;width: 80px;height: 30px;}
.layui-layer{width:362px;}

/*<!-- 导出字段 -->*/
.floatbox{position: fixed;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
.floatbox .tishitext{margin-bottom: 4px;}
.floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
.checkboxsdiv input,.quanbuxuan input{display: inline-block;}
.checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
.floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
.floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
.quanbuxuan{display: inline-block;padding: 5px 0 0 10px;vertical-align: middle;margin-top: -5px;}
.subdivb{display: inline-block;vertical-align: middle;}
/*<!-- 导出字段 End -->*/

/*<!--excel导出动画-->*/
#topLoader {width: 256px;height: 256px;margin-bottom: 32px;position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100; padding:1px;}
#per_container {width: 500px;padding: 0px;margin-left: auto;margin-right: auto;}
#BgDiv{background-color:#e3e3e3; position:absolute; z-index:99; left:0; top:0; display:none; width:100%;height:1000px;opacity:0.5;filter: alpha(opacity=50);-moz-opacity: 0.5;}
#DialogDiv{position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100;background-color:#fff; border:1px #8FA4F5 solid; padding:1px;}
/*<!--excel导出动画End-->*/
</style>

</head>

<body id="bod" style="min-height: 580px;">
	<div id="BgDiv"></div>
	<div id="per_container">
	<div style="display:none" id="topLoader"></div>
	</div>
	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			
				<div class="WSY_column_header">
				<div class="WSY_columnnav_currency WSY_columnnav">
					<a href="">卡列表</a>	
                    <a href="currency_recharge_detail.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">充值明细</a>						
				</div>
			</div>
		
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list" style="margin-bottom:0px;">

	<form action="currency_recharge_key_detail?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>" method="get" id="upform" name="upform" >
      	<input type="hidden" name="keyid" id="keyid" value="<?php echo $keyid;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;"  > 

      	<div style="margin-left:40px;margin-top:0px;">
		    <span>状态：</span>
      		<select name="status" id="status" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;">
				<option value="0" id="type_0">--全部--</option>
				<option value="1" id="type_1">--待充值--</option>
				<option value="2" id="type_2">--已充值--</option>				
			</select>

      		<span>关键词：</span>
      		<input type="text" name="keyword" id="keyword" value="<?php echo $keyword;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;"  > 
			<input type="submit" class="my_search" id="my_search" value="搜索">
			<input type="button" class="my_search" id="my_excel"  value="导出">
			<ul class="WSY_righticon">
				<li style="margin-top: 20px;margin-right: 60px;"><a href="currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">返回</a></li>
			</ul>	
		</div>

	</form>
             <br class="WSY_clearfloat";>
        </div> 
        <!--列表按钮开始-->
		
        <!--表格开始-->
		
		
		<table class="WSY_t2"  width="95%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
					<th width="2%" >序号</th>
					<th width="2%" >批次号（卡号）</th>
					<th width="2%" >金额</th>
					<th width="4%" >状态</th>
					<th width="4%">卡密</th>
					<th width="8%">有效期</th>
					<th width="1%">二维码</th>					
				</tr>
			</thead>
			<tbody>
			<?php 
				while ($row = mysql_fetch_object($result)) {
					$key_id = $row->id;
					$account = $row->account;
					$money   = $row->money;
					$key_status = $row->status;
					$card_key = $row->card_key;
					$code_url = $row->code_url;
					$starttime = $row->starttime;
					$endtime = $row->endtime;
					
					if($key_status ==1){
						$style= "待充值";
					}else{
						$style= "已充值";
					}
			?>
				<tr style="border:1px solid #D8D8D8">
					<td><?php echo $key_id;?></td>
					<td><?php echo $account;?></td>
					<td><?php echo $money;?></td>
					<td><?php echo $style;?></td>
					<td><?php echo $card_key;?></td>
					<td><?php echo $starttime."至".$endtime;?></td>
					<td class="WSY_t4" id="WSY_t4" style="position:relative;">
						<a title="二维码" href="javascript:void(0);" onmouseover="showqr(this,<?php echo $key_id;?>)" onmouseout="hideqr(this,<?php echo $key_id;?>)">
							<img src="../../../common/images_V6.0/operating_icon/icon09.png">
						</a>
						<div class="single_qr" id="single_qr_<?php echo $key_id;?>">
								<!--<img class="loading" src="__PUBLIC__/img/loading/ajax_small.gif" />-->
								<div class="qr_wrap" id="qr_<?php echo $key_id;?>"></div>
						</div>
					</td>										
				</tr>
				<script>
					var qrcode = new QRCode("qr_<?php echo $key_id;?>", {
						text: "<?php echo $code_url;?>",
						width: 100,
						height: 100,
						colorDark : "#000000",   
						colorLight : "#ffffff",
						correctLevel : QRCode.CorrectLevel.L
					});
				</script>
			<?PHP }?> 
			
			</tbody>
			
			</table>
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
	
		<div class="floatbox">
		    <p class="tishitext">导出字段选择</p>
		    <div class="checkboxsdiv">
		        <div><input type="checkbox" checked name="excel_field" value="id"><p>序号</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="account"><p>批次号（卡号）</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="money"><p>金额</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="status"><p>状态</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="card_key"><p>卡密</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="time"><p>有效期</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="code_url"><p>二维码</p></div>
		    </div>
		    <div class="quanbuxuan">
		    	<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
		    </div>
		    <div class="subdivb">
		    	<input type="submit" class="floatinputs" value="确定">
		    	<input type="submit" class="floatinputc" value="取消">
		    </div>
		</div>
		<script src="../../../js/fenye/jquery.page1.js"></script>
		<script type="text/javascript">
		 var pagenum = <?php echo $pagenum ?>;
		  var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页
			var status = <?php echo $status;?>;
			var keyword = '<?php echo $keyword;?>';					
			
			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "currency_recharge_key_detail.php?pagenum="+p+"&keyid=<?php echo $keyid;?>"+"&status="+status+"&keyword="+keyword;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "currency_recharge_key_detail.php?pagenum="+a+"&keyid=<?php echo $keyid;?>"+"&status="+status+"&keyword="+keyword;
			}
		  }	
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script>
 $(".WSY_columnnav_currency").find("a").eq(0).addClass('white1');
 $("#status option:eq(<?php echo $status;?>)").attr("selected",true);
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
	
	var status = $('#status').val();
	var customer_id = <?php echo $customer_id ?>;
	var keyword = $('#keyword').val();
	var keyid = $('#keyid').val();		
	
	if(status==""){
		status = 0;
	}
	if(keyword==""){
		keyword = 0;
	}
	if(keyid==""){
		keyid = 0;
	}			
		
	var url_base='/weixin/plat/app/index.php/Excel/currency_recharge_key_excel/excel_fields/'+text+'/keyid/'+keyid+'/customer_id/'+customer_id+'/status/'+status+"/keyword/"+encodeURI(keyword);
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
   //订单导出 End

	//excel导出动画
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
			  this.setValue(Math.round(val * 100.0) + '%初始化中，请勿刷新和关闭页面！');
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
	//excel导出动画 End	
	function showqr(e,keyid){
		$("#single_qr_"+keyid).show();			
	}
	function hideqr(e,keyid){
		$("#single_qr_"+keyid).hide();
	}
</script>

<?php 

mysql_close($link);
?>

</body>
</html>
