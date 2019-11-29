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

$is_web = $_GET['is_web'];
if ($is_web == 1) {
    //头文件----start
    require('../../../../weixinpl/common/common_from.php');
    //头文件----end
} else {
    require('../../../../weixinpl/back_init.php');
}
require('./switch_type.php');

$str = $_GET['type'];
$postid = $_GET['postid'];
$batchcode = $_GET['batchcode'];

$query = "select is_kuaidi,appkey,appsecret,appcode from weixin_commonshops where isvalid=true and customer_id=".$customer_id; 
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$is_kuaidi = 0; //快递查询方式：0免费查询，1付费查询 默认0
while ($row = mysql_fetch_object($result)) {
    $is_kuaidi = $row->is_kuaidi;
    $AppKey = $row->appkey;
    $AppSecret = $row->appsecret;
    $AppCode = $row->appcode;
}

if ($is_kuaidi != 1) {
    if ($is_web == 1) {
        echo "<script>alert('错误访问方式');history.back(-1);</script>";
        exit;
    } else {
        echo "<script>alert('错误访问方式');</script>";
        exit;
    }
}

$query_orders = "select pid from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and batchcode='".$batchcode."'";
$result_orders = _mysql_query($query_orders) or die('query failed3'.mysql_error());
while($row_orders = mysql_fetch_object($result_orders)){
    $pid            = $row_orders->pid;               //商品ID
}

if ($pid) {
    $query = "select default_imgurl from weixin_commonshop_products where customer_id=".$customer_id." and id=".$pid;
    $result = _mysql_query($query) or die('query failed6'.mysql_error());
    while($row = mysql_fetch_object($result)){
        $product_default_imgurl = $row->default_imgurl;    //商品封面图
    }
}

if(empty($product_default_imgurl) && $pid){
    $query6 = "select imgurl from weixin_commonshop_product_imgs where isvalid=true and customer_id=".$customer_id." and product_id=".$pid." limit 1";

    $result6 = _mysql_query($query6) or die('query failed6'.mysql_error());
    while($row6 = mysql_fetch_object($result6)){
        $product_default_imgurl = $row6->imgurl;    //商品封面图
    }
}

$type_ex = kuaidi_ex($str);

$str = '&type='.$type_ex;

if($type_ex == 'SFEXPRESS'){
    $query  = "select phone FROM weixin_commonshop_order_addresses WHERE batchcode ='".$batchcode."'";
    $result = _mysql_query($query) or die('query query'.mysql_error());
    while($row = mysql_fetch_object($result)){
        $phone  = $row->phone;
        $postid = $postid.':'.substr($row->phone,-4);
    }
}

if ( !$_SESSION['expiretime'.$postid] ) {
    $_SESSION['expiretime'.$postid] = 0;
}

if ( $_SESSION['expiretime'.$postid] < time() ) {
    $host = "https://wuliu.market.alicloudapi.com";//api访问链接
    $path = "/kdi";//API访问后缀
    $method = "GET";
    // $appcode = "";//替换成自己的阿里云appcode
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $AppCode);
    $querys = "no=".$postid.$str;  //参数写在这里
    $bodys = "";
    $url = $host . $path . "?" . $querys;//url拼接

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    // curl_setopt($curl, CURLOPT_HEADER, true); //如不输出json, 请打开这行代码，打印调试头部状态码。
    //状态码: 200 正常；400 URL无效；401 appCode错误； 403 次数用完； 500 API网管错误
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $res = curl_exec($curl);
    
    $res_ck = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
    
    switch ($res_ck) {
        case '400':
            echo "<script>alert('URL无效');</script>";
            break;
        case '401':
            echo "<script>alert('appCode错误');</script>";
            break;
        case '403':
            echo "<script>alert('次数用完');</script>";
            break;
        case '500':
            echo "<script>alert('API网管错误');</script>";
            break;
        default:
            # code...
            break;
    }

    $res = json_decode($res,true);

    /*  1.在途中 2.正在派件 3.已签收 4.派送失败  */
    switch ($res['result']['deliverystatus']){
        case 1:
            $res['result']['deliverystatus'] = '在途中';
            break;
        case 2:
            $res['result']['deliverystatus'] = '正在派件';
            break;
        case 3:
            $res['result']['deliverystatus'] = '已签收';
            break;
        case 4:
            $res['result']['deliverystatus'] = '派送失败';
            break;
        case 4:
            $res['result']['deliverystatus'] = '疑难件';
            break;
        case 4:
            $res['result']['deliverystatus'] = '退件签收';
            break;
        default:
            $res['result']['deliverystatus'] = '未知状态';
            break;
    }

    $_SESSION['expiretime'.$postid] = time() + 5;
    $_SESSION['kuaidi_res'.$postid] = $res; 
} else {
    $res = $_SESSION['kuaidi_res'.$postid];
}

if ($res['status'] != 0) {
    // if ($is_web == 1) {
    //     echo "<script>alert('".$res['msg']."');window.location.href = '/weixinpl/mshop/orderlist.php?customer_id=".$customer_id_en."';</script>";
    // } else {
    //     echo "<script>alert('".$res['msg']."');</script>";
    //     exit;
    // }
} else {
    if ( $res['status'] == '' ) {
        if ($is_web == 1) {
            // echo "<script>alert('appCode错误或者次数用完');history.back(-1);</script>";
        } else {
            // echo "<script>alert('appCode错误或者次数用完');</script>";
            // exit;
        }
    }
}

