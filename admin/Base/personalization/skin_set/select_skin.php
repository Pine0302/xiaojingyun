<?php
$skin = -1;//商城前端自定义皮肤颜色，1：橘红色，2：红色，3：蓝色，4：绿色，5：黑色，6：紫色
$query = "select custom_skin from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." and shop_id=".$shop_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$skin = $row->custom_skin;
}
