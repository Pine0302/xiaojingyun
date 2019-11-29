<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$head=3;

//分页---start
$pagenum = 1;
$pagesize = 20;
$begintime="";
$endtime ="";

$name 			= '';	//姓名
$weixin_name 	= '';	//微信名
$account 		= '';	//绑定的号码
$money 			= 0;	//设计金额
$type 			= 0;	//进出账 0：进账 1：出账 2:充值
$batchcode 		= '';	//订单号
$createtime 	= '';	//时间
$remark 		= '';	//备注



/*$query = "SELECT l.id,u.name,u.weixin_name,s.account,l.money,l.type,l.batchcode,l.remark,l.createtime,l.user_id from weixin_users u LEFT JOIN  moneybag_log l on u.id=l.user_id LEFT JOIN system_user_t s ON l.user_id=s.user_id where u.isvalid=true and l.isvalid=true and l.customer_id=".$customer_id;*/

$query = "SELECT id,money,type,batchcode,remark,createtime,user_id,after_money,operation_user  FROM moneybag_log WHERE isvalid=true AND customer_id=".$customer_id;
$query_rcount_q = "SELECT count(id) as rcount_q FROM moneybag_log WHERE isvalid=true AND customer_id=".$customer_id;
//搜索条件
	//日期条件--开始时间
	$begintime = "";
	if( !empty($_GET['AccTime_E']) ){  //结算/发放 时间
		$begintime = $_GET['AccTime_E'];
		$query 	.=" and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime);
		$query_rcount_q 	.=" and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime);
		//$query1 = $sql;
	}
	//日期条件--结束时间
	$endtime = "";
	if( !empty($_GET['AccTime_B']) ){   //结算/发放 End
		$endtime = $_GET['AccTime_B'];
		$query .= " and UNIX_TIMESTAMP(createtime)<=".strtotime($endtime);
		$query_rcount_q .= " and UNIX_TIMESTAMP(createtime)<=".strtotime($endtime);
	}

	$promoter = null;
	if( !empty($_GET["promoter"]) && is_numeric($_GET["promoter"]) ){
		$promoter = (int)$configutil->splash_new($_GET["promoter"]);
		$query .= " and user_id=".$promoter;
		$query_rcount_q .= " and user_id=".$promoter;
	}

	$search_status  = -1;	//消费类型
	if( isset($_GET["search_status"]) && (int)$_GET["search_status"] >= 0 ){
		$search_status = $_GET["search_status"];
		if($search_status == 38){
			//云店奖励（提成收入）
			$query .= " and commission_type=25 ";
			$query_rcount_q .= " and commission_type=25 ";
		}else if($search_status == 39){
			//云店店主自营产品收入
			$query .= " and commission_type=26 ";
			$query_rcount_q .= " and commission_type=26 ";
		} else if ($search_status == 52) {
        $query .= " and (pay_style = 52 or pay_style = 53) ";
        $query_rcount_q .= " and (pay_style = 52 or pay_style = 53) ";
    } else{
			$query .= " and pay_style=".$search_status;
			$query_rcount_q .= " and pay_style=".$search_status;
		}
	}

	$search_type  = -1;	//进出账 0：进账 1：出账
	if( isset($_GET["search_type"]) && (int)$_GET["search_type"] >= 0 ){
		$search_type = $_GET["search_type"];
		$query .= " and type=".$search_type;
		$query_rcount_q .= " and type=".$search_type;
	}

//搜索条件 End
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
if(!empty($_GET["num"])){
   $num = $configutil->splash_new($_GET["num"]);
}
$end = $pagesize;
if ($num==1) {
	$result_rcount_q = _mysql_query($query_rcount_q) or die('Query failed2: ' . mysql_error());
	if($row_rcount_q = mysql_fetch_object($result_rcount_q)){
		$rcount_q = $row_rcount_q->rcount_q;
	}
	$pagenum=ceil($rcount_q/$end);
}
$start = ($pagenum-1) * $pagesize;



 /* 输出数量结束 */

