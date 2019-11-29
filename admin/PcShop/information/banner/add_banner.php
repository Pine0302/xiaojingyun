<?php
	header("Content-type: text/html; charset=utf-8"); 
	require('../../../../../weixinpl/config.php');
	require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
	require('../../../../../weixinpl/back_init.php');
	$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	_mysql_query("SET NAMES UTF8");
	require('../../../../../weixinpl/proxy_info.php');
	require_once('../../../../../weixinpl/common/utility_common.php');
	
	$keyid = 0;
	$op = "";

	if(!empty($_GET["keyid"])){
		$keyid = passport_decrypt($configutil->splash_new($_GET["keyid"]));
	}
	$pagenum = 1;
	if(!empty($_GET["pagenum"])){
	   $pagenum = $configutil->splash_new($_GET["pagenum"]);
	}
	
	if(!empty($_GET["op"])){
		$op = $configutil->splash_new($_GET["op"]);
	}
	
	if($op=="del"){
		$query = 'update pcshop_package_banners set isvalid=false where id='.(int)$keyid;
		_mysql_query($query);
		$error =mysql_error();
		mysql_close($link);
		//echo $error;
		echo "<script>location.href='package_banner.php?customer_id=".$customer_id_en."&pagenum=".$pagenum."';</script>";
		return;
	}

	$imgurl  = "";
    $imglink = "";
    $sort = 0;

    if($keyid>0){
    
        $query = 'SELECT banner_imgurl,banner_url,sort FROM pcshop_package_banners where id='.$keyid;
        $result = _mysql_query($query) or die('Query failed02: ' . mysql_error());  
    	while ($row = mysql_fetch_object($result)) {
    		$imgurl  	= $row->banner_imgurl;
    		$imglink 	= $row->banner_url;
    		$sort 		= $row->sort;
        }
    }
	//获取链接
	$link = new shopLink_Utlity($customer_id);
	$link_arr = $link->getSelectLink(array(1,2,3,4),1);
	$fixedlink = $link_arr['fixedlink'];
	$brandarr = $link_arr['brand_arr'];
	$type_arr = $link_arr['type_arr'];
	$template_link = $link_arr['template_link'];
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>添加图片</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../../js/tis.js"></script>
<script type="text/javascript" src="../../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../../common/js/layer/layer.js"></script>
<script>
 function submitV(a){
	if($(a).hasClass("disable")){
        return;
    }
	<?php if( $imgurl == '' ){?>
	var upfile = document.getElementById("upfile").value;
	if(upfile==""){
	    alert('请上传图片!');
	    return;
	}
	<?php }?>
	$(a).addClass('disable').val("提交中...");
    document.getElementById("upform").submit();
 }

</script>
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content">  
       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    	<!--列表头部切换开始-->
    	<div class="WSY_column_header">
        	<div class="WSY_columnnav">
            	<a class="white1">添加图片</a>
            </div>
        </div>
        <form action="save_banner.php?customer_id=<?php echo $customer_id_en ?>&pagenum=<?php echo $pagenum ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
			<div class="WSY_data">		
				<dl class="WSY_member">
                    <dt>图片链接</dt>
                    <dd><input type=text value="<?php echo $imglink ?>" name="imglink" id="imglink" style="width:400px;" /></dd>
                </dl>

                <dl class="WSY_member">
					<dt>选择链接内容</dt>
					<dd>
                        <select name="link_type" id="link_type" onchange="get_column_list()">
                            <?php 
								if( !empty($fixedlink) ){
									foreach( $fixedlink as $key => $value ){
										$option_arr = explode('_',$value);
										$option_val = $option_arr[0];
										$option_name = $option_arr[1];
							?>
							<option value="<?php echo $option_val;?>"><?php echo $option_name;?></option>
							<?php 	}
								}
								if( !empty($type_arr['-1']) ){
							?>
							<option value="1">产品分类</option>
							<?php
								}
								if( !empty($brandarr) ){
							?>
							<option value="2">品牌供应商店铺</option>
							<?php
								}
								if( !empty($template_link) ){
							?>
							<option value="3">活动页模板</option>
							<?
								}
							?>
                        </select>
						<!-- 产品分类 -->
						<select id="product_type" style="display:none;" onchange="change_product_type()">
							<?php
								foreach( $type_arr['-1'] as $key => $value ){
									$option_arr = explode('_',$value);
									$option_val = $option_arr[0];
									$option_name = $option_arr[1];
							?>
							<option value="<?php echo $option_val;?>"><?php echo $option_name;?></option>
								<?php
									if( !empty($type_arr[$option_val]) ){
										foreach( $type_arr[$option_val] as $key2 => $value2 ){
											$option_arr = explode('_',$value2);
											$option_val = $option_arr[0];
											$option_name = $option_arr[1];
								?>
							<option value="<?php echo $option_val;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
								<?php
									if( !empty($type_arr[$option_val]) ){
										foreach( $type_arr[$option_val] as $key3 => $value3 ){
											$option_arr = explode('_',$value3);
											$option_val = $option_arr[0];
											$option_name = $option_arr[1];
								?>
							<option value="<?php echo $option_val;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
								<?php
									if( !empty($type_arr[$option_val]) ){
										foreach( $type_arr[$option_val] as $key4 => $value4 ){
											$option_arr = explode('_',$value4);
											$option_val = $option_arr[0];
											$option_name = $option_arr[1];
								?>
							<option value="<?php echo $option_val;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
								<?php
										}
									}
										}
									}
										}
									}
								}
							?>
						</select>
						<!-- 品牌供应商店铺 -->
						<select id="brand_supply" style="display:none;" onchange="change_brand_supply()">
							<?php
								foreach( $brandarr as $key => $value ){
									$option_arr = explode('_',$value);
									$option_val = $option_arr[0];
									$option_name = $option_arr[1];
							?>
							<option value="<?php echo $option_val;?>"><?php echo $option_name;?></option>
							<?php
								}
							?>
						</select>
						<!-- 模板 -->
						<select id="template_link" style="display:none;" onchange="change_template_link()">
							<?php
								foreach( $template_link as $key => $value ){
									$option_arr = explode('_',$value);
									$option_val = $option_arr[0];
									$option_name = $option_arr[1];
							?>
							<option value="<?php echo $option_val;?>"><?php echo $option_name;?></option>
							<?php
								}
							?>
						</select>
						<!-- 产品 -->
						<select id="products" style="display:none;" onchange="change_product()">
							
						</select>
                    </dd>
				</dl>
				<dl class="WSY_member">
					<dt>排序</br></dt>
					<dd><input type="text" name="sort" value="<?php echo $sort;?>" onkeyup="check_num(this)"/><span style="color:red">（数值越大，轮播广告排的越前面）</span></dd>
				</dl>
				<dl class="WSY_member">
                    <dt>图片</dt>
                    <div class="WSY_memberimg">
						<?php if($imgurl!=""){?>
                        <img src="<?php echo $imgurl; ?>" style="width:160px;height:100px;">
						<?php }else{ ?>
						<img src="../../../../common/images_V6.0/table_icon/photo.png" style="width:150px;height:150px;">
						<?php } ?>
                        <span>(建议尺寸：1200*260)</span>
                        <!--上传文件代码开始-->
                        <div class="uploader white">
                            <input type="text" class="filename" readonly/>
                            <input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="upfile" id="upfile" type=file value="<?php echo $imgurl ?>">
							<input type=hidden value="<?php echo $imgurl ?>" name="imgurl" id="imgurl" /> 
                        </div>
                        <!--上传文件代码结束-->
                    </div>
                </dl>
							
				<div class="WSY_text_input01">
				  <div class="WSY_text_input"><input type="button" class="WSY_button"  value="提交" onclick="submitV(this);" /></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);"/></div>
				</div>
				<input type="hidden" name="keyid" value="<?php echo passport_encrypt((string)$keyid) ?>" />
			</div>
		</form>	
	</div>
