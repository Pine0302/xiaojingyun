<?php

header("Content-type: text/html; charset=utf-8");

//获取当前文件的路径的根目录
$basedir = dirname(__FILE__);
$badir   = explode('/weixinpl', $basedir);
$dir     = $badir[0];

//脚本访问
if(!isset($_SERVER['REMOTE_ADDR']))
{
	$_SERVER['DOCUMENT_ROOT'] = $dir; 
}

require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once $_SERVER['DOCUMENT_ROOT'].'/weixinpl/common/excel/phpExcelReader/Excel/reader.php';

_file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/save_suppy_order_excel.log",date("Y-m-d H:i:s",time())."==_SESSION==".var_export($_SESSION,true)."\n\n",FILE_APPEND);

if(empty($customer_id)){
	echo "<script>alert('登录超时！请重新登陆！');location.href='/weixin/plat/app/index.php/Supplier/order_consignment.html';</script>";
	return;
}

$log_username = $_SESSION['username'];

require_once($_SERVER['DOCUMENT_ROOT'].'/addons/vendor/PHPExcel/PHPExcel/IOFactory.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/addons/vendor/PHPExcel/PHPExcel.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/common/utility_shop.php');  //商城方法
$shopmessage = new shopMessage_Utlity();

ini_set('memory_limit','1024M');
ini_set('max_execution_time', '86400');
set_time_limit(0);
ignore_user_abort(true);

$link  = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$set   = "set interactive_timeout=2880000";
_mysql_query("SET NAMES UTF8");
_mysql_query($set);

$Execex = exec("ps aux|grep save_supply_order_excel.php|grep -v grep| wc -l",$error,$status);
$is_error = $_REQUEST['is_error'] ? 1 : 0;
if ($error[0] >= 1 && $status == 0 && $argv[1] != 'jbyx' && $is_error==0) {
	_file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/save_suppy_order_excel.log",date("Y-m-d H:i:s",time())."==error==1\n\n",FILE_APPEND);
	echo "<script>alert('有一条任务正在执行请稍后再尝试！');location.href='/weixin/plat/app/index.php/Supplier/order_consignment.html';</script>";
	exit;
}

//尝试连接常查询，如果错误抛出异常
define('IN_SWOOLE',true);
try{
	$db = DB::getInstance();
	$db->query("set session wait_timeout=2880000;");
    $db->query("set session interactive_timeout=2880000;");
} catch(Exception $e){
	_file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/save_suppy_order_excel.log",date("Y-m-d H:i:s",time())."==db==1\n\n",FILE_APPEND);
	echo "<script>alert('数据库发生未知错误！');location.href='/weixin/plat/app/index.php/Supplier/order_consignment.html';</script>";
	exit;
}

$email         = $_POST['email'];
$param_json    = $_POST['param_json'];
$function_name = $_POST['function_name'];
$type   	   = 3;//3-导入

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!is_uploaded_file($_FILES["excelfile"]["tmp_name"]))
	//是否存在文件
	{
	}else{
	    _file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/save_suppy_order_excel.log",date("Y-m-d H:i:s",time())."==_FILES==".var_export($_FILES,true)."\n\n",FILE_APPEND);


	    $Import_TmpFile = $_FILES['excelfile']['tmp_name'];

		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('utf-8');
        $data->read($Import_TmpFile);

		//原始文件名
		$soure_name = $_FILES["excelfile"]['name'];

        $folder ="../../../../resources/"; //上传文件路径
        if(!file_exists($folder)){
		    mkdir($folder);
		} else {
            if(!file_exists($folder.$customer_id."/")){
			    mkdir($folder.$customer_id."/");
			}
		}

		$param_url = $folder.$customer_id."/suppy_order_excel_".$_SESSION['supplier_Acount']."_".time().".xls";

		if(!move_uploaded_file ($_FILES["excelfile"]['tmp_name'], $param_url)) {
			echo "<script>alert('导入文件出错！');history.go(-1);</script>";
			exit;
		}

		$email = '';
		$function_name = 'supply_order_excel';
		$remark = date('Y-m-d H:i:s',time()).'任务待执行...';

		$arr['customer_id'] = $customer_id;
		$arr['url']         = $param_url;
		$arr['sel_type']    = $_POST['sel_type'];//1.普通excel 2.飞豆excel
		$param_json = json_encode($arr);

		//插入任务数据
		$query  = "INSERT INTO export_excel(customer_id,email,param_json,function_name,isvalid,createtime,type,result,remark) VALUES(".$customer_id.",'".$email."','".$param_json."','".$function_name."',true,now(),3,0,'".$remark."')";
		$result = _mysql_query($query) or die ('query failed21' .mysql_error());

		$http_url = Protocol.$_SERVER['HTTP_HOST'].'/mshop/admin/Order/order/save_suppy_order_excel.php?is_error=1';
	    $shellExecs = shell_exec("nohup curl -k --connect-timeout 60 -m 86400 '".$http_url."' > ./debug.txt 2>&1 &");

	    _file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/save_suppy_order_excel.log",date("Y-m-d H:i:s",time())."==http_url==".$http_url."\n\n",FILE_APPEND);
		echo "<script>alert('导入成功,等待加载数据！');location.href='/weixin/plat/app/index.php/Supplier/order_consignment.html';</script>";
		exit;
	}
}

