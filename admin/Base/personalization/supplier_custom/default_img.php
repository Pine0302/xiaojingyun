<?php
//显示首页分类图
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");

$temid = $configutil->splash_new($_GET["temid"]);
$cat_op=$_POST["cat_op"];

$default_img="";
$supplier_id = $configutil->splash_new($_GET["supplier_id"]);
if(!empty($_GET["default_img"])){
    $default_img=$configutil->splash_new($_GET["default_img"]);
}else{
	if($customer_id>0){
		$query2="select default_img from weixin_commonshop_supply_diy_template where isvalid=true and id=".$temid." and supplier_id=".$supplier_id." and customer_id=".$customer_id;
		$result2 = _mysql_query($query2) or die('Query failed2: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
			$default_img=$row2->default_img;   
		}
	}
}
$op="";
if(!empty($_GET["op"])){
	$op = $configutil->splash_new($_GET["op"]);
	if($op=="del"){  
		$temid = $configutil->splash_new($_GET["temid"]);
		$query="update weixin_commonshop_supply_diy_template set default_img='' where id=".$temid." and supplier_id=".$supplier_id."";	
		_mysql_query($query);
		$default_img="";
	}  
}

$new_baseurl = Protocol.$http_host; 
 
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>

<script type="text/javascript" src="../../../Common/js/Base/personalization/jquery.uploadify-3.1.min.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/default_img.css">

</head>
<body style="font-size:12px;">
<?php if(!empty($default_img)){ ?>
<span style="position:absolute;top:9px;left:203px;cursor:pointer;z-index:99;" onclick="delImg(<?php echo $temid; ?>);"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png" alt="" ></span>
<?php }?>
<div id="products" class="r_con_wrap" style="background:none">
<span class="input">
<span class="upload_file">  
	<div>
	   <form action="save_default_img.php?customer_id=<?php echo $customer_id_en; ?>" id="frm_img" enctype="multipart/form-data" method="post">
			<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id_en; ?>" />
			<input type=hidden name="temid" id="temid" value="<?php echo $temid; ?>" />
			<input type=hidden name="supplier_id" id="supplier_id" value="<?php echo $supplier_id;?>" >
			<label class="fi-name" style="margin-left:0">异步加载背景图片：</label>
			<div class="uploader white">
				<div class="imgnav-select">
					<input type="file" name="upfile" id="upfile" value="Submit" size="20" onchange="uploading()"/>
					<?php if(!empty($default_img)){ ?>
					<div id="default_img" style="display:block;text-align:center;">
						<img style="width:90px;height:90px;display:inline-block;" src="<?php echo $new_baseurl.$default_img; ?>">
					</div>	  
					<?php }else{?>
					<div id="default"><a ><img style="height:90px;width:90px;" src="images/loading.gif"></a></div>
					<?php }?>	
				</div>
			</div>  
		</form>

		<div class="clear"></div>
	</div>  
</span>
<div class="clear"></div>
</div>
<?php 
    
mysql_close($link);
?>
<script type="text/javascript">  
function upload(){  
	var element = document.getElementById("upfile");  
	if("\v"=="v")  
	{  
		element.onpropertychange = uploadHandle;  
	}  
	else  
	{  
		element.addEventListener("change",uploadHandle,false);  
	}  

	function uploadHandle()  
	{  
		if(element.value)  
		{  
		  
		  $("#frm_img").submit();

		}  
	}  

} 

function delImg(id){
	if(window.confirm('确定删除？')){
		url = "default_img.php?op=del&temid="+id+"&customer_id=<?php echo $customer_id_en; ?>&supplier_id=<?php echo $supplier_id;?>";
		document.location= url;
	}else{
		return false;
	}
}
  
</script>
 
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>