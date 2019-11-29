
<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/config.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
    $link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
    mysql_select_db(DB_NAME) or die('Could not select database');
    require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/proxy_info.php');
    _mysql_query("SET NAMES UTF8");
	$diy_count=0;//判断渠道是否开启自定义模板
	$sp_query="select count(1) as diy_count from ".DB_NAME.".customer_funs cf inner join ".DB_NAME.".columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='自定义模板' and c.id=cf.column_id";
	$sp_result = _mysql_query($sp_query) or die('W_is_supplier Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($sp_result)) {
	   $diy_count = $row->diy_count;
	   break;
	}
    $personalization = '/weixinpl/back_newshops/Base/personalization';
?>

    	<div class="WSY_column_header">  
        	<div class="WSY_columnnav">
            	<a  href="<?php echo $personalization; ?>/home_template/fengge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">首页模板</a>
				<a href="<?php echo $personalization; ?>/home_decoration/defaultset.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">首页装修</a>
				<a  href="<?php echo $personalization; ?>/mall_setting/setting.php?customer_id=<?php echo $customer_id_en; ?>">商城购物设置</a>
				<a  href="<?php echo $personalization; ?>/micro_shop/microshop_set.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">微店设置</a>
				<a  href="<?php echo $personalization; ?>/personal_center/shop_subscribes.php?customer_id=<?php echo $customer_id_en; ?>" >添加自定义功能</a>
				<a  href="<?php echo $personalization; ?>/distributor_article/distributor_article.php?customer_id=<?php echo $customer_id_en; ?>" >单品推广页面设置</a>
				<?php if($diy_count>0){?>
				<a  href="<?php echo $personalization; ?>/custom/custom_control.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>" >自定义模板</a>
				<?php }?>
				<a  href="<?php echo $personalization; ?>/skin_set/skin_set.php?customer_id=<?php echo $customer_id_en; ?>" >皮肤设置</a>
				<!-- <a  href="../privilege_set/privilege_set.php?customer_id=<?php echo $customer_id_en; ?>" >特权设置</a> -->
<!--				<a  href="../navigation/setting.php?customer_id=--><?php //echo $customer_id_en; ?><!--" >导航设置</a>-->
<!--				<a  href="../bottom_label/index.php?customer_id=--><?php //echo $customer_id_en; ?><!--" >底部设置</a>-->
                <a  href="/mshop/admin/index.php?m=navigation&a=template_list&customer_id=<?php echo $customer_id_en; ?>" >导航设置</a>
                <a  href="/mshop/admin/index.php?m=bottom_label&a=template_list&customer_id=<?php echo $customer_id_en; ?>" >底部设置</a>
            </div>
        </div>  
<script>
var head = <?php echo $head; ?>;
$(".WSY_columnnav").find("a").eq(head).addClass('white1');
</script>