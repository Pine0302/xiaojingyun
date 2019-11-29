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

$postid = $_GET['postid'];
$batchcode = $_GET['batchcode'];

$query = "select is_kuaidi,appkey,appsecret,appcode from weixin_commonshops where isvalid=true and customer_id=".$customer_id; 
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$is_kuaidi = 0; //快递查询方式：0免费查询，1付费查询 默认0
while ($row = mysql_fetch_object($result)) {
    $is_kuaidi = $row->is_kuaidi;
}

if ($is_kuaidi != 2) {
    if ($is_web == 1) {
        echo "<script>alert('错误访问方式');window.location.href = '/weixinpl/mshop/orderlist.php?customer_id=".$customer_id_en."';</script>";
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

$query = "select default_imgurl from weixin_commonshop_products where customer_id=".$customer_id." and id=".$pid;
$result = _mysql_query($query) or die('query failed6'.mysql_error());
while($row = mysql_fetch_object($result)){
    $product_default_imgurl = $row->default_imgurl;    //商品封面图
}

if(empty($product_default_imgurl)){
    $query6 = "select imgurl from weixin_commonshop_product_imgs where isvalid=true and customer_id=".$customer_id." and product_id=".$pid." limit 1";

    $result6 = _mysql_query($query6) or die('query failed6'.mysql_error());
    while($row6 = mysql_fetch_object($result6)){
        $product_default_imgurl = $row6->imgurl;    //商品封面图
    }
}

if ( !$_SESSION['expiretime'.$postid] ) {
    $_SESSION['expiretime'.$postid] = 0;
}

$kd_dan = Getkuaidi('',$postid,1)[0]['comCode'];
if(!$kd_dan) {
    // if ($is_web == 1) {
    //     echo "<script>alert('运单未找到');window.location.href = '/weixinpl/mshop/orderlist.php?customer_id=".$customer_id_en."';</script>";
    // } else {
    //     echo "<script>alert('运单未找到');</script>";
    //     exit;
    // }
} else {
    $res    = Getkuaidi($kd_dan,$postid,2);
}

// if ($res['status'] != 200) {
//     if ($is_web == 1) {
//         echo "<script>alert('".$res['message']."');window.location.href = '/weixinpl/mshop/orderlist.php?customer_id=".$customer_id_en."';</script>";
//     } else {
//         echo "<script>alert('".$res['message']."');</script>";
//         exit;
//     }
// }

$str    = kuaidi_ex($res['com']);

/*      state：
 *      0：在途，即货物处于运输过程中；
 *      1：揽件，货物已由快递公司揽收并且产生了第一条跟踪信息；
 *      2：疑难，货物寄送过程出了问题；
 *      3：签收，收件人已签收；
 *      4：退签，即货物由于用户拒签、超区等原因退回，而且发件人已经签收；
 *      5：派件，即快递正在进行同城派件；
 *      6：退回，货物正处于退回发件人的途中;
 */
if ($res['state']) {
   switch ($res['state']){
        case 0:
            $res['state'] = '在途中';
           break;
        case 1:
            $res['state'] = '揽件中';
          break;
        case 2:
            $res['state'] = '疑难';
          break;
        case 3:
            $res['state'] = '已签收';
          break;
        case 4:
            $res['state'] = '已退签';
          break;
        case 5:
            $res['state'] = '派件中';
          break;
        case 6:
            $res['state'] = '退回';
          break;
        default:
            $res['state'] = '未知状态';
          break;
    } 
} else {
    $res['state'] = '未知状态';
}


function Getkuaidi ($comCode,$postid,$is_type) {
    $path   = "";  //API访问后缀
    $querys = "";  //参数写在这里

    if ($is_type == 1) {
        $path = "/autonumber/auto";
        $querys = "num=".$postid;
    } else {
        $path = "/query";
        $querys = "type=".$comCode."&postid=".$postid;
    }

    $host = "https://m.kuaidi100.com";//api访问链接
    $method = "GET";//传参方式 GET && POST
    $url = $host . $path . "?" . $querys;//url拼接

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $res = curl_exec($curl);
    $res = json_decode($res,true);
    return $res;
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
                <p class="status skin-color"><?php echo $res['state'];?></p>
                <p class="order"><?php echo $str;?><span>运单编号：<?php echo $postid;?></span></p>
                <!-- <p class="tel-num">官方电话<span><?php echo $res['result']['expPhone'];?></span></p> -->
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
        
        <?php if ($res['status'] != 200 || empty($res['state']) ) {?>
        <div class="record">
            <div class="list active">
                <div class="explain" style="text-align:center"><?php echo $res['msg']?$res['msg']:'暂无信息';?></div>
            </div>
        </div>
        <?php } ?>

        <div class="record">
            <?php foreach ($res['data'] as $k => $v) { $time = explode(" ", $v['time']); ?>
            <div class="list <?php if($k==0) { echo 'active';}?>">
                <div class="time">
                    <p class="hour"><?php echo $time[1];?></p>
                    <p class="day"><?php echo $time[0];?></p>
                </div>
                <div class="explain"><?php echo $v['context'];?></div>
            </div>
            <?php } ?>
        </div>
    </body>
</html>