//$query = $query." and id <= (SELECT id FROM moneybag_log ORDER BY id desc LIMIT ".$start.", 1)  ORDER BY  id desc LIMIT ".$end;
$query = $query."   ORDER BY  id desc LIMIT ".$start.",".$end;

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>待提现记录</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" rev="stylesheet" href="../../../css/inside.css" media="all">
<link rel="stylesheet" href="../../Common/js/percent/jquery.percentageloader.0.2.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script src="../../Common/js/Data/js/echarts/echarts.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>

<!-- 新页数 -->
<!-- 图标 CSS -->
<link rel="stylesheet" href="/mp/admui/public/fonts/font-awesome/font-awesome.css">
<link rel="stylesheet" href="/mp/admui/public/fonts/web-icons/web-icons.css">
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="/mp/admui/public/themes/classic/global/css/new_bootstrap.css">
<link rel="stylesheet" href="/mp/admui/public/themes/classic/base/css/site.css" id="admui-siteStyle">
<!-- 插件 CSS -->
<script src="/mp/admui/public/vendor/twbs-pagination/jquery.twbsPagination.min.js"></script>
<!-- 新页数 end-->
<style>
.WSY_orderformbox select{height: 26px}

table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
table td img{width: 20px;height: 20px;margin-left: 5px;}

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
#per_container {width: 500px;padding: 10px;margin-left: auto;margin-right: auto;}
#BgDiv{background-color:#e3e3e3; position:absolute; z-index:99; left:0; top:0; display:none; width:100%;height:1000px;opacity:0.5;filter: alpha(opacity=50);-moz-opacity: 0.5;}
#DialogDiv{position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100;background-color:#fff; border:1px #8FA4F5 solid; padding:1px;}
/*<!--excel导出动画End-->*/
</style>

</head>