$postid = $_GET['postid'];

if($res['result']['deliverystatus'] == "未知状态" && $type_ex == 'SFEXPRESS'){
    echo "<script>alert('抱歉！未查到此运单信息，已跳转到顺丰官网');window.location.href = 'http://www.sf-express.com/mobile/cn/sc/dynamic_function/waybill/waybill_query_by_billno.html';</script>";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>快递查询</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta content="no" name="apple-touch-fullscreen">
        <meta name="MobileOptimized" content="320" />
        <meta name="format-detection" content="telephone=no">
        <meta name=apple-mobile-web-app-capable content=yes>
        <meta name=apple-mobile-web-app-status-bar-style content=black>
        <meta http-equiv="pragma" content="nocache">
        <style type="text/css">
            *{padding: 0;margin:0;}
            body{background-color: #f5f5f5;}
            .skin-color{color: #ff8430;}
            .order-infor{display:-webkit-flex;display:flex;-webkit-justify-content:space-between;justify-content:space-between;-webkit-align-items: center;align-items: center;background-color: #fff;padding: 15px 4%;}
            .order-infor .img-box{font-size: 0;overflow: hidden;width: 65px;height: 65px;min-width: 65px;box-sizing: border-box;border:solid 1px #eee;border-radius: 3px;}
            .order-infor .img-box img{max-width: 100%;height: 100%;}
            .order-infor .text-box{font-size: 15px;width: calc(100% - 65px);line-height: 1.5;box-sizing: border-box;padding-left: 10px;}
            .order-infor .status{font-size: 15px;}
            .order-infor .order{color: #000;}
            .order-infor .order>span{margin-left: 5px;}
            .order-infor .tel-num>span{color: #6a7fa6;margin-left: 5px;}
            .courier{display:-webkit-flex;display:flex;-webkit-justify-content:space-between;justify-content:space-between;-webkit-align-items: center;align-items: center;background-color: #fff;margin:10px 0;padding: 15px 4%;}
            .courier .img-box{font-size: 0;width: 50px;height: 50px;overflow: hidden;border-radius: 50%;}
            .courier .img-box img{max-width: 100%;height: 100%;}
            .courier .infor-box{-webkit-flex: 1;flex: 1;box-sizing: border-box;padding: 0 10px;line-height: 1.5;}
            .courier .infor-box .name{font-size: 15px;color: #1c1f20;}
            .courier .infor-box .small{font-size: 12px;color: #999;}
            .courier .right .icon{height: 24px;}
            .courier .right{font-size: 0;background: url(images/icon_right.png) no-repeat right center;background-size: auto 12px;padding-right: 16px;}
            .record{background-color: #fff;padding: 15px 4%;}
            .record .list{display:-webkit-flex;display:flex;-webkit-justify-content:space-between;justify-content:space-between;margin:10px 0;}
            .record .list .time:after{content: '';display: block;height: 25px;border-left:solid 1px #999;margin-top: 12px;margin-left:50%;}
            .record .list:last-child .time:after{display: none;}
            .record .list .time{width: 20%;text-align: center;line-height: 1.4;}
            .record .list .hour{font-size: 15px;color: #999;}
            .record .list .day{font-size: 12px;color: #999;}
            .record .list .explain{-webkit-flex: 1;flex: 1;font-size: 15px;color: #999;word-break: break-all;box-sizing: border-box;padding-left: 12px;}
            .record .list.active .hour{color: #1c1f20;}
            .record .list.active .explain{color: #1c1f20;}
        </style>
    </head>
    <body>
        <div class="order-infor">
            <div class="img-box"><img src="<?php echo $product_default_imgurl;?>"></div>
            <div class="text-box">
                <p class="status skin-color"><?php echo $res['result']['deliverystatus'];?></p>
                <p class="order"><?php echo $res['result']['expName'];?><span>运单编号：<?php echo $postid;?></span></p>
                <p class="tel-num">官方电话<span><?php echo $res['result']['expPhone'];?></span></p>
            </div>
        </div>
        <!-- <div class="courier">
            <div class="img-box"><img src="http://weisanyun.com/images/img1.png"></div>
            <div class="infor-box">
                <p class="name">武邑</p>
                <p class="small">配送员</p>
            </div>
            <a href="javascript:;" class="right">
                <img class="icon" src="images/icon_tel.png">
            </a>
        </div> -->
        <?php if ($res['status'] != 0) {?>
        <div class="record">
            <div class="list active">
                <div class="explain" style="text-align:center"><?php echo $res['msg'];?></div>
            </div>
        </div>
        <?php } ?>

        <div class="record">
            <?php foreach ($res['result']['list'] as $k => $v) { $time = explode(" ", $v['time']); ?>
            <div class="list <?php if($k==0) { echo 'active';}?>">
                <div class="time">
                    <p class="hour"><?php echo $time[1];?></p>
                    <p class="day"><?php echo $time[0];?></p>
                </div>
                <div class="explain"><?php echo $v['status'];?></div>
            </div>
            <?php } ?>
        </div>
    </body>
</html>