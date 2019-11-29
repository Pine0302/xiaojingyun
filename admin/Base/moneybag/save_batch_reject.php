<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/common/utility_shop.php');

$box_arr   = $_POST["box_arr"];
$batch_tis_str = $_POST['batch_tis_str'];
$box_arr   = json_decode($box_arr,true);//json转数组
for( $i=0; $i < count($box_arr); $i++ )
{
    $id     =  $box_arr[$i]['id'];
    $type   =  'false_type';
    $tis    =  $batch_tis_str;
    $type 	=  $configutil->splash_new($type);
    $id 	=  $configutil->splash_new($id);
    $tis    =  $configutil->splash_new($tis);

    save_cash_type($id, $type, $tis, $customer_id);
}



function save_cash_type($id, $type, $tis, $customer_id){

    $shopMessage_Utlity = new shopMessage_Utlity;

    //驳回理由
    $remark             = $tis;

    $user_id 	= -1;
    $getmoney 	= 0;
    $batchcode  = "";
    $status     = -1;
    $query      = "SELECT user_id,getmoney,batchcode,status FROM weixin_cash_being_log WHERE isvalid=true AND customer_id=$customer_id AND id=".$id." LIMIT 1";
    $result     = _mysql_query($query) or die('Query failed 25: ' . mysql_error()." query ==".$query);
    while( $row = mysql_fetch_object($result) ){
        $user_id 	= $row->user_id;
        $getmoney 	= $row->getmoney;
        $batchcode 	= $row->batchcode;
        $status 	= $row->status;
    }

    /*查询现在的零钱余额*/
    $sql = "select balance from moneybag_t where isvalid=true and user_id=".$user_id;
    $result= _mysql_query($sql) or die('Query failed 251: ' . mysql_error()." query ==".$sql);
    $balance = mysql_result($result,0,0);
    $after_money = $balance+$getmoney;

    _mysql_query('set autocommit=0') or die('Query failed4: ' . mysql_error());
    _mysql_query('SET session TRANSACTION ISOLATION LEVEL SERIALIZABLE') or die('Query failed4: ' . mysql_error());
    _mysql_query('start transaction');

    if($type=='delete_type'){
        //如果该提现未被驳回，则需要退钱进钱包
        //提现不通过则返还金额给用户
        if( $status == 0 ){
            $query = "UPDATE moneybag_t SET balance = balance + $getmoney WHERE isvalid = true AND customer_id = $customer_id AND user_id = $user_id";
            _mysql_query($query) or die('Query failed 252: ' . mysql_error()." query ==".$query);
            //插入日志
            $remark = "提现申请退回";
            $query = "INSERT INTO moneybag_log(isvalid,customer_id,user_id,money,type,batchcode,pay_style,remark,createtime,before_money,after_money) VALUES(true,$customer_id,$user_id,'$getmoney',0,'$batchcode',5,'$remark',now(),".$balance.",".$after_money.")";
            _mysql_query($query) or die('Query failed 25: ' . mysql_error()." query ==".$query);
        }

        $query = "UPDATE weixin_cash_being_log SET isvalid = false WHERE isvalid=true AND customer_id=$customer_id AND id=".$id;
        _mysql_query($query) or die('Query failed 25: ' . mysql_error()." query ==".$query);
        _mysql_query("COMMIT");
        echo json_encode(400);
        return false;
    }


    if($status == 0){	//当该笔提现状态为未审核情况下才能操作

        //提现不通过则返还金额给用户
        $query = "UPDATE moneybag_t SET balance = balance + $getmoney WHERE isvalid = true AND customer_id = $customer_id AND user_id = $user_id";
        _mysql_query($query) or die('Query failed 25: ' . mysql_error()." query ==".$query);

        //插入日志
        //$remark = "商家驳回您的提现，提现金额为：【".$getmoney."】元";
        $query = "INSERT INTO moneybag_log(isvalid,customer_id,user_id,money,type,batchcode,pay_style,remark,createtime,before_money,after_money) VALUES(true,$customer_id,$user_id,'$getmoney',0,'$batchcode',5,'$remark',now(),".$balance.",".$after_money.")";
        _mysql_query($query) or die('Query failed 253: ' . mysql_error()." query ==".$query);

        switch ($type) {
            case 'false_type'://驳回申请
                $query = "UPDATE weixin_cash_being_log SET status = 2,processing_time=now() WHERE isvalid=true AND customer_id=$customer_id AND id=".$id;
                _mysql_query($query) or die('Query failed 25: ' . mysql_error()." query ==".$query);
                if( $user_id > 0 ){
                    $weixin_fromuser = '';
                    $sql = "SELECT weixin_fromuser FROM weixin_users WHERE isvalid=true AND id=".$user_id." LIMIT 1";
                    $res = _mysql_query($sql) or die('Query failed 25: ' . mysql_error()." query ==".$sql);
                    while( $row = mysql_fetch_object($res) ){
                        $weixin_fromuser = $row->weixin_fromuser;
                    }
                    $msg_content = 	"亲，您申请提现被驳回 \n".
                        "申请提现零钱：【".$getmoney."】\n".
                        "驳回理由：【".$remark."】\n".
                        "时间：".date( "Y-m-d H:i:s")."";
                    $shopMessage_Utlity->SendMessage($msg_content,$weixin_fromuser,$customer_id);
                }
                _mysql_query("COMMIT");
//                $json['status'] = 400;
//                $json['datetime'] = date( "Y-m-d H:i:s");
//                echo json_encode($json);

                break;

                // case 'delete_type':
                $query = "UPDATE weixin_cash_being_log SET isvalid = false WHERE isvalid=true AND customer_id=$customer_id AND id=".$id;
                _mysql_query($query) or die('Query failed 25: ' . mysql_error()." query ==".$query);
                _mysql_query("COMMIT");
                echo json_encode(400);

            // break;

            default:
                # code...
                break;
        }

    }
    // _mysql_query("COMMIT");
}

$json["status"] = 0;
$json["msg"] = "批量驳回提现完成";
$jsons=json_encode($json);
die($jsons);

?>