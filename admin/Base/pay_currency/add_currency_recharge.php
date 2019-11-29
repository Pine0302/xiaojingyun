<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/function_model/currency.php');

$currency = new Currency();
$head=0;

$keyid=0;
$op = "";
if(!empty($_GET["keyid"])){
	$keyid 	 = $configutil->splash_new($_GET["keyid"]);
}
if(!empty($_GET["op"])){
	$op 	 = $configutil->splash_new($_GET["op"]);
}
$title 	   = "";
$num 	   = "";
$money 	   = "";
$starttime = "";
$endtime   = "";
if($keyid>0){			
	if($op == "del"){  //删除操作
		$conditions['id']     = $keyid;	
		
		$fileds = array();
		$fileds['isvalid']     = "del";
		
		$result = $currency ->update_recharge_card_list($conditions,$fileds);	

		$errcode = $result['errcode'];
		$errmsg  = $result['errmsg'];

		$description = '用户:'.$_SESSION['username'].'(编号:'.$_SESSION['C_id'].')于'.date('Y-m-d H:i:s', time()).'对充值卡批次(编号：'.$keyid.')进行了删除操作';
		/*记录操作日志*/
		$sql_logs = "insert into operlogs(operuser_id,ip,description,type,isvalid,createtime) values(".$_SESSION['C_id'].",'".GetIP()."','".$description."',2,true,now())";
		
		_mysql_query($sql_logs) or die('sql_logs failed: ' .mysql_error());
		
		if($result['errcode'] == 0){
			echo "<script>location.href='currency_recharge.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";	
		}			
	}elseif($op == "stop"){//冻结
		$conditions['id']     = $keyid;	
		
		$fileds = array();
		$fileds['status']     = 3;
		
		$result = $currency ->update_recharge_card_list($conditions,$fileds);	
        
		$errcode = $result['errcode'];
		$errmsg  = $result['errmsg'];

		$description = '用户:'.$_SESSION['username'].'(编号:'.$_SESSION['C_id'].')于'.date('Y-m-d H:i:s', time()).'对充值卡批次(编号：'.$keyid.')进行了冻结操作';
		/*记录操作日志*/
		$sql_logs = "insert into operlogs(operuser_id,ip,description,type,isvalid,createtime) values(".$_SESSION['C_id'].",'".GetIP()."','".$description."',2,true,now())";
		
		_mysql_query($sql_logs) or die('sql_logs failed: ' . mysql_error());

        //插入操作日志
        $query = "select title,num,money,starttime,endtime from currency_recharge_card_list_t where id=".$keyid." and isvalid=true";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());

        while ($row = mysql_fetch_object($result)) {
            $title 	   = $row->title;
        }
        require_once(ROOT_DIR.'/wsy_pub/admin/model/sys_plat_log.php');
        $sys_plat_log = new \model_sys_plat_log($customer_id);
        $sys_plat_log->add_log('shop_system_currency_recharge_card','冻结卡名称【'.$title.'】；');
		
		if($result['errcode'] == 0){
			echo "<script>alert('该批次卡号已停止使用');location.href='currency_recharge.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";	
		}			
	}elseif($op == "thaw_stop"){//解冻
		$conditions['id']     = $keyid;	
		
		$fileds = array();
		
		//查找该批次是否已充值完成
		$sql_check = "select num,used from currency_recharge_card_list_t where id=".(int)$keyid." and customer_id=".$customer_id." and isvalid=true";
		$result_check = _mysql_query($sql_check) or die('sql_check failed: ' . mysql_error());
		
		while ($row_check = mysql_fetch_object($result_check)) {
		     $num = $row_check->num;
			 $used = $row_check->used;
		}
		
		if($num == $used){  //充值全部完成
			$fileds['status']     = 4;
		
		}else{
			$fileds['status']     = 2;
		}				
		$result = $currency ->update_recharge_card_list($conditions,$fileds);	
        
		$errcode = $result['errcode'];
		$errmsg  = $result['errmsg'];

		$description = '用户:'.$_SESSION['username'].'(编号:'.$_SESSION['C_id'].')于'.date('Y-m-d H:i:s', time()).'对充值卡批次(编号：'.$keyid.')进行了解冻操作';
		/*记录操作日志*/
		$sql_logs = "insert into operlogs(operuser_id,ip,description,type,isvalid,createtime) values(".$_SESSION['C_id'].",'".getIP()."','".$description."',2,true,now())";
		
		_mysql_query($sql_logs) or die('sql_logs failed: ' . mysql_error());
		
		if($result['errcode'] == 0){
			echo "<script>alert('该批次卡号已解冻成功');location.href='currency_recharge.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";	
		}			
	}else{
		$query = "select title,num,money,starttime,endtime from currency_recharge_card_list_t where id=".$keyid." and isvalid=true";
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		
		while ($row = mysql_fetch_object($result)) {
			$title 	   = $row->title;
			$num 	   = $row->num;
			$money 	   = $row->money;
			$starttime = $row->starttime;
			$endtime   = $row->endtime;
		}	
	}
		
}

