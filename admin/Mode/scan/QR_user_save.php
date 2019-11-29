<?php 
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');
  $customer_id = passport_decrypt($customer_id);
  require('../../../../weixinpl/back_init.php');
  $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
   mysql_select_db(DB_NAME) or die('Could not select database');
   _mysql_query("SET NAMES UTF8");
   require('../../../../weixinpl/proxy_info.php');
    
   $keyid = -1;
  $statu = "";
  $cheid = -1;
  $name ="";
  $password ="";
  $newpassword="";	
  
  
  $customer_id=passport_decrypt($_GET["customer_id"]);
  $name = $_POST['name']; 
  $password = $_POST['password']; 
  $newpassword = $_POST['newpassword'];
  $confirm_name=$customer_id."-".$name;
  
 $destination1 = "";
	if(!empty($_FILES['upfile1']['name'])){
		$rand1=rand(0,9);
		$rand2=rand(0,9);
		$rand3=rand(0,9);
		$filename=date("Ymdhis").$rand1.$rand2.$rand3;
		$filetype=substr($_FILES['upfile1']['name'], strrpos($_FILES['upfile1']['name'], "."),strlen($_FILES['upfile1']['name'])-strrpos($_FILES['upfile1']['name'], "."));
		$filetype=strtolower($filetype);
		if(($filetype!='.jpg')&&($filetype!='.png')&&($filetype!='.gif')){
				echo "<script>alert('文件类型或地址错误');</script>";
				echo "<script>history.back(-1);</script>";
				exit ;
			}
		$filename=$filename.$filetype;
		$savedir='../../../'.Base_Upload.'Mode/scan/';
		if(!is_dir($savedir)){
			mkdir($savedir,0777,true);
		}
		$savefile=$savedir.$filename;
		if (!_move_uploaded_file($_FILES['upfile1']['tmp_name'], $savefile)){
			echo "<script>history.back(-1);</script>";
			exit;
		}
		$destination1=$savefile;
		$destination1 = str_replace("../","",$destination1);
		// $destination1 = "/weixinpl/".$destination1;	
		$destination1 = "/mshop/".$destination1;		
	}else{
	$destination1=$configutil->splash_new($_POST['logo_img']);
	} 
  
 
  if(!empty($_POST["keyid"])){
			$keyid = $configutil->splash_new($_POST["keyid"]);
		}

	if($keyid>0){
		
			if($newpassword!=""){
					
					 $query="update weixin_commonshop_ticketclerk set name='".$name."',password='".md5($newpassword)."',confirm_name='".$confirm_name."' , logo_img='".$destination1."'  where id=".$keyid."";
					 _mysql_query($query) or die('Query failed1: ' . mysql_error());  
					
				}else{
					$query="update weixin_commonshop_ticketclerk set name='".$name."',confirm_name='".$confirm_name."' ,logo_img='".$destination1."'  where id=".$keyid."";
					_mysql_query($query) or die('Query failed1: ' . mysql_error());  
					
				}
			 
			  
			}elseif($keyid<0){
				
				
					 $query="insert into weixin_commonshop_ticketclerk(name,password,createtime,customer_id,confirm_name,logo_img)values('".$name."','".md5($password)."',now(),".$customer_id.",'".$confirm_name."','".$destination1."')";
					 _mysql_query($query) or die('Query failed2: ' . mysql_error()); 	
			
				}
				//echo $keyid;
				//echo $query;
	 

echo "<script>location.href='QR_user.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
?>