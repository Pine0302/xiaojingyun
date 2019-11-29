<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');


$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

//运行开始时间
$starttime = explode(' ',microtime());

 $ins_sql="insert into
weixin_commonshop_supply_album (customer_id,supply_id,isvalid,createtime,brand_adimg,brand_adurl,brand_ad_foreign_id,brand_ad_detail_id,brand_linktype) values";

$mSQL1	= "";

$sql_album = "select customer_id,brand_adimg,brand_adurl,brand_ad_foreign_id,brand_ad_detail_id,brand_linktype,createtime,isvalid from weixin_commonshop_supplys where isvalid=true";
$result= _mysql_query($sql_album) or die('sql failed 35: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$customer_id         = $row->customer_id;
	$brand_adimg         = $row->brand_adimg;
	$brand_adurl         = $row->brand_adurl;
	$brand_ad_foreign_id = $row->brand_ad_foreign_id;
	$brand_ad_detail_id  = $row->brand_ad_detail_id;
	$brand_linktype      = $row->brand_linktype;
	$createtime          = $row->createtime;
	$isvalid             = $row->isvalid;
	if('' != $brand_linktype){
		$mSQL1 .= "(".$customer_id.",-1,".$isvalid.",'".$createtime."','".$brand_adimg."','".$brand_adurl."','".$brand_ad_foreign_id."','".$brand_ad_detail_id."','".$brand_linktype."'),";		
	}

}

if( !empty( $mSQL1 ) ){
	echo $QUERY = rtrim("$ins_sql$mSQL1",",");			
	_mysql_query($QUERY) or die('Query failed 54: ' . mysql_error());
}
	
	
$endtime = explode(' ',microtime());
$thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
$thistime = round($thistime,3);
echo "数据迁移完毕！执行耗时：'.$thistime.'秒。";
echo '<script>alert("数据迁移完毕！执行耗时：'.$thistime.'秒。")</script>';
mysql_close($link);

?>