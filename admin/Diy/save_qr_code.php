<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../weixinpl/config.php');
require('../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


$keyid         	= $configutil->splash_new($_POST["keyid"]);
$P_num         	= $configutil->splash_new($_POST["P_num"]);
$is_ncomission	= $configutil->splash_new($_POST["is_ncomission"]);
$is_shareholder	= $configutil->splash_new($_POST["is_shareholder"]);
$is_team 		= $configutil->splash_new($_POST["is_team"]);
$isOpenAgent 	= $configutil->splash_new($_POST["isOpenAgent"]);
$isOpenSupply	= $configutil->splash_new($_POST["isOpenSupply"]);
$p_str 			= "";
$p 				= "";
$g_str 			= "";
$q_str 			= "";
$D_1 			= "";
$Y_1 			= "";
$permanent_code = "";
if( $is_ncomission ){
	for( $i = 1; $i <= $P_num; $i++){
		$p = $configutil->splash_new($_POST["P_".$i]);
		if( $p =='on' ){
			$p_str .= "P_".$i.","; 
		}		
	}

}else{
	$P_1		= $configutil->splash_new($_POST["P_1"]);
	if( $P_1 =='on' ){
		$p_str .= "P_1,";
	}	

}
if( !empty( $p_str ) ){
	$permanent_code .= $p_str;
}
if( $is_shareholder ){
	$G_1	= $configutil->splash_new($_POST["G_1"]);
	$G_2	= $configutil->splash_new($_POST["G_2"]);
	$G_3	= $configutil->splash_new($_POST["G_3"]);
	$G_4	= $configutil->splash_new($_POST["G_4"]);
	if( $G_1 =='on' ){
		$g_str .= "G_1,"; 
	}
	if( $G_2 =='on' ){
		$g_str .= "G_2,"; 
	}
	if( $G_3 =='on' ){
		$g_str .= "G_3,"; 
	}
	if( $G_4 =='on' ){
		$g_str .= "G_4,"; 
	}
}
if( !empty( $g_str ) ){
	$permanent_code .= $g_str;
}
if( $is_team ){
	$is_diy_area	= $configutil->splash_new($_POST["is_diy_area"]);
	$Q_1			= $configutil->splash_new($_POST["Q_1"]);
	$Q_2			= $configutil->splash_new($_POST["Q_2"]);
	$Q_3			= $configutil->splash_new($_POST["Q_3"]);
	$Q_4			= $configutil->splash_new($_POST["Q_4"]);
	if( $Q_1 =='on' ){
		$q_str .= "Q_1,"; 
	}
	if( $Q_2 =='on' ){
		$q_str .= "Q_2,"; 
	}
	if( $Q_3 =='on' ){
		$q_str .= "Q_3,"; 
	}
	if( $Q_4 =='on' and $is_diy_area == 1 ){
		$q_str .= "Q_4,"; 
	}
}
if( !empty( $q_str ) ){
	$permanent_code .= $q_str;
}
if( $isOpenAgent ){
	$D_1 = $configutil->splash_new($_POST["D_1"]);
}
if( !empty( $D_1 ) ){
	$permanent_code .= "D_1,";
}
if( $isOpenSupply ){
	$Y_1 = $configutil->splash_new($_POST["Y_1"]);
}
if( !empty( $Y_1 ) ){
	$permanent_code .= "Y_1,";
}

/*weixin_commonshops_extend是否存在记录start*/
$wce_id = -1;
$query = "select id from weixin_commonshops_extend where isvalid=true and shop_id=".$keyid;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$wce_id = $row->id;
}
/*weixin_commonshops_extend是否存在记录end*/
if($wce_id>0){
	$sql="update weixin_commonshops_extend set 
	permanent_code='".$permanent_code."'
	where isvalid=true and shop_id=".$keyid;
}else{
	$sql="insert into weixin_commonshops_extend(shop_id,createtime,isvalid,customer_id,is_Pinformation,is_stockOut,is_division,is_promoter,permanent_code) values(".$keyid.",now(),true,".$customer_id.",0,0,0,0,'".$permanent_code."')";
}
 
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());

//echo $sql;
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
$error =mysql_error();
mysql_close($link);
echo "<script>location.href='qr_code.php?customer_id=".$customer_id_en."';</script>"
?>