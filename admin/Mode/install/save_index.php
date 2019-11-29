<?php

header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$id = 0;
$show_index = 0;

if(!empty($_GET["show_index"])){
	$show_index = $configutil->splash_new($_GET["show_index"]);
}
if($show_index == 0){
	$setting_id = $configutil->splash_new($_POST["setting_id"]);
	$reward1 = $configutil->splash_new($_POST["reward1"]);
	$reward2 = $configutil->splash_new($_POST["reward2"]);
	$reward3 = $configutil->splash_new($_POST["reward3"]);
	
	$weight = $configutil->splash_new($_POST["weight"]);
	
	$star1 = $configutil->splash_new($_POST["star1"]);
	$star2 = $configutil->splash_new($_POST["star2"]);
	$star3 = $configutil->splash_new($_POST["star3"]);
	$star4 = $configutil->splash_new($_POST["star4"]);
	$star5 = $configutil->splash_new($_POST["star5"]);
	
	//$punishment = $configutil->splash_new($_POST["punishment"]);
	//$rewardcomment = $configutil->splash_new($_POST["rewardcomment"]);
	$quitscore = $configutil->splash_new($_POST["quitscore"]);
	$reward_account = $configutil->splash_new($_POST["reward_account"]);
	$registcomment = $configutil->splash_new($_POST["registcomment"]);
	
	if($setting_id > 0){
		/*$query = "update weixin_install_settings set reward1 = '".$reward1."' ,reward2 = '".$reward2."' , reward3 = '".$reward3."' ,weight = '".$weight."' , punishment = '".$punishment."',
			rewardcomment = '".$rewardcomment."' ,star1 = '".$star1."' , star2 = '".$star2."' , star3 = '".$star3."' , star4 = '".$star4."' , star5 ='".$star5."' ,quitscore = '".$quitscore."',reward_account = '".$reward_account."',registcomment = '".$registcomment."'
			where isvalid = true and customer_id=".$customer_id." and id = ".$setting_id;
		_mysql_query($query) or die ("L36  query error : ".mysql_error());*/
		$query = "update weixin_install_settings set reward1 = '".$reward1."' ,reward2 = '".$reward2."' , reward3 = '".$reward3."' ,weight = '".$weight."' ,star1 = '".$star1."' , star2 = '".$star2."' , star3 = '".$star3."' , star4 = '".$star4."' , star5 ='".$star5."' ,quitscore = '".$quitscore."',reward_account = '".$reward_account."',registcomment = '".$registcomment."'
			where isvalid = true and customer_id=".$customer_id." and id = ".$setting_id;
		_mysql_query($query) or die ("L36  query error : ".mysql_error());
	}else{
		/*$query = "insert into weixin_install_settings(customer_id,reward1,reward2,reward3,weight,punishment,rewardcomment,star1,star2,star3,star4,star5,createtime,isvalid,quitscore,reward_account,registcomment)
			values ('".$customer_id."','".$reward1."','".$reward2."','".$reward3."','".$weight."','".$punishment."','".$rewardcomment."','".$star1."','".$star2."','".$star3."','".$star4."','".$star5."',now(),1,'".$quitscore."','".$reward_account."','".$registcomment."')";
		_mysql_query($query) or die ("L40  query error : ".mysql_error());*/
		$query = "insert into weixin_install_settings(customer_id,reward1,reward2,reward3,weight,star1,star2,star3,star4,star5,createtime,isvalid,quitscore,reward_account,registcomment)
			values ('".$customer_id."','".$reward1."','".$reward2."','".$reward3."','".$weight."','".$star1."','".$star2."','".$star3."','".$star4."','".$star5."',now(),1,'".$quitscore."','".$reward_account."','".$registcomment."')";
		_mysql_query($query) or die ("L40  query error : ".mysql_error());
	}
	
	echo "<script type='text/javascript'>alert('保存成功');location.href='index.php?customer_id=".passport_encrypt($customer_id)."&show_index=0';</script>";
}else if($show_index == 1){
	$scores = $_POST["score"];
	$rewards = $_POST["reward"];
	
	//echo $scores."<br/>";
	//echo $rewards."<br/>";
	
	$msg = "奖励设置成功！";
	$hasError = false;

	//echo $scores." = ".$rewards;
	
	_mysql_query('START TRANSACTION');
	
	$query="delete from weixin_install_reward_settings where isvalid = true and customer_id = ".$customer_id;
	_mysql_query($query);
	$error = mysql_error();
	if(!empty($error)){
		$msg = "L57 : ".$error;
		$hasError = true;
	}
	for($i = 0; $i < count($scores); $i++){
		$score = $scores[$i];
		$reward = $rewards[$i];
		//echo "score : ".$score." reward : ".$reward;
		if(!empty($score) && !empty($reward)){
			$query = "insert into weixin_install_reward_settings(customer_id,score,reward,createtime,isvalid) values('".$customer_id."','".$score."','".$reward."',now(),1)";
			_mysql_query($query);
			$error = mysql_error();
			if(!empty($error)){
				$msg = "L64 : ".$error;
				$hasError = true;
			}
		}
	}
	//echo "msg : ".$msg." hasError : ".$hasError;
	if($hasError == true){
		_mysql_query('ROLLBACK');
	}else{
		_mysql_query('COMMIT');
	}
	
	echo "<script type='text/javascript'>alert('".$msg."');location.href='index.php?customer_id=".passport_encrypt($customer_id)."&show_index=1';</script>";
}else if($show_index == 4){ //保存文章配置
	$title = $configutil->splash_new($_POST["title"]);
	$content = $configutil->splash_new($_POST["content"]);
	$icon = $configutil->splash_new($_POST["icon"]);
	$ordernum = $configutil->splash_new($_POST["ordernum"]);
	$article_id = $configutil->splash_new($_POST["article_id"]);
	if(empty($article_id) || $article_id <= 0){
		$query = "insert into weixin_install_article (title,content,ordernum,icon,customer_id,createtime,isvalid) 
		values('".$title."','".$content."','".$ordernum."','".$icon."','".$customer_id."',now(),1)";
		_mysql_query($query) or die("L98 query error : ".mysql_error());
	}else{
		$query = "update weixin_install_article set title = '".$title."', content = '".$content."' , icon = '".$icon."' , ordernum = '".$ordernum."' where isvalid = true and id = ".$article_id;
		_mysql_query($query) or die("L101 query error : ".mysql_error());
	}
	echo "<script type='text/javascript'>location.href='index.php?customer_id=".passport_encrypt($customer_id)."&show_index=4';</script>";
}
$isOpenInstall = 0;
$isOpenInstall =$configutil->splash_new($_POST["isOpenInstall"]);

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
 

if($customer_id>0){
	$sql="update weixin_commonshops set isOpenInstall=".$isOpenInstall." where customer_id=".$customer_id;
	//echo $sql."<br/>";
	_mysql_query($sql);
	
}else{
	$sql = "insert into weixin_commonshops(isOpenInstall) values (".$isOpenInstall.")";
	 //echo $sql."<br/>";
	_mysql_query($sql);
 }
 $error =mysql_error();
mysql_close($link);
	//echo $error; 
 echo "<script>location.href='index.php?customer_id=".$customer_id_en."';</script>"

?>