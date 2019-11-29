<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');
  $customer_id = passport_decrypt($customer_id);
  require('../../../../weixinpl/back_init.php');
  $customer_id_2=$customer_id;
  $link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
  mysql_select_db(DB_NAME) or die('Could not select database');
  _mysql_query("SET NAMES UTF8");
 require('../../../../weixinpl/proxy_info.php');
 $query = 'SELECT * FROM sf_import where  customer_id='.$customer_id;
 

 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
	$ison=0;
	$head="";
	$token="";
	$authToken="";
	$businessLogo="";
	$Sendcompany="";
	$Sendconcact="";
	$Sendtelphone="";
	$Sendmobile="";
	$Sendcountry="";
	$Sendprovinoce="";
	$Sendcitycode="";
	$Sendcity="";
	$Sendcounty="";
	$Sendzipcode="";
	$Sendaddress="";
	$monthlyAccount="";
	$customsBatchNumber="";
	$taxSetAccounts="";
 while ($row = mysql_fetch_object($result)) {
	$ison=$row->ison;
	$head=$row->head;
	$token=$row->token;
	$checkWord=$row->checkWord;
	$authToken=$row->authToken;
	$businessLogo=$row->businessLogo;
	$Sendcompany=$row->Sendcompany;
	$Sendconcact=$row->Sendconcact;
	$Sendtelphone=$row->Sendtelphone;
	$Sendmobile=$row->Sendmobile;
	$Sendcountry=$row->Sendcountry;
	$Sendprovinoce=$row->Sendprovinoce;
	$Sendcitycode=$row->Sendcitycode;
	$Sendcity=$row->Sendcity;
	$Sendcounty=$row->Sendcounty;
	$Sendzipcode=$row->Sendzipcode;
	$Sendaddress=$row->Sendaddress;
	$monthlyAccount=$row->monthlyAccount;
	$customsBatchNumber=$row->customsBatchNumber;
	$taxSetAccounts=$row->taxSetAccounts;
	break;
 }
 
  
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<!--<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script> -->
<script type="text/javascript" src="../../../common/utility.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">

