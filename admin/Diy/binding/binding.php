<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');		
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/auth_user.php');
_mysql_query("SET NAMES UTF8");		
$head=0;

$is_web_reg   = -1;//是否开启网页注册
$is_bind_chat = -1;//微信端是否强制绑定
$query = "select is_web_reg,is_bind_chat,is_chat_bind_apph5,is_chat_bind_usedphone from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('W21 Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$is_web_reg   = $row->is_web_reg;
	$is_bind_chat = $row->is_bind_chat;
	$is_chat_bind_apph5 = $row->is_chat_bind_apph5;
	$is_chat_bind_usedphone = $row->is_chat_bind_usedphone;
}
//查询商城是否开启同步区块链基因
$block_chain_gene  = 0;
$sql_chain_gene    = "SELECT block_chain_gene FROM ".WSY_SHOP.".block_chain_setting where customer_id=".$customer_id." LIMIT 1 ";
$result_chain_gene = _mysql_query($sql_chain_gene) or die("sql1 query error : ".mysql_error());
if($val_chain_gene = mysql_fetch_object($result_chain_gene))
{
    $block_chain_gene  = $val_chain_gene->block_chain_gene;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>网页注册和微信端绑定</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/personal_center/personal_center.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/mall_setting/setting.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script>

function change_is_web_reg(a){
	$('#is_web_reg').val(a);
	
	if(a == 1){
		$("#WSY_bot2").click(); 
        //$("#apph5").show();
        //$("#wxbinded").show();
	}
	

}
function change_is_bind_chat(a){
	var is_bind_chat = $('#is_bind_chat').val();
	if (is_bind_chat == 1 && a == 1)
	{
		return false;
	}
	var block_chain_gene = '<?php echo $block_chain_gene; ?>';
	if (block_chain_gene == '1' || block_chain_gene == 1) 
	{
		alert("已开启同步区块链系统用户基因开关，无法关闭强制绑定手机号开关！");
		return;
	}	
	$('#is_bind_chat').val(a);
	
	if(a == 0){
		$("#WSY_bot").click(); 
        //$("#apph5").hide();
        //$("#wxbinded").hide();
        
/*         var apph5 = $('#is_chat_bind_apph5').val();
        var wxbinded = $('#is_chat_bind_usedphone').val();
        
        if(apph5==1){
            $("#WSY_bot3").click(); 
        }
        
        if(wxbinded==1){
            $("#WSY_bot4").click(); 
        } */
        
	}else{
        //$("#apph5").show();
        //$("#wxbinded").show();
    }
}

function change_is_chat_bind_apph5(a){	
	$('#is_chat_bind_apph5').val(a);
    
    if(a == 1){
		$("#WSY_bot4").click();
	}
}

function change_is_chat_bind_usedphone(a){	
	$('#is_chat_bind_usedphone').val(a);
    
    if(a == 1){
		$("#WSY_bot3").click();
	}
}

 function submitV(a){
	 document.getElementById("upform").submit();	
 }	
</script>

<style type="text/css">
    .WSY_remind_dl02 {width: 100% !important;}
</style>

</head>
	
<body>
<form id="upform" action="save_binding.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">

	<div class="WSY_content">
		<div class="WSY_columnbox">

		<?php
			// include("../../../../weixinpl/back_newshops/Diy/binding/head.php"); 
			include($_SERVER['DOCUMENT_ROOT'].'/mshop/admin/Diy/binding/head.php');
		?>		
		<div class="WSY_data">
		<p style="color:red;margin:20px 20px 0 20px;">若开启网页注册,则强制绑定手机会开启,否则出现数据错乱</p>
              
				<div class="WSY_remind_main">	
					<dl class="WSY_remind_dl02">
					<dt>开启网页注册:</dt>
						 <dd>
							<?php if($is_web_reg==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_web_reg(0)" class="WSY_bot" id="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_web_reg(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_web_reg(0)" class="WSY_bot"  id="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_web_reg(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>			
					</dl>
					
					<dl class="WSY_remind_dl02">
					<dt>微信端强制绑定:</dt>
						 <dd>
							<?php if($is_bind_chat==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li <?php if ($block_chain_gene == 0) {echo 'class="WSY_bot"';} ?> onclick="change_is_bind_chat(0)"  style="left: 0px;"></li>
								<span onclick="change_is_bind_chat(1)" <?php if ($block_chain_gene == 0) {echo 'class="WSY_bot2"';} ?> id="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_bind_chat(0)" <?php if ($block_chain_gene == 0) {echo 'class="WSY_bot"';} ?> style="display: none; left: 30px;"></li>
								<span onclick="change_is_bind_chat(1)" <?php if ($block_chain_gene == 0) {echo 'class="WSY_bot2"';} ?> id="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						
					</dl>
                    
					<!--dl class="WSY_remind_dl02" id="apph5" style="<?php if(!$is_bind_chat){ ?>display: none; <?php } ?>">
					<dt>开启微信端绑定APP和H5注册账号:</dt>
						 <dd>
							<?php if($is_chat_bind_apph5==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_chat_bind_apph5(0)" class="WSY_bot" id="WSY_bot3" style="left: 0px;"></li>
								<span onclick="change_is_chat_bind_apph5(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_chat_bind_apph5(0)" class="WSY_bot" id="WSY_bot3" style="display: none; left: 30px;"></li>
								<span onclick="change_is_chat_bind_apph5(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
                        <span style="color:red;">此功能使用会将微信端的数据清空，请谨慎使用</span>
						
					</dl>

					<dl class="WSY_remind_dl02" id="wxbinded" style="<?php if(!$is_bind_chat){ ?>display: none; <?php } ?>">
					<dt>开启微信端绑定已绑过微信的手机号码:</dt>
						 <dd>
							<?php if($is_chat_bind_usedphone==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
								<li onclick="change_is_chat_bind_usedphone(0)" class="WSY_bot" id="WSY_bot4" style="left: 0px;"></li>
								<span onclick="change_is_chat_bind_usedphone(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_chat_bind_usedphone(0)" class="WSY_bot" id="WSY_bot4" style="display: none; left: 30px;"></li>
								<span onclick="change_is_chat_bind_usedphone(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>						
							<?php } ?>
						</dd>
						<span style="color:red;">此功能适用于公众号被封使用的，请谨慎使用</span> 
					</dl-->                    
									
					<input type="hidden" name="is_bind_chat" id="is_bind_chat" value="<?php echo $is_bind_chat; ?>" />
					<input type="hidden" name="is_web_reg" id="is_web_reg" value="<?php echo $is_web_reg; ?>" />
					<input type="hidden" name="is_chat_bind_apph5" id="is_chat_bind_apph5" value="<?php echo $is_chat_bind_apph5; ?>" />
					<input type="hidden" name="is_chat_bind_usedphone" id="is_chat_bind_usedphone" value="<?php echo $is_chat_bind_usedphone; ?>" />
					<div style="clear:both"></div>
							   
				  </form>
				</div>

				<div class="WSY_text_input01" style="margin-left: 44%;">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
				</div>			
			
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>
	</div>
</form>	
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>