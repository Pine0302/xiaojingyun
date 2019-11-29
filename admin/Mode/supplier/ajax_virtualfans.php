<?php
header("Content-type: text/html; charset=utf-8");  
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once("../../../../weixinpl/common/common_ext.php");
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

//$supplys_id =$configutil->splash_new($_POST["supplys_id"]); 
//$vf_flag =$configutil->splash_new($_POST["vf_flag"]);//是否开启虚拟粉丝
//$virtual_fans_nums=$configutil->splash_new($_POST["virtual_fans_nums"]);//虚拟粉丝数
$supplys_id = i2post("supplys_id"); 
$vf_flag = i2post("vf_flag"); 
$virtual_fans_nums = i2post("virtual_fans_nums",0); 
$tcount=array();
	if($vf_flag!=3){
		if($vf_flag==1 /* && $virtual_fans_nums.length>0 0的长度为0*/){	
		}else{
			$vf_flag=0;
		}
		$query= "update weixin_commonshop_applysupplys set virtual_fans_flag='".$vf_flag."',virtual_fans_nums='".$virtual_fans_nums."' where isvalid=true and user_id='".$supplys_id."' ";	
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
	if($vf_flag==3){
		$query= "select virtual_fans_nums from weixin_commonshop_applysupplys  where isvalid=true and user_id='".$supplys_id."'";
		//array_push($tcount,$query);
		$virtual_fans_nums ="";
		$result = _mysql_query($query) or die('Query failed: ' . mysql_error());		
		while ($row = mysql_fetch_object($result)) {
			$virtual_fans_nums = $row->virtual_fans_nums;
			array_push($tcount,$virtual_fans_nums);
		}
		//array_push($tcount,$supplys_id.$query);
		//array_push($tcount,$query);
		if($virtual_fans_nums!=""){
			$query= "update weixin_commonshop_applysupplys set virtual_fans_flag=1 where isvalid=true and user_id='".$supplys_id."' ";
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());	
		}
					
		echo json_encode($tcount);
		return;
		break;
	}
		


?>