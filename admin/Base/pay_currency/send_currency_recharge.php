<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/function_model/currency.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/common/phpqrcode/phpqrcode.php');//引入二维码文件		

$currency = new Currency();

if(!empty($_POST["keyid"])){
	$keyid 	 = $configutil->splash_new($_POST["keyid"]);
}

if(!empty($_GET["customer_id"])){
	$customer_id 	 = (int)$configutil->splash_new(passport_decrypt((string)$_GET["customer_id"]));
}

if($keyid>0){			
	$conditions['id']     = $keyid;	
	
	$fileds = array();
	$fileds['status']     = 2;
	
	//$res = 0;
	
	$query = "select num from currency_recharge_card_list_t where id=".$conditions['id']." and isvalid=true";
	$result = _mysql_query($query)or die( 'Query failed in 28: ' . mysql_error() );
	while ($row = mysql_fetch_object($result)) {
		$num    = intval($row->num);	
		break;				
	}
	
	//页数输出 Start
	$limit_count = 0; 
	if((isset($_GET['limit_count'])) && (is_numeric($_GET['limit_count']))){ $limit_count = $_GET['limit_count']; }
	$limit_p = 1; 
	if((isset($_GET['limit_p'])) && (is_numeric($_GET['limit_p']))){ $limit_p = $_GET['limit_p']; }	
	//页数输出 End	
	$array_result = array('code'=>0,'count'=>0,'page_count'=>0,'page'=>0);
	
	if($limit_count>0){

		if($limit_p==0){
			
			$re_count = $num;
			$re_num = $re_count;
			
			$array_result['count'] 			= $re_num;
			$array_result['page_count'] 	= intval($re_num/$limit_count)+1;
			$array_result['page']			= 1;					
			die(json_encode($array_result));
		}else{															
			if($limit_p==1){ 
				$_SESSION['num']           = $num;
				$_SESSION['key_array']     = array();
				$array_result['code']=1;
							
				 /*更新购物币充值卡状态*/
				 $result  = $currency ->update_recharge_card_list($conditions,$fileds);
			}									
            if($_GET['op']=="output"){  //判断生成的卡密数量是否符合要生成的数量
				if(!empty($_SESSION['key_array'])){
					$query = "select num from currency_recharge_card_list_t where id=".$conditions['id']." and isvalid=true";
					$result = _mysql_query($query)or die( 'Query failed in 28: ' . mysql_error() );
					while ($row = mysql_fetch_object($result)) {
						$num    = intval($row->num);	
						break;				
					}
					
					if(count($_SESSION['key_array'])<$num){    //生成的卡密小于需求的卡密数量时
						$re_build_key = $num - count($_SESSION['key_array']);
						$key_arr = $currency->key_generator($re_build_key);
						
						$key_array = $_SESSION['key_array'];
						$build_key = array();  // 要生成卡密数组,每次都清零			
						
						//var_dump($key_arr);
						foreach($key_arr as $val){ 
							 if(!in_Array($val,$key_array)){
								 $key_array[] = $val;
								 $build_key[] = $val;
							 }				 
						}
						$_SESSION['key_array'] = $key_array;
								 
						insert_recharge_key($conditions,$customer_id,$build_key);										
					}

				}
				$array_result['code'] 	= 1;
				$array_result['msg'] 	= "发布成功";
				echo json_encode($array_result);
				return;
			}
			if($_SESSION['num']<=$limit_count){
				$limit_count = $_SESSION['num'];
			}		
			$array_result['count'] 			= intval($_GET['count']);
			$array_result['page_count'] 	= intval($_GET['page_count']);
			$array_result['page']			= intval($_GET['limit_p'])+1;
			
			$_SESSION['num'] =$_SESSION['num']-$limit_count;
			
			$array_result['num'] = $_SESSION['num'];
            $array_result['key_array'] = $_SESSION['key_array'];
			
			if($array_result['page_count']<$array_result['page']){

				echo json_encode($array_result);						
			}else{
				echo json_encode($array_result);
			}													
			//分页输出-分页缓存				
			$key_arr = $currency->key_generator($limit_count);
			
			$key_array = $_SESSION['key_array'];
            $build_key = array();  // 要生成卡密数组,每次都清零			
			foreach($key_arr as $val){ 
			     if(!in_Array($val,$key_array)){
					 $key_array[] = $val;
					 $build_key[] = $val;
				 }				 
			}
			$_SESSION['key_array'] = $key_array;
					 
			insert_recharge_key($conditions,$customer_id,$build_key);
			if($limit_count>0){
			}else{

			}			
	//分页输出-分页缓存			
		}
		
	}		
	/*_mysql_query('set autocommit=0') or die('Query failed4: ' . mysql_error());   
	_mysql_query('SET session TRANSACTION ISOLATION LEVEL SERIALIZABLE') or die('Query failed4: ' . mysql_error());
	_mysql_query('start transaction;');   //开启事务*/
			
	//$result  = $currency ->update_recharge_card_list($conditions,$fileds);
	//$res = $result['res'];	
	
	//$result1 = $currency ->insert_recharge_key($conditions,$customer_id);
	/*if($res){
		_mysql_query("COMMIT");//执行事务
		_mysql_query('set autocommit=1') or die('Query failed4: ' . mysql_error());
	}else{
		_mysql_query("ROLLBACK");//回滚
	}*/			
}

