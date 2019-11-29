<?php
session_start();
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$customer_id_en = $customer_id;
$customer_id = passport_decrypt($customer_id);

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

if (!$customer_id) {
    echo "<script>alert('缺少customer_id');history.back(-1);</script>";
    exit;
}

$query = "select is_kuaidi,appkey,appsecret,appcode from weixin_commonshops where isvalid=true and customer_id=".$customer_id; 
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$is_kuaidi = 0; //快递查询方式：0免费查询，1付费查询 默认0
while ($row = mysql_fetch_object($result)) {
    $is_kuaidi = $row->is_kuaidi;
}

$is_web    = $_GET['is_web']?1:0;//是否手机端
$str       = $_GET['type'];      //快递类型
$postid    = $_GET['postid'];    //运送单号
$batchcode = $_GET['batchcode']; //订单号

if ($is_web == 1) {
    // if (!$str && $is_kuaidi==1) {
    //     echo "<script>alert('缺少快递类型');window.location.href = '/weixinpl/mshop/orderlist.php?customer_id=".$customer_id_en."';</script>";
    //     exit;
    // }

    if (!$postid) {
        echo "<script>alert('缺少运送单号');history.back(-1);</script>";
        exit;
    }

    if (!$batchcode && ($is_kuaidi==1 || $is_kuaidi==2) ) {
        echo "<script>alert('缺少订单号');history.back(-1);</script>";
        exit;
    }

    if (!$customer_id) {
        echo "<script>alert('缺少customer_id');history.back(-1);</script>";
        exit;
    }

} else {
    // if (!$str && $is_kuaidi==1) {
    //     echo "<script>alert('缺少快递类型');</script>";
    //     exit;
    // }

    if (!$postid) {
        echo "<script>alert('缺少运送单号');</script>";
        exit;
    }

    if (!$batchcode && ($is_kuaidi==1 || $is_kuaidi==2) ) {
        echo "<script>alert('缺少订单号');</script>";
        exit;
    }

    if (!$customer_id) {
        echo "<script>alert('缺少customer_id');</script>";
        exit;
    }
}

switch ($is_kuaidi) {
    case 0:
        $href = "https://m.kuaidi100.com/index_all.html?postid=".$postid."&type=".$str;
        break;
    case 1:
        $href = $_SERVER['DOCUMENT_ROOT']."/weixinpl/back_newshops/Distribution/settings/kuaidi_ck.php?is_web=".$is_web."&customer_id=".$customer_id_en."&batchcode=".$batchcode."&postid=".$postid."&type=".$str;
        break;
    case 2:
        $href = $_SERVER['DOCUMENT_ROOT']."/weixinpl/back_newshops/Distribution/settings/kuaidi100.php?is_web=".$is_web."&customer_id=".$customer_id_en."&batchcode=".$batchcode."&postid=".$postid;
        break;
    default:
        $href = "https://m.kuaidi100.com/index_all.html?postid=".$postid."&type=".$str;
        break;
}

echo "<script>window.location.href = '".$href."';</script>";
?>