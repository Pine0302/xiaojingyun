<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require_once '../../../../weixinpl/common/excel/phpExcelReader/Excel/reader.php';
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
set_time_limit(0); 
$show_index = 3;
$data = new Spreadsheet_Excel_Reader();
// Set output Encoding.
$data->setOutputEncoding('utf-8');

$msg = "导入成功！";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["excelfile"]["tmp_name"]))
	//是否存在文件
	{
		$msg = "文件不存在！";
	}
	else
	{
		$totalcount = 0;
		$totalcount_m = 0;
		$importArr = array();
		$curr_province = "";
		$curr_proxy = "";
		$curr_type = 0;
		$curr_count = 0;
		
		$Import_TmpFile = $_FILES['excelfile']['tmp_name'];
		//echo  "Import_TmpFile : ".$Import_TmpFile;
		$data->read($Import_TmpFile);
		
		$rows = $data->sheets[0]['numRows'];
		$succ_rows = 0;
		$fail_rows = 0;
		//echo "rows : ".$rows;
		
		for($i = 2; $i <= $rows; $i++) {
			$ordernum = $data->sheets[0]['cells'][$i][1]; //订单号
			$product_name = $data->sheets[0]['cells'][$i][2]; //商品名
			$product_num = $data->sheets[0]['cells'][$i][13]; //备注信息
			$product_count = $data->sheets[0]['cells'][$i][3]; //商品数量
			$install_cost = $data->sheets[0]['cells'][$i][4]; //安装费
			$contact = $data->sheets[0]['cells'][$i][5]; //联系人
			$phone = $data->sheets[0]['cells'][$i][6]; //联系电话
			
			$location_p = $data->sheets[0]['cells'][$i][7]; //省
			$location_c = $data->sheets[0]['cells'][$i][8]; //市
			$location_a = $data->sheets[0]['cells'][$i][9]; //区
			$address = $data->sheets[0]['cells'][$i][10]; //地址
			$reservation_date = $data->sheets[0]['cells'][$i][11]; //预约安装日期
			$remark = $data->sheets[0]['cells'][$i][12]; //备注信息
			
			$timestamp = strtotime($reservation_date);
			$year = date('Y',$timestamp);
			$month = date('m',$timestamp);
			$day = date('d',$timestamp);	
			//echo "  timestamp : ".$year."-".$month."-".$day."<br/>";
			//echo " after : ".$year."-".$day."-".$month."<br/>";			
			$rightTypeTime = $year."-".$day."-".$month;
			$rightTime = strtotime($rightTypeTime)-86400;
			//echo $rightTime."<br/>";
			$lastTime =date('Y-m-d',$rightTime);
			
			$stringtime = date("Y-m-d H:i:s",time());  
			$install_order=strtotime($stringtime); 
			$install_order=$customer_id."".$i."".$install_order;
			
			$query = "insert into weixin_install_reservation(reservation_num,order_num,createtime,status,contact,phone,location_p,location_c,location_a
			,address,reservation_date,remark,install_cost,customer_id,isvalid,ordertype,product_name,product_count,product_num) 
			values('".$install_order."','".$ordernum."',now(),0,'".$contact."','".$phone."','".$location_p."','".$location_c."','".$location_a."',
			'".$address."','".$lastTime."','".$remark."','".$install_cost."','".$customer_id."',1,1,'".$product_name."','".$product_count."','".$product_num."')";
			
			_mysql_query($query) or die("L67 : Query error : ".mysql_error());
			$id = mysql_insert_id(); 
			
			//$query = "update weixin_install_reservation set reservation_date = date_sub(reservation_date,interval 1 day) where id = ".$id;
			
			if(!empty($id) && $id > 0){
				$succ_rows++;
			}else{
				$fail_rows++;
			}
		}
		$msg = "订单导入完成！成功：".$succ_rows.",失败：".$fail_rows;
		
	}
		
}

echo "<script>alert('".$msg."');location.href='index.php?customer_id=".passport_encrypt($customer_id)."&show_index=3&ordertype=1'</script>";
mysql_close($link);

?>
