<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../config.php');

$content_id = $_POST["content_id"];

//连接数据库
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

// Set the uplaod directory
//$uploadDir = 'uploads/';
echo $content_id;
//设置上传目录
$uploadDir = "../../../../up/friend/".$customer_id."/"; //上传文件存放路径
$sqluploadDir = "../../../up/friend/".$customer_id."/"; //上传文件数据库路径

if(!is_dir($uploadDir)){mkdir($uploadDir,777,true);}  //创建目录

// 设置可上传文件类型
$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // 允许的文件后缀

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {  //验证token
	$tempFile   = $_FILES['Filedata']['tmp_name'];

	//得到文件名字与类型名
	$fileName = iconv("UTF-8","GB2312",$_FILES["Filedata"]["name"]);

	//截取名字
		$flag = strpos($fileName, '.');
		if($flag !== FALSE){
			$arr = explode('.', $fileName);
			$title = $arr[0];
		}else{
			$title = $fileName;
		}


	$pinfo = pathinfo($fileName);
	$ftype = $pinfo["extension"];
	$picname = time().rand(100,999);
	$targetFile = $uploadDir.$picname.".".$ftype;//上传文件存放路径
	$targetFile2 = $sqluploadDir.$picname.".".$ftype;//上传文件数据库路径


	// 验证文件类型
	$fileParts = pathinfo($_FILES['Filedata']['name']);


	if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

		// 保存文件
		_move_uploaded_file($tempFile, $targetFile);

		//插入数据库
		$sql="insert into
	    weixin_friend_imgs (imgurl,content_id,customer_id,isvalid) values ('".$targetFile2."','".$content_id."',".$customer_id.",true)";
		//echo $sql;return;


		_file_put_contents("pic.txt", "query2=============".$sql.date("y-m-d h:i:s")."\r\n",FILE_APPEND);

		_mysql_query($sql);

		$error =mysql_error();
		//echo "file=====".$error;
		echo $fileName."上传成功！";
		//echo $sql."----".$targetFile;
	} else {

		// The file type wasn't allowed
		echo 'Invalid file type.';

	}
}

mysql_close($link);
?>