<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

$dbinfo = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
$db = new PDO($dbinfo,DB_USER,DB_PWD);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
/*************************数据获取********************************/
$jdpay_customernumber = '';
$jdpay_secret = '';
$jdpay_id = $_POST["jdpay_id"];
$jdpay_customernumber = $_POST["jdpay_customernumber"];
$jdpay_secret = $_POST["jdpay_secret"];
$public_pem_v = $_POST["private_pem_v"];
$private_pem_v = $_POST["private_pem_v"];
if(!is_numeric($jdpay_customernumber) || !$jdpay_secret){
    echo "<script>alert('出现意外的出错');</script>";
    echo "<script>location.href='jdpay_set.php?customer_id=".$customer_id_en."';</script>";
    exit();
}
//print_r($_POST);
//print_r($_FILES['public_pem']);
//die();
session_start();
/*************************************处理config.ini************************************************************/
$destination_folder = "../../../jdPay/config/".$_SESSION['customer_id'];
// echo $destination_folder;
// die();
try{
    if(!file_exists($destination_folder)){
        mkdir($destination_folder,0777,true);
    }
    //$jdpay_customernumber = "22294531";
    //$jdpay_secret = "ta4E/aspLA3lgFGKmNDNRYU92RkZ4w2t";
    file_put_contents("$destination_folder/config.ini","[wepay]\r\n\r\n");
    file_put_contents("$destination_folder/config.ini","merchantNum = $jdpay_customernumber\r\n\r\n",FILE_APPEND);
    file_put_contents("$destination_folder/config.ini","desKey = $jdpay_secret\r\n\r\n",FILE_APPEND);
    file_put_contents("$destination_folder/config.ini","serverPayUrl=https://h5pay.jd.com/jdpay/saveOrder\r\n\r\n",FILE_APPEND);
    file_put_contents("$destination_folder/config.ini","serverQueryUrl=http://paygate.jd.com/service/query\r\n\r\n",FILE_APPEND);
    file_put_contents("$destination_folder/config.ini","refundUrl=http://paygate.jd.com/service/refund\r\n\r\n",FILE_APPEND);
    file_put_contents("$destination_folder/config.ini","callbackUrl=".Protocol.$_SERVER['HTTP_HOST']."/weixinpl/common_shop/jiushop/order_aplay_promote.php\r\n\r\n",FILE_APPEND);
    file_put_contents("$destination_folder/config.ini","notifyUrl="Protocol.$_SERVER['HTTP_HOST']."/weixinpl/jdPay/action/AsynNotifyAction.php\r\n\r\n",FILE_APPEND);
}catch(Exception $e){
    echo $e->getMessage();
}

/***************************************pem的处理***************************************************/

function upload_file($data,$name){
    $_FILES['upfile'] = $data;
    try {

		if(!is_uploaded_file($_FILES['upfile']['tmp_name'])){
			throw new RuntimeException('不是post上传的');
		}

        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1024) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
        $destination_folder = "../../../jdPay/config/".$_SESSION['customer_id']; //上传文件路径
		//没有该路径就创建
		if(!file_exists($destination_folder))
			mkdir($destination_folder,0777,true);
		//拼接路径
		$destination_folder = $destination_folder."/";

		if(!file_exists($destination_folder))
			mkdir($destination_folder,0777,true);
			//获取服务器缓存的文件名字
			$filename = $_FILES['upfile']["tmp_name"];
			//获取上传文件的信息
			$pinfo = pathinfo($_FILES['upfile']["name"]);
			//获取格式
			$ftype = $pinfo["extension"];
			if($ftype!="pem"){
				throw new RuntimeException('格式错误');
			}
			$destination_cert = $destination_folder.$name."_key.".$ftype;//创建的文件路径名称
			$overwrite = true;

			if (file_exists($destination_cert) && $overwrite != true)
			{
			  throw new RuntimeException('文件已经存在');
			}

			if(!_move_uploaded_file ($filename, $destination_cert))
			{
			 throw new RuntimeException('移动失败');
			}
			return date("Y-m-d H:i:s");
    } catch (RuntimeException $e) {

        echo $e->getMessage();

    }
}
if(0 == $_FILES['public_pem']['error']){
    $public_pem_v = upload_file($_FILES['public_pem'],'pubilc');
    echo "1";
}
if(0 == $_FILES['private_pem']['error']){
    $private_pem_v = upload_file($_FILES['private_pem'],'private');
    echo "2";
}
//echo $_FILES['public_pem']['error'];
//echo 'time'.$public_pem_v;
//echo 'time'.$private_pem_v;
//exit();

/*************************数据库处理，有id就更新，没有id就创建**************************************/
try{
    if($jdpay_id>0){
        $sql = "update jdpay set customernumber=:customernumber,secret=:secret,public_pem=:public_pem,private_pem=:private_pem where customer_id=:customer_id";
    }else{
        // $sql=sprintf("insert into jdpay(customernumber,secret,customer_id,isvalid)values('%s','%s',%d,true)",$jdpay_customernumber,$jdpay_secret,$customer_id);
        $sql = "insert into jdpay(customernumber,secret,customer_id,isvalid,public_pem,private_pem)values(:customernumber,:secret,:customer_id,true,:public_pem,:private_pem)";
    }

    $result = $db->prepare($sql);
//商户号
    $result->bindParam(':customernumber',$jdpay_customernumber);
//密钥
    $result->bindParam(':secret',$jdpay_secret);
//系统用户id
    $result->bindParam(':customer_id',$customer_id);
    $result->bindParam(':public_pem',$public_pem_v);
    $result->bindParam(':private_pem',$private_pem_v);
    $result->execute();
    $row = $result->rowCount();
}catch(PDOException $ex){
    echo $ex->getMessage();
}


if($row>0){
    echo "<script>location.href='jdpay_set.php?customer_id=".$customer_id_en."';</script>";
}else{
    //echo "<script>alert('操作失败');</script>";
    echo "<script>location.href='jdpay_set.php?customer_id=".$customer_id_en."';</script>";
}
