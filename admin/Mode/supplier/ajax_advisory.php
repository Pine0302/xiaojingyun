<?php
header("Content-type: text/html; charset=utf-8");  
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$supplys_id =$configutil->splash_new($_POST["supplys_id"]); 
$su_id =$configutil->splash_new($_POST["su_id"]); 
$advisory_flag =$configutil->splash_new($_POST["advisory_flag"]);//是否开启咨询电话
$advisory_telephone=$configutil->splash_new($_POST["advisory_telephone"]);//咨询电话
$tcount=array();
	if($advisory_flag!=3){
		if($advisory_flag==1 && $advisory_telephone.length>0){	
		}else{
			$advisory_flag=0;
			  //$advisory_telephone=0;
		}
		$query= "update weixin_commonshop_applysupplys set advisory_flag='".$advisory_flag."',advisory_telephone='".$advisory_telephone."' where id='".$supplys_id."' ";	
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		//==================================(返回区)==================================
		if($result){
			array_push($tcount,"设置成功");
		}else{
			array_push($tcount,"设置失败");
		}
		//array_push($tcount,$query);
		echo json_encode($tcount);
		return;
		break;
	}
	if($advisory_flag==3){
		$query= "select advisory_telephone from weixin_commonshop_applysupplys  where id='".$supplys_id."'";
		//array_push($tcount,$query);
		$advisory_telephone ="";
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());		
		while ($row = mysql_fetch_object($result)) {
			$advisory_telephone = $row->advisory_telephone;
			array_push($tcount,$advisory_telephone);
		}
		//array_push($tcount,$supplys_id.$query);
		//array_push($tcount,$query);
		if($advisory_telephone!=""){
			$query= "update weixin_commonshop_applysupplys set advisory_flag=1 where id='".$supplys_id."' ";
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());	
		}
					
		echo json_encode($tcount);
		return;
		break;
	}
		


?>