//根据数据执行脚本
/**
 * [add_excel/add_excel_ex 插入任务数据]
 * @param  [array] $_POST  [搜索条件]
 * @return [array] $return [是否成功与成功id]
 */
$sql1  = "select id,email,customer_id,param_json,function_name,fields,type from export_excel where isvalid=1 and result=0";
$res1  = _mysql_query($sql1) or die("sql1 faile ".mysql_error());

$i     = 0;
while ($row = mysql_fetch_object($res1)) {
	$data[$i]['customer_id']   = $row->customer_id;
	$data[$i]['email'] 	       = $row->email;
	$data[$i]['param_json']    = $row->param_json;
	$data[$i]['function_name'] = $row->function_name;
	$data[$i]['fields'] 	   = $row->fields;
	$data[$i]['id'] 	   	   = $row->id;
	$data[$i]['type'] 	   	   = $row->type;

	$i++;
}

$count = count($data);
for($j = 0;$j < $count; $j++) {
	//修改任务状态为进行中
	$remark        = date('Y-m-d H:i:s',time()).' 任务执行中...';
	$sql3          = "update export_excel set result = 3,remark = '".$remark."'  where id=".$data[$j]['id'];
	_mysql_query($sql3) or die("sql3 export_excel failed:".mysql_error());

	//执行导出方法
	$function_name = $data[$j]['function_name'];
	$result 	   = $function_name($data[$j]);//根据查询出来的数据调用对应的方法
	sleep(1);
}

/**
* [get_filetype 返回文件后缀名]
* @param  [str] $path [原始文件名]
* @return [str] $str  [返回文件名 如'.php']
*/
function get_filetype($path) {
	$pos = strrpos ( $path, '.' );
	if ($pos !== false) {
		return substr ( $path, $pos );
	} else {
		return '';
	}
}

/**
* [import_excel 导入excel文件]
* @param  [str]   $file   [excel文件路径]
* @param  [str]   $type   [excel文件类型]
* @return [array] $data [excel文件内容数组]
*/
function import_excel($file,$type='xlsx'){
    ini_set('max_execution_time', '0');
    // 判断文件是什么格式
    switch ($type) {
    	case 'xlsx':
    		$type = 'Excel2007';
    		break;
    	case 'xls':
    		$type = 'Excel5';
    		break;
    	case 'csv':
    		$type = 'CSV';
    		break;
    	default:
    		$type = 'Excel2007';
    		break;
    }

    $objReader = PHPExcel_IOFactory::createReader($type);

    // 判断使用哪种格式
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getSheet(0);
    // 取得总行数
    $highestRow = $sheet->getHighestRow();
    // 取得总列数
    $highestColumn = $sheet->getHighestColumn();
    //循环读取excel文件,读取一条,插入一条
    $data=array();
    //从第一行开始读取数据

    for($j=1;$j<=$highestRow;$j++){
        //从A列读取数据
        for($k='A';$k<=$highestColumn;$k++){
            // 读取单元格
            $data[$j][]=(string)$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
        }
    }
    return $data;
}

