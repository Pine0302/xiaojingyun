<?php

header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

$batchcode = $configutil->splash_new($_GET["batchcode"]);
$pay_batchcode = -1;
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$totalprice=0;
$pay_status= "";
$transaction_id = "";
$query="select pay_status,aftersale_type,aftersale_state,pay_type,order_price,pay_batchcode from ".WSY_DH.".orderingretail_order where isvalid=true and batchcode='".$batchcode."' limit 1";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $pay_status = $row->pay_status;
    $aftersale_type = $row-> aftersale_type;
    $aftersale_state = $row->aftersale_state;
    $pay_type = $row->pay_type;
    $totalprice = $row->order_price;
    $pay_batchcode = $row->pay_batchcode;
}

$price				= 0;
$weipay = "select total_fee,transaction_id from weixin_weipay_notifys where isvalid=true and out_trade_no='".$batchcode."' and attach='orderingretail_purchasing'";
$result_weipay = _mysql_query($weipay) or die('Query_weipay failed: ' . mysql_error());
while ($row_result_weipay = mysql_fetch_object($result_weipay)) {
    $transaction_id = $row_result_weipay->transaction_id;
    $price = $row_result_weipay->total_fee;
}
$price = $price/100;

//$sql = "select order_price as totalpricee  from orderingretail_order where isvalid=true and pay_batchcode='".$pay_batchcode."'";
////echo $sql;
//$result = _mysql_query($sql) or die('paySql failed: ' . mysql_error());
//while ($row = mysql_fetch_object($result)) {
//    $totalprice = $row->totalprice;
//    $score 		= $row->score;
//}

$paystatus_str="未付款";
if($pay_status=="pay"){
    $paystatus_str="已付款";
}

$return_status_str="未退款";
//if($aftersale_state=="refund"){
//    $return_status_str = "退款中";
//}
$batchcode_totalprice = 0;
//$sql_changeprice = "select totalprice from weixin_commonshop_changeprices where status=1 and isvalid=1 and batchcode='".$batchcode."' order by id desc limit 1";
//$result_cp = _mysql_query($sql_changeprice) or die('Query sql_changeprice failed: ' . mysql_error());
//if ($row_cp = mysql_fetch_object($result_cp)) {
//    $batchcode_totalprice = $row_cp->totalprice;
//}else{
////查询订单价格表中的记录
//    $sql_price = "select price from weixin_commonshop_order_prices where isvalid=true and batchcode='".$batchcode."'";
//    $result_price = _mysql_query($sql_price) or die('Query sql_price failed: ' . mysql_error());
//    if ($row_price = mysql_fetch_object($result_price)) {
//        //获取订单的真实价格（可能是折扣总价）
//        $batchcode_totalprice = $row_price->price;
//    }
//}


$refund= 0;
$query5="select sum(refund) as refund from weixin_commonshop_refunds where isvalid=true and batchcode='".$batchcode."'";
$result5 = _mysql_query($query5) or die('Query failed: ' . mysql_error());
while ($row5 = mysql_fetch_object($result5)) {
    $refund = $row5->refund;
}
$check_refund_status = 0; //检查他是否可以退款 0:可以退款 1:不可退款
if($refund >= $price){
    $check_refund_status = 1;
}

$refundable = $price;
$out_trade_no = $pay_batchcode;

?>
<!doctype html>
<html><head><meta charset="utf-8">
    <title>订单管理</title>
    <link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
    <script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
    <script charset="utf-8" src="../../../common/js/layer/V2_1/layer.js"></script>

</head>
<script>
    function submitV(){
        document.getElementById("keywordFrm").submit();
    }
</script>
<style>
    .WSY_remind_dl01 dd, .WSY_remind_dl02 dt{
        width:115px;
        height:20px;
    }
    .WSY_remind_dl01 dd, .WSY_remind_dl02 dd{
        line-height:20px;
    }