function insert_recharge_key($conditions="",$customer_id,$build_key){
    $customer_id_en  = passport_encrypt((string)$customer_id);
	$data = array();//返回数组	

	if($conditions['id']>0){
		//发卡数量大于0时
		//$path = $_SERVER['DOCUMENT_ROOT']."/weixinpl/back_newshops/Base/pay_currency/up/".$conditions['id']."/";

		//mkdir($path,0755,true);  //递归创建目录
		
		$query = "select starttime,endtime,money,account from currency_recharge_card_list_t where id=".$conditions['id']." and isvalid=true";
		$result = _mysql_query($query)or die( 'Query failed in 28: ' . mysql_error() );
		while ($row = mysql_fetch_object($result)) {	
			$starttime   = $row->starttime;	
			$endtime   = $row->endtime;	
			$money   = $row->money;	
			$account   = $row->account;
			break;				
		}

		$sql = "insert into currency_recharge_card_key_t(recharge_id,`status`,`key`,account,money,starttime,endtime,customer_id,isvalid) VALUES";
		for($i=0;$i<count($build_key);$i++){
			
			//$fileName = $build_key[$i].'.png';  //二维码图片名称
			
			//$qr_path = "http://".$_SERVER['HTTP_HOST']."/weixinpl/back_newshops/Base/pay_currency/up/".$conditions['id']."/".$fileName;
			
			$sql .= "(".$conditions['id'].",1,'".$build_key[$i]."','".$account."','".$money."','".$starttime."','".$endtime."',".$customer_id.",true),";										
	
												
		}
		$sql = rtrim($sql,',');
		_mysql_query($sql) or die('sql Query failed: ' . mysql_error());
			
		$codeContents = Protocol.$_SERVER['HTTP_HOST']."/weixinpl/mshop/currency_recharge.php?customer_id=".$customer_id_en."&keyid=";
		
		$query = "update currency_recharge_card_key_t set code_url=CONCAT('".$codeContents."', sha(id)) where customer_id=".$customer_id." and isvalid=true and code_url='' and account=".$account;  //更新二维码路径，id加密后拼接在路径后面
		_mysql_query($query) or die('query Query failed: ' . mysql_error());
		//QRcode::png($codeContents, $path.$fileName, QR_ECLEVEL_L, 10);	
			
		$data['errcode'] = 0;
		$data['errmsg']  = "添加成功";	
	}else{
		$data['errcode'] = 40001;
		$data['errmsg']  = "添加充值卡卡密失败";	
	}			
	return $data;
	
}
mysql_close($link);
?>