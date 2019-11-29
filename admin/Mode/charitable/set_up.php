<?php
header("Content-type: text/html; charset=utf-8"); 
//ini_set('display_errors','On');
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件  0基本设置,1基金明细
$query = "select id,is_charitable,charitable_propotion,integration_price from charitable_set_t where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$is_charitable        =  0;	//是否开启慈善公益：0、关闭，1、开启
$charitable_propotion =  0;  //慈善公益最低分配率
$integration_price    =  1;  //捐赠多少钱得1积分
$charitable_id 		  = -1;  //慈善id

while ($row = mysql_fetch_object($result)) {
	$charitable_id        = $row->id; 
	$is_charitable        = $row->is_charitable; 
	$charitable_propotion = $row->charitable_propotion; 
	$integration_price	  = $row->integration_price; 
}

?>  
<!doctype html>
<html><head><meta charset="utf-8">
<title>基本设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/charitable/set_up.css">

</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			// include("../../../../weixinpl/back_newshops/Mode/charitable/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/charitable/basic_head.php");
			?>
			<!--列表头部切换结束-->
						
			<form action="save_set_up.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
				<div class="WSY_remind_main">
					<dl class="WSY_remind_dl02"> 
						<dt style="line-height:20px;font-weight:normal;margin-left:28px" class="WSY_left">开启慈善公益：</dt>
						<dd>
							<?php if($is_charitable==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_sendstatus(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_sendstatus(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?> 
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
			 					<li onclick="change_sendstatus(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_sendstatus(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
							<span style="float: left;margin: -16px 210px;color: #888;"></span>
						</dd>						
						<input type="hidden" name="is_charitable" id="is_charitable" value="<?php echo $is_charitable; ?>" />
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;margin-left: 14px;" class="WSY_left">慈善公益最低分配率：</dt>
						<dd>
							<input type="text" name="charitable_propotion" id="charitable_propotion" style="width:50px;background:#fefefe;" value="<?php echo round($charitable_propotion,2); ?>" />（0~1）						
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;margin-left: 14px;" class="WSY_left">捐赠：</dt>
						<dd>
							<input type="text" name="integration_price" id="integration_price" style="width:50px;background:#fefefe;" value="<?php echo $integration_price; ?>" /><?php echo OOF_T ?>得1慈善分						
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<table width="97%" class="WSY_table" id="WSY_t1">
							<thead class="WSY_table_header">
								<!-- <th width="25%" class="WSY_table_little">字段类型</th> -->
								<th width="25%" class="WSY_table_little">慈善等级</th>
								<th width="25%" class="WSY_table_little">额度限制</th>
								<th width="25%" class="WSY_table_little">操作</th>
							</thead>
							<?php 
							$query    = "select id,name,price_limit from charitable_name_t where isvalid=true and customer_id=".$customer_id;
							$result   = _mysql_query($query) or die('Query failed: ' . mysql_error());
							$rcount_q = mysql_num_rows($result);
							$name        = "一星大使";//等级名称
							$price_limit = "";//等级额度
							$diy_num     = 1;//等级额度
							$name_id     = 0;//id
							
							if( 0 < $rcount_q ){
								
								while ($row = mysql_fetch_object($result)) {
									$name        = $row->name; 
									$price_limit = $row->price_limit; 
									$name_id     = $row->id; 
								
								?>
								<tr class="diy_one_two" id="diy_item_<?php echo $diy_num; ?>">
									<!-- <td>
										单行文字
									</td> --> 
									<input type=hidden name="name_id<?php echo $diy_num; ?>" id="name_id<?php echo $diy_num; ?>" value="<?php echo $name_id; ?>" />
									<td>
										<input type=text name="singletext_<?php echo $diy_num; ?>" id="singletext_<?php echo $diy_num; ?>" value="<?php echo $name; ?>" placeholder="请输入等级名称" />
									</td>
									<td>
										<input type=text class="singletext_con" name="singletext_con_<?php echo $diy_num; ?>" id="singletext_con<?php echo $diy_num; ?>" value="<?php echo $price_limit; ?>" placeholder="请输入额度" />
										-
										不限
									</td>
									<td>
										<a title="删除" href="javascript:diy_del(<?php echo $diy_num; ?>);"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>&nbsp;
										<a title="添加" href="javascript:diy_add(1);"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
									</td>
								</tr>
							<?php 
									$diy_num++;
								}
							}else{  
							?>
								<tr class="diy_one_two" id="diy_item_<?php echo $diy_num; ?>">
									<!-- <td>
										单行文字
									</td> -->
									<input type=hidden name="name_id<?php echo $diy_num; ?>" id="name_id" value="0" />
									<td>
										<input type=text name="singletext_<?php echo $diy_num; ?>" id="singletext_<?php echo $diy_num; ?>" value="<?php echo $name; ?>" placeholder="请输入等级名称" />
									</td>
									<td>
										<input type=text class="singletext_con" name="singletext_con_<?php echo $diy_num; ?>" id="singletext_con<?php echo $diy_num; ?>" value="<?php echo $price_limit; ?>" placeholder="请输入额度" />
										-
										不限
									</td>
									<td>
										<a title="删除" href="javascript:diy_del(<?php echo $diy_num; ?>);"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>&nbsp;
										<a title="添加" href="javascript:diy_add(1);"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
									</td>
								</tr>
							<?php } ?>
						</table>
					</dl>
					<input type=hidden name="charitable_id" id="charitable_id" value="<?php echo $charitable_id; ?>" />
					<input type=hidden name="diy_num" id="diy_num" value="<?php echo $diy_num; ?>" />
					<div class="WSY_text_input"><input type="submit" class="WSY_button" value="提交保存" onclick="return subBase();"><br class="WSY_clearfloat"></div>
				</div> 
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	
<script> 
var charitable_id = <?php echo $charitable_id?>;
var customer_id   = '<?php echo $customer_id_en?>';
var diy_num       = <?php echo $diy_num?> - 1;

</script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Mode/charitable/set_up.js"></script>
</body>
</html>