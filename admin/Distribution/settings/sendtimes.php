<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');

$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

$head  = 1;	//0:配送方式；1:送货时间

_mysql_query("SET NAMES UTF8");
$query = 'SELECT id,title FROM weixin_sendtimes where isvalid=true and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());


?>
<html>
<head>
	<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
	<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/icon.css" media="all">
	<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
	<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
	<script type="text/javascript" src="../../../common/js/jquery.js"></script>
	<script type="text/javascript" src="../../../common/js/inside.js"></script>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<style type="text/css">
		a:hover{text-decoration: none;}
		.button_blue{margin-left: 17px;font-size: 14px;display: block;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:15px;color: #fff;}
		.button_blue:hover{background:#0e98c9;}
		.WSY_righticon{margin-top:0px;}
	</style>
</head>
<body>
	<div >  
		<div class="WSY_content">
			<div class="WSY_columnbox">
				<div class="WSY_column_header">
					<div class="WSY_columnnav">
						<?php include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Distribution/settings/basic_head.php"); ?>
					</div >
				</div>
				<div class="WSY_list">
					<li class="WSY_left"><a>送货时间列表</a></li>
					<ul class="WSY_righticon">
						<li class="WSY_inputicon"><input type="button" value="添加送货时间+" onClick="gweiUrl2('addsendtime.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>',1,'//<?php echo $http_host;?>/weixinpl/back_newshops/Distribution/settings/')" class="button_blue" style="cursor:hand;float: right;"></li>
					</ul>
				</div>
				<!--<div style="width: 97%;">
				<input type="button" value="添加送货时间+" onClick="gweiUrl2('addsendtime.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>',1,'//<?php echo $http_host;?>/weixinpl/back_newshops/Distribution/settings/')" class="button_blue" style="cursor:hand;float: right;">
				</div>-->
				<!--<hr>-->
				<table cellspacing="1" cellpadding="1" border="0" width="97%" class="WSY_table WSY_t2" id="WSY_t1">
					<thead class="WSY_table_header">
						<tr>
							<th width="5%" height="30"  >ID</th>
							<th width="15%" height="30"  >送货时间</th>
							<th width="20%" height="30"  >操作</th>
						</tr>	
					</thead>
					<form name="form1" method="post">
						<?php
						while ($row = mysql_fetch_object($result)) {
							$keyid =  $row->id ;
							$title = $row->title;
							?>
							<tr  onMouseOver="this.style.backgroundColor='#e4f1fc'" onMouseOut="this.style.backgroundColor='#fff'">
								<td align="center"><?php echo $keyid ?></td>
								<td  style="text-align: left;padding-left: 30px;"><?php echo $title; ?></td>
								<td >
									<a href="addsendtime.php?keyid=<?php echo $keyid ?>&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>" style="cursor:pointer;" title="编辑"><span class="edit_icon"></span></a>
									<a  class="btn"  href="javascript: G.ui.tips.confirm('您确定删除吗？','addsendtime.php?keyid=<?php echo $keyid ?>&op=del&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>');"  title="删除">
										<span class="remove_icon"></span>
									</a>
								</td>
							</tr>
							<?php
						}

						mysql_close($link);
						?>
					</form>
				</table>
				<br/>
				<div style="width:100%;height:20px;">
				</div>
			</div>
		</div>
	</div>
<!--
<table cellspacing="0" cellpadding="0" border="0" height="30" width="92%">
<tr>
<td valign="bottom" width="40"><input type="button" value="全选" onClick="selectall()" class="button"></td>
<td valign="bottom" width="40"><input type="button" value="反选" onClick="selectother()" class="button"></td>
<td valign="bottom" width="40"><input type="button" value="删除" onClick="del();" class="button"></td>
<td valign="bottom"></td>
</tr>
</table>
-->
<script type="text/javascript" src="../../../js/tis.js"></script>
</body>
</html>