</div>

<?php

mysql_close($link);
?>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<script>
var customer_id_en = '<?php echo $customer_id_en; ?>';
//写入链接start
function get_column_list(){
	var link_type = $('#link_type').val();	//链接类型
	var url = '';
	
	$('#product_type').hide();
	$('#brand_supply').hide();
	$('#template_link').hide();
	$('#products').hide();
	
	if( link_type > 0 ){
		switch( link_type ){
			case '1':
				$('#product_type').show();
				
				change_product_type();
			break;
			case '2':
				$('#brand_supply').show();
				
				change_brand_supply();
			break;
			case '3':
				$('#template_link').show();
				
				change_template_link();
			break;
		}
	} else {
		switch( link_type ){
			case '-2':
				url = '/shop/index.php/Home/Index/index';
			break;
			case '-3':
				url = '/shop/index.php/Home/Product/ProductList';
			break;
			case '-4':
				url = '/shop/index.php/Home/Cart/order_cart';
			break;
			case '-5':
				url = '/shop/index.php/Home/My/index';
			break;
			case '-6':
				url = '/shop/index.php/Home/My/orderList';
			break;
			case '-7':
				url = '/shop/index.php/Home/MyShop/index';
			break;
			case '-8':
				url = '/shop/index.php/Home/MyStore/myStoreList';
			break;
			case '-9':
				url = '/shop/index.php/Home/Qiang/index';
			break;
			case '-10':
				url = '/shop/index.php/Home/Package/index';
			break;
			case '-11':
				url = '/shop/index.php/Home/ScoreShop/index';
			break;
		}
		
		$('#imglink').val(url);
	}
	
}
//产品分类
function change_product_type(){
	var product_type = $('#product_type').val();
	url = '/shop/index.php/Home/Product/ProductList/type_id/'+product_type;
	
	get_products(product_type);
	
	$('#imglink').val(url);
}
//品牌供应商店铺
function change_brand_supply(){
	var brand_supply = $('#brand_supply').val();
	url = '/shop/index.php/Home/MyStore/index/supplier_id/'+brand_supply;
	
	$('#imglink').val(url);
}
//活动页模板
function change_template_link(){
	var template_link = $('#template_link').val();
	url = '/shop/index.php/Home/Product/ActivityPage/tem_id/'+template_link;
	
	$('#imglink').val(url);
}
//产品
function change_product(){
	var product = $('#products').val();
	if( product > 0 ){
		url = '/shop/index.php/Home/Detail/index/product_id/'+product;
	} else {
		var product_type = $('#product_type').val();
		url = '/shop/index.php/Home/Product/ProductList/type_id/'+product_type;
	}
	
	$('#imglink').val(url);
}
//获取产品
function get_products(product_type){
	$.ajax({
		url: 'get_products.php',
		dataType: 'json',
		type: 'post',
		data: {
			'type_id' : product_type
		},
		success: function(data){
			var products = document.getElementById("products");;
			var len = data.length;
			
			$('#products > option').remove();
			
			var new_option = new Option("---请选择一个产品---",-1);
			products.options.add(new_option);
			
			for( i = 0; i < len; i++ ){
				var pid = data[i].pid;
				var pname = data[i].pname;

				var new_option = new Option(pname,pid);
				products.options.add(new_option);
			}
			
			$('#products').show();
		}
	});
}
//写入链接end

//纯数字
function check_num(obj){
	obj.value = obj.value.replace(/[^\d.]/g,"");
	obj.value = obj.value.replace(/\./g,""); 
}
</script>
</body>
</html>
