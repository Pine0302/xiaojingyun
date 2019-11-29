<?php
ignore_user_abort(true); // 忽略客户端断开
set_time_limit(0);    // 设置执行不超时

header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

$log_username = $_SESSION['username'];

$result['status'] = 0;
$result['msg']  = '提交完成';

$today = date("Y_m_d");

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    if (!is_uploaded_file($_FILES["excelfile"]["tmp_name"])){
		//不存在文件
		$result['status'] = 42002;
		$result['msg']  = '文档不存在';
		//不存在文件  end
    }else{

		$Import_TmpFile = $_FILES['excelfile']['tmp_name'];
		require_once '../../../../weixinpl/common/excel/phpExcelReader/Excel/reader.php';

		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('utf-8');

        $data->read($Import_TmpFile);

        $order_way = $_POST['order_way'] ? $_POST['order_way'] : 1;	//订单来源，1：导出记录，2：导出飞豆

        $f2c_id    = $_GET['f2c_id'] ? $_GET['f2c_id'] : -1;       //f2c店编号

        $from_page = $_GET['from_page'] ? $_GET['from_page'] : 0;

		//var_dump($data);

		$numRows = $data->sheets[0]['numRows'];  //获取最大的行数

		//限制数量
		if($numRows>2010){
			$result['status'] = 42003;
			$result['msg']  = '导入订单最大数量限制为2000条，请重新编辑后导入！';
		}else{

		//查找f2c店的user_id
		   $query="select user_id from f2c_accounts where  id=".$f2c_id;

			$user_id = -1;
			$result_user = _mysql_query($query) or die('Query failed48: ' . mysql_error());
			while ($row = mysql_fetch_object($result_user)) {
				$user_id  = $row->user_id;
			}


		//查找f2c店的user_id End


			//循环数据
            for($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				$batchcode      = $data -> sheets[0]['cells'][$i][1];  //订单号
				$expressname    = $data -> sheets[0]['cells'][$i][2];  //快递名称
				$expressnum     = $data -> sheets[0]['cells'][$i][3];  //快递单号
				$express_remark = $data -> sheets[0]['cells'][$i][4];  //快递备注

                //去掉特殊符号
                $batchcode      = str_replace("'",'',$batchcode);
                $batchcode      = str_replace("＇",'',$batchcode);
                $expressnum     = str_replace("'",'',$expressnum);
                $expressnum     = str_replace("＇",'',$expressnum);
				//去掉特殊符号 End

				//发送curl
				$express_url = Protocol . $_SERVER['HTTP_HOST'] . "/addons/index.php/f2c/ordering_service/send_order_express";
				$param['customer_id']    = $customer_id;
				$param['express_num']    = $expressnum;
				$param['express_comp']   = $expressname;
				$param['batchcode']      = $batchcode;
				$param['express_remark'] = $express_remark;
				$param['user_id']        = $user_id;
				$param['is_from_pc']     = 1;			//PC端
				$param['is_msg_timer']   = 1;			//消息定时发送

				_file_put_contents("log/f2c_order_send_" . $today . ".txt", "\r\n\r\n0.param=======".var_export($param,true)."\r\n",FILE_APPEND);
				_file_put_contents("log/f2c_order_send_" . $today . ".txt", "\r\n\r\n1.express_url=======".var_export($express_url,true)."\r\n",FILE_APPEND);

				$oCurl = curl_init();
				if(stripos($express_url,"https://")!==FALSE){
					curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
				}
				if (is_string($param)) {
					$strPOST = $param;
				} else {
					$aPOST = array();
					foreach($param as $key=>$val){
						$aPOST[] = $key."=".urlencode($val);
					}
					$strPOST =  join("&", $aPOST);
				}
				curl_setopt($oCurl, CURLOPT_URL, $express_url);
				curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt($oCurl, CURLOPT_POST,true);
				curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
				$sContent = curl_exec($oCurl);
				$aStatus = curl_getinfo($oCurl);
				curl_close($oCurl);
				if(intval($aStatus["http_code"])==200){
					//return $sContent;
				}else{
					//return false;
				}

				_file_put_contents("log/f2c_order_send_" . $today . ".txt", "2.sContent=======".var_export($sContent,true)."\r\n",FILE_APPEND);
				//发送curl



			}
			//循环数据


		}
		//限制数量 End





	}

}else{

	$result['status'] = 42001;
	$result['msg']  = '文档提交方式错误';

}

$error =mysql_error();
mysql_close($link);

die(json_encode($result, JSON_UNESCAPED_UNICODE));

?>