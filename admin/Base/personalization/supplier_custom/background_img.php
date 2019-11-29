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

$background_img="";
$supplier_id = $configutil->splash_new($_GET["supplier_id"]);
if(!empty($_GET["background_img"])){
    $background_img=$configutil->splash_new($_GET["background_img"]);
}else{
	if($customer_id>0){
		$query2="select background_img from weixin_commonshop_supply_diy_template where isvalid=true and id=".$temid." and supplier_id=".$supplier_id." and customer_id=".$customer_id;
		$result2 = _mysql_query($query2) or die('Query failed2: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
			$background_img=$row2->background_img;   
		}
	}
}
$background_img_arr = explode('|',$background_img);
if( $background_img != '' ){
	$background_img_num = count($background_img_arr);
}else{
	$background_img_num = 0;
}
$op="";
if(!empty($_GET["op"])){
	$op = $configutil->splash_new($_GET["op"]);
	$background_img_str = '';
	if($op=="del"){  
		$temid 	= $configutil->splash_new($_GET["temid"]);
		$img_id = $configutil->splash_new($_GET["img_id"]);
		
		for( $i=0;$i<$background_img_num;$i++ ){
			if( $img_id != $i && !empty($background_img_arr[$i]) ){
				$background_img_str .= $background_img_arr[$i].'|';
			}
		}
		$background_img_str = substr($background_img_str,0,-1);
		$query="update weixin_commonshop_supply_diy_template set background_img='".$background_img_str."' where id=".$temid." and supplier_id=".$supplier_id."";	
		_mysql_query($query);
		
		$background_img_arr = explode('|',$background_img_str);
		if( $background_img_str != '' ){
			$background_img_num = count($background_img_arr);
		}else{
			$background_img_num = 0;
		}
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

<div id="products" class="r_con_wrap" style="background:none">
<span class="input">
<span class="upload_file">  
	<div>
	   <form action="save_background_img.php?customer_id=<?php echo $customer_id_en; ?>" id="frm_img" enctype="multipart/form-data" method="post">
			<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id_en; ?>" />
			<input type=hidden name="temid" id="temid" value="<?php echo $temid; ?>" />
			<input type=hidden name="supplier_id" id="supplier_id" value="<?php echo $supplier_id;?>" >
			<label class="fi-name" style="margin-left:0">店铺背景图：<br/>640*320像素</label>
			<div class="uploader white">
			<?php
				for( $j=0;$j<$background_img_num;$j++ ){
			?>
				<div class="imgnav-select" style="display:inline-block;margin-right:10px;">
					<input type="file" accept="image/*" name="upfile[]" id="upfile" class="upfile" value="Submit" size="20" onchange="uploading(this)"/>
					<?php if( !empty($background_img_arr[$j]) ){ ?>
					<span style="position:relative;top:11px;left:81px;cursor:pointer;z-index:99;" onclick="delImg(<?php echo $temid; ?>,<?php echo $j;?>);"><img src="../../../Common/images/Base/home_decoration/operating_icon/guanbi.png" alt="" ></span>
					<div id="background_img" style="display:block;text-align:center;">
						<img style="width:90px;height:90px;display:inline-block;" src="<?php echo $new_baseurl.$background_img_arr[$j]; ?>">
					</div>	  
					<?php }else{?>
					<div id="default"><a ><img style="height:90px;width:90px;" src="images/shop_bgimg.png"></a></div>
					<?php }?>	
				</div>
			<?php
				}
				if( $background_img_num < 5 && $background_img_num > 0 ){
			?>
				<div class="imgnav-select" style="display:inline-block;">
					<input type="file" accept="image/*" name="upfile[]" id="upfile" class="upfile" value="Submit" size="20" onchange="uploading(this)"/>
					<div id="default"><a ><img style="height:90px;width:90px;" src="images/icon_image_add.png"></a></div>	
				</div>
			<?php
				}else if( $background_img_num == 0 ){
			?>
				<div class="imgnav-select" style="display:inline-block;">
					<input type="file" accept="image/*" name="upfile[]" id="upfile" class="upfile" value="Submit" size="20" onchange="uploading(this)" style="z-index:11111;"/>
					<div id="default" style="background: url('images/shop_bgimg.png') no-repeat;background-size: 90px 90px;opacity: 0.5;">
						<a><img style="height:90px;width:90px;" src="images/icon_image_add.png"></a>
					</div>	
				</div>
			<?php
				}
			?>
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
	var element = document.getElementsByClassName("upfile");  
	for( var i=1;i<=element.length;i++ ){
		if("\v"=="v")  
		{  
			element[i].onpropertychange = uploadHandle(i);  
		}  
		else  
		{  
			element[i].addEventListener("change",uploadHandle(i),false);  
		}  
	}
	// if("\v"=="v")  
	// {  
		// element.onpropertychange = uploadHandle;  
	// }  
	// else  
	// {  
		// element.addEventListener("change",uploadHandle,false);  
	// }  

	function uploadHandle(i)  
	{  
		if(element[i].value)  
		{  
		  
		  $("#frm_img").submit();

		}  
	}  

} 
function uploading(obj)  
	{  
		if(obj.value)  
		{  
		  
		  $("#frm_img").submit();

		}  
	}  

function delImg(id,img_id){
	if(window.confirm('确定删除？')){
		url = "background_img.php?op=del&temid="+id+"&customer_id=<?php echo $customer_id_en; ?>&supplier_id=<?php echo $supplier_id;?>&img_id="+img_id;
		document.location= url;
	}else{
		return false;
	}
}
  
</script>
 
<script type="text/javascript">  
    // upload();  
</script>  
</body>
</html>