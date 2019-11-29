<?php
// header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
require('../../../../weixinpl/function_model/public_function.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");


$op		    ="";  //ajax操作
$status     ="0";

if($_POST["op"]){
    $op	=	$configutil->splash_new($_POST["op"]);
}
if($_POST["id"]){
    $id	=	$configutil->splash_new($_POST["id"]);
}
if($_POST["timelimit_type"]){
    $timelimit_type	=	$configutil->splash_new($_POST["timelimit_type"]);
}
if($_POST["id"]){
    $status	= $configutil->splash_new($_POST["ad_status"]);
    $status = abs($status - 1);
}

switch ($op){
    case 'change_status' :
        //上架前要判断广告是否已经过期
        $result1 = ads_isOverdue($status,$timelimit_type,$end_time,$customer_id,$id);
        if($result1 != -1){
            $query1 = "update weixin_commonshop_ads set status=".$status." where customer_id=".$customer_id." and id=".$id;
            $result=_mysql_query($query1) or die ('ads_change_status failed' .mysql_error());
        }
        break;
    case 'del_ads' :
            $result = del($customer_id,$id);
        break;
    default;
}

if($result){
    $str->code =1;
}else{
    $str->code =0;
}
if($result1 == -1){
    $str->code =-1;
}
echo json_encode($str);

//判断广告是否过期,返回-1则过期
function ads_isOverdue($status,$timelimit_type,$end_time,$customer_id,$id){
    if($status == 1 && $timelimit_type == 1){
        $nowtime = strtotime(date("Y-m-d H:i:s",time()));
        $query = "select end_time,$timelimit_type from weixin_commonshop_ads where  customer_id=".$customer_id." and id=".$id;
        $res=_mysql_query($query) or die ('ads_select failed' .mysql_error());
        while ($row = mysql_fetch_object($res)) {
            $end_time = $row->end_time;
        }
        if($end_time != ''){
            $end_time = strtotime($end_time);
            if(($nowtime - $end_time )>0){
                return  -1;
            }
        }
    }
}

//删除广告
function del($customer_id,$id){
    $del_query="update  weixin_commonshop_ads set isvalid=false and status=0 where customer_id=".$customer_id." and id=".$id;
    return _mysql_query($del_query) or die ('del_ads failed' .mysql_error());
}

?>