</style>
<body>
<!--内容框架-->
<div class="WSY_content">
    <!--列表内容大框-->
    <div class="WSY_columnbox">
        <!--列表头部切换开始-->
        <div class="WSY_column_header">
            <div class="WSY_columnnav">
                <a class="white1">微信支付详情</a>
            </div>
        </div><br />
        <!--列表头部切换结束-->
        <li style="margin-right: 60px;"><a href="javascript:history.go(-1);" class="WSY_button" style="margin-top: 0;width: 60px;height: 28px;vertical-align: middle;line-height: 28px;">返回</a></li>


        <div class="WSY_remind_main" style="min-height:250px;">
            <dl class="WSY_remind_dl02" style="margin-top:40px;margin-left:100px;">
                <dt style="line-height:20px;" class="WSY_left">编号：</dt>
                <dd>
                    <span class="input"><?php echo $batchcode; ?></span>
                </dd>
            </dl>
            <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                <dt style="line-height:20px;" class="WSY_left">微信交易单号：</dt>
                <dd><span class="input">
					<?php echo $transaction_id; ?>
				</span></dd>
            </dl>
            <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                <dt style="line-height:20px;" class="WSY_left">总费用：</dt>
                <dd><span class="input">
					¥<?php  echo number_format($totalprice,2); ?>元
				</span></dd>
            </dl>
            <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                <dt style="line-height:20px;" class="WSY_left">交易状态：</dt>
                <dd><span class="input">
					<?php  echo $paystatus_str; ?>
				</span></dd>
            </dl>


            <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                <dt style="line-height:20px;" class="WSY_left">微信支付：</dt>
                <dd><span class="input">
					<?php  echo $price; ?>
				</span></dd>
            </dl>


            <?php if($refund>0){ ?>
                <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                    <label>已退款金额：</label>
                    <span class="input">
						<?php echo $refund; ?>
					</span>
                </dl>
                <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                    <label></label>
                    <span class="input">
					   <?php
                       $query5="select refund,createtime from weixin_commonshop_refunds where isvalid=true and batchcode='".$batchcode."'";
                       $result5 = _mysql_query($query5) or die('Query failed: ' . mysql_error());
                       while ($row5 = mysql_fetch_object($result5)) {
                           $refund 	= $row5->refund;
                           $createtime = $row5->createtime;
                           $refundable = $refundable - $refund;

                           ?>
                           <div style="height:20px;">退款金额：<?php echo $refund; ?> &nbsp;&nbsp;&nbsp;退款时间：<?php echo $createtime; ?></div>
                       <?php } ?>
					</span>
                </dl>
            <?php } ?>
            <?php if(($aftersale_type=="refund" or $aftersale_type=="return") and $check_refund_status==0){ //只有申请退款或退货的才能显示退款操作
                ?>
                <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                <dt style="line-height:20px;" class="WSY_left">退款状态：</dt>
                <span class="input">
                <?php if($aftersale_state=="agree"){ ?>
                        &nbsp;&nbsp;<a href="javascript:showReturn();" style="color:blue" >退款</a><span style="color:red">(请到微信支付设置上传退款证书)</span>
				</span>
                    </dl>
                    <dl class="WSY_remind_dl02" style="margin-top:20px;margin-left:100px;">
                        <dt style="line-height:20px;" class="WSY_left">可退金额：</dt>
                        <span class="input"><?php echo $refundable; ?></span>
                    </dl>
                    <dl class="WSY_remind_dl02" id="div_return" style="display:none;margin-top:20px;margin-left:100px;">
                        <dt style="line-height:40px;" class="WSY_left"><span style="font-size:14px;">退款金额：</span></dt>
                        <dd style="line-height:40px;">
                            <span><input class="not_agent_tip" value="0" name="return_money" id="return_money" style="width:50px;height:24px;text-align:center;border:solid 1px #ccc;border-radius:2px;" type="text">元(不超过订单金额）</span>
                        </dd>
                        <dd class="WSY_bottonli con-button" style="float:left;margin-left:80px;"> <input id="subReturn" value="退款"  type="button" onclick="subReturn();" /></dd>
                    </dl>
                <?php  }else{  ?>
                    <span style="color:red;" >请先审核退款或退货申请</span>
                <?php }
            } ?>

        </div>
        <div style="width:100%;height:20px;">
        </div>
    </div>
</div>

<?php mysql_close($link);?>

<script>
    function showReturn(){
        document.getElementById("div_return").style.display="block";
    }
    var totalprice = <?php echo $price; ?>;
    var refundable = <?php echo $refundable; ?>;
    function subReturn(){
        $('#subReturn').attr("disabled",true);
        var return_money = document.getElementById("return_money").value;
        if(return_money>refundable){
            // alert("退款金额大于支付金额");
            alert("退款金额大于可退金额");
            $('#subReturn').attr("disabled",false);
            return;
        }
            document.location = "../../../common_shop/jiushop/refund_ordering.php?customer_id=<?php echo $customer_id_en; ?>&pay_batchcode=<?php echo $pay_batchcode; ?>&total_fee=<?php echo $price; ?>&batchcode=<?php echo $batchcode?>&out_trade_no=<?php echo $out_trade_no; ?>&refund_fee=" + return_money + "&transaction_id=<?php echo $transaction_id;?>";

    }
</script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>