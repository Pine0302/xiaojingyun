<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = $_GET['customer_id'];
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$op = $_GET['op'];
if($op!=''){
	$result = array(
		'code'=>10000,
		'msg'=>''
	);
	switch ($op){
		case 'asort_edit':
			$asort = $_POST['asort'];
			$keyid = $_GET['keyid'];
			
			
			$query = "update weixin_commonshop_guess_you_like set asort='".$asort."' where isvalid=true  and  customer_id=".$customer_id." and id=".$keyid."";
			//echo $query;
			$res=_mysql_query($query)or die('Query failed'.mysql_error());
			if($res){
					$result = array(
						'code'=>10001,
						'msg'=>'保存成功！'
					);
			
			}else{
					$result = array(
						'code'=>10004,
						'msg'=>'保存失败，请重试！'
					);
				
			}
			$out = json_encode($result);
			echo $out;
		break;
	}
}else{
	//添加或者更改
	$keyid = 0;
	$keyid = $_GET['keyid'];
	if($keyid>0){			//更新
		$asort = $_POST['asort'];
		$pro_id = $_POST['pro_id'];
		$query = "update weixin_commonshop_guess_you_like set asort='".$asort."' ,pro_id=".$pro_id." where isvalid=true  and  customer_id=".$customer_id." and id=".$keyid."";
		//echo $query;
		_mysql_query($query)or die('Query failed'.mysql_error());
		
	}else{					//添加
		$asort = $_POST['asort'];
		$pro_id = $_POST['pro_id'];
		$query = "insert into weixin_commonshop_guess_you_like(pro_id,customer_id,asort,isvalid,createtime)values(".$pro_id.",".$customer_id.",".$asort.",true,now())";
		//echo $query;
		_mysql_query($query)or die('Query failed'.mysql_error());
	}
	echo "<script>location.href='guess_you_like.php?customer_id=".$customer_id_en."';</script>";
}

mysql_close($link);

?>