<?php
$data_express = array(
/*  '中通快递'=>'ZTO',
  '中通速递'=>'ZTO',
  '顺丰速运'=>'SF',*/
  '申通E物流'=>'STO',
  '申通'=>'STO',
  '申通速递'=>'STO',
  '申通快递'=>'STO',
/*  '圆通速递'=>'YTO',
  '韵达速递'=>'YD',
  '邮政国内小包'=>'YZPY',
  '安能快递'=>'ANEKY',
  '百世汇通'=>'HTKY',
  'EMS'=>'EMS',
  '京东快递'=>'JD',
  '快捷速递'=>'SF',
  '国通'=>'GTO',
  '民航快递'=>'MHKD',
  '天天快递'=>'HHTT',
  '德邦快递'=>'DBL',*/
);

$data = $_REQUEST;
$type = $data['type'];
$id = $data['id'];
$code = $data_express[$type];

if(!empty($code)){
  $page_show = 1;
}else{
  $page_show = 2;
}


//电商ID
//defined('EBusinessID') or define('EBusinessID', 'test1379950');
defined('EBusinessID') or define('EBusinessID', '1379950');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', '475d6882-8773-4171-9613-fc40eb016e72');
//请求url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');
//defined('ReqURL') or define('ReqURL', 'http://sandboxapi.kdniao.cc:8080/kdniaosandbox/gateway/exterfaceInvoke.json');
$state_info = "暂无物流状态";
//调用查询物流轨迹
//---------------------------------------------
if($page_show==1){
  $logisticResult=getOrderTracesByJson($code,$id);
  $trace = $logisticResult['Traces'];
  $state = $logisticResult['State'];
  if($state==3){
    $state_info = "已签收";
  }elseif($state==4){
    $state_info = "问题件";
  }elseif($state==2){
    $state_info = "在途中";
  }else{
    $state_info = "暂无物流状态";
  }

  if (!empty($trace)) {
    $trace_ori = $trace;
    $trace = array_reverse($trace);
    $first = $trace[0];
    unset($trace[0]);
  }else{
    $trace = array();
  }
  //var_dump($trace);exit;
  ///$trace
}


//---------------------------------------------
 
/**
 * Json方式 查询订单物流轨迹
 */
function getOrderTracesByJson($code,$id){
  $requestData= "{'ShipperCode':'STO','LogisticCode':'{$id}'}";
  //$requestData= "{'ShipperCode':'STO','LogisticCode':'3374879252024'}";
  
  $datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '8001',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, AppKey);
    $result=sendPost(ReqURL, $datas); 
    $result = json_decode($result,true);

  return $result;
}
 
/**
 *  post提交数据 
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据 
 * @return url响应返回的html
 */
function sendPost($url, $datas) {
    $temps = array(); 
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);    
    } 
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
  if(empty($url_info['port']))
  {
    $url_info['port']=80; 
  }
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
  $headerFlag = true;
  while (!feof($fd)) {
    if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
      break;
    }
  }
    while (!feof($fd)) {
    $gets.= fread($fd, 128);
    }
    fclose($fd);  
    
    return $gets;
}