<body id="bod" style="min-height: 580px;">

	<!--excel导出动画-->
	<div id="BgDiv"></div>
	<div id="per_container">
	<div style="display:none" id="topLoader"></div>
	</div>
	<!--excel导出动画 End-->

	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/moneybag/basic_head.php"); ?>
			<!--列表头部切换结束-->

	<!--门店列表开始-->
    <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list">

	<form action="" >

      	<div class="WSY_orderformbox" style="margin-left:40px;margin-top:0px;">
      		<span style="margin-left:10px;">会员编号：</span>
      		<input type="text" name="promoter" id="promoter_id" value="<?php echo $promoter;?>" style="height:25px;border:1px solid #ccc;border-radius:3px;" onkeyup="clearNoNum(this)" onblur="clearNoNum(this)" >

			<span style="margin-left:10px;">消费类型:</span>
			<select name="search_status" id='search_status'>
				<option value="-1">所有</option>
				<option <?php if($search_status==0) { ?>selected <?php } ?> value="0">商城消费</option>
				<option <?php if($search_status==1) { ?>selected <?php } ?> value="1">返佣</option>
				<option <?php if($search_status==2) { ?>selected <?php } ?> value="2">消费返现</option>
				<option <?php if($search_status==3) { ?>selected <?php } ?> value="3">大礼包</option>
				<option <?php if($search_status==4) { ?>selected <?php } ?> value="4">商家后台充值</option>
				<option <?php if($search_status==5) { ?>selected <?php } ?> value="5">提现</option>
				<option <?php if($search_status==6) { ?>selected <?php } ?> value="6">全球分红</option>
				<option <?php if($search_status==7) { ?>selected <?php } ?> value="7">会员分红</option>
				<option <?php if($search_status==8) { ?>selected <?php } ?> value="8">城市商圈消费</option>
				<option <?php if($search_status==9) { ?>selected <?php } ?> value="9">线下商城分享卡</option>
				<option <?php if($search_status==17) { ?>selected <?php } ?> value="17">订货系统分仓派单结算</option>
				<option <?php if($search_status==18) { ?>selected <?php } ?> value="18">订货系统推荐奖励</option>
				<option <?php if($search_status==19) { ?>selected <?php } ?> value="19">订货系统提成奖励</option>
				<option <?php if($search_status==20) { ?>selected <?php } ?> value="20">F2C系统派单结算入账</option>
                <option <?php if($search_status==31) { ?>selected <?php } ?> value="31">零钱转换<?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?></option>
                <option <?php if($search_status==32) { ?>selected <?php } ?> value="32">零钱转换货款</option>
                <option <?php if($search_status==38) { ?>selected <?php } ?> value="38">云店奖励(提成收入)</option>
                <option <?php if($search_status==39) { ?>selected <?php } ?> value="39">云店店主自营产品收入</option>
        <option <?php if($search_status==52) { ?>selected <?php } ?> value="52">区域奖励</option>
			</select>

			<span style="margin-left:10px;">进出账:</span>
			<select name="search_type" id='search_type' >
				<option value="-1">所有</option>
				<option <?php if($search_type==0) { ?>selected <?php } ?> value="0">进账</option>
				<option <?php if($search_type==1) { ?>selected <?php } ?> value="1">出账</option>
			</select>

      	<!--
		<span style="margin-left:20px;">会员卡编号：</span>
      		<input type="text" name="card_num" id="card_member_id" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;">
		-->
		<div class="WSY_position1" style="float:left">
			<ul>
				<li class="WSY_position_date tate001" >
					<p>时间：<input class="date_picker" type="text" name="AccTime_E" id="begintime" value="<?php echo $begintime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'endtime\')}'});"></p>
					<p style="margin-left:0px;">&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="AccTime_B" id="endtime" value="<?php echo $endtime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'begintime\')}'});"></p>
				</li>
			</ul>
		</div>
		<input type="submit" id="my_search" value="提交查询" >

		<input type="button" class="search_btn" id="change_excel" value="日志导出" >
		</div>



	</form>

        <br class="WSY_clearfloat";>
        </div>
        <!--列表按钮开始-->

        <!--表格开始-->
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;">
		<table class="WSY_t2"  width="97%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
					<th width="2%" >ID</th>
					<th width="4%" >编号</th>
					<th width="6%">姓名（微信名）</th>
					<th width="6%">绑定手机</th>
					<th width="6%">涉及金额</th>
					<th width="6%">账户余额</th>
					<th width="6%">进出账</th>
					<th width="6%">交易号</th>
					<th width="8%">时间</th>
					<th width="4%">操作人</th>
					<th width="10%">备注信息</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$count =0;
				$result= _mysql_query($query);
				while($row=mysql_fetch_object($result)){
					$count =1;//判断是否有数据
					$id 			= $row->id;
					$user_id 		= $row->user_id;
					$name        = '';
					$weixin_name = '';
					$weixin_fromuser = '';//CRM 16765
					$query_u = "SELECT name,weixin_fromuser,weixin_name FROM weixin_users WHERE isvalid=true AND id=$user_id";
					$result_u= _mysql_query($query_u) or die('Query failed 163: ' . mysql_error());
					while( $info = mysql_fetch_object($result_u) ){
						$name = $info->name;
						$weixin_fromuser = $info->weixin_fromuser;
						$weixin_name = $info->weixin_name;
					}
					$query_v = "SELECT weixin_name FROM weixin_users WHERE weixin_fromuser='$weixin_fromuser'";
					$result_v= _mysql_query($query_v) or die('Query failed 163: ' . mysql_error());
					while( $res = mysql_fetch_object($result_v) ){
						if ($weixin_name == '' && $weixin_fromuser != ''){
							$weixin_name = $res->weixin_name;
						}
					}
					$account = '<span style="color:#c22439;font-weight:blod;font-size:14px;">尚未绑定</span>';
					$query_s = "SELECT account FROM system_user_t WHERE isvalid=true AND user_id=$user_id";
					$result_s= _mysql_query($query_s) or die('Query failed 163: ' . mysql_error());
					while( $info = mysql_fetch_object( $result_s )){
						$account = $info->account;
					}
					if( $account == '' || $account == NULL ){
							$account = '<span style="color:#c22439;font-weight:blod;font-size:14px;">尚未绑定</span>';
						}

					$money 			= $row->money;
					$type 			= $row->type;
					$after_money 	= $row->after_money;
					switch($type){
						case '0':
							$type   = '<span style="color:#c22439;font-weight:blod;font-size:14px;">进账</span>';
						break;

						case '1':
							$type   = '<span style="color:#68af27;font-weight:blod;font-size:14px;">支出</span>';
						break;

						case '2':
							$type   = '充值';
						break;
					}
					$batchcode 		= $row->batchcode;
					$createtime 	= $row->createtime;
					$operation_user = $row->operation_user;
					$remark 		= str_replace("<br />","",$row->remark);


			?>
				<tr style="border:1px solid #D8D8D8">
					<td><?php echo $id;?></td>
					<td><?php echo $user_id;?></td>
					<td><?php echo $name;?>（<?php echo empty($weixin_name)?"-":$weixin_name;?>）</td>
					<td><?php echo $account?></td>
					<td><?php echo round($money,2);?></td>
					<td><?php echo round($after_money,2);?></td>
					<td><?php echo $type;?></td>
					<td><?php echo $batchcode;?></td>
					<td><?php echo $createtime;?></td>
					<td><?php echo $operation_user;?></td>
					<td><?php echo $remark;?></td>
				</tr>
			<?PHP }?>

			</tbody>

			</table>

			<!-- 导出字段选择 -->
			<div class="floatbox">
				<p class="tishitext">导出字段选择</p>
				<div class="checkboxsdiv">
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="id"><p>ID</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="userid"><p>会员编号</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="name"><p>姓名</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="WXname"><p>微信名</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="phone"><p>绑定手机</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="money"><p>涉及金额</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="money_before"><p>变动前的余额</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="money_after"><p>变动后的余额</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="account"><p>进出账</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="batchcode"><p>订单号</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="time"><p>时间</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="operation_user"><p>操作人</p></div>
					<div><input type="checkbox" name="excel_field[]" checked="checked" value="remark"><p>备注信息</p></div>
				</div>
				<div class="quanbuxuan">
					<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
				</div>
				<div class="subdivb">
					<input type="submit" class="floatinputs" value="确定">
					<input type="submit" class="floatinputc" value="取消">
				</div>
			</div>
			<!-- 导出字段选择 End -->

				<!-- 新页面开发2018/12/07 -->
				<?php if($count==0){ ?>
				<div style="width:100%;height: 400px;text-align: center;margin-top: 200px;font-size: 14px;">暂无更多数据</div>
				<?php }?>
				<!-- 新页面开发2018/12/07 -->
				<!-- 2018/12/07 新页数开发 林荣碟  开始-->
				<div class="WSY_page" style="margin-bottom: 20px; height:20px; ">
					<nav aria-label="Page navigation">
					    <ul class="pagination pagination-gap pull-left" id="pagination"></ul>
					    <div class="clearfix"></div>
					</nav>
				</div>
				<!-- 2018/12/07 新页数开发 林荣碟  结束-->
			<!--翻页结束-->
		</div>
		<script src="../../Common/js/percent/jquery.percentageloader.0.2.js"></script>
		<script type="text/javascript">
