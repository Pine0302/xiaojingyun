<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


$stu = '';
$stu = $_GET['stu'];

$Res['code'] = 10001;
$Res['msg'] = '';


switch ($stu){
	case 'save':
		$data_array = $_POST['data_array'];
		$choose_type = $_POST['choose_type'];
		$data_array = json_decode($data_array,true);//json转数组
		//var_dump($data_array);
		foreach ($data_array as $i => $value){

					$_a = $value[2];
					if($value[2] == '' and  gettype($value[2]) == 'boolean'){		
						$_a = 0;
					}
					//echo $_a;
					$query = '';
				if(1 == $choose_type){			//上传产品权限
					$query = "update weixin_4m_control set is_upload_pros = ".$_a." where isvalid=true and id= ".$value[0]."";
				}elseif(2 == $choose_type){		//修改价格权限
					$query = "update weixin_4m_control set is_change_pros_price = ".$_a." where isvalid=true and id= ".$value[0]."";
				}
				//echo $query;
				_mysql_query($query)or die('Query failed:'.mysql_error());  
			
		}
			$error = mysql_error();
			if($error == 0){
				$Res['code'] = 10002;
				$Res['msg'] = "保存成功";

			}else{
				
				$Res['code'] = 10004;
				$Res['msg'] = "保存失败";
			}
			
			$out=json_encode($Res);
			echo ($out);
	break;
}


































?>