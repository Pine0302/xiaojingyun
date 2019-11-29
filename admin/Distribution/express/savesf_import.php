<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$ison =$configutil->splash_new($_POST["ison"]);
$head =$configutil->splash_new($_POST["head"]);
$checkWord =$configutil->splash_new($_POST["checkWord"]);
$token =$configutil->splash_new($_POST["token"]);
$authToken =$configutil->splash_new($_POST["authToken"]);
$Sendcompany =$configutil->splash_new($_POST["Sendcompany"]);
$Sendconcact =$configutil->splash_new($_POST["Sendconcact"]);
$Sendtelphone =$configutil->splash_new($_POST["Sendtelphone"]);
$Sendmobile =$configutil->splash_new($_POST["Sendmobile"]);
$Sendcountry =$configutil->splash_new($_POST["Sendcountry"]);
$Sendprovinoce =$configutil->splash_new($_POST["Sendprovinoce"]);
$Sendcity =$configutil->splash_new($_POST["Sendcity"]);
$Sendcounty =$configutil->splash_new($_POST["Sendcounty"]);
$Sendaddress =$configutil->splash_new($_POST["Sendaddress"]);
$Sendcounty =$configutil->splash_new($_POST["Sendcounty"]);
$Sendzipcode =$configutil->splash_new($_POST["Sendzipcode"]);
$monthlyAccount =$configutil->splash_new($_POST["monthlyAccount"]);
$customsBatchNumber =$configutil->splash_new($_POST["customsBatchNumber"]);
$taxSetAccounts =$configutil->splash_new($_POST["taxSetAccounts"]);
$customer_id =$configutil->splash_new($_POST["customer_id"]);
$Sendcitycode =$configutil->splash_new($_POST["Sendcitycode"]);


 $link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
    mysql_select_db(DB_NAME) or die('Could not select database'.mysql_error());

	$sql="select id from sf_import where customer_id=$customer_id";
 
 $re=_mysql_query($sql);
 $row=mysql_num_rows($re);
  echo '====='.$row;
  if($row>0){
	   $row=mysql_fetch_object($re);
	  $id=$row->id;
	  echo '============='.$id;
	  $sql_1="update sf_import set customer_id='$customer_id',ison='$ison',checkWord='$checkWord',head='$head',token='$token',authToken='$authToken',Sendcompany='$Sendcompany',Sendconcact='$Sendconcact',Sendtelphone='$Sendtelphone',Sendmobile='$Sendmobile',Sendcountry='$Sendcountry',Sendprovinoce='$Sendprovinoce',Sendcitycode='$Sendcitycode',Sendcity='$Sendcity',Sendcounty='$Sendcounty',Sendzipcode='$Sendzipcode',Sendaddress='$Sendaddress',monthlyAccount='$monthlyAccount',customsBatchNumber='$customsBatchNumber',taxSetAccounts='$taxSetAccounts' where id=$id";
  }else{
	 
	  $sql_1="insert into sf_import (customer_id,ison,head,token,authToken,Sendcompany,Sendconcact,Sendtelphone,Sendmobile,Sendcountry,Sendprovinoce,Sendcitycode,Sendcity,Sendcounty,Sendzipcode,Sendaddress,monthlyAccount,customsBatchNumber,taxSetAccounts,checkWord) values ('$customer_id','$ison','$head','$token','$authToken','$Sendcompany','$Sendconcact','$Sendtelphone','$Sendmobile','$Sendcountry','$Sendprovinoce','$Sendcitycode','$Sendcity','$Sendcounty','$Sendzipcode','$Sendaddress','$monthlyAccount','$customsBatchNumber','$taxSetAccounts','$checkWord')";
	  
	  
  }

 _mysql_query($sql_1);
 mysql_close($link);
 $error=mysql_error();
 //echo $error;
 if($error){
	 echo "出错==".$error;
 }else{
	echo "<script>location.href='sf_import.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
 
 }
?>