var user_id       = $("#promoter_id").val();     //会员编号
var AccTime_E     = $("#begintime").val();       //开始时间
var AccTime_B     = $("#endtime").val();         //结束时间
var search_status = $("#search_status").val();   //消费类型
var search_type   = $("#search_type").val();     //结束时间
var pagenum = <?php echo $pagenum ?>;  //当前页数
var num = "<?php echo $num?>"; //按直接跳转到最后一页操作 判断是不是跳到最后一页
var num_order = "<?php echo $count; ?>"; //按跳转按钮到最后一页操作  判断当前页有没数据
var total_page = 1000000;
if ((num==1 || num_order==0) && pagenum > 9){total_page=pagenum;}
var last_page =0;//点击最后一页的判断
$('#pagination').show();
//新页数开发开始
$('#pagination').twbsPagination({
    totalPages: total_page,    //总页数
    startPage: pagenum,	  //当前页数
    visiblePages: 9,      //最大可见页数 
    onPageClick: function(event, page) {
    	if (last_page==1) {  //跳转尾页 
			document.location="recharge_log.php?num=1&promoter="+user_id+"&AccTime_E="+AccTime_E+"&AccTime_B="+AccTime_B+"&search_status="+search_status+"&search_type="+search_type;
    	}else{
    		document.location="recharge_log.php?pagenum="+page+"&num=2&promoter="+user_id+"&AccTime_E="+AccTime_E+"&AccTime_B="+AccTime_B+"&search_status="+search_status+"&search_type="+search_type;
    	}  
    }
});
//点击最后一页
$('.last').click(function(){ last_page=1;$('#pagination').hide(); });
//改变跳转按钮的颜色
$('.btn-primary').addClass('WSY-skin-bg');
$('.pagination > .active > a').addClass('WSY-skin-bg');
//页数结束
//点击下一页的验证 
$('.next').click(function(){ 
	
});


		$("#change_excel").click(function(){
			$(".floatbox").toggle();
		});

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

		//订单导出
		$(".floatinputs").click(function(){
            var str="";
            $("input[name='excel_field[]']:checkbox").each(function(){
                if($(this).attr("checked")){
                    str += $(this).val()+","
                }
            })
            //alert(str);
            str = str.substring(0,str.length-1);
			var user_id             = $('#promoter_id').val();     //会员编号
			var begintime           = $('#begintime').val();       //开始时间
			var endtime             = $('#endtime').val();         //结束时间
			var search_type         = $('#search_type option:selected').val();         //进出账
			var search_status       = $('#search_status option:selected').val();       //消费类型
			if(user_id==""){
				user_id = 0;
			}
			if(begintime==""){
				begintime = 0;
			}
			if(endtime==""){
				endtime = 0;
			}
			if(str==""){
				str = 0;
			}
			var url_base = '/weixin/plat/app/index.php/Excel/commonshop_excel_smallChange/customer_id/<?php echo $customer_id; ?>/excel_fields/'+str+"/search_status/"+search_status+"/search_userId/"+user_id+"/begintime/"+begintime+"/endtime/"+endtime+"/search_type/"+search_type;
			//console.log(url)

			inti_per();
			ShowDIV('topLoader');

			if (topLoaderRunning) {
				return;
			}
			topLoaderRunning = true;
			var oFunc = function () {
				url = url_base + '/limit_count/10000/limit_p/'+obj_json.page+'/page_count/'+obj_json.page_count+'/count/'+obj_json.count+'/';
				console.log(url);
				$.ajax({type:'GET', async:false, url:url,
					success:function(data){
						obj_json = eval('('+data+')');

						if(obj_json.page_count<obj_json.page){
							closeDiv('topLoader');
							window.location.href=url+'output/go/';

						}else{ }


						console.log(obj_json.code);
					}
				});

				glo_add = glo_add + glo_per;
				$topLoader.percentageLoader({progress: glo_add});
				$topLoader.percentageLoader({value: ('导出中，请勿刷新和关闭页面！')});
				//console.log('nothing'+obj_json.page);
				if(glo_add<1){
					setTimeout(oFunc, 200);
				}else{
					topLoaderRunning = false;
				}
			}

			if(obj_json.length==0){
				$topLoader.percentageLoader({progress: glo_add});
				$topLoader.percentageLoader({value: ('导出中，请勿刷新和关闭页面！')});
				url = url_base + '/limit_count/10000/limit_p/0/';
				$.ajax({type:'GET', async:false, url:url,
					success:function(data){
						obj_json = eval('('+data+')');
						glo_per = 1 / obj_json.page_count;
						//console.log(obj_json.code);
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

		//数字检验
		function clearNoNum(obj)
		{
		//先把非数字的都替换掉，除了数字和.
		obj.value = obj.value.replace(/[^\d.]/g,"");
		//必须保证第一个为数字而不是.
		obj.value = obj.value.replace(/^\./g,"");
		//保证只有出现一个.而没有多个.
		obj.value = obj.value.replace(/\.{2,}/g,".");
		//保证.只出现一次，而不能出现两次以上
		obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
		}
		//数字检验	End

		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">


<?php

mysql_close($link);
?>

</body>
</html>
