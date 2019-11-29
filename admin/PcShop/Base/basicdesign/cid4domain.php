<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/common/common_ext.php');
	
    $key        = '';
    $timestamp  = time();
    $domain     = '';
    $find_customers = mysql_find('SELECT `id`,`login_password`,`cid4domain` FROM customers WHERE id='.$customer_id.'');
	if($find_customers){
        $key    = md5($find_customers['id'].$find_customers['login_password'].$timestamp);
        $domain = $find_customers['cid4domain'];
    }
    //print_r($_SERVER['HTTP_HOST']);die();
    $pattern = "/\..+\..+/";
    preg_match_all($pattern, $_SERVER['HTTP_HOST'], $ou_str);
    $top_domain = $ou_str[0][0]; 
    if($domain!=''){
        $domain = str_replace($top_domain,'',$domain); 
    }
    //$select_customers = mysql_select('SELECT `id`,`name`,`cid4domain` FROM customers WHERE isvalid=true LIMIT 0,20');
    
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../Common/js/Base/basicdesign/layer.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../../common/utility.js"></script>

<title>PC商城基本资料</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			include("../../../../../weixinpl/back_newshops/PcShop/Base/basicdesign/basic_head.php"); 
		?>	
    
    <form action="<?php echo Protocol.$_SERVER['HTTP_HOST']; ?>/shop/index.php/Home/WeixinplSet/setCustomid4Domain" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02"> 
				<dt style="margin-top: 20px;">要绑定的域名</dt>
				<dl class="WSY_remind_dl02">
                    <dd>
                        <input size="17" name="cid4domain_view" style="width:208;border:1px solid; font-size:9pt; background-color:#ffffff; height:18;margin-top: 5px;margin-bottom: 5px;"  id="cid4domain_view" type="text" value="<?php echo $domain;?>"><span style="vertical-align: middle;display: inline-block;padding-top: 5px;"><?php echo $top_domain;?></span>
                    </dd>	
				</dl>
			</dl>
			<input type="hidden" value="<?php echo $key;?>" name="key" id="key" /> 
            <input type="hidden" value="<?php echo $timestamp;?>" name="timestamp" id="timestamp" /> 
            <input type="hidden" value="<?php echo $customer_id;?>" name="customer_id" id="customer_id" /> 
            <input type="hidden" value="<?php echo $customer_id;?>" name="set_customer_id" id="set_customer_id" /> 
            <input type="hidden" value="<?php echo $top_domain;?>" name="top_domain" id="top_domain" /> 
            <input type="hidden" value="" name="cid4domain" id="cid4domain" /> 
		</div>
		
	</form>    
    <div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="return saveData(this);" style="cursor:pointer;">
		</div>
	</div>
	
</div> 
<script type="text/javascript" src="../../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script>
function saveData(){
    $('#cid4domain').val($('#cid4domain_view').val()+$('#top_domain').val());
	document.getElementById("saveFrom").submit();	
}

</script>
</body>
</html>