/**
 * 电商Sign签名生成
 * @param data 内容   
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}

?>


<!DOCTYPE html>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title></title>
    <link rel="stylesheet" type="text/css" href="css/common.css" />
    <style type="text/css">
      body {
        margin: 0;
        padding: 0;
        font-size: 0.26rem;
      }
      
      .container {
        width: 100%;
        height: auto;
        background: #F2F2F2;
      }
      
      .package {
        width: 100%;
        height: auto;
        background: #FFFFFF;
      }
      
      .paTitle {
        width: 100%;
        height: 1rem;
        line-height: 1rem;
        display: flex;
        background: #2E72FB;
        justify-content: center;
      }
      
      .paTitle span {
        font-size: 0.36rem;
        color: #FFFFFF;
        font-weight: bold;
      }
      
      .paHeader {
        width: auto;
        height: 1.2rem;
        padding: 0.3rem;
        display: flex;
        display: -webkit-flex;
        flex-direction: row;
      }
      
      .paHeaderLeft {
        width: 18%;
        height: 1.2rem;
      }
      
      .paHeaderLeft img {
        width: 1.12rem;
        height: 1.1rem;
      }
      
      .paHeaderRight {
        display: flex;
        display: -webkit-flex;
        flex-direction: column;
        width: 82%;
        height: 1.2rem;
        padding: 0.1rem;
      }
      
      .qs {
        width: 100%;
        height: 50%;
        font-size: 0.36rem;
      }
      
      .orderNumber {
        width: 100%;
        height: 50%;
        font-size: 0.24rem;
        color: #999999;
      }
      /*顶部结束*/
      
      ul li {
        list-style: none;
      }
      
      .track-rcol {
        width: 100%;
        height: auto;
        background: #FFFFFF;
        margin-top: 0.35rem;
        overflow: hidden;
      }
      
      .track-list {
        margin: 0.4rem;
        padding-left: 0.1rem;
        position: relative;
      }
      
      .track-list li {
        position: relative;
        padding: 9px 0 0 25px;
        line-height: 0.36rem;
        border-left: 1px solid #d9d9d9;
        color: #999;
      }
      
      .track-list li.first {
        color: red;
        padding-top: 0;
        border-left-color: #fff;
      }
      
      .track-list li .node-icon {
        position: absolute;
        left: -0.12rem;
        top: 50%;
        width: 0.22rem;
        height: 0.22rem;
        background: url(img/order-icons.png) -0.42rem -1.44rem no-repeat;
      }
      
      .track-list li.first .node-icon {
        background-position: 0 -1.44rem;
      }
      
      .track-list li .time {
        margin-right: 1.4rem;
        position: relative;
        top: 0.08rem;
        display: inline-block;
        vertical-align: middle;
      }
      
      .track-list li .txt {
        max-width: 12rem;
        position: relative;
        top: 0.08rem;
        display: inline-block;
        vertical-align: middle;
      }
      
      .track-list li.first .time {
        margin-right: 0.4rem;
      }
      
      .track-list li.first .txt {
        max-width: 12rem;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="package">
        <div class="paTitle"><span>快递详情</span></div>
        <div class="paHeader">
          <div class="paHeaderLeft">
            <img src="./Sign_in.png" />
          </div>
          <div class="paHeaderRight">
            <span class="qs"><?php echo $state_info ?></span>
            <span class="orderNumber">申通速递：<span id="orderNumber"><?php echo $id ?></span></span>
          </div>
        </div>
      </div>

      <div class="track-rcol">
        <div class="track-list">
          <?php if(count($trace_ori)>0) {?>
          <ul>
            <li class="first">
              <i class="node-icon"></i>
              <span class="time"><?php echo $first['AcceptTime'] ?></span>
              <span class="txt"><?php echo $first['AcceptStation'] ?></span>
            </li>

            <?php  foreach($trace as $kt=>$vt){ ?>
            <li>
              <i class="node-icon"></i>
              <span class="time"><?php echo $vt['AcceptTime'] ?></span>
              <span class="txt"><?php echo $vt['AcceptStation'] ?></span>
            </li>
            <?php }?> 
          </ul>
          <?php }?>
        </div>
      </div>

    </div>
    
  </body>

<style type="text/css">
  body,dl,dt,dd,ul,ol,li,th,td,p,blockquote,pre,form,fieldset,legend,input,button,textarea,hr,samp,select{ margin: 0; padding: 0; font-family: "微软雅黑"; }
body,button,input,select,textarea{ outline: none; color: #F5F5F5F;font-size: 0.24rem }
h1,h2,h3,h4,h5,h6{ margin: 0; font-weight: normal; }
li{ list-style: none; }
.productlists{ border-collapse: collapse; border-spacing: 0; }
a{ text-decoration: none;color:#333 }
em,i{ font-style: normal; }
img{ border:none; display: block;}
input[type=button], input[type=submit], input[type=file], button { cursor: pointer; -webkit-appearance: none; opacity: 1}
.clearfix:before,.clearfix:after{content:"."; display:block; visibility:hidden; height:0; line-height:0; font-size:0;}
.clearfix:after{clear:both;}
.clearfix{zoom:1;}
/*type number*/

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button{
    -webkit-appearance: none !important;
    margin: 0;
}
input[type="number"]{-moz-appearance:textfield;}
input[type="number"]{-o-appearance:textfield;}
input[type="number"]{appearance:textfield;}
html{
    font-size: 100px;
}
body{
    max-width:750px;margin:0 auto;
}
@media screen and (min-width: 320px) {
    html {
        font-size: 42.66667px; } }

@media screen and (min-width: 360px) {
    html {
        font-size: 48px; } }

@media screen and (min-width: 375px) {
    html {
        font-size: 50px; } }

@media screen and (min-width: 384px) {
    html {
        font-size: 51.2px; } }

@media screen and (min-width: 414px) {
    html {
        font-size: 55.2px; } }

@media screen and (min-width: 435px) {
    html {
        font-size: 58px; } }

@media screen and (min-width: 460px) {
    html {
        font-size: 61.33333px; } }

@media screen and (min-width: 480px) {
    html {
        font-size: 64px; } }

@media screen and (min-width: 540px) {
    html {
        font-size: 72px; } }

@media screen and (min-width: 640px) {
    html {
        font-size: 85.33333px; } }

@media screen and (min-width: 720px) {
    html {
        font-size: 96px; } }

@media screen and (min-width: 750px) {
    html {
        font-size: 100px; } }

/*@media screen and (min-width: 768px) {*/
    /*html {*/
        /*font-size: 102.4px; } }*/

/*@media screen and (min-width: 1080px) {*/
    /*html {*/
        /*font-size: 144px; } }*/
.pull-left{
    float:left;
}
.pull-right{
    float:right;
}
.header{
    height: 0.88rem;
    line-height: 0.88rem;
    text-align: center;
    font-size: 0.34rem;
    background: #fff;
    font-weight: bold;
    /*position: relative;*/
    position: fixed;
    width: 100%;
    z-index:9;
    left:0

}
.header .iconfont{
    position: absolute;
    left: 0.35rem;
    font-size: 0.7rem;
    font-weight: normal;
    height: 100%;

}
.header .fh_img{
    width:0.55rem;height: 0.39rem;
    margin-top:0.25rem
}
.header .rule{
    position: absolute;
    right:0.2rem;
    font-weight: normal;
    font-size: 0.28rem;color:#fff;
}
/*字体图标*/
@font-face {font-family: "iconfont";
    src: url('/fonts/iconfont.eot?t=1471830393'); /* IE9*/
    src: url('/fonts/iconfont.eot?t=1471830393#iefix') format('embedded-opentype'), /* IE6-IE8 */
    url('/fonts/iconfont.woff?t=1471830393') format('woff'), /* chrome, firefox */
    url('/fonts/iconfont.ttf?t=1471830393') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/
    url('/fonts/iconfont.svg?t=1471830393#iconfont') format('svg'); /* iOS 4.1- */
}
@font-face {font-family: "iconfont";
    src: url('/fonts/iconfont1.eot?t=1471830393'); /* IE9*/
    src: url('/fonts/iconfont1.eot?t=1471830393#iefix') format('embedded-opentype'), /* IE6-IE8 */
    url('/fonts/iconfont1.woff?t=1471830393') format('woff'), /* chrome, firefox */
    url('/fonts/iconfont1.ttf?t=1471830393') format('truetype'), /* chrome, firefox, opera, Safari, Android, iOS 4.2+*/
    url('/fonts/iconfont1.svg?t=1471830393#iconfont') format('svg'); /* iOS 4.1- */
}
.iconfont {
    font-family:"iconfont" !important;
    font-style:normal;
}
.iconfont1 {
    font-family:"iconfont" !important;
    font-style:normal;
}
/*bottom*/
.ui-grid-trisect{
    height: 1rem;overflow: auto;width:100%;
    font-size: 0.24rem;text-align:center;
    position: fixed;
    bottom:0px;
    border-top: 1px solid #ebebeb;
    background: #fff;
    z-index: 999;
    left:0
}
.ui-grid-trisect li a{
    float:left;width:25%;
}
.ui-grid-trisect li a{
    color:#666;display: inline-block;text-align: center;
}
.ui-grid-trisect li span{
    display: block;
    font-size:0.48rem;
    margin-top:0.05rem;
}
.home_swoiper {
    width: 100%;
    height: 3.2rem;

}
.swiper-slide {
    overflow: hidden;
}
.ui-grid-trisect li.active,.ui-grid-trisect li.active a{
    color:#ff8525
}
.message-mar{
    height: 0.4rem;
    line-height: 0.4rem;
    padding:0.17rem 0.24rem 0.1rem 0.24rem;
    font-size: 0.24rem;
    border-bottom: 1px solid #ebebeb;
    background: #fff;
}
.bulletin{
    width: 5rem;
    float: left;
    height: 0.4rem;
    line-height: 0.4rem;
    overflow: hidden;
    margin-left:0.15rem;
}
.bulletin li{
    display: inline-block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.message-container i ,.message-container span{
    float: left;
    font-size: 0.26rem;
    color:#ff8200
}
.message-container i{
    margin-right:0.15rem;
    font-size: 0.36rem;
}
.message-container span{
    padding-right:0.15rem;
    border-right: 1px solid #ebebeb;
}
.home_linav{
    overflow: hidden;
    clear: both;
    width: 100%;
    background: #fff;
}
.home_linav li{
    display: inline-block;
    width: 24%;
    height: 1.54rem;
    font-size: 0.24rem;
    text-align: center;

}
.home_linav img{
    margin:0.16rem auto 0.15rem;
    width:0.72rem;
    height:0.72rem;
}

.green .line{
    position: absolute;
    height: 10px;
    width:3.3rem;display: table;margin:0 auto;
    background: #fff;    left: 26.3%;
    z-index: 9999999999;
    top: 3.38rem;
}

.home_newdiv h3{
    margin-top: 0.2rem;
   font-size: 0.32rem;
    color: #555;
    background-color: #fff;
    padding-left: 0.2rem;
    line-height: 0.75rem;
}
.home_newdiv ul li{
    padding: 0.3rem 0.2rem;
    background: #fff;
    border-top:1px solid #e5e5e5;
    position: relative;
}
.home_newdiv ul li h2{
    font-size: 0.28rem;
    color: #333;
    margin-bottom: 0.3rem;
}
.home_newdiv ul li img{
    position: absolute;
    right:0;
    top:0px;
    width: 1.06rem;
}
.home_newdiv ul li .index_xin1{
    width: 45%;
}
.home_newdiv ul li .index_xin1 p{
    font-size: 0.62rem;
    color: #fe8c14;
}
.home_newdiv ul li .index_xin1 p em{
    font-size: 0.24rem;
}
.home_newdiv ul li .pull-left span{
    margin-top: 0.15rem;
    display: inline-block;
    color: #999;
}
.home_newdiv ul li .index_xin2{
    width: 20%;
}
.home_newdiv ul li .index_xin2 p{
    font-size: 0.45rem;
    color: #666;
    margin-top: 0.21rem;
}
.home_newdiv ul li .index_xin2 p em{
    font-size: 0.24rem;
    color: #999;
}
.home_newdiv ul li  .pull-right{
    width: 1.85rem;
    height: 0.58rem;
    background-color: #fe8c14;
    border-radius: 20px;
    margin-top: 0.45rem;
    font-size: 0.28rem;
    color: #fff;
    text-align: center;
    line-height: 0.58rem;
    display: block;
}
.home_newdiv ul li .index_xin3{
    padding-left: 0.4rem;
    padding-top: 0.1rem;
}
.home_newdiv ul li .index_xin3 p{
    font-size: 0.24rem;
    color: #999;
}
.home_newdiv ul li .index_xin3 p em{
    font-size: 0.3rem;
    color: #333;
    padding-left: 0.2rem;
}
.home_newdiv .list_more{
    line-height: 0.76rem;
    background-color: #fff;
    text-align: center;
    font-size: 0.28rem;
    color: #666;
    border-top: 1px solid #e6e6e6;
}


.circle {
    width: 3.5rem;
    height:3.5rem;
    position: absolute;
    border-radius: 50%;
    z-index: 9;
    background: #fd9438;

}
.pie_left, .pie_right {
    width: 3.5rem;
    height: 3.5rem;
    position: absolute;
    top: 0;
    left: 0;

}
.circle .left, .circle .right {
    width: 3.5rem;
    height:3.5rem;
    background: #fdeadd;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: 0;
}
.circle .pie_right, .circle .right {
    clip:rect(0,auto,auto,1.75rem);
}
.circle .pie_left, .circle .left {
    clip:rect(0,1.75rem,auto,0);
}
.circle .mask {
    width: 3.4rem;
    height: 3.4rem;
    border-radius: 50%;
    left: 0.05rem;
    top: 0.05rem;
    background: #FFF;
    position: absolute;
    text-align: center;
    line-height: 3.5rem;
    font-size: 30px;
    font-weight: bold;
    color: #fd9438;
}
.circle .yuan{
    height:0.1rem;width:0.1rem;background:#fe8749;border-radius: 50%;display: block;
}
/*水波*/
.wrapper {
    background-color: #fff;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: box;
    display: flex;
    -webkit-box-align: center;
    -o-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
    align-items: center;
    -webkit-box-pack: center;
    -o-box-pack: center;
    -ms-flex-pack: center;
    -webkit-justify-content: center;
    justify-content: center;
    -webkit-box-orient: vertical;
    -o-box-orient: vertical;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    height: 100%;
}


.green .progress,
.red .progress,
.orange .progress {
    position: relative;
    border-radius: 50%;
    background: #fff;

}

.green .progress,
.red .progress,
.orange .progress {
    width: 3.5rem;
    height:3.5rem;
    margin:0 auto;
}

.green .progress,
.red .progress,
.orange .progress {
    -webkit-transition: all 1s ease;
    transition: all 1s ease;
}

.green .progress .inner,
.red .progress .inner,
.orange .progress .inner {
    position: absolute;
    overflow: hidden;
    z-index: 2;
    border-radius: 50%;
}
.green .progress .inner,
.red .progress .inner,
.orange .progress .inner {
    width: 3.5rem;
    height:3.5rem;
}

.green .progress .inner,
.red .progress .inner,
.orange .progress .inner {

}

.green .progress .inner,
.red .progress .inner,
.orange .progress .inner {
    -webkit-transition: all 1s ease;
    transition: all 1s ease;
}

.green .progress .inner .water,
.red .progress .inner .water,
.orange .progress .inner .water {
    position: absolute;
    z-index: 1;
    width: 200%;
    height: 200%;
    left: -50%;
    border-radius: 40%;
    -webkit-animation-iteration-count: infinite;
    animation-iteration-count: infinite;
    -webkit-animation-timing-function: linear;
    animation-timing-function: linear;
    -webkit-animation-name: spin;
    animation-name: spin;
    z-index: 114;
}

.green .progress .inner .water.w2 {
    left: 20%;
    z-index: 110;
}

.green .progress .inner .water {
    top: 65%;
}

.green .progress .inner .water {
    background: #fe8749;
}

.green .progress .inner .water.w2 {
    background: #f8ce9e;
}

.green .progress .inner .water,
.red .progress .inner .water,
.orange .progress .inner .water {
    -webkit-transition: all 1s ease;
    transition: all 1s ease;
}

.green .progress .inner .water,
.red .progress .inner .water,
.orange .progress .inner .water {
    -webkit-animation-duration: 10s;
    animation-duration: 10s;
}

.green .progress .inner .glare,
.red .progress .inner .glare,
.orange .progress .inner .glare {
    position: absolute;
    top: -120%;
    left: -120%;
    z-index: 5;
    width: 200%;
    height: 200%;
    -webkit-transform: rotate(45deg);
    transform: rotate(45deg);
    border-radius: 50%;
}

.green .progress .inner .glare,
.red .progress .inner .glare,
.orange .progress .inner .glare {
    background-color: rgba(255, 255, 255, 0.15);
}

.green .progress .inner .glare,
.red .progress .inner .glare,
.orange .progress .inner .glare {
    -webkit-transition: all 1s ease;
    transition: all 1s ease;
}

.green .progress .inner .percent,
.red .progress .inner .percent,
.orange .progress .inner .percent {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    font-weight: bold;
    text-align: center;
}

.green .progress .inner .percent,
.red .progress .inner .percent,
.orange .progress .inner .percent {
    line-height: 2.9rem;
    font-size: 30px;
    font-weight: normal;
}

.green .progress .inner .symbol {
    color: inherit;
    font-size: 20px;
    font-weight: normal;
}

.green .progress .inner .txt {
    color: #999;
    font-size: 0.24rem;
    position: absolute;
    top: 20px;
    width: 100%;
    text-align: center;
}

.green .progress .inner .up {
    position: absolute;
    bottom: 0.5rem;
    left: 1.2rem;
    z-index: 123;
    font-size: 0.3rem;
}
.green .progress .inner .up a{
    color: #fff;
}
.green .progress .inner .up-arrow {
    width: 12px;
    height: 12px;
    display: inline-block;
    position: relative;
    background-image: url(../img/icon-up.png);
    margin-left: 3px;
    vertical-align: top;
    margin-top: 3px;
}

.green .progress .inner .percent {
    color: #fe8c14;
}

.green .progress .inner .percent {
    /*text-shadow: 0 0 10px #029502;*/
}

.green .progress .inner .percent,
.red .progress .inner .percent,
.orange .progress .inner .percent {
    -webkit-transition: all 1s ease;
    transition: all 1s ease;
}

#copyright {
    margin-top: 25px;
    background-color: transparent;
    font-size: 14px;
    color: #b3b3b3;
    text-align: center;
}



@-webkit-keyframes spin {
    from {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    to {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}

@keyframes spin {
    from {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    to {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}

/*波浪效果*/

.product_waves1{border-radius: 50%;overflow: hidden; position: absolute;}
.product_waves1 .waves_1{width: 3000px;  background: url(/img/waves_2.png) repeat-x;  background-size: 667px 396px;  /*position:absolute; bottom: 0; left: 0;*/
    animation:mymove 40s infinite; -webkit-animation:mymove 40s infinite; -o-animation:mymove 40s infinite; -moz-animation:mymove 40s infinite;}
.product_waves1 .waves_3{width: 3000px;  background: url(/img/waves_3.png) repeat-x;  background-size: 667px 396px;  /*position:absolute; bottom: 0; left: 0;*/
    animation:mymove 40s infinite; -webkit-animation:mymove 40s infinite; -o-animation:mymove 40s infinite; -moz-animation:mymove 40s infinite;}
@keyframes mymove
{
    0%   {margin-left: 0px;}
    50%  {margin-left: -1500px;}
    100% {margin-left: 0px;}
}

@-moz-keyframes mymove /* Firefox */
{
    0%   {margin-left: 0px;}
    50%  {margin-left: -1500px;}
    100% {margin-left: 0px;}
}

@-webkit-keyframes mymove /* Safari and Chrome */
{
    0%   {margin-left: 0px;}
    50%  {margin-left: -1500px;}
    100% {margin-left: 0px;}
}

@-o-keyframes mymove /* Opera */
{
    0%   {margin-left: 0px;}
    50%  {margin-left: -1500px;}
    100% {margin-left: 0px;}
}

.product_waves2{border-radius: 50%;overflow: hidden; position: absolute;}
.product_waves2 .waves_2{width: 3000px; margin-left: -1500px;  background: url(/img/waves_1.png) repeat-x;  background-size: 667px 396px;  /*position:absolute; bottom: 0; left: 0;*/
    animation:mymoves 40s infinite; -webkit-animation:mymoves 40s infinite; -o-animation:mymoves 40s infinite; -moz-animation:mymoves 40s infinite;}

@keyframes mymoves
{
    0%   {margin-left: -1500px;}
    50%  {margin-left: 0px;}
    100% {margin-left: -1500px;}
}

@-moz-keyframes mymoves /* Firefox */
{
    0%   {margin-left: -1500px;}
    50%  {margin-left: 0px;}
    100% {margin-left: -1500px;}
}

@-webkit-keyframes mymoves /* Safari and Chrome */
{
    0%   {margin-left: -1500px;}
    50%  {margin-left: 0px;}
    100% {margin-left: -1500px;}
}

@-o-keyframes mymoves /* Opera */
{
    0%   {margin-left: -1500px;}
    50%  {margin-left: 0px;}
    100% {margin-left: -1500px;}
}


.result{

    background-color: #fff;
    padding-top: 10px;
    padding-bottom: 20px;
}
.apply {
    position: relative;
    display: block;
    width: 90%;
    margin: 0 auto;
    border: 1px solid #EBEBEB;
    border-radius: 5px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    font-size: 14px;
}

.apply .icon-apply {
    width: 16px;
    height: 16px;
    display: inline-block;
    background-image: url(../img/icon-apply.png);
    vertical-align: top;
    margin-top: 12px;
    margin-left: 3px;
}
.loan{
    position: relative;
    width: 100%;
    background-color:#f5f5f5 ;
}
.loan .hot{
    font-size: 12px;
    text-indent: 10px;
    height: 30px;
    line-height: 30px;
}
.loan .recommand-list li{
    background-color: #fff;
    margin-bottom: 10px;
    padding-bottom: 5px;

}
.loan .recommand-list li .tab-img{
    display: table-cell;
    width:90px;
    vertical-align: top;
}
.loan .recommand-list li .tab-content{
    display: table-cell;
    vertical-align: top;
    padding-top:10px;
}
.loan .recommand-list li .dai {
    background-image: url(../img/icon-daikuan.png);
    display: block;
    width: 90px;
    height: 90px;
    zoom: 0.8;
    margin:0 auto;
}

.loan .recommand-list li .haodai{
    background-position: -0px -0px;

}

.loan .recommand-list li .rong360 {
    background-position: -90px -0px;
}

.loan .recommand-list li .wallet {
    background-position: -180px -0px;
}
.loan .recommand-list li .tab-content .name{
    font-size: 15px;
    margin-right: 5px;
    height: 20px;
    line-height: 20px;
}
.loan .recommand-list li .tab-content .amount{
    color:#E20002;
    font-size: 12px;
    height: 20px;
    line-height: 20px;
}
.loan .recommand-list li .tab-content .describe{
    color:#999;
    font-size: 12px;
    line-height: 20px;
}
.hone_div1{
    text-align: center;
    font-size: 0.18rem;
    color:#999
}
.hone_div1 .p1{
    margin:0.2rem 0;
}
.hone_div1 ul{
    overflow: auto;
}
.hone_div1 ul li{
    width: 33.3%;
    float: left;
    text-align: center;
    font-size: 0.15rem;
}
.hone_div1 ul li img{
    margin:0.15rem auto;
    height:0.56rem;
    width:0.5rem;
}
.debt_list {

    font-size: 0.24rem;
    padding-bottom:1.6rem;
}
.debt_list li{
    background: #fff;
}
.debt_list .a1{
    padding-left: 0.5rem;
    display: block;
    height: 2.18rem;
    border-top: 1px solid #EBEBEB;
    position:relative;
}
.debt_list .a1 h2{
    padding:0.28rem 0 0.25rem 0;
    font-size: 0.3rem;
}
.debt_list .h3_title{
    line-height: 0.6rem;
    font-size: 0.28rem;
    color: #333;
    background-color: #fff;
    margin-top: 0.2rem;
}
.debt_list .h3_title img{
    width: 0.3rem;
    margin-left: 0.3rem;
    margin-top: 0.15rem;
    float: left;
    margin-right: 0.15rem;
}
.debe_date{
    overflow: auto;
}
.debe_date .p1{
    font-size: 0.48rem;
    color:#ff8200
}
.debe_date .p2{
    color:#999
}
.debe_date .p3{
    color:#999;
}
.debe_date .p3 span{
    color:#666;
    margin-right:0.1rem;
    font-size: 0.48rem;
}
.debe_date .p1 span{
    font-size:0.24rem
}
.debe_date .pull-left{
    width: 2.6rem;
    /*border-right: 1px solid #ebebeb;*/
 }
.debe_date .pull-right{
    width: 3.95rem;
}
.circliful {

}
.circle-text, .circle-info, .circle-text-half, .circle-info-half {
    width: 100%;
    position: absolute;
    text-align: center;
    display: inline-block;
}
.circle-info, .circle-info-half {
    color: #999;
}

.circliful .fa {
    margin: -10px 3px 0 3px;
    position: relative;
    bottom: 4px;
}
#banner_bolang_bg_1{
height:0.75rem;background:url(/img/wave1.png) repeat-x;

    position:absolute;
    top:0.2rem;width:40000px;
    left:-2.36rem;z-index:999;
    animation:wave1 15s cubic-bezier(0.54, 0.29, 1, 1) .4s infinite normal;
    -moz-animation:wave1 15s cubic-bezier(0.54, 0.29, 1, 1) .4s infinite normal;
    -webkit-animation:wave1 15s cubic-bezier(0.54, 0.29, 1, 1) .4s infinite normal;
    -o-animation:wave1 15s cubic-bezier(0.54, 0.29, 1, 1) .4s infinite normal}
#banner_bolang_bg_2{
   height:0.7rem;background:url(/img/wave2.png) repeat-x;_background:0 0;
    position:absolute;top:0.15rem;width:40000px;left:0;z-index:998;
    animation:wave2 50s cubic-bezier(0.57, 0.38, 1, 1) .2s infinite normal;
    -moz-animation:wave2 50s cubic-bezier(0.57, 0.38, 1, 1) .2s infinite normal;
    -webkit-animation:wave2 50s cubic-bezier(0.57, 0.38, 1, 1) .2s infinite normal;
    -o-animation:wave2 50s cubic-bezier(0.57, 0.38, 1, 1) .2s infinite normal
}
@-webkit-keyframes wave1{
    from{left:-236px}to{left:-1233px}}
@-moz-keyframes wave1{from{left:-236px}to{left:-1233px}}
@-o-keyframes wave1{from{left:-236px}to{left:-1233px}}
@keyframes wave1{from{left:-236px}to{left:-1233px}}
@-webkit-keyframes wave2{from{left:0}to{left:-1009px}}
@-moz-keyframes wave2{from{left:0}to{left:-1009px}}
@-o-keyframes wave2{from{left:0}to{left:-1009px}}
@keyframes wave2{from{left:0}to{left:-1009px}}
.debt_img{
    height: 0.8rem;
    background: #f9c34a;
    width: 100%;
    overflow: hidden;
    position: relative;
}.awardspan {
     color: #fff;
     background: #fd9438;
     display: inline-block;
     height:0.35rem;
    padding:0 0.06rem;
    line-height: 0.35rem;
     font-size: 0.18rem;
     border-radius: 0.08rem;
     margin-left: 0.1rem;
 }
 .btn_tips{
     position: absolute;
     right: 0;
     bottom:0;
 }
.btn_tips img{
    height:1.31rem;
    width:1.56rem
} li.opaClass {
     opacity: 0.65;
 }
li.endClass .p1{
    color:#c2c2c2
}
li.endClass {
    opacity: 0.65;
}
li.endClass .customew,li.opaClass .customew {
    display: none;
}

  .newspan{
      position: absolute;
      right:0;
      top:-0.06rem;
  }
.newspan img{
    height:0.9rem;
}
.item_mes{
    background: #fff;
    padding:0.6rem 0 0.4rem 0;
}
.item_mes .p1{
    text-align: center;
    font-size: 0.28rem;
    color:#999
}
.item_mes .p2{
    text-align: center;
    color:#ff8200;
    font-size: 0.35rem;
}
.item_mes .p2 span{
    font-size: 1.25rem;
}
.item_mes .p3{
    text-align: center;
    font-size: 0.24rem;
color:#999;
    margin-top: 0.36rem;
}
.item_mes .p3 i{
    background: #ebebeb;
    display: inline-block;
    height: 0.4rem;
    vertical-align: middle;
    margin:0 0.2rem;
    width: 1px;
}
.progress {
    width: 6.42rem;
    height: 0.08rem;
    background: #ebebeb;
    margin: 0 auto;
}

.progress-bar {
    border-radius: 5px;
    width: 0;
    height: 100%;
    background-color: #ff8200;
    -webkit-transition: width .6s ease;
}
.progress_div {
    position: relative;
    width: 6.42rem;
    margin: 0.5rem auto 0.48rem;
}
.progress_div span{
    color:#ff8200;
    font-size: 0.16rem;
    position: absolute;
    margin-top:0.1rem;
    margin-left: -0.2rem;
}
.progress_div img{
    position: absolute;
    top:-0.36rem;
    margin-left:-0.2rem
}
.item_mes .p4{
    font-size: 0.24rem;
    color:#999;
    width: 6.42rem;
    margin:0 auto
}
.item_mes .p4 i{
    color:#ff8200
}
.item_tab {

    margin-top: 0.45rem;
}
.item_tab .ul1 li{
    font-size: 0.28rem;
    height: 0.75rem;
    border-bottom: 1px solid #ebebeb;
    line-height: 0.75rem;
    padding:0 0.54rem;
    position: relative; background: #fff;
}
.item_tab .ul1 li .iconfont{
    font-size: 0.48rem;
    position: absolute;
    transform:rotate(270deg);-webkit-transition:rotate(270deg);
    -moz-transition: rotate(270deg);
    -o-transition: rotate(270deg);
    right:0.36rem;
    color:#999
}
.item_tab .ul1 li.li1{
    height: 1.06rem;
    line-height: 1.06rem;
}
.animate-positive{animation: animate-positive 2s;transition: width 1s linear 0s;-webkit-transition: width .6s ease;transition: width .6s ease;}
@-webkit-keyframes animate-positive{
    0% { width: 0%; }
}
@keyframes animate-positive{
    0% { width: 0%; }
}
.item_list .ul2 li{
    width: 33.3%;
    float:left;
    height: 1rem;
    text-align: center;
    line-height: 1rem;
    margin-top:0.15rem;background:#fff;
}
.item_list .ul2{
    overflow: hidden;
    width: 100%;
    border-bottom:1px solid #ebebeb;
}
.item_list .ul2 li span{
    display: inline-block;
    margin:0 auto;
    padding:0 0.1rem;
    font-size: 0.32rem;

}
.item_list .ul2 li.active  span{
    color:#ff8200;
    border-bottom: 0.5rem solid #ff8200 ;
    height:0.95rem;
}
.item_list .ul3 li{
    background: #fff;
    height: 1.2rem;
    padding:0 0.24rem;
    clear: both;
    border-bottom: 1px solid #ebebeb;

}
.item_list .ul3 li .p1{
    margin:0.2rem  0 0.15rem 0;
}
.item_list .ul3 li .p2{
    color:#999;
    font-size: 0.18rem;
}
.item_list .ul3 li .p3{
    margin-top: 0.4rem;
}
.item_list{
    padding-bottom:1.3rem;
}
.item_list .ul3 li .p3 span{
    color:#ff8200
}
.show_btn{
    height: 1.01rem;
    color:#fff;
    font-size: 0.38rem;
    line-height: 1.01rem;
    text-align: center;
    width:100%;
    border:none;
    position: fixed;
    bottom:0;
    left:0;
    background: #ff8200;
    border-radius: 0;
}
.detaile_pos{
    background: #fff;
    font-size: 0.18rem;
    line-height: 1.8;
    margin-bottom:0.15rem;
    padding:0.25rem 0  0.15rem 0;
}
.detaile_pos>p{
padding:0 0.35rem
}

.details_t h3{
    height:0.45rem;
    width: 2rem;
    display: inline-block;
color:#fff;
    font-size: 0.24rem;
    background: url("/img/ddd.png");
    background-size: 100% 100%;
   padding-left:0.2rem
}
.detaile_pos .mgcol666{
    padding:0 0.35rem;
    font-size: 0.28rem;

}
.details_t .item_ol1 img{
width: 100%;
}

.btn_input{
    background:#DDDDDD ;
}
.details_t>div{
    display: none;
}
.show_head{
    background: #ff8200;
    color:#fff;
}
.show_head .iconfont{
color:#fff
}
.show_xx{
    background: #fabf82;
    color:#fff;
    display: block;
    padding:0.28rem 0 0.28rem 0.54rem
}
.show_xx .iconfont{
    font-size: 0.36rem;
}
.details_main{
    margin-top:0;
    background: transparent;
}
.details_main .ul1 li{
    padding: 0 0.3rem;
    color:#999;
    border:none;
    background: #fff;
}
.details_main .ul1{
    margin-bottom:0.24rem;
}
.details_main .ul2 li{
    background: #fff;
    height: 0.95rem;
    line-height: 0.95rem;
    padding:0 0.3rem;
    font-size: 0.3rem;
    border-bottom: 1px solid #ebebeb;
}
.details_main .ul2 li .a1{
    float:right;
    color:#fd9746;
    text-decoration: underline;
}.details_main .ul2 li .sp1{
    height: 0.48rem;
    color:#fff;
    width:0.9rem;
    background: #fd9746;
    font-size: 0.3rem;
    display: inline-block;float: right;
    line-height: 0.48rem;
    text-align:center;    margin-top: 0.25rem;
    border-radius: 0.05rem;
 }
.details_main .ul2 li input{
    border:none;
    font-size: 0.3rem;
    width:4rem;
}
.details_main .ul3{
    margin-top: 0.24rem;
    height: 0.9rem;padding:0 0.3rem; font-size: 0.3rem;
    line-height: 0.9rem;
    background: #fff;
}
.fix_btn{
    height: 1.45rem;background: #fff;
    position: fixed;
    width: 100%;
    bottom:0;
}
.fix_btn input{
    width: 7.02rem;
    height: 0.85rem;
    color:#fff;
    background: #fd9746;
    margin:0.22rem 0.24rem;border:none;
    font-size: 0.36rem;
}
.details_main .p1{
    margin:0.2rem 0 0 0.36rem
}
.details_main .ul3 select{
    border:none;
    background: #fff;
    float:right;
    color:#999;
    font-size: 0.2rem;
    margin-top:0.2rem;
    width: 5rem;

}
.detaile_h3{
    line-height:0.47rem ;
}


.about_main>a{
    height: 3.6rem;
    background: #47b8c8;
    display: block;
    color:#fff;
    border-bottom: 1px solid #fff;
}
.about_main .a1 img{
    width: 1.87rem;
    margin-left:0.65rem;
}
.about_main>a .pull-left,.about_main>a .pull-right{
    margin-top: 1.06rem;
}
.about_main>a .pull-right{
    margin-right: 1.36rem;
    width: 3.2rem;
    text-align: center;
}
.about_main>a .pull-right h2{
    font-size: 0.36rem;
}
.about_main>a .pull-right span{
    display: inline-block;
    height: 0.48rem;
    border-radius: 0.30rem;
    border:1px solid #fff;
    width: 100%;
    font-size: 0.24rem;
    margin-top:0.4rem;
    line-height: 0.48rem;
    letter-spacing: 0.03rem;
}
.about_main .p1{
    color:#999;
    text-align:center;
    margin-top:0.5rem;
    /*padding-bottom: 1.8rem;*/
}
.about_bn img{
    height: 3.2rem;
    width: 100%;

}
.about_list{
    background: #fff;
    height: 1.92rem;
    margin-top:0.25rem

}
.about_list li,.about_list>a{
    float:left;
    text-align: center;
    font-size: 0.28rem;
    display: inline-block;
}
.about_list>a{
     width: 33.3%;
 }

.about_list a img{
    width: 0.65rem;
    height: 0.65rem;
    margin:0.3rem auto 0.15rem;
}
.about_ul1{
    margin-top:0.2rem
}
.about_ul1 li{
    width: 100%;background: #fff;
    position: relative;
    border-bottom: 1px solid #ebebeb;
}
.about_ul1 li>a{
    height: 0.98rem;
    line-height: 0.98rem;
    display: inline-block;
    width: 91%;
    padding:0 0.4rem 0 0.24rem;

    font-size: 0.28rem;
}
.about_ul1 li>a .pull-right{
    position: absolute;
    right: 0.2rem;
    top:0rem;
    transform:rotate(180deg);-webkit-transition:rotate(180deg);
    -moz-transition: rotate(180deg);
    -o-transition: rotate(180deg);

}
.announce_main .list .pull-left{
    width: 0.6rem;
    height: 1.9rem;
    position: relative;

}
.announce_main .list .pull-left i{
    display: block;
    width: 1px;
    height:1.95rem;
    background: #aaa;
    margin: 0 0.3rem;
}
.announce_main .list .pull-left span{
    position: absolute;
    width: 0.15rem;
    height: 0.15rem;
    border-radius:50%;
    background: #ff8624;
    left: 0.22rem;
    top: 0.9rem;
}
.announce_main .list .pull-right{
    width: 6.58rem;
    height: 1.66rem;
    background: url("/img/ann.png");
    background-size: 100%;
    margin:0.15rem 0.32rem 0.15rem 0;
    overflow: hidden;
 }
.announce_main .list .pull-right h2,.announce_main .list .pull-right p{
    padding:0 0.1rem 0 0.3rem;

}
.announce_main .list .pull-right p{
    font-size: 0.2rem;
}
.announce_main .list .pull-right h2{
    font-size: 0.28rem;
    margin:0.15rem 0 0.05rem 0;
    height:0.32rem;
    overflow: hidden;
}
.announce_main .list .pull-right .time{
    color:#999;
    font-size: 0.18rem;
    margin-bottom: 0.05rem
}
.announce_main .list {
    display: block;
    overflow: auto;
}
.announce_main,.about_gg_xq,.about_bn{

    padding-top:0.88rem
}
.about_main{

}
.about_gg_xq img{
    width: 100%;
}
#annunce_list {
    margin-top: 0.2rem;
}
.news_con{
    padding-top:0.88rem
}
.news_con img{
    width: 100%;
}
.about_gg_xq h3{
text-align: center;
    font-size: 0.28rem;
    margin-top: 0.25rem;
}
.about_gg_xq .p2{
    margin:0.2rem 0;
    text-align: center;
}
.about_gg_xq .p3{
    padding:0 0.15rem;
    line-height: 1.5;
}
.about_gg_xq .p2 span{
    color:#ff8624;
    margin-left:0.15rem;
}

/*帮助中心*/
.help_list li a{
    height: 0.85rem;
    display: block;
    line-height: 0.85rem;
    background: #fff;
    border-bottom: 1px solid #ebebeb;
    padding-left: 0.2rem;
}
.help_list{
    margin-top: 0.2rem;
}
.help_list .iconfont{
    float: right;
    transform:rotate(180deg);-webkit-transition:rotate(180deg);
    -moz-transition: rotate(180deg);
    -o-transition: rotate(180deg);
    margin-right: 0.4rem;
    font-size: 0.4rem;
    margin-left:0
}
.help_list .pull-right p{
    text-align: right;
 line-height: 1.1;font-size: 0.26rem;
}
.help_list .pull-right{
    margin:0.18rem 0.5rem 0 0
}

.help_lists{height:auto;}
.help_lists ul{padding: 0.2rem 0 0 0.3rem;background: #fff;margin-top:0.2rem}
.help_lists ul li{border-bottom:0.01rem solid #E5E5E5;}
.help_lists ul li .list1{background: #fff;padding:0.4rem 0.3rem 0.7rem 0.3rem;}
.help_lists ul li .list1 span{font-size:0.28rem;color: #3C3C3C;float: left;display: inline-block;margin-left: 0.2rem;}
.help_lists ul li .list1 .img1{width: 0.37rem;height: 0.37rem;float:left;margin-left: -0.3rem;}
.help_lists ul li .list1 .img2{width: 0.22rem;height: 0.13rem;float: right;display: block; -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;}
.help_lists ul li .list2 {background: #fff; padding-bottom:0.25rem;line-height: 1.6;}
.help_lists ul li .list2 .img1{width: 0.37rem;height: 0.37rem;float: left}
.help_lists ul li .list2 .list2_one p{font-size:0.26rem;color: #999;}
.help_lists ul li .list2 .list2_one{display: inline-block; width: 5.15rem;margin-left: 0.2rem;}

.m1{
    transform: rotate(180deg);
    -ms-transform: rotate(180deg); /* IE 9 */
    -moz-transform: rotate(180deg); /* Firefox */
    -webkit-transform: rotate(180deg); /* Safari 和 Chrome */
    -o-transform: rotate(180deg); /* Opera */
}
/*媒体资讯*/
.news_main .list{
    display: block;
    width: 6.8rem;
    background: #fff;
    margin:0 auto;
    padding: 0.2rem;
    margin-top: 0.2rem;
    -webkit-box-shadow: 0px 2px 5px #ccc;
    -moz-box-shadow: 0px 2px 5px #ccc;
    box-shadow: 0px 2px 5px #ccc;
}
.news_main .list img{
    width: 100%;
    height: 3rem;
}
.news_main .list .p1{
    font-size: 0.32rem;
    margin:0.35rem 0
}
.news_main .list .p2{
    line-height: 0.4rem;
    font-size: 0.28rem;
}
.fx_div{
    position: absolute;
    display: none;
    top:0;
    background: rgba(0,0,0,0.5);
    height: 100%;
}
.fx_div img{
    padding-top:1rem;
    width: 100%;
}
.news_con .p1{
text-align: center;
    margin:0.4rem 0 0.3rem 0;
}
.news_con .p2{
    padding: 0 0.24rem;
   margin-bottom:0.3rem
}
.news_con .p2 p{
     width: 100%!important;
     margin-left:0!important;
     background: transparent!important;
 }
.news_con .p2 p span{
    background: transparent!important;
}

.news_con .p3{
    text-align: right;
    padding-right: 0.3rem;
    line-height: 1.8;
}
/*公司简介*/
.com_bn img{
    width: 100%;
    margin-top:0.2rem;
}
.com_bn{
    background: #fff;
}
.com_bn p{
 padding:0.3rem 0.24rem 0.5rem 0.24rem;
    line-height: 0.5rem;
}
 .com_title {
    width: 5rem;
    text-align: center;
    margin: 0 auto 0.38rem;
    position: relative;
     padding-top:0.25rem

}
.com_title h2 {
    font-size: 0.28rem;
    font-weight: bold;
    margin-bottom: 0.16rem;
}
.com_title span {
    height: 1px;
    background: #fe8749;
    width: 100%;
    display: block;
    position: absolute;
    top:0.95rem;
}
.com_title p {
    display: table;
    margin: 0 auto;
    background: #fff;
    color: #999;
    width:3rem;
    position: relative;
    z-index: 99;
    font-size: 0.14rem;
    letter-spacing:0.1px;
    font-family:'arial';
}
.com_con{
    background: #fff;
    margin-top: 0.16rem;
}
.com_div1 .list1 li{
    width: 1.87rem;
    float:left;
    font-size: 0.2rem;
    line-height: 0.4rem;
}
.com_div1 .list1{
    overflow: auto;
    text-align: center;
    padding-bottom: 0.5rem;
}
.com_div1 .list1 li img{
    width: 1.14rem;
    margin:0.4rem auto;
}
.company_swiper{
    width: 7.02rem;
    padding: 0.65rem 0;
}
.company_swiper  .swiper-slide img{
    width: 2.12rem;
    float: left;
    margin-right: 0.2rem;
}
.company_swiper  .swiper-button-next{
    position: absolute;
}
.com_his {
    position: relative;
    width: 7.02rem;
    margin:0 auto 0.3rem ;


}
.com_his  .com_left,.com_his  .com_right{
    float:left;
    width: 3.5rem;
    position: relative;
    text-align: right;

}
.com_his  .com_left p{
    padding-right: 0.7rem;
    font-size: 0.2rem;
}
.com_his  .com_right p{
    text-align: left;
    padding-left: 0.7rem;
    font-size: 0.2rem;
}
.com_div2{
    height: 8rem;
}.com_his  .com_left li{
    position: relative;
    margin-top: 1.2rem;
 }
.com_his  .com_right li{
    position: relative;
    margin-bottom: 1.2rem;
}
.com_his  .com_left span,.com_his  .com_right span{
    height: 10px;
    width: 10px;display: inline-block;
    background: #fe8749;
    position: absolute;
    right: 0.5rem;
    border-radius: 50%;
    top:0.36rem
}
.com_his  .com_left i,.com_his  .com_right i{
    height: 1px;
    width: 0.5rem;display: inline-block;
    background: #ddd;
    position: absolute;
    right: 0;
    top:0.4rem

}
.com_his  .com_right i{
    left:0;
}
.com_his  .com_right span{
    left:0.5rem;
}
.com_his .sp1{
    height: 4.8rem;
    width: 1px;
    background: #dbdbdb;
    display: block;
    position: absolute;
    left: 50%;
}
/*公司简介lzh*/
/*公司简介*/
.about_main .title{width:4.1rem;text-align: center; margin: 0 auto 0.4rem; position: relative;}
.about_main .title h2{font-size: 0.28rem;font-weight: bold;margin-bottom:0.25rem;margin-top: 0;padding-top: 0.25rem;}
.about_main .title span{height:1px;background: #fe8749;width:100%;display: block; position: absolute;margin-top: 0.16rem;}
.about_main .title p{display:table;background: #fff;margin: 0 auto;color: #999;width:2.5rem; position: relative; z-index: 8;font-size: 0.16rem;}
.about_main .main1 .main1_p{margin: 0 0.2rem;}
.about_main .main1 .main1_p p{font-size:0.2rem;text-align: center;margin-bottom:0.15rem;margin-top: 0}
.main2,.main3,.main4,.main5{margin-top: 0.25rem;background:#fff;}
.about_main .main1{width: 100%;background:#fff;}
.about_wrap{padding-top:0.88rem}
.about_main .main1{height:auto;padding-bottom: 0.2rem;}
.about_main .main2{height:6.3rem;padding-bottom: 0.6rem}
.about_main .main3{height:8rem;}
.about_main .main4{padding-bottom: 0.3rem;}
.about_main .main5{height:5.7rem;}
.about_main .main5 p{width: 2.7rem;}

.about_main .main2 .main2_p div{border-radius:0  0.5rem 0.5rem 0;height:0.78rem;margin: 0;line-height: 0.78rem;color:#fff}
/*.about_main .main2 .main2_p div span{color:#fff;}*/
.about_main .main2 .main2_p .s1{width: 4.8rem;background:#61C0FF;}
.about_main .main2 .main2_p .s2{width: 5.6rem;background:#FE6F4D;margin-top:0.35rem}
.about_main .main2 .main2_p .s2 .p1{
    font-size: 0.36rem;margin-left:0.4rem

}
.about_main .main2 .main2_p .s1 .p1{margin-left:0.24rem}
.about_main .main2 .main2_p .s3 .p1{margin-left:0.6rem}
.about_main .main2 .main2_p .s4 .p1{margin-left:0.8rem}
.about_main .main2 .main2_p .s3{width: 5.6rem;background:#FFA313;margin-top:0.35rem}
.about_main .main2 .main2_p .s4{width: 5rem;background:#3DC8A7;margin-top:0.35rem}
.about_main .main2 .main2_p .p1{font-size: 0.36rem;font-style: italic;color:#fff;margin-right: 0.25rem}
.about_main .main2 .main2_p .p2{font-size: 0.24rem;margin-right: 0.3rem}
.about_main .main2 .main2_p .p3{font-size: 0.2rem;}
.about_main .main3 .main3_p{margin: 0 0.9rem;}
.about_main .main3 .main3_p img{width:2.5rem;height: 1.7rem;float: left;margin-bottom: 0.3rem;}
.about_main .main3 .main3_p img:nth-child(odd){margin-right: 0.68rem;}
.about_main .main4 .main4_p{margin: 0 0.1rem;}
.about_main .main4 .main4_p .right{width:3.5rem;float: left;border-left:1px solid #E5E5E5 }
.about_main .main4 .main4_p .left{width:3.5rem;height:6rem;float: left; }
.about_main .main4 .main4_p .left .p1{font-size: 0.24rem;text-align: right;margin: 0.74rem 0.15rem 0 0;color:#333}
.about_main .main4 .main4_p .left .p2{font-size: 0.18rem;text-align: right;margin: 0.65rem 0.8rem 0 0;}
.about_main .main4 .main4_p .left .p3{font-size: 0.18rem;text-align: right;margin:0.2rem 0.6rem 0 0}
.about_main .main4 .main4_p .left .p4{font-size: 0.24rem;text-align: right;margin: 0.7rem 0.15rem 0 0;}
.about_main .main4 .main4_p .left .p5{font-size: 0.24rem;text-align: right;margin: 0.7rem 0.7rem 0 0;}
.about_main .main4 .main4_p .left .p6{font-size: 0.18rem;text-align: right;margin:0.2rem 0.4rem 0 0}
.about_main .main4 .main4_p .left .p7{font-size: 0.18rem;text-align: right;margin:0.2rem 0.3rem 0 0}
.about_main .main4 .main4_p .right .p1{font-size: 0.24rem;text-align: left;margin: 0.55rem 0 0 0.7rem;color:#333}
.about_main .main4 .main4_p .right .p2{font-size: 0.18rem;text-align:left;margin: 0.2rem 0 0 0.7rem;}
.about_main .main4 .main4_p .right .p3{font-size: 0.24rem;text-align: left;margin:1.3rem 0 0 0.7rem;}
.about_main .main4 .main4_p .right .p4{font-size: 0.18rem;text-align:left;margin: 0.2rem 0 0 0.7rem;}
.about_main .main4 .main4_p .right .p5{font-size: 0.24rem;text-align: left;margin:0.7rem 0 0 0.7rem;}
.about_main .main4 .main4_p .right .p6{font-size: 0.18rem;text-align: left;margin:0.2rem 0 0 0.6rem}
.about_main .main4 .main4_p .right .p7{font-size: 0.18rem;text-align:left;margin:0.1rem 0 0 0.6rem}
.about_main .main4 .main4_p .line{height:2px;display: inline-block;width: 0.4rem;background:#E5E5E5;position: absolute;}
.about_main .main4 .main4_p .cirle{width:0.13rem;height:0.13rem;display: inline-block;background:#FE8749;border-radius:50%;position: absolute;}
.about_main .main4 .main4_p .l1{top:0.2rem;right:0;}
.about_main .main4 .main4_p .c1{top:0.15rem;right:0.4rem;}
.about_main .main4 .main4_p .l2{top:0.15rem;right:0;}
.about_main .main4 .main4_p .c2{top:0.1rem;right:0.4rem;}
.about_main .main4 .main4_p .l3,.l4{top:0.4rem;left:0;}
.about_main .main4 .main4_p .c3,.c4{top:0.35rem;left:0.4rem;}
.about_main .main4 .main4_p .l5{top:0.15rem;left:0;}
.about_main .main4 .main4_p .c5{top:0.1rem;left:0.4rem;}
.about_main .main5 .main5_p img{width: 6.24rem;height: 3.47rem;}
.swiper-container {width: 100%;height: 100%;}
.swiper-slide {text-align: center;display: -webkit-box;display: -ms-flexbox;display: -webkit-flex;display: flex;-webkit-box-pack: center;-ms-flex-pack: center; -webkit-justify-content: center;justify-content: center;-webkit-box-align: center;-ms-flex-align: center;-webkit-align-items: center;align-items: center;}

.swiper-container-horizontal>.swiper-pagination-bullets, .swiper-pagination-custom, .swiper-pagination-fraction{
    bottom:0.1rem;
}
.swiper-pagination-bullet-active{background:#FE8749}

/*安全保障*/


.fullpage .main1 .product{ width: 2.38rem;height:0.67rem }
.fullpage .title{margin-top: 0.3rem}
.fullpage .title p{font-size: 0.2rem;color:#979797;text-align: center;line-height:1.5;letter-spacing:2px;margin: 0;}
.fullpage .main1 .main1_list1{margin:0.6rem 0 0.2rem 0;overflow: auto}
.fullpage .main1 .main1_list1 li{float: left;text-align: center; position: relative;width: 33%;}
.fullpage .main1 .main1_list1 p{font-size: 0.24rem;color:#333;margin: 0;text-align: center}
.fullpage .main1 .main1_list1 img{width: 1rem;height: 1rem;text-align: center;margin:0.15rem auto}
.fullpage .main1 .main1_list2 li{margin-left:0.54rem;padding-top: 0.4rem}
.fullpage .main1 .main1_list2 img{width: 0.32rem;height: 0.32rem;display: inline-block;vertical-align: middle}
.fullpage .main1 .main1_list2 h2{display: inline-block;font-size: 0.24rem;color:#333;}
.fullpage .main1 .main1_list2 p{font-size: 0.2rem;color:#999;margin: 0;line-height: 1.5;padding-right:0.24rem;padding-top:0.15rem}
.fullpage .main1 .yuan{
    position: absolute;
    position: absolute;
    height: 0.2rem;
    width: 0.2rem;
    background: #fe8749;
    border-radius: 50%;    margin-left: -0.4rem;
}
.fullpage .main1 .i1{
    width: 1px;background: #fe8749;height: 50%;display: block;position: absolute;    margin-left: -0.3rem;
}
.fullpage .main1{margin-top:0.2rem}
.fullpage .main2 .cur{ width: 2.38rem;height:0.67rem;display: block; margin-top: 0.15rem; }
.fullpage .main2 .main2_list2{margin-top: 0.7rem;}
.fullpage .main2 .main2_list2 li{margin: 0 0 0 0.5rem;overflow: auto;}
.fullpage .main2 .main2_list2 li .left{float: left;}
.fullpage .main2 .main2_list2 li .left img{float: left;width: 1.21rem;height: 1.47rem;}
.fullpage .main2 .main2_list2 li .right{float: left;}
.fullpage .main2 .main2_list2 li .r1{background: url(/img/c_09.png) no-repeat;background-size: 100%;width: 4.25rem;height: 1.2rem;margin:0.4rem 0 0 0.75rem; position: relative}
.fullpage .main2 .main2_list2 li .r2{background: url(/img/c2_15.png) no-repeat;background-size: 100%;width: 3.8rem;height: 1.18rem;margin:0.6rem 0 0 0.75rem; position: relative}
.fullpage .main2 .main2_list2 li .r3{background: url(/img/c2_20.png) no-repeat;background-size: 100%;width: 4.25rem;height: 1.2rem;margin:0.6rem 0 0 0.75rem; position: relative}
.fullpage .main2 .main2_list2 li .r4{background: url(/img/c1_26.png) no-repeat;background-size: 100%;width: 3.8rem;height: 1.18rem;margin:0.6rem 0 0 0.75rem; position: relative}
.fullpage .main2 .main2_list2 li .right span{font-size: 0.2rem;display: inline-block;position: absolute}
.fullpage .main2 .main2_list2 li .right span:nth-child(1){left:0.5rem;top:0.2rem;}
.fullpage .main2 .main2_list2 li .right span:nth-child(2){right:1.3rem;top:0.2rem;}
.fullpage .main2 .main2_list2 li .right span:nth-child(3){left:0.5rem;top:0.65rem;}
.fullpage .main2 .main2_list2 li .right span:nth-child(4){right:0.2rem;top:0.65rem;}

.fullpage .main3{margin-top: 0.8rem;}
.fullpage .main3 .mm{ width: 2.38rem;height:0.67rem;display: block }
.fullpage .main3 .main3_list3{margin-top: 0.6rem;}
.fullpage .main3 .main3_list3 li{width: 6.16rem;height: 1.45rem;background: #fff;margin-left: 0.65rem;}
.fullpage .main3 .main3_list3 li .div2{background: #4FA2FF;height: auto;padding: 0.3rem 0;line-height: 1.5;display: none;}
.fullpage .main3 .main3_list3 li .div1{line-height:1.4rem }
.fullpage .main3 .main3_list3 li .div2 p{font-size: 0.2rem;color:#fff;margin: 0 0.2rem; }
.fullpage .main3 .main3_list3 li img{display: inline-block}
.fullpage .main3 .main3_list3 li span{display: inline-block;font-size: 0.32rem;}

.fullpage .main4{margin-top: 0.8rem;}
.fullpage .main4 .zz{ width: 2.38rem;height:0.67rem;display: block;margin-bottom: 0.45rem }
.fullpage .main4 .main4_list4 li{overflow: auto;padding:0 0.4rem 0.45rem 0.65rem;position: relative}
.fullpage .main4 .main4_list4 li .s1{font-size: 0.24rem;color:#66ADFE;display: inline-block;margin-bottom: 0.24rem}
.fullpage .main4 .main4_list4 li .div1{font-size: 0.18rem;color:#fff;width: 0.7rem;height: 0.3rem;background: #66ADFE;border-radius: 2px;display: inline-block;font-size: 0.18rem;line-height: 0.3rem;text-align: center}
.fullpage .main4 .main4_list4 li .p1{font-size: 0.2rem;color:#333;margin-bottom: 0.1rem;}
.fullpage .main4 .main4_list4 li .p2{font-size: 0.18rem;color:#B6B6B6;line-height: 1.7}
.main1_list2 .sp1{height: 1.8rem;width: 1px;display:block;background: #FF8624;margin: 0 auto}
.main1_list2 .sp2{height: 1.8rem;width: 1px;display:block;background: #FF8624;margin: 0 auto}
.fullpage .main4 .main4_list4 li .i2{position: absolute;height: 0.2rem;width: 0.2rem;display: block;border-radius: 50%;background: #439aee;margin-left:-0.45rem}
.fullpage .main4 .main4_list4 li .i3{position: absolute;height: 100%;width: 1px;display: block;background: #439aee;margin-left:-0.35rem}
.main4_list4 .sp1{height: 4.8rem;width: 1px;display:block;background: #439AEE;margin: 0 auto}
.main4_list4 .sp2{height: 3.5rem;width: 1px;display:block;background: #439AEE;margin: 0 auto}
.main4_list4 .sp3{height: 1.9rem;width: 1px;display:block;background: #439AEE;margin: 0 auto}


.fullpage .main5{background:  #4FA2FF;margin-top: 0.8rem;padding-bottom: 0.8rem}
.fullpage .main5 .pp{ width: 2.38rem;height:0.67rem;display: block }
.fullpage .main5 .main5_list5{width:7.05rem;height:5.08rem;background: url(/img/f_07.jpg) no-repeat;background-size: 100%;margin-left: 0.2rem;margin-top: 0.6rem;
    position:relative;}
.fullpage .main5 .main5_list5 li div{width: 5.6rem;height: 0.65rem;}
.fullpage .main5 .main5_list5 li p:nth-child(1){font-size: 0.24rem;color:#fff}
.fullpage .main5 .main5_list5 li p:nth-child(2){font-size: 0.2rem;color:#fff;margin-top: 0.1rem;}

/*活动中心*/
.active_main{
    padding: 0 0.3rem;
}
.active_main li{
    background: #fff;
    margin-top: 0.2rem;
    position: relative;
    padding-bottom: 0.15rem;
}
.active_main li p{
    padding-left:0.3rem
}
.active_main li .p1{
    font-size: 0.28rem;
    margin-bottom: 0.1rem;
}
.active_main li .p2{
    font-size: 0.24rem;
    color:#999
}
.active_main  li .img1{
    height: 2.8rem;
    width: 100%;
    margin-bottom: 0.25rem;
}
.active_main  li .a1{
    position: absolute;
    right: -0.05rem;
    top:0.2rem;

}
.active_main  li .a1 img{
    width: 1.19rem;
}




/*登录*/
.header_reg{
    position: relative !important;
}
.login_warp .p1{
color:#999;font-size: 0.28rem;
margin:0.2rem 0;

}
.login_warp .a1{
    color:#999;
    float: right;
    margin-top: 0.25rem;
}
.login_warp{
    padding: 0 0.15rem 0;
}
.login_warp input.form-control{
    width: 100%;
    height: 0.9rem;
    border:none;
    padding-left: 1.8rem;
    font-size: 0.28rem;
}
.login_warp .form-group{
    position: relative;
    overflow: hidden;
}
.login_warp .yqm_div .p3{
    font-size: 0.30rem;
    margin-bottom: 0.2rem ;
}
.login_warp .form-group .u1{
font-size: 0.32rem;
    height: 0.9rem;
    line-height: 0.9rem;
    position:absolute;
    top:0;left:0;
    width: 1.8rem;
    display: inline-block;
    text-align: center
}
.btn-logoin{
    height: 0.95rem;
    width: 100%;
    background: #fd9746;color:#fff;
    display: block;
    line-height: 0.95rem;
    font-size: 0.32rem;
    text-align: center;
    border-radius: 5px;

}
.login_warp .p2{
    margin:0.35rem 0;
}
.tipsYz{display:block;color: #ff8624;padding:5px 25px;text-align: left;background: url(/img/err.png) 5px 8px no-repeat;
    display:none;
    margin-bottom:2px;
    clear: both;
}
.form-horizontal .tipsMob{
    display:block;color: #74d588;padding:5px 25px;text-align: left;margin-bottom: 10px;background: url(/img/suc.png) 5px 8px no-repeat transparent;
    display:none;
    clear: both;
}
.yqm_div .iconfont{
    display: inline-block;
    transform:rotate(90deg);
    -webkit-transition:rotate(90deg);
    -moz-transition: rotate(90deg);
    -o-transition: rotate(90deg);
}

.hide{
    display: none;
}
.yqm_div .yaoqm_rot{
    transform:rotate(270deg);
    -webkit-transition:rotate(270deg);
    -moz-transition: rotate(270deg);
    -o-transition: rotate(270deg);
}
/*账户中心*/
.personal_main{
    height: 5.05rem;
    background: #FF8525;

    color:#fff
}
.personal_main a{
    color:#fff;
    position: relative;
    z-index: 99;
}
.personal_main a:active p{
background: transparent;
}
.personal_user .touxiang{
    height: 0.58rem;
    width: 0.58rem;
    border-radius: 50%;
    margin-left:0.05rem;
    margin-right: 0.15rem;
}
.personal_user .pull-left .dj{
    color:#fdf0e1;
    border:1px solid #fdf0e1;
    padding:2px 0;
    border-radius: 5px;
    margin-top: 5px;
    display: block;
    text-align: center;
    width:0.65rem
}
.pull-number {
    float:left
}
.personal_user{
    overflow: auto;
    padding:0.2rem 0 0 0.24rem
}
.personal_main .p1{
    font-size: 0.28rem;
    text-align: center;
    margin:0rem 0 0.5rem 0;
    line-height: 2.2;

}
.personal_main .p2{
    font-size: 0.6rem;text-align: center;
}
.personal_main .list1{
    overflow: auto;
    margin-top: 0.6rem;
}
.personal_main .list1 li{
    float: left;
    width: 33.3%;text-align: center;
}
.personal_main .list1 li .p2{
    font-size: 0.32rem;
    margin-bottom: 0.1rem;
}
.personal_main .list1 li .p3 {
    font-size: 0.28rem;
}
.personal_reg .pull-left,.personal_reg .pull-right{
    width: 50%;text-align: center;
    background: #fff;
    padding:0.3rem 0;


}
.personal_reg .line{
    position: absolute;
    height: 100%;
    top:0;
    left:50%;
    width: 1px;
    background: #ebebeb;
}
.personal_reg .pull-left a,.personal_reg .pull-right a{
    display: inline-block;
    height: 0.75rem;
    width: 2.15rem;
    line-height: 0.75rem;
    text-align: center;
    border:1px solid #fe8749;
    color:#fe8749;
    font-size: 0.36rem;
    border-radius: 5px;

}
.personal_reg{
    overflow: auto;position: relative;
}
.personal_tab{
    background: #fff;
    margin-top:0.3rem
}
.personal_tab ul{
    overflow: auto;
}
.personal_tab ul li a{
 display: inline-block;
    width: 1.85rem;
    text-align: center;
    float:left;
    height: 1.82rem;
    font-size: 0.24rem;
    border-right: 0.01rem solid #ebebeb;
    border-bottom:1px solid #ebebeb;

}
.personal_tab ul li .iconfont{
    font-size: 0.48rem;
    margin:0.38rem 0 0.2rem 0;
    display: block;
    color:#fe8749 ;
 }
.personal_tab ul li .iconfont1{
    font-size: 0.48rem;
    margin:0.38rem 0 0.2rem 0;
    display: block;
    color:#fe8749 ;
}
.personal_tab ul li .p2{
    color:#fe8749;

}
.personal_bt .p2{
    color:#bcbbbb;
    font-size: 0.18rem;
    margin-top: 0.15rem;
    padding-bottom: 1.6rem;
}.personal_bt{
    text-align: center;
    margin-top: 0.45rem;
    font-size: 0.2rem;
    color:#999
 }

/*回款计划*/
.inv_main .inv_tit{
    height: 1.76rem;
    background: #ffeddf;
    text-align: center;
}
.inv_main .inv_tit .p1{
 padding-top: 0.45rem;
    color:#fe8749
}
.inv_main .inv_tit .p1 span{
    font-size: 0.3rem;
}
.inv_main .inv_tit .p2{
    padding-top: 0.20rem;
    font-size: 0.2rem;
}
.inv_list li{
background: #fff;
    margin-top: 0.22rem;

}
.inv_list li>div{
    overflow: auto;
    padding: 0 0.24rem;
    line-height: 0.6rem;
    color:#999
}
.inv_list li>div .tit{
    font-size: 0.28rem;
}
.inv_list li .div1{
    height: 0.6rem;
    line-height: 0.6rem;
    border-bottom: 1px solid #ebebeb;
    color:#333;
    overflow: hidden;
}
.inv_list li .div1 .a1{
    display: block;
    height: 0.2rem;
    border: 1px solid #ccc;
    line-height: 0.2rem;
    padding: 0.1rem;
    border-radius: 5px;
    margin-top: 0.1rem;
}
.inv_list li>div .gr{
    color:#11a93b
}
.inv_list li>div .re{
    color:#f04324
}
.inv_list li>div .or{
    color:#fb7e1c
}

/*回款计划*/
.with_main,.pay_main,.rec_main,.bonus_main,.inv_ban,.invite_main,.cash_list,.inv_main,.integ_main,.personal_auto,.bank,.assets,.details_main,.active_main,.help_lists{
    padding-top:0.88rem;
}.item_mes{
    padding-top:1.48rem
 }
 .pay_main .list1{
     height: 0.82rem;
    line-height: 0.82rem;
        overflow: hidden;
 }
.pay_main .list1 li{
    width: 50%;text-align: center;background: #fff;
}
.pay_main .list1 li span{
    display: inline-block;
    width: 2rem;
    height:0.79rem ;
}
.pay_main .list1 li.active{
  color:#fb7e1c;
}
.pay_main .list1 li.active span{
    border-bottom: 1px solid #fb7e1c;
}
.pay_tit>div{
height: 1.4rem;
    background: #ffedde;
    overflow: auto;
    display: none;
}.pay_tit>div>div{
    width: 50%;
    text-align: center;
 }
.pay_tit .p1{
    color: #fb7e1c;
    font-size: 0.3rem;
    margin-top: 0.3rem;
    margin-bottom: 0.15rem;
}
/*我的积分*/
.integ_main .integ_bg{
    height: 1.71rem;
    background: url("/img/inbg1.png") no-repeat;
    background-size: 100%;
    text-align: center;
    color:#fff;
    font-size: 0.28rem;
}
.integ_main .integ_bg .p1{
    font-size: 0.6rem;
    padding:0.15rem 0 0.1rem 0
}
.integ_main .p2{
    margin:0.2rem 0.24rem
}
.integ_list li{
    background: #fff;
    padding: 0.25rem 0.24rem 0.1rem 0.24rem;
    position: relative;
}
.integ_list{
    padding-bottom: 0.25rem;
}
.integ_list li .sp1{
    display: block;
    height: 0.18rem;
    width: 0.18rem;
    border-radius:50%;
    background: #ebebeb;
    position: absolute;

}
.integ_list li .sp2{
    display: block;
    height: 0.7rem;
    background: #ebebeb;
    width: 2px;
    position: absolute;
    left: 0.31rem;
    top: 0.5rem;
}
.integ_list li p{
    margin-left: 0.35rem;
    color:#999;
    font-size: 0.18rem;
}
.integ_list li .p3{
    font-size: 0.28rem;
    margin-bottom: 0.2rem;
    color:#fe8749
}
.integ_list li .p3 span{
    font-size: 0.2rem;
}
/*我的红包*/
.bonus_main .list1,.invite_main .list1{
    overflow: auto;
}
.bonus_main .list1 li,.invite_main .list1 li{
    height: 0.85rem;
    width: 50%;
    float:left;
    text-align: center;
    line-height: 0.85rem;
    background: #fff;
}
.bonus_main,.invite_main{
    position: relative;
}
.bonus_main .line ,.invite_main .line{
    position: absolute;
    height: 0.85rem;
    width: 1px ;
    background: #ebebeb;
    left: 50%;
    top:0;
}
.bonus_ul li{
    width: 7rem;
    height:2.5rem;
    margin:0.25rem auto 0;
    font-size: 0.3rem;
}
.bonus_ul li.w3{
    background: url("/img/bon2.png");
    background-size: 100% 100%;
    color:#999
}
.bonus_ul li.w4{
    background: url("/img/bon3.png");
    background-size: 100% 100%;
    color:#999
}
.bonus_ul li.w2{
    background: url("/img/bon1.png");
    background-size: 100% 100%;

}
.bonus_ul li.w4 .pull-left{color:#999}
.bonus_ul li.w3 .pull-left{color:#fe8749}
.bonus_ul li .pull-left {
    width: 2.1rem;
    text-align: center;
    line-height: 1.92rem;
    font-size: 0.53rem;
    color: #ff8624;
}
.bonus_ul li .pull-right {
    width: 4.2rem;
}

.bonus_ul li .pull-right h2{
    margin:0.15rem 0 0.1rem 0;
    font-size: 0.24rem;
}


.bonus_ul li .pull-right .sp2,.bonus_ul li .pull-right .sp3{
    font-size: 0.18rem;
}
.bonus_ul li .pull-right .sp4{
    margin-top: 0.15rem;
    font-size: 0.18rem;
}
.bonus_main .list1 li.active a{
    color:#ff8624
}
.bonus_ul>div{
    display: none;
}
.none_img{
    margin:1rem  auto 0;
    width: 2.34rem;
}
.bonus_no{
    margin:1rem  auto 0;
    width: 1.8rem;
}
.personal_list .pull-right{
    margin:0;    color: #999;
    clear: both;

    display: table;
}
.personal_list .pull-right .wei{
    color:#4fa2ff
}
.personal_list li a{
    clear: both;
}
.logout_btn{
    width: 7rem;
    margin:0.7rem  auto 0;
}
.personal_us .pull-right{
    margin:0.3rem 0.3rem 0 0;

}
.personal_us .pull-right img{
    height: 0.83rem;
    width: 0.83rem;
    border-radius: 50%;

}
.personal_us li label{
    height: 1.5rem;
    line-height: 1.5rem;
    background: #fff;
    padding:0 0.24rem;display: block;
}
.modal{
    position: fixed;
    height: 100%;
    overflow: auto;
    background: rgba(0,0,0,0.3);
    width: 100%;
    top: 0;
    z-index: 100;
}
.hq_btn{
    font-size: 0.28rem;
    color:#fd9746;
    width: 1.7rem;
    height: 0.9rem;
    line-height: 0.9rem;
    border:none;
    background: #fff;
    position: absolute;
    right: 0;
    top:0
}
.btn_cf{
    color:#999
}
/*弹出框*/
.modal-alert{
    position: fixed;height:100%;width:100%;background: rgba(0,0,0,0.3);top:0;left:0;z-index: 9999;
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#7f000000,endColorstr=#3f000000);    /*IE8支持*/
}
.modal-dialog-alert{width:6.8rem;background: #fff;margin:20% auto 0;text-align:center;padding-bottom: 0.4rem;}
.modal-conten-alert img{margin:0 auto;padding-top: 0.4rem}
.modal-conten-alert .p1{color:#ff8a10;margin:0.35rem 0 0.35rem 0;font-size:0.32rem;}
.modal-conten-alert .btn_zd{font-size:0.28rem;color:#ff8a10;border:1px solid #ff8a10;padding:2px 0.3rem;display:table;margin:0 auto;border-radius: 8px;}

.show_ps img{
    margin:0.15rem auto 0;
    width: 0.6rem;
}
.show_ps{
    background-image: url("/img/ps2.png");
    background-size: 100%
}
.hide_ps{
    background-image: url("/img/ps1.png");
    background-size: 100%;
}
/*账户总额*/
.assets .jine_all{
    position: absolute;
    top: 3rem;
    left: 0;
    width: 100%;
    font-size: 0.25rem;
    text-align: center;

}

.assets .jine_all .p2{
    font-size: 0.4rem;
    margin-bottom:0.2rem
}
.assets .container{
background: #fff;
}
.jine_div li{
    height: 0.9rem;
    line-height: 0.9rem;
    font-size: 0.28rem;
    border-bottom: 1px solid #ebebeb;
    position: relative;
    padding-right: 0.24rem;
}
.jine_div  ul{
    padding-left:0.5rem;
    padding-top:0.4rem
}
.jine_div{
    background: #fff;
    margin-top: 0.25rem;
}
.jine_div .li1>div{
    height: 0.6rem;
    line-height: 0.6rem;
}.jine_div .li1{
    height: 1.9rem;
 }
.jine_div li i{
    display:inline-block;
    height: 0.1rem;
    width: 0.1rem;
    border-radius: 50%;
    background: #76adf3;
    position: absolute;
    margin-left: -0.3rem;
    margin-top: 0.4rem;
}
.assets_div {
    height: 1.35rem;
    line-height: 1.35rem;
    background: #fff;
    margin-top: 0.15rem;
    position: relative;

}
.assets_div .pull-left{
    width: 50%;
    height: 1rem;
    line-height: 1rem;
    margin-top: 0.15rem;

}
.assets_div .pull-right{
    width: 50%; height: 1rem;
    line-height: 1rem;margin-top: 0.15rem;

}
.assets_div .line{
    width: 1px;
    height: 1rem;
    background: #ebebeb;
    display: inline-block;
    position: absolute;
    left:50%;
    top:0.15rem
}
.assets_div i{
    height: 0.1rem;
    background: #6cc6a9;
    width: 0.1rem;
    display: inline-block;
    margin: 0 0.1rem 0 0.2rem ;
}
.assets_div span{
    margin-left:0.1rem;
    font-size: 0.28rem;
}
/*资金明细*/
.cash_list li{
    background: #fff;
    height: 1.3rem;
    border-bottom: 1px solid #ebebeb;
    padding:0 0.24rem
}
.cash_list li .p1{
    font-size:0.3rem;
    padding:0.25rem 0 0.2rem 0
}
.cash_list li .p2{
    font-size: 0.2rem;
    color:#999
}
.cash_sele li{
    width: 2.3rem;
    float:left;
    height: 0.65rem;
    border:1px solid #ebebeb;
    line-height: 0.65rem;
    text-align: center;
    margin-left:0.1rem;
    margin-top:0.3rem;
    background: #fff;
    border-radius:3px;
}
.cash_sele li.active{
    color:#ff8200;
    border:1px solid #ff8200
}
.cash_warp {
    display: none;
    position: fixed;
    top:0.88rem;
    left:0;
    height: 100%;
    background: rgba(0,0,0,0.3);
}
.cash_warp .cash_btn{
    width:1.5rem;
    height:0.65rem;
    text-align: center;
    display: table;
    background: #ff8200;
    color:#fff;
    line-height: 0.65rem;
    float:right;
    margin:0.15rem 0.2rem 0.2rem 0; border-radius:3px;

}
.cashflow_head .img{
    display: inline-block;
    width:0.3rem;
    margin:0 0 0 0.15rem;
    vertical-align: middle;
}
.cashflow_head .img_tran{
    transform:rotate(180deg);
    -webkit-transition:rotate(180deg);
    -moz-transition: rotate(180deg);
    -o-transition: rotate(180deg);

}
/*自动投标*/
.auto_list li{
    height: 1.3rem;
    line-height: 1.3rem;
    background: #fff;
    border-bottom: 1px solid #ebebeb;
    padding:0 0.24rem;
    clear: both;
}
.slide_warp{
    width: 7rem;
    margin:0 auto;
}
.slide_warp .p1{
    color:#999;font-size: 0.28rem;
    margin:0.4rem 0 0.4rem
}
.slide_warp .p2{
    color:#999;font-size: 0.28rem;
    margin-left:0.5rem
}
.slider-container{
    width: 6rem!important;
}
.auto_list{
    margin-bottom: 0.25rem;
    font-size: 0.3rem;
}
.bank_main{
    padding-top: 0.9rem;
}
.personal_auto .btn{
    width: 7rem;
    height: 0.9rem;
    line-height: 0.9rem;
    background: #fd9746;
    text-align: center;
    display: block;
    margin:0.7rem auto ;
    border-radius: 3px;
    color:#fff;
    font-size: 0.32rem;
}
.auto_type .pull-right .type_text label{
    display: none;
}
.auto_type .pull-right .type_text .show{
    display: inline-block;
}
.auto_list .pull-right{
    color:#999
}

.auto_list .input_m{
    width:1.2rem;height:0.65rem ;text-align: center;border:1px solid #999;border-radius: 5px;
    -webkit-appearance: none;font-size: 0.28rem;
}
.auto_list .pull-right .bl{
  font-size: 0.28rem;height: 0.65rem;width: 1.2rem;font-size: 0.28rem;
    border:1px solid #999;text-align: center;border-radius: 5px;
}

.auto_list .pull-right .iconfont{
    display: inline-block;
    transform:rotate(180deg);
    -webkit-transition:rotate(180deg);
    -moz-transition: rotate(180deg);
    -o-transition: rotate(180deg);

}
.auto_swwarp{
    position: fixed;
    height: 100%;
    background: rgba(0,0,0,0.3);
    width: 100%;
    top:0;
    left:0
}
.auto_sw{
    height: 3.12rem;
    position: absolute;
    width: 100%;
    background:#fff;
    bottom:0
}
.auto_sw .auto_tt{
    height: 1rem;
    background: #f7f7f7;
    line-height: 1rem;
    text-align: right;padding-right: 0.24rem;color:#fe8749
}
.auto_sw>label{
    width: 1.65rem;
    height: 0.6rem;
    line-height: 0.6rem;
    text-align: center;
    display: inline-block;
    border:1px solid #ebebeb;
    margin-left:0.6rem;
    margin-top: 0.6rem;
    position: relative;
}
.auto_sw>label.active{
    border:1px solid #fe8749;
}
.regular-checkbox {display: none; }
.regular-checkbox:checked + label:after {
    text-align: center;border:1px solid #fe8749;line-height: 12px;content: '';
    font-size: 12px;position: absolute;top: -1px;left: -1px;color: #fe8749;width: 1.65rem;
    height: 0.6rem;
}
.check-lab{    display: inline-block; }
.regular-checkbox1{vertical-align: middle;margin-right: 5px;}
.mr_i{margin:0 0.1rem}
/*邀请有礼*/
.inv_ban img{
    width: 100%;
    margin-bottom: 0.2rem;
}
.invite_list li .pull-right{
    margin:0
}
.inv_ewm{
    text-align: center;
    margin:0.8rem 0 1rem 0;

    padding-bottom: 1.2rem;
}
.inv_ewm img{
    width: 3.44rem;
    margin:0 auto;
}
.inv_ewm p{
    font-size: 0.28rem;
    margin-top: 0.2rem;
}
.inv_btnyq{
    background: #fff;
    height: 1.25rem;
    position: fixed;
    width: 100%;
    bottom: 0;
    left:0
}
.inv_btnyq a{
    width: 7rem;
    background: #ff8624;
    height: 0.88rem;
    color:#fff;
    display: block;
    font-size: 0.36rem;
    line-height: 0.88rem;
    text-align: center;
    margin:0.2rem auto;
    border-radius: 3px;
}
.invite_ul1 li{
    height: 1.4rem;
    background: #fff;
    margin-top: 0.25rem;
    padding:0 0.56rem;
    font-size: 0.28rem;

}
.setTabMain>div{
    display: none;
}
.details_t .details_tab{
    border: 0;
    width: 100%;
    border-collapse: collapse;
}
.details_t .details_tab tr{
    border-top: 1px solid #ddd;
}
.details_t .details_tab tr:last-child td{
    border-bottom: 1px solid #ddd;
}
.details_t .details_tab tr:last-child th{
    border-bottom: 1px solid #ddd;
}
.details_t .details_tab th{
    border: 1px solid #ddd;
    border-bottom: 0;
    vertical-align: middle;
    width: 30%;
}
.details_t .details_tab td{
    border-width: 1px 1px 1px 0;
    border: 1px solid #ddd;
    border-left: 0;
    border-bottom: 0;
    padding: 0.1rem;
}
.details_t .div2 p{
    font-size: 0.26rem;
}
.details_t .div2 h4{
    font-size: 0.31rem;
    margin: 0.15rem 0;
}
.details_t .div2_tab{
    text-align: center;
    width: 100%;
    border: 1px solid #ddd;
    border-collapse: collapse;
    border-right: 0;
}
.details_t .div2_tab tr{
    border-bottom: 1px solid #ddd;
}
.details_t .div2_tab td{
    text-align: center;
    border-right: 1px solid #ddd;
}
.details_t .div2_tab th{
    text-align: center;
    border-right: 1px solid #ddd;
}
.invite_ul1 li .p2{
    color:#999;
    font-size: 0.18rem;
}
.invite_ul1 li .p1{
    padding:0.3rem 0 0.2rem 0
}
.invite_ul2>li{
    height: 1.9rem;
    margin-top: 0.25rem;
    background: #fff;
    padding:0 0.24rem
}
.invite_ul2>li .p1{
    line-height: 0.62rem;
    font-size: 0.28rem;
    border-bottom: 1px solid #ebebeb;
}
.invite_ul2>li .p1 .pull-right{
    color:#999;
    font-size: 0.18rem;
}
.invite_ul2>li .invite_ol{
    overflow: auto;
}
.invite_ul2>li .invite_ol li{
    width: 2.34rem;
    float:left;
    text-align: center;
    font-size: 0.2rem;
    color:#999
}
.invite_ul2>li .invite_ol li .p2{
    margin:0.25rem 0 0.2rem 0;font-size: 0.24rem;
    color: #ff8525;
}
.invite_main .list1{
    border-top: 1px solid #ebebeb;
}
.invite_main .list1 .active{
    color:#fe8749
}
/*添加银行卡*/
.bank_main .p1{
    font-size: 0.28rem;
    color:#999;
   margin:0.2rem 0 0.2rem 0.24rem
}
.bank_div1{
    background: #fff;
    padding:0 0.24rem 0.35rem ;
    font-size: 0.28rem;
    margin-bottom: 0.25rem;

}
.bank_div1 p{
    padding-top:0.35rem
}
.bank_list{

}

.bank_list li{
    height: 0.95rem;
    line-height: 0.95rem;
    background: #fff;
    padding-left:0.24rem;
    border-bottom: 1px solid #ebebeb;
}
.bank_list li input,.bank_list li select{
    width: 4.95rem;
    height: 0.95rem;
    float: right;
    border:none;
    background: transparent;
    color:#999
}
.bank_main .btn{
    width: 7rem;
    height: 0.9rem;
    line-height: 0.9rem;
    font-size: 0.32rem;
    text-align: center;
    background: #fd9746;
    color:#fff;
    display: block;
    margin:0.7rem auto;
}
.bank li{
    width: 5.8rem;
    height: 2rem;
    margin:0.4rem auto;
    background: #fff;
    border-radius: 3px;
    text-align: center;
    padding:0.24rem 0.3rem;
}
.bank .addbank img{
    margin:0 auto;
    padding:0.6rem 0
}
.bank .addbank a{
    color:#fe8749;
}
.bank .bank_modify {
    height: 0.5rem;
    width: 100%;
    line-height: 0.5rem;
    border-bottom: 1px dashed #fe8749;
}.bank .upgrade {
     margin-left: 5px;
 }.bank .upgrade, .up {
      float: right;
      color: #fe8749;
      margin-top:0.12rem;
  }
.regbank_img {
    height: 0.4rem;
    margin-bottom: 0.25rem;
    margin-top: 0.05rem;
}
.bankli p{
    text-align: left;
    margin-bottom: 0.1rem;
}

.bank .bank_modify .modify, .remove {
    float: right;
    color: #fe8749;
}
.bank .bank_modify .modify {
    margin-left: 10px;
}

.bank .bank_modify .modify, .remove {
    float: right;
    color: #fe8749;
}
/*充值*/
.rec_main .top{height: 4rem;background: url(/img/r_02.png) no-repeat; background-size: 100%;}
.rec_main .top img{height: 0.4rem;margin: 0 0.1rem  0 1.4rem;padding-top:0.75rem}
.rec_main .top .p1{color:#FD9746;font-size: 0.28rem;width: 3.7rem;margin: 0.3rem 0 0 1.4rem;}
.rec_main .top .p2{color:#FD9746;font-size: 0.24rem;width: 3rem;margin: 0.3rem 0 0 1.4rem;}
.rec_main .p3{color:#FF6817;font-size: 0.24rem;padding-left: 0.2rem;margin: 0.2rem 0 0.2rem 0}
.rec_main .p3 .s1{color:#FF6817;font-size: 0.24rem;display: inline-block;margin-left: 0.1rem;}
.rec_main .chongzhi{height: 1.05rem;background: #fff;margin-bottom: 0.4rem}
.rec_main .chongzhi span{color:#333;font-size: 0.32rem;float: left;    display: inline-block; margin: 0.3rem 0.4rem;}
.rec_main .chongzhi input{color:#B6B6B6;font-size: 0.28rem;float: left;border: 0;height: 100%;}
.rec_main .sure{background: #FD9746;border-radius: 0.1rem;height: 0.95rem;width: 6.93rem;color: #fff;font-size: 0.32rem;border: 0;text-align: center;margin-left: 0.3rem;}

/*提现*/
.with_main .top{height: 4rem;background: url(/img/r_02.png) no-repeat; background-size: 100%;}
.with_main .top img{height: 0.4rem;margin: 0 0.1rem  0 1.4rem;padding-top:0.75rem}
.with_main .top .p1{color:#FD9746;font-size: 0.28rem;width: 3.7rem;margin: 0.3rem 0 0 1.4rem;}
.with_main .top .p2{color:#FD9746;font-size: 0.24rem;width: 3rem;margin: 0.3rem 0 0 1.4rem;}
.with_main .p3{color:#FF6817;font-size: 0.24rem;padding-left: 0.2rem;margin: 0.2rem 0 0.2rem 0}
.with_main .p3 .s1{color:#FF6817;font-size: 0.24rem;display: inline-block;margin-left: 0.1rem;}
.with_main .tixian{height: 1.05rem;background: #fff;overflow: hidden;clear: both}
.with_main .password{border-top:1px solid #EBEBEB}
.with_main .tixian span{color:#333;font-size: 0.32rem;float: left;    display: inline-block; margin: 0.3rem 0.4rem;}
.with_main .tixian .s2{width: 0.95rem;height: 0.45rem;display: inline-block;background: #FD9746;color:#fff;float: right;text-align: center;line-height: 0.45rem;border-radius: 0.08rem;font-size: 0.28rem;}
.with_main .tixian input{color:#B6B6B6;font-size: 0.28rem;float: left;border: 0;    height: 0.5rem;
    margin-top: 0.25rem;}
.with_main .sure{background: #FD9746;border-radius: 0.1rem;height: 0.95rem;width: 6.93rem;color: #fff;font-size: 0.32rem;border: 0;text-align: center;margin-left: 0.3rem;}
.with_main .p4{color: #333;font-size: 0.24rem;text-align: right;    margin: 0.2rem 0.2rem 0.2rem 0;}


/*头像上传*/
.upload_a {
    background-color: rgba(0, 0, 0, 0.8);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#5f000000,endColorstr=#5f000000);
    z-index: 99999;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
.a-upload {
    padding: 4px 0.05rem;
    height: 0.3rem;
    line-height: 0.3rem;
    position: relative;
    cursor: pointer;
    color: #999;
    background: #fafafa;
    border: 1px solid #999;
    border-radius: 10px;
    overflow: hidden;
    display: inline-block;
    *display: inline;
    *zoom: 1;
    margin-left: 0.3rem;
    width: 1.3rem;
    text-align: center;
}

.a-upload  input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
    filter: alpha(opacity=0);
    cursor: pointer
}
/*.upload_a .upload {*/
    /*font-size: 12px;*/
    /*position: absolute;*/
    /*margin: auto;*/
    /*top: 0;*/
    /*left: 0;*/
    /*right: 0;*/
    /*bottom: 0;*/
    /*width: 680px;*/
    /*height: 545px;*/
    /*background: #fff;*/
/*}*/



.upload_a .upload .imagetop  .del{
    float:right;
    margin: 10px  15px 0 0;
    width: 30px;
    height: 30px;
    cursor: pointer;
}
.upload_a .upload .imagetop h2 {

    display: inline-block;
    font-size: 18px;
    margin: 10px 0 0 20px;
}

.upload_a .imageBox {
    position: relative;
    height:8rem;
    /*width:419px;*/

    background: #fff;
    overflow: hidden;
    background-repeat: no-repeat;
    cursor: move;

}

.upload_a .imageBox .thumbBox {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 6rem;
    height: 6rem;
    margin-top: -3rem;
    margin-left: -3rem;

    box-sizing: border-box;
    border: 1px solid rgb(102, 102, 102);
    box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.3);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#3f000000,endColorstr=#3f000000);
    background: none repeat scroll 0% 0% transparent;
}

.upload_a .imageBox .spinner {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    text-align: center;
    line-height: 400px;
    background: rgba(0, 0, 0, 0.7);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#7f000000,endColorstr=#7f000000);
}

/*.upload_a .upload p { line-height: 12px;  line-height: 0px;  height: 0px;  margin: 10px;  color: #bbb  }*/
.upload .action .new-contentarea {
    width: 115px;
    height: 34px;
    float: left;
    margin: 15px 0 0 20px;
}

.upload_a .upload .action {
    height: 65px;
    overflow: hidden;
}

.upload_a .upload .cropped {
    position: absolute;
    top:115px;
    right:20px;
    width: 196px;
    height: 345px;
    border: 1px #dfdfdf solid;

}
.upload_a .upload .cropped img{
    width: 132px;
    height: 132px;
    margin: 80px 0 0 30px;
}

/*选择图片上传*/
.upload_a .upload a.upload-img label:hover {
    background-color: #ff8525;
}

.upload_a .upload .new-contentarea label {
    background-color: #ff8525;
    border-radius:3px;
    display: block;
    color: #fff;
}

.upload .new-contentarea input[type=file] {
    width: 188px;
    height: 60px;
    background: #333;
    margin: 0 auto;
    position: absolute;
    right: 50%;
    margin-right: -94px;
    top: 0;
    right: 0px;
    margin-right: 0px;
    width: 10px;
    opacity: 0;
    filter: alpha(opacity=0);
    z-index: 2;
}

.upload_a .upload a.upload-img {
    width: 115px;
    height: 36px;
    display: inline-block;
    line-height: 36px;
    text-align: center;
    font-size: 16px;
    color: #FFF;
    cursor: pointer;
}

.upload_a .upload .new-contentarea span {
    font-size: 14px;
    position: absolute;
    bottom: 200px;
    left: 70px;
}

.upload_a .upload .Btnsty_peyton {
    float: left;
    width: 115px;
    height: 36px;
    line-height: 36px;
    display: inline-block;
    font-size: 16px;
    color: #999;
    border-radius: 3px;
    text-decoration: none;
    cursor: pointer;
    margin: 15px 0 0 20px;
    text-align: center;
    border: 1px solid #dfdfdf;
    background: none;
    position: relative;
}
.upload_a .upload .Btnsty_peyton:hover{
    background-color: #ff8525;
    color:#fff;
}
.upload_a .upload .jq:hover .jq1{
    display: none;
}

.upload_a .upload .Btnsty_sure {
    float: right;
    width: 2rem;
    height: 1rem;
    line-height: 1rem;
    display: inline-block;
    font-size: 0.28rem;
    color: #FFF;
    background: transparent;
    text-align: center;
    border: none;
}
.upload_a .upload .Btnsty_cal{
    display: inline-block;
    height: 1rem;
    line-height: 1rem;
    color:#fff;
    width: 2rem;
    text-align: center;
}
.upload_a .upload .bp {
    background: #fff;
    color: #9C9C9C;
    border: 1px solid #9C9C9C
}
.upload_btn{
    height: 1rem;
    width: 100%;
    background: #131313;
    position: fixed;
    bottom:0;
}
/*提现规则*/
.rule_warp .p1{
    margin:0.2rem 0.24rem
}
.rule_warp{
    padding-top:0.88rem
}
.rule_div{
    background: #fff;
    padding:0.4rem 0.25rem;
    line-height: 1.6;
}
/*load*/
@-webkit-keyframes scale {
    0% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 1; }

    45% {
        -webkit-transform: scale(0.1);
        transform: scale(0.1);
        opacity: 0.7; }

    80% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 1; } }
@keyframes scale {
    0% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 1; }

    45% {
        -webkit-transform: scale(0.1);
        transform: scale(0.1);
        opacity: 0.7; }

    80% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 1; } }

.ball-pulse > div:nth-child(0) {
    -webkit-animation: scale 0.75s 0s infinite cubic-bezier(.2, .68, .18, 1.08);
    animation: scale 0.75s 0s infinite cubic-bezier(.2, .68, .18, 1.08); }
.ball-pulse > div:nth-child(1) {
    -webkit-animation: scale 0.75s 0.12s infinite cubic-bezier(.2, .68, .18, 1.08);
    animation: scale 0.75s 0.12s infinite cubic-bezier(.2, .68, .18, 1.08); }
.ball-pulse > div:nth-child(2) {
    -webkit-animation: scale 0.75s 0.24s infinite cubic-bezier(.2, .68, .18, 1.08);
    animation: scale 0.75s 0.24s infinite cubic-bezier(.2, .68, .18, 1.08); }
.ball-pulse > div:nth-child(3) {
    -webkit-animation: scale 0.75s 0.36s infinite cubic-bezier(.2, .68, .18, 1.08);
    animation: scale 0.75s 0.36s infinite cubic-bezier(.2, .68, .18, 1.08); }
.ball-pulse > div {
    background-color: #fd9746;
    width: 15px;
    height: 15px;
    border-radius: 100%;
    margin: 2px;
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
    display: inline-block; }
.loader-inner{
display: none;
    left:0;
    top:0;
position: fixed;
    height: 100%;
    width: 100%;
    background: rgba(0,0,0,0.05);
}
.loader-inner .ball-pulse{

display: table;
   z-index: 9;
   margin:40% auto 0;
 }
/*投资成功*/
.data_sucWarp{
padding-top:0.88rem;
    width: 6.3rem;
    margin:0 auto;
    text-align: center;
}
.data_sucWarp .img1{
    width: 1.94rem;
    margin:1.5rem auto 0.6rem
 }
.data_sucWarp .p1{
font-size: 0.38rem;
    color:#222
}

.data_sucWarp .p2{
    color:#999;font-size: 0.3rem;
    margin-top:0.3rem
}
.data_sucWarp a{
display: inline-block;
    width: 2.8rem;
    height: 0.8rem;
    border:1px solid #fd9746;
    border-radius: 5px;
    color:#fd9746;
    line-height: 0.8rem;
    font-size: 0.3rem;
    margin-top:1.2rem
}
.data_sucWarp .a1{
background: #fd9746;
    color:#fff
}
/*充值记录*/
.recharge_list{

    padding-top:0.88rem;
}
.recharge_list .img1{
width: 0.3rem;
    margin-right:0.15rem
}
.recharge_list .img2{
    width: 0.4rem;
    margin-right:0.15rem
}
.recharge_list li{
    overflow: auto;
    padding:0.3rem 0 0.38rem  0.3rem;
    border-bottom:1px solid #EBEBEB;
    background: #fff;
    color:#999;
}
.recharge_list li p{
margin-bottom:0.1rem
}
.recharge_list li .p1{
font-size: 0.28rem;
    margin-bottom: 0.15rem;
    color:#333
}

.chk_3{display: none}
.chk_3 + label {
    margin-top:0.3rem;
    background-color: #fafbfa;
    padding: 9px;
    border-radius: 50px;
    display: inline-block;
    position: relative;
    -webkit-transition: all 0.1s ease-in;
    transition: all 0.1s ease-in;
    width: 40px;
    height: 15px;
}

.chk_3  + label:after {
    content: ' ';
    position: absolute;
    top: 0;
    -webkit-transition: box-shadow 0.1s ease-in;
    transition: box-shadow 0.1s ease-in;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 100px;
    box-shadow: inset 0 0 0 0 #eee, 0 0 1px rgba(0,0,0,0.4);
}

.chk_3  + label:before {
    content: ' ';
    position: absolute;
    background: white;
    top: 1px;
    left: 1px;
    z-index: 5;
    width: 31px;
    -webkit-transition: all 0.1s ease-in;
    transition: all 0.1s ease-in;
    height: 31px;
    border-radius: 100px;
    box-shadow: 0 3px 1px rgba(0,0,0,0.05), 0 0px 1px rgba(0,0,0,0.3);
}

.chk_3:active + label:after {
    box-shadow: inset 0 0 0 20px #eee, 0 0 1px #eee;
}

.chk_3:active + label:before {
    width: 37px;
}

.chk_3:checked:active + label:before {
    width: 37px;
    left: 20px;
}

.chk_3  + label:active {
    box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0px 1px 3px rgba(0,0,0,0.1);
}

.chk_3:checked + label:before {
    content: ' ';
    position: absolute;
    left: 26px;
    border-radius: 100px;
}

.chk_3:checked + label:after {
    content: ' ';
    font-size: 1.5em;
    position: absolute;
    background: #4cda60;
    box-shadow: 0 0 1px #4cda60;
}
/*平台数据*/
.section1 .img1{
    width: 5.4rem;
    margin:0 auto 1rem ;
}
.section{
    text-align: center;
    color:#fff;
}
.section1 .p1{
margin-bottom:0.65rem;font-size: 0.38rem;
}
.section1 .p2{
    color:#ffa400;
    font-size: 0.5rem;
    margin-bottom: 1.4rem;
}
.section2 img{
margin:0 auto 0.4rem;

}
.section2 .p1{
font-size: 0.32rem;
margin-bottom: 0.4rem;
    color:#fff
}
.section2 .p2{
    color:#ffa400;
    font-size: 0.5rem;
    margin-bottom:0.4rem
}
.section3 .d1{
height: 2.61rem;width: 4.85rem;background: url("/img/pt8.png");
    margin:0 auto 2.1rem;
    background-size: 100%;
    color:#ffa400;
    font-size: 0.5rem;

}
.section3 .d1 .p1{
    padding:0.55rem 0; font-size: 0.32rem;color:#fff
}
/*回款表*/
.paytable_main{
position: relative;
    padding-top:0.88rem;
}
.paytable_main .line{
width: 1px;
    background: #f76b24;
    display: block;
    position: absolute;
    height: 100%;
    top: 0;
    z-index: -9;
    left:0.7rem
}
.paytable_main .item_tab{
margin-top:0;padding-top:0.88rem
}
.paytable_main .item_tab .ul1 li{
    height: 0.86rem;
    line-height: 0.86rem;
}
.paytable_main .ul2{
overflow: auto;

}
.paytable_main .sp1{
height: 0.5rem;line-height: 0.5rem;border:1px solid  #fcd6aa;background: #fbf6ee;display: block;color:#999;padding:0 0.15rem;
    margin-bottom:0.3rem;

}
.paytable_main .i1{
color:#ff8200
}
.paytable_main .ul2 li{
   width: 33.33%;
float:left;text-align: center;
    background: #fff;
    padding-bottom: 0.15rem;
}
.paytable_main .ul2 li .p1{
height: 1rem;line-height: 1rem;
    font-weight: bold;
    font-size: 0.28rem;
}
.paytable_main .ul2 li p{
    height: 0.5rem;line-height: 0.5rem;
}

#zoom {
    z-index: 99990;
    position: fixed;
    top: 0;
    left: 0;
    display: none;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#8f000000,endColorstr=#8f000000);

    -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";
}
#zoom .content {
    z-index: 99991;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    height: 200px;
    background: #ffffff no-repeat 50% 50%;
    padding: 0;
    margin: -100px 0 0 -100px;
    box-shadow: -20px 20px 20px rgba(0, 0, 0, 0.3);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#3f000000,endColorstr=#3f000000);
    border-radius: 4px;

}
#zoom .content.loading {
    background-image: url('/img/loading.gif');
}
#zoom img {
    display: block;
    max-width: none;
    background: #ececec;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#3f000000,endColorstr=#3f000000);
    border-radius: 4px;
}
#zoom .close {
    z-index: 99993;
    position: absolute;
    top: 0;
    right: 0;
    width: 50px;
    height: 50px;
    cursor: pointer;
    background: transparent url('/img/close.png') no-repeat 50% 50%;
    opacity: 1;
    filter: alpha(opacity=100);
    border-radius: 0 0 0 4px;
}




#zoom .previous,
#zoom .next {
    z-index: 99992;
    position: absolute;
    top: 50%;
    overflow: hidden;
    display: block;
    width: 1rem;
    height: 1rem;
    margin-top: -25px;
}
#zoom .previous {
    left:  0.2rem;
    background: url('/img/about_pre.png') no-repeat 0 0;
    border-radius: 0 4px 4px 0;
}
#zoom .next {
    right: 0.2rem;
    background: url('/img/about_next.png') no-repeat 100% 0;
    border-radius: 4px 0 0 4px;
}
.team_ul{
    padding-top:0.88rem
}
.team_ul li{
margin-top:0.18rem;
    padding:0 0.24rem;
background: #fff;
    overflow: auto;
    padding-bottom:0.25rem
}
.team_ul .div1{
width: 6.6rem;
    background: #f5f5f5;
overflow: auto;
    margin: 0.35rem auto;
}
.team_ul .div2 p{
 text-indent: 2em;
    line-height: 1.8;
}
.team_ul .img1{
    width: 1.65rem;
    border-radius: 50%;
    margin:0.2rem 0.35rem 0.2rem 0.2rem;
}
.team_ul .div1 h3{
font-size: 0.34rem;
margin:0.8rem 0 0.2rem 0;

}
.team_ul .div1 p{
color:#999;
}
/*注册协议*/
.alertbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 100;
    color: #5e5e5e;background: rgba(0,0,0,0.3);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#7f000000,endColorstr=#3f000000);    /*IE8支持*/
    display: none;
}.alertbox .reg_alertbox {
     position: absolute;
     background: #fff;
     top: 5%;
     left: 7%;
     width: 86%;
     border-radius: 8px;padding-bottom:0.3rem;
 }
.alertbox  .content{
    padding: 0 0.25rem 0px 0.2rem;
}
.alertbox  .title{
    height:0.6rem;background: #fe8749;line-height:0.6rem;color:#fff;
    font-weight:bold;padding:0 0.2rem;margin-bottom:0.25rem;border-radius: 8px 8px 0 0;
}
.xyTextAlert{height:400px;
    overflow:auto;
}
.xyTextAlert p{margin-bottom:10px;    }
.alertbox  .title img{vertical-align: middle;cursor: pointer;float:right;}

.valide{    width: 660px;
    height: 136px;margin:160px auto;}

.valide p{
    color: #333;
    font-size: 30px;
    margin: 10px 0 40px 0;
}
.valide a{
    margin: 5px 0 8px 0;
    text-align:center;border-radius: 8px;
    padding: 0;
    height: 40px;
    line-height: 40px;
    border:1px solid  #ff8a10;
    color: #ff8a10;
    font-size: 16px;display:block;width:146px;
}

/*提现弹框*/
.zhao_t{
    position: absolute;
    top:0;
    bottom: 0;
    left:0;
    right: 0;
    width: 100%;
    background-color: #000;
    opacity: 0.2;
    display: none;
}
.tishi_t{
    width: 90%;
    height:5rem;
    background-color: #fff;
    position: absolute;
    top:23%;
    left:5%;
    display: none;
    border-radius: 0.2rem;
}
.tishi_t p{
    height: 1rem;
    width: 95%;
    color: #333;
    font-size: 0.4rem;
    padding-left:5%;
    border-bottom: 0.01rem solid #eee;
    line-height: 1rem;
}
.tishi_t .tishi_main{
    width: 90%;
    height: 1.5rem;
    margin:auto;
    margin-top:0.9rem;
}
.tishi_t .tishi_main h2{
    color: #333;
    text-align: center;
    font-size: 0.38rem;
    margin-bottom:1rem;
}
.tishi_t .tishi_main ul{
    width: 90%;
    height:1rem;
    margin: auto;
}
.tishi_t .tishi_main ul li{
    width:50%;
    height:1rem;
    float: left;
}
.tishi_t .tishi_main ul li a{
    width:85%;
    height: 0.8rem;
    color: #fff;
    text-align: center;
    line-height:0.8rem;
    font-size: 0.4rem;
    display: block;
    border-radius: 10px;
    cursor: pointer;
}



</style>
</html>