//导入订单，飞豆
function supply_order_excel($obj){
	$param    = json_decode($obj['param_json'],true);
    $sqlid 	  = $obj['id']; //执行的任务表数据的id
    $customer_id              = $param['customer_id'];
    $url                      = $param['url'];
    $sel_type                 = $param['sel_type'];

    if (file_exists($url)) {
    	$c_data = import_excel($url,'xls');
	    _file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/save_suppy_order_excel.log",date("Y-m-d H:i:s",time())."==url==".var_export($url ,true)."\n\n",FILE_APPEND);
	    _file_put_contents($_SERVER['DOCUMENT_ROOT']."/weixinpl/save_suppy_order_excel.log",date("Y-m-d H:i:s",time())."==sel_type==".var_export($sel_type ,true)."\n\n",FILE_APPEND);
		if(!empty($c_data)){
			// //删除第一行
			array_shift($c_data);

			foreach($c_data as $k=>$v) {

			    if($sel_type == 1){		//普通excel获取参数
					$batchcode      = $v[1];
					$package_name   = $v[5];
					$express_remark = $v[23];
					$expressname    = $v[21];
					$expressnum     = $v[22];			
				}elseif($sel_type == 2){//飞豆excel获取参数
					$batchcode      = $v[6];			//订单号
					$package_name   = $v[7];			//商品名
					$express_remark = $v[13];			//订单备注
					$expressname    = $v[15];			//快递
					$expressnum     = $v[16];			//快递单号		
				}

				//去掉特殊符号
				$batchcode      = str_replace("'",'',$batchcode);
				$batchcode      = str_replace("\'",'',$batchcode);
				$batchcode      = str_replace("＇",'',$batchcode);
				$expressnum     = str_replace("'",'',$expressnum);
				$expressnum     = str_replace("\'",'',$expressnum);
				$expressnum     = str_replace("＇",'',$expressnum);
				
				$express_id = -1;
				$query = 'SELECT id FROM weixin_expresses_supply where isvalid=true and customer_id='.$customer_id.' and name= "'.$expressname.'"';
				$result=_mysql_query($query)or die('Query failed'.mysql_error());
				while($row=mysql_fetch_object($result)){
					$express_id = $row->id;
				}
				
				$query="select user_id,agent_id,totalprice,agentcont_type,sendstatus,pid,is_QR from weixin_commonshop_orders where isvalid=true and batchcode='".$batchcode."'";
				$result = _mysql_query($query) or die('Query failed26: ' . mysql_error());
				$user_id=-1;
				$agent_id=-1;
				while ($row = mysql_fetch_object($result)) {
					$user_id	    = $row->user_id;
					$agent_id		= $row->agent_id; 	//代理商user_id
					$totalprice     = $row->totalprice; 	//订单总金额
					$agentcont_type = $row->agentcont_type; 	
					$sendstatus     = $row->sendstatus;
					$pid            = $row->pid;
					$is_QR			= $row->is_QR;				
					
					$sql="select agent_discount from weixin_commonshop_products where isvalid=true and id='".$pid."'";
					$result_sql = _mysql_query($sql) or die('Query failed: ' . mysql_error());
					$agent_discount=0;
					while ($row_sql = mysql_fetch_object($result_sql)) {
						$agent_discount = $row_sql->agent_discount;
					}
					if($agent_id>0 and $agentcont_type==1 and $sendstatus==0){
						//,扣除代理库存余额 和 代理得到的金额 start
						$query2="select agent_inventory from promoters where status=1 and isvalid=true and user_id=".$agent_id;	//查找代理商代理剩余库存金额
						$agent_inventory = 0;
						$result2 = _mysql_query($query2) or die('Query failed43: ' . mysql_error());
						while ($row2 = mysql_fetch_object($result2)) {
							$agent_inventory = $row2->agent_inventory;
						}
						if($agent_discount==0){
							$query2="select agent_discount from weixin_commonshop_applyagents where status=1 and isvalid=true and user_id=".$agent_id;	
							$result2 = _mysql_query($query2) or die('Query failed48: ' . mysql_error());
							$agent_discount =0;
							while ($row2 = mysql_fetch_object($result2)) {
								$agent_discount = $row2->agent_discount;
							}
						}
						$agent_discount =  $agent_discount/100;
						$agent_cost_inventorymoney = $totalprice*$agent_discount;	//从代理金额扣除成本价
						$agent_cost_inventorymoney = round($agent_cost_inventorymoney,2);
						$agent_inventory = $agent_inventory - $agent_cost_inventorymoney;
						$agent_cost_inventorymoney = 0-$agent_cost_inventorymoney;
						$sql1 = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory) values(".$agent_id.",'".$batchcode."',".$agent_cost_inventorymoney.",'发货(出库)',1,true,now(),".$agent_inventory.")";

						_mysql_query($sql1);		//插入扣除成本价
						$sql = "update promoters set agent_inventory=".$agent_inventory." where user_id=".$agent_id;
						_mysql_query($sql) or die('Query failed63: ' . mysql_error());
						//购买支付后,扣除代理库存余额 和 代理得到的金额 end
					}
				}
				if( !empty($expressname) && !empty($expressnum) && $sendstatus == 0 ){
					$query="select weixin_fromuser from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
					$result = _mysql_query($query) or die('Query failed70: ' . mysql_error());
					$order_fromuser="";
					while ($row = mysql_fetch_object($result)) {
						$order_fromuser = $row->weixin_fromuser;
						break;
					}

					$query_cus = " select auto_cus_time from weixin_commonshops where isvalid = true and customer_id = ".$customer_id;
					$result_cus = _mysql_query($query_cus);
					$auto_cus_time = mysql_result($result_cus,0,0);
					if(empty($auto_cus_time) || $auto_cus_time <= 0){ //如没有设置时间默认为7天
						$auto_cus_time = 7;
					}
					$sql="update weixin_commonshop_orders set sendstatus = 1, expressname = '".$expressname."',confirm_sendtime=now(),auto_receivetime = DATE_ADD( now(), INTERVAL ".$auto_cus_time." DAY ),send_express_id=".$express_id.",expressnum='".$expressnum."',send_remarks='".$express_remark."' where batchcode='".$batchcode."'";
					_mysql_query($sql) or die('Query failed103: ' . mysql_error());

		            //券类订单发放二维码
					if($is_QR==1){
						$shopmessage->GetQR($batchcode,$order_fromuser,$customer_id);
					}
					$content = "商家已经确认您的订单号：".$batchcode.",于".date( "Y-m-d H:i:s")."向您发货，快递单号为:<a href='//m.kuaidi100.com/result.jsp?nu=".$expressnum."'>".$expressnum."(点击查看物流)</a>，快递公司:".$expressname.",请注意查收！";
					if(!empty($express_remark)){
						$content=$content."备注:".$express_remark;
					}
					$shopmessage->SendMessage($content,$order_fromuser,$customer_id);
					
					$username = $_SESSION['supplier_Acount'];
					$roletype = $_SESSION['user_roletype']; //1 ：代理商 ； 3 ：供应商
					if($roletype == 1){
						$roletypeStr = "代理商";
					}else if($roletype == 3){
						$roletypeStr = "合作商";
					}
					$query_logs = "insert into weixin_commonshop_order_logs(batchcode,operation,descript,operation_user,createtime,isvalid) 
					values('".$batchcode."',4,'".$roletypeStr."发货[物流：".$expressname.",单号：".$expressnum."]','".$username."',now(),1)";
					_mysql_query($query_logs) or die("L365 query error  : ".mysql_error());
				}

	            $remark   = date('Y-m-d H:i:s',time())." 导入完成！";
	            $rewu_sql = "update export_excel set result = 1,remark = '".$remark."' where id = $sqlid";

	            unlink ($url);
				_mysql_query($rewu_sql)or die('Query rewu_sql'.mysql_error());
			}
		}
	} else {
		$remark   = date('Y-m-d H:i:s',time())." 文件不存在，导入失败！";
        $rewu_sql = "update export_excel set result = 2,remark = '".$remark."' where id = $sqlid";

        unlink ($url);

		_mysql_query($rewu_sql)or die('Query rewu_sql'.mysql_error());
	}

}
mysql_close();
?>