//获取用户Ip
/*function GetIP()
{
	if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} else {
		if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} else {
			if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
				$ip = getenv('REMOTE_ADDR');
			} else {
				if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
					$ip = $_SERVER['REMOTE_ADDR'];
				} else {
					$ip = 'unknown';
				}
			}
		}
	}
	return $ip;
}*/	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/layer.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<title>添加充值卡</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<div class="WSY_column_header">
				<div class="WSY_columnnav_currency WSY_columnnav">
					<a href="currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">卡列表</a>	
                    <a href="currency_recharge_detail.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">充值明细</a>							
				</div>
			</div>	
	<form action="save_set_currency_recharge?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>" method="post" id="upform" name="upform" onSubmit="return subBase()">
		<input type="hidden" class="keyid" name="keyid" value="<?php echo $keyid;?>">
		<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02"> 
				<dt>卡名称：</dt>
				<dd>
					<input type="text" class="name" name="name" value="<?php echo $title;?>" placeholder="" style="width:150px;height:25px;border:1px solid #ccc;border-radius:3px;margin-left:60px;">
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>发卡数量：</dt>
				<dd>
					<input type="number" class="num" name="num" value="<?php echo $num;?>" placeholder="" style="width:150px;height:25px;border:1px solid #ccc;border-radius:3px;margin-left:60px;" min="1">
							<span style="">自动生成等量卡密</span>
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>额：</dt>
				<dd>
					<input class="money" name="money" value="<?php echo $money;?>" type="number" placeholder="" style="width:150px;height:25px;border:1px solid #ccc;border-radius:3px;margin-left:60px;" min="1">
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>有效时间：</dt>
				<dd>
					<p><input class="date_picker" type="text" name="begintime" id="begintime" value="<?php echo $starttime;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'<?php echo date("Y-m-d H:i",time());?>',maxDate:'#F{$dp.$D(\'endtime\')}'});" readonly="readonly" style="width:150px;height:25px;border:1px solid #ccc;border-radius:3px;margin-left:60px;"></p>
					<dt style="width:5px;">至</dt>
					<p style="margin-left:0px;"><span></span><input class="date_picker" type="text" name="endtime" id="endtime" value="<?php echo $endtime;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'begintime\')}'});" readonly="readonly" style="width:150px;height:25px;border:1px solid #ccc;border-radius:3px;margin-left:10px;"></p>
				</dd>
			</dl>
			
		</div>
	<div class="submit_div">
			<input type="submit" class="WSY_button" value="保存"  style="cursor:pointer;">
			<input  style="" type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);"/>
	</div>	
	</form>
	
	</div>
</div> 
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
//var head = <?php echo $head; ?>;
var head = <?php echo $head;?>;
$(".WSY_columnnav_currency").find("a").eq(head).addClass('white1');

function subBase(){
	var name = $(".name").val();
	var num = $(".num").val();
	var money = $(".money").val();
	var account = $(".account").val();
    var begintime = $("#begintime").val();
	var endtime = $("#endtime").val();
	var keyid   = <?php echo $keyid;?>;
	
	var old_begintime = '<?php echo $starttime;?>';
	var old_endtime   = '<?php echo $endtime;?>';
	var custom_name   = "<?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>";
	if(name==null || name == ""){
		alert("卡名称不能为空！");
		return false;
	}
	if(name.length>10){
		alert("卡名称长度不能超过10个字符！");
		return false;
	}
	var member = /^(\+\d+|\d+|\-\d+|\d+\.\d+|\+\d+\.\d+|\-\d+\.\d+)$/;
	if(num=='' || !member.test(num) || num <=0 ||isNaN(num)){
		alert("请输入正确的发卡数量！");
		return false;
	}
	if(num > 100000){
		alert("发卡数量不能超过十万张！");
		return false;
	}
	if(money=='' || !member.test(money) || money <=0 || isNaN(money)){
		alert("请输入正确的"+custom_name+"额！");
		return false;
	}
    if(begintime==null || begintime == "" || endtime==null || endtime==""){
		alert("有效时间不能为空！");
		return false;
	}

	var re_time  = /^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2})$/;
	var re_time2 = /^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/;

	if((!re_time.test(begintime) && !re_time2.test(begintime)) || begintime.toString() == "0000-00-00 00:00" || begintime.toString() == "0000-00-00 00:00:00"){
		alert("开始时间输入格式错误，请重新输入");
		return false;
	}
	if((!re_time.test(endtime) && !re_time2.test(endtime)) || endtime.toString() == "0000-00-00 00:00" || endtime.toString() == "0000-00-00 00:00:00"){
		alert("结束时间输入格式错误，请重新输入");
		return false;
	}	
	
	var oDate1 = new Date();
	var oDate2 = new Date(begintime);
	var oDate3 = new Date(endtime);
	
	oDate1 = oDate1.getTime();
	oDate2 = oDate2.getTime()+1000*60;
	oDate3 = oDate3.getTime()+1000*60;
	
	if((keyid > 0 && begintime !=old_begintime ) || keyid==0){
		if(oDate1 > oDate2){
			alert("开始时间不能小于当前时间");
			return false;
		}
	}

	if((keyid > 0 && endtime !=old_endtime)|| keyid==0){
		if(oDate1 > oDate3){
			alert("结束时间不能小于当前时间");
			return false;
		}
	}
	
	if(oDate2 >= oDate3){
		alert("结束时间必须大于开始时间");
		return false;
	}
}
</script>
</body>
</html>
<?php 

mysql_close($link);
?>