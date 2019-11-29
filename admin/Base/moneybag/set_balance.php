<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head = 0;
$customer_id = passport_decrypt($customer_id);



// $lianmu = 2;
// //子栏目
// $quanx_arr1 = array(
// 				'/mshop/admin/Base/moneybag/set_balance.php'      => 'sc_Base_moneybag',
// 				'/mshop/admin/Base/pay_currency/set_currency.php' => 'sc_Base_moneybag'
// 					);

// $url = $_SERVER['PHP_SELF'];

// //栏目
// $quanx_arr2 = array();

// $c_id = $_SESSION['C_id'];
// $u_id = $_SESSION['user_id'];
// if($c_id != $customer_id)
// {
// 	echo '商家信息错误';
// 	die();
// }

// if($lianmu = 1) //大栏目
// {

// }


// if(!empty($c_id) && !empty($u_id)) //子栏目
// {
// 	$sql  = "select count(id) as id from customer_user_funs2 where C_id = $c_id and user_id = $u_id and funs='".$quanx_arr1[$url]."'";

// 	$res  = _mysql_query($sql);
// 	$row  = mysql_fetch_assoc($res);
// 	$re   = $row['id'];

// 	if(empty($re))
// 	{
// 		echo '无权限操作';
// 		die();
// 	}
// }

	



$user_id = $configutil->splash_new($_GET["user_id"]); 
$balance   = 0;
$real_name = '';
$query = "SELECT t.balance,u.name FROM moneybag_t t RIGHT JOIN weixin_users u on t.user_id=u.id where t.isvalid=true and u.id=".$user_id." LIMIT 1";
$result = _mysql_query($query);
while($row=mysql_fetch_object($result)){
	$balance   = round($row->balance,2);
	$real_name = $row->name;
	if($balance==NULL){
		$balance = 0;
	}
}

require_once (ROOT_DIR . "/wsy_pub/admin/model/security_sms.php");  //短信验证
$security_sms = new \model_security_sms($customer_id);
$check_result = $security_sms->sms_verification_check('moneybag_recharge');
if ($check_result["errcode"] != 0){
    echo"<script>alert('短信验证错误');history.go(-1);</script>";
    return;
}


?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>零钱后台</title>
<link rel="stylesheet" type="text/css" href="../../../../weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../../market/Common/css/Mode/agent/set.css">
<script type="text/javascript" src="../../../../weixinpl/common/js/jquery-1.7.2.min.js"></script>
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
			include("../../../../weixinpl/back_newshops/Base/moneybag/pay_head.php"); 
			?> 
			<!--列表头部切换结束-->
			<form action="save_set_balance.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id;?>" enctype="multipart/form-data" method="post" id="upform" name="upform" onSubmit="return subBase()">
				<div class="WSY_remind_main">
					
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">余额：</dt>
						<dd>
							<input type="text" class="berfor" name="" value="<?php echo $balance;?>" type="text" placeholder=""style="margin-left:8px;" disabled="disabled">
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">充值：</dt>
						<dd>
							<input type="text" class="not_agent_tip" name="balance" value=""  placeholder=""style="margin-left:22px;" onkeyup="clearNoNum2(this);">
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">充值备注：</dt>
						<dd>
							<textarea style="border-radius: 5px; border:1px solid #dadada; " rows="6" cols="40" name="remark" value=""></textarea>
						</dd>
					</dl>
					<input type="hidden" name="real_name" value="<?php echo $real_name; ?>">
					<!--列表按钮开始-->

					<div class="WSY_text_input"><button style="float: left;margin: 20px 10px 20px 10%;" class="WSY_button" >充值</button>
					<input type="button" class="WSY_button" style="float: left;margin: 20px 0 20px 0;"  value="取消" onclick="javascript:history.go(-1);"></div>
				</div> 
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	

<script>
var isSub = 0;	//是否已提交


function subBase(){
	if (isSub) {
		alert("正在提交，请勿重复操作！");
		return false;
	}
	isSub = 1;
	var berfor_money = $(".berfor").val();
	var money = $(".not_agent_tip").val();

	var member = /^(\+\d+|\d+|\-\d+|\d+\.\d+|\+\d+\.\d+|\-\d+\.\d+)$/;
	if(money=='' || !member.test(money)){
		isSub = 0;
		alert("请输入正确的金额！");
		return false;
	}
	if( money == 0 ){
		isSub = 0;
		alert("请输入不等于0的金额");
		return false;
	}
	if( money > 1000000){
		isSub = 0;
		alert("一次请不要充值超过100万");
		return false;
	}
	// var after_money = parseFloat(berfor_money)+parseFloat(money);
	// if( after_money < 0 ){
	// 	isSub = 0;
	// 	alert("用户余额不足扣取！");
	// 	return false;
	// }
	if( after_money > 1000000 ){
		isSub = 0;
		alert("用户钱包金额最大为1000000.00元！");
		return false;
	}
}


//中文，英文，多小数点过滤
function clearNoNum2(obj,type){
	if( type == 1 ){			//纯数字
		obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
		obj.value = obj.value.replace(/\./g,"");
	}else if(type == 2){		//纯数字+2位小数
		obj.value = obj.value.replace(/[^\d.-]/g,""); //清除"数字"和"."和"-"以外的字符
		obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
		obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
		obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
		obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
	}else if(type == 4){        //纯数字+4位小数
		obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
		obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
		obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
		obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
		obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d\d\d).*$/,'$1$2.$3'); //只能输入四个小数
	}

}


function clearNoNum(obj){
	//obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
	//obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
	obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}

</script>

</body>
</html>