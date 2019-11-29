<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/common/utility_fun.php');

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');

$head = 9;
$customer_id = passport_decrypt($customer_id);

$user_id = $configutil->splash_new($_GET["user_id"]); 

//检测用户是否存在
$user_isvalid = 0;
$query_user = "SELECT 1 FROM weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id;
$result = _mysql_query($query_user) or die('Query failed101: ' . mysql_error());
$user_isvalid = mysql_num_rows($result);


if($user_isvalid != 1){
   echo "<script>alert('用户编号:".$user_id."，不存在');</script>";
   exit;	
}
//检测用户是否存在 End


$currency = 0;
$query = "SELECT currency FROM weixin_commonshop_user_currency where isvalid=true and user_id=".$user_id." and customer_id=".$customer_id." order by id asc LIMIT 1";
$result = _mysql_query($query) or die('Query failed101: ' . mysql_error());
while($row=mysql_fetch_object($result)){
	$currency = $row->currency;
	$currency = cut_num($currency,2);//使用utility_fun方法
}
//echo $query;
//echo $currency;
// function cut_num($menber,$places){ //使用公共的
// 	$places = $places+1;
// 	$num = substr(sprintf("%.".$places."f", $menber),0,-1); 
// 	return $num;	
// }
//echo cut_num($currency,2);
require_once (ROOT_DIR . "/wsy_pub/admin/model/security_sms.php");  //短信验证
$security_sms = new \model_security_sms($customer_id);
$check_result = $security_sms->sms_verification_check('currency_recharge');
if ($check_result["errcode"] != 0){
    echo"<script>alert('短信验证错误');history.go(-1);</script>";
    return;
}

?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/agent/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<title>购物币充值</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<style>
.white1{background-color: #fff;
border-bottom: solid 2px #06a7e1;}
</style>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
				<?php
			include("../../../../weixinpl/back_newshops/Base/pay_currency/pay_head.php"); 
			?> 
			<!--列表头部切换结束-->
			<form action="save_set_currency.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id;?>" enctype="multipart/form-data" method="post" id="upform" name="upform" onSubmit="return subBase()">
				<div class="WSY_remind_main">
					
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">余额：</dt>
						<dd>
							<input type="text" class="berfor" name="" value="<?php echo cut_num($currency,2);?>" type="text" placeholder="" style="margin-left:28px;" disabled="disabled">
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">充值：</dt>
						<dd>
							<input type="text" class="not_agent_tip" name="currency" value="" type="text" placeholder=""style="margin-left:28px;">
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">充值备注：</dt>
						<dd>
							<textarea style="border-radius: 5px; border:1px solid #dadada; " rows="6" cols="40" name="remark" value=""></textarea>
						</dd>
					</dl>
					<!--列表按钮开始-->

					<div class="WSY_text_input">
						<button style="display:inline-block;float:left;margin:30px 10px 30px 10%;" class="WSY_button"  onkeydown="if(event.keyCode==13)return false;" >保存</button>
						<input  style="display:inline-block;float:left;margin-bottom:30px;" type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);"/>
				</div> 
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	

<script>



function subBase(){
	var berfor_money = $(".berfor").val();
	var money = $(".not_agent_tip").val();

	var member = /^(\+\d+|\d+|\-\d+|\d+\.\d+|\+\d+\.\d+|\-\d+\.\d+)$/;
	if(money=='' || !member.test(money) || money ==0 ){
		alert("请输入正确的金额！");
		return false;
	}
	var after_money = parseFloat(berfor_money)+parseFloat(money);
	if( after_money < 0 ){
		alert("用户余额不足扣取！");
		return false;
	}
	if( after_money > 10000000 ){
		alert("充值后余额不能超过1000万！");
		return false;
	}
}





// function change_sendstatus(obj){ 
// 	$("#sendstatus").val(obj);
// }
// function change_is_showdiscount(obj){ 
// 	$("#is_showdiscount").val(obj);
// }

// function change_isOpenBonus(obj){
// 	$("#isOpenBonus").val(obj);
// }


</script>
<!-- <script type="text/javascript" src="../../../common/js_V6.0/content.js"></script> -->
</body>
</html>