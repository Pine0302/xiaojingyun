<?php
 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$new_baseurl = $http_host;  
$head=1;//头部文件0支付方式，1微信支付,2支付宝,3财务通,4通联支付
$INDEX=0;
if(!empty($_GET["INDEX"])){
	$INDEX = $configutil->splash_new($_GET["INDEX"]);
}
$query = "select id,appid,appsecret,apiclient_cert_path,apiclient_key_path,paysignkey,partnerid,version,partnerkey from weixinpays where isvalid=true and customer_id=".$customer_id;
//echo $query;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$weixinpay_id = -1;//微信支付信息ID
$appid="";
$appsecret="";
$paysignkey ="";
$partnerid="";
$partnerkey="";
$version = 1;//接口版本
$apiclient_cert_path="";//退款和红包证书id
$apiclient_key_path="";//退款和红包证书key
while ($row = mysql_fetch_object($result)) {
	$weixinpay_id = $row->id;
	$appid = $row->appid;
	$appsecret=$row->appsecret;
	$paysignkey = $row->paysignkey;
	$partnerid = $row->partnerid;
	$partnerkey = $row->partnerkey;
	$version = $row->version;
	$apiclient_cert_path = $row->apiclient_cert_path;
	$apiclient_key_path=$row->apiclient_key_path;
	break;
}

$type = $_GET['type'];//为city则表示城市商圈
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/pay_set/weixin_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>


<title>微信支付</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
.WSY_commonbox02{border:solid 1px #ccc;}
</style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content">

		<!--列表内容大框开始-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
				include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php"); 
			?>
			<!--列表头部切换结束-->
            
        <!--微信支付设置代码开始-->
			<div class="WSY_data">
                <!--列表按钮开始-->
                <div class="WSY_list">
                    <li class="WSY_left"><a>微信支付设置</a></li>
                </div>
                <!--列表按钮结束-->
				<form action="save_weixinpay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
				<input type=hidden name="weixinpay_id" id="weixinpay_id" value="<?php echo $weixinpay_id; ?>" />
					<ul class="WSY_commonbox" id="WSY_commonbox">
						<li class="WSY_common01">如何申请支付：</li>
						<li>
							<a href="../../../weixin_pay/公众号支付商户接入指南(商户版）.pdf" target="_blank">
								<img src="../../Common/images/Base/pay_set/merchants.png">
							</a>
						</li>
						<li>
							<a href="../../../weixin_pay/商户接入指引.pdf" target="_blank">
								<img src="../../Common/images/Base/pay_set/common.png">
							</a>
						</li>
					</ul>
					
					<ul class="WSY_commonbox">
						<li class="WSY_weight" style="width:142px;text-align:right;line-height:24px;">版本类型：</li>
						<li>
							<select class="version" name="version" onchange="selPayType(this.value);">
								<option value=1 <?php if($version==1){?> selected <?php } ?>>老版本</option>
								<option value=2 <?php if($version==2){?> selected <?php } ?>>新版本</option>
							</select>	
						</li>
						<style>
							.version{border:solid 1px #dadada;border-radius:2px;padding:3px;height:24px;width:100px;margin-left:-7px;}
						</style>
					</ul>
					<div class="WSY_commonbox01">
						<li>
							<span>APPID：</span>
							<input class="text_input" type="text" name="appid" value="<?php echo $appid; ?>">
						</li>
						<li>
							<span>AppSecret：</span>
							<input class="text_input" id="appsecret" type="text" name="appsecret" value="<?php if(!empty($appsecret)){echo substr_replace($appsecret,"*********************",2,20);} ?>">
						</li>
						<li>
							<span>PaySignKey(Key)：</span>
							<input class="text_input" id="paysignkey" type="text" name="paysignkey" value="<?php if(!empty($paysignkey)){echo substr_replace($paysignkey,"*********************",2,20);} ?>">
							<a class="WSY_red">(如果是新微信接口，请填写在商户平台设置的32位密钥)</a>
						</li>
						<li>
							<span>商户号(PartnerID)：</span>
							<input  class="text_input"type="text" name="partnerid" value="<?php echo $partnerid; ?>">
						</li>
						<li>
							<span>子商户号(SubID)：</span>
							<input  class="text_input" type="text" name="sub_mch_id" value="<?php echo ''; ?>">
							<a class="WSY_red">(非服务商请勿填写)</a>
						</li>
						<li>
							<span>标价币种(FeeType)：</span>
							<input  class="text_input" type="text" name="fee_type" value="<?php echo ''; ?>">
							<a class="WSY_red">(非境外服务商请勿填写)</a>
						</li> 
						<li id="div_partnerkey" <?php if($version==2){?>style="display:none"<?php  }else{ ?>style="display:block"<?php } ?>>
							<span>初始密钥(PartnerKey)：</span>
							<input class="text_input" type="text" name="partnerkey" value="<?php echo $partnerkey; ?>">
						</li>
					</div>
					<div  class="WSY_commonbox02" id="div_refund">
						<div class="WSY_common02">
							<a>退款需要以下证书</a><!--每个设置项标题-->
						</div>
						<ul class="WSY_commonbox">
							<li>apiclient_cert.pem</li>
							<!--上传文件代码开始-->
								<div class="uploader white" id="WSY_commondiv">
									<input type="text" class="filename"  value="<?php if($apiclient_cert_path!=""){echo $apiclient_cert_path;}else{echo "请选择文件...";} ?>" readonly/>
									<input type="button" name="file" class="button" value="上传..."/>
									<input type="file" name="apiclient_cert_path" size="30"/>
									<input type=hidden name="apiclient_cert_path_v" value="<?php echo $apiclient_cert_path; ?>" />
								</div>
								<!--上传文件代码结束-->
							<span><?php echo $apiclient_cert_path; ?></span>  
						</ul>
						<ul class="WSY_commonbox">
							<li>apiclient_key.pem</li>
							<!--上传文件代码开始-->
								<div class="uploader white" id="WSY_commondiv">
									<input type="text" class="filename"  value="<?php if($apiclient_key_path!=""){echo $apiclient_key_path;}else{echo "请选择文件...";} ?>" readonly/>
									<input type="button" name="file" class="button" value="上传..."/>
									<input type="file" name="apiclient_key_path" size="30"/>
									<input type=hidden name="apiclient_key_path_v" value="<?php echo $apiclient_key_path; ?>" />   
								</div> 
								<!--上传文件代码结束-->
							<span><?php echo $apiclient_key_path; ?></span>
						</ul>
					</div>
				</form>
                <div class="WSY_text_input" id="WSY_text_input"><button onclick="submitV(this);" class="WSY_button">提交</button><br class="WSY_clearfloat"></div>
			</div>
        <!--微信支付设置代码结束-->
		</div>
	</div>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Base/pay_set/weixin_set.js"></script>
<script>
selPayType(<?php echo $version; ?>);
</script>
</body>
</html>