<style>
body{background: #e4e4e4;}
a:hover{text-decoration: none;}
.rows input{
	width:200px;
	height:24px;
	border-radius:2px;
	padding-left:5px;
}
.button_blue{width: 100px;height:32px;cursor: pointer;font-size: 14px;display: block;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin:20px 0 20px 49px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.w50{width: 40%;text-align: left;float: left;}
.rows{margin-top: 20px;margin-left: 50px;}
.rows label{width: 100px;display: inline-block;font-size: 14px;text-align:right;}
.WSY_columnbox{padding-bottom:20px;}
</style>
</head>
<body> 
	<div >  
		<div class="WSY_content">  
			<div class="WSY_columnbox"> 
				<?php 
				$header = 2;
				include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Distribution/express/head.php");
				
				?>
				<form action="savesf_import.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>"  method="post" id="upform" name="upform">
					<div class="main">
						<div class="WSY_remind_main">
							<dl class="WSY_remind_dl02" style="margin-left:81px;"> 
								<dt>快递开关：</dt>
								<dd> 
									<?php if($ison){ ?>
									<ul style="background-color: rgb(255, 113, 112);">
										<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
										<li onclick="change_ison(0)" class="WSY_bot" style="left: 0px;"></li>
										<span onclick="change_ison(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
									</ul>
									<?php }else{ ?>
									<ul style="background-color: rgb(203, 210, 216);">
										<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
										<li onclick="change_ison(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
										<span onclick="change_ison(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
									</ul>						
									<?php } ?>
								</dd>
								<input type="hidden" name="ison" id="ison" value="<?php echo $ison; ?>" />
							</dl>
						</div>
						<div class="w50">
							<div class="rows">
								<label>客户代码：</label>
								<span class="input">
									<input type=text value="<?php echo $head ?>" name="head" id="head" />
								</span>
							</div>
							<div class="rows" >
								<label>校验码：</label>
								<span class="input">
									<input type=text value="<?php echo $checkWord ?>" name="checkWord" id="checkWord" />
								</span>
							</div>
							<div class="rows" > 
								<label>令牌：</label>
								<span class="input">
									<input type=text value="<?php echo $token ?>" name="token" id="token" />
								</span>
							</div>
							<div class="rows" >
								<label>认证令牌：</label>
								<span class="input">
									<input type=text value="<?php echo $authToken ?>" name="authToken" id="authToken" />
								</span>
							</div>
							<div class="rows" >
								<label>发货公司：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendcompany ?>" name="Sendcompany" id="Sendcompany" />
								</span>
							</div>
							<div class="rows" >
								<label>发货联系人：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendconcact ?>" name="Sendconcact" id="Sendconcact" />
								</span>
							</div>
							<div class="rows" >
								<label>发货人电话：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendtelphone ?>" name="Sendtelphone" id="Sendtelphone" />
								</span>
							</div>
							<div class="rows" >
								<label>发货人手机：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendmobile ?>" name="Sendmobile" id="Sendmobile" />
								</span>
							</div>
							<div class="rows" >
								<label>发货国家：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendcountry ?>" name="Sendcountry" id="Sendcountry" />
								</span>
							</div>
						</div>
						<div class="w50">
							<div class="rows" >
								<label>发货省：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendprovinoce ?>" name="Sendprovinoce" id="Sendprovinoce" />
								</span>
							</div>
							<div class="rows" >
								<label>发货市：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendcity ?>" name="Sendcity" id="Sendcity" />
								</span>
							</div>
							<div class="rows" >
								<label>发货区(镇)：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendcounty ?>" name="Sendcounty" id="Sendcounty" />
								</span>
							</div>
							<div class="rows" >
								<label>发货地址：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendaddress ?>" name="Sendaddress" id="Sendaddress" />
								</span>
							</div>
							<div class="rows" >
								<label>发货地区代码：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendcitycode ?>" name="Sendcitycode" id="Sendcitycode" />
								</span>
							</div>
							<div class="rows" >
								<label>发货地区邮编：</label>
								<span class="input">
									<input type=text value="<?php echo $Sendzipcode ?>" name="Sendzipcode" id="Sendzipcode" />
								</span>
							</div>
							<div class="rows" >
								<label>月结卡号：</label>
								<span class="input">
									<input type=text value="<?php echo $monthlyAccount ?>" name="monthlyAccount" id="monthlyAccount" />
								</span>
							</div>
							<div class="rows" >
								<label>报关批次：</label>
								<span class="input">
									<input type=text value="<?php echo $customsBatchNumber ?>" name="customsBatchNumber" id="customsBatchNumber" />
								</span>
							</div>
							<div class="rows" >
								<label>税金结算账号：</label>
								<span class="input">
									<input type=text value="<?php echo $taxSetAccounts ?>" name="taxSetAccounts" id="taxSetAccounts" />
								</span>
							</div>
						</div>
						<div class="clear"></div>
						<input type=button class="WSY_button"  value="提交" onclick="submitV();" style="border:0 none;border-radius:3px;display:block;float:left;margin-left:88px;"/>
						<input type=hidden name="customer_id" value="<?php echo $customer_id ?>" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script> 
<?php
mysql_close($link);
?>
<script> 
function change_ison(obj){
	$("#ison").val(obj);
}
 function submitV(){
	 var sendtelphone = $("#Sendtelphone").val();
	 var sendmobile = $("#Sendmobile").val();
	 if(sendtelphone == ''){
		 alert("请输入发货人电话！");
		 return;
	 }else if(!chk400(sendtelphone) && !chkTelephone(sendtelphone)){
		 alert("请输入正确的发货人电话！");
		 return;
	 }
	 if(sendmobile == ''){
		 alert("请输入发货人手机！");
		 return;
	 }else if(!chkPhoneNumber(sendmobile)){
		 alert("请输入正确的发货人手机！");
		 return;
	 }
    document.getElementById("upform").submit();
 } 
</script>
</body>
</html>