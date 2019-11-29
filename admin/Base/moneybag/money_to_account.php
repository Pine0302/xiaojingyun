<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件0商城资料，1分享设置,2购物设计


$is_open_change            = 0; 	//零钱转货款开关，默认关闭
$min_change_price          = -1;		//最低转换金额条件：默认-1不限制
$coefficient               = -1;	//转换系数：默认 -1：不限，10：整10，100：整100，1000：整1000 （目前仅4种选项）
$change_rule               = 1.00;	//转换规则
$comment                   = '';		//说明


$query = "SELECT is_open_change,min_change_price,coefficient,change_rule,comment FROM orderingretail_change_account_setting where customer_id=".$customer_id." LIMIT 1";
$result= _mysql_query($query);
while($row=mysql_fetch_object($result)){
    $is_open_change	= $row->is_open_change;
    $min_change_price	= $row->min_change_price;
    $coefficient 	= $row->coefficient;
    $change_rule 	 	= $row->change_rule;
    $comment 		 	= $row->comment;
}
if($min_change_price==-1){
    $min_change_price='-1';
}
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
    <script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<!--    <script type="text/javascript" src="../../Common/js/Base/basicdesign/layer.js"></script>-->
    <script type="text/javascript" src="../../Common/js/layer/layer.js">
    <script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
    <script type="text/javascript" src="../../../common/utility.js"></script>

    <title>零钱转货款</title>

    <meta http-equiv="content-type" content="text/html;charset=UTF-8">
    <style>
        .distr_type_div i{margin-top:7px;}
        .WSY_remind_dl02 .distr_type_div {height:35px;}
        .cash_name{float:left;line-height:25px;margin-right:}
        .cash_coefficient_dd{display:inline-block;margin-right:10px;margin-top:5px;}
        .xuxiandiv{border: 2px dashed #999999;margin: 23px 23px 40px 23px;position: relative;padding-bottom: 20px;}
        .shezhidiv{position: absolute;top: -18px;left: 32px;border: 2px solid #cccccc;background-color: #ffffff;padding: 6px 20px;}
        .is_fee_input *{vertical-align: middle;}
        .mb5{margin-bottom:5px;}
    </style>
</head>
<body>
<div class="WSY_content">
    <div class="WSY_columnbox">
        <?php
        // include("../../../../weixinpl/back_newshops/Base/moneybag/basic_head.php");
        include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/moneybag/basic_head.php");
        ?>
        <form action="save_moneybag_to_account.php?customer_id=<?php echo $customer_id_en; ?>" method="post" id="saveFrom" name="saveFrom">
            <div class="WSY_remind_main">
                <dl class="WSY_remind_dl02">
                    <dt>零钱转货款开关：</dt>
                    <dd>
                        <?php if($is_open_change==1){ ?>
                            <ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
                                <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
                                <li onclick="set_is_open_change(0)" class="WSY_bot" style="left: 0px;"></li>
                                <span onclick="set_is_open_change(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                            </ul>
                        <?php }else{ ?>
                            <ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
                                <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
                                <li onclick="set_is_open_change(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                                <span onclick="set_is_open_change(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                            </ul>
                        <?php } ?>
                        <span style="color:red; font-size: 14px;margin-left: 5px">仅针对订货系统货款功能</span>
                        <input type="hidden" name="is_open_change" id="is_open_change" value="<?php echo $is_open_change; ?>" />
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>转换条件：</dt>
                    <dt style="width:100px;margin-left: 0px!important;">最低转换金额：</dt>
                    <dd>
                        <input type="text" id="min_change_price" name="min_change_price" value="<?php echo $min_change_price;?>"/>（-1表示不限）
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>转换系数：</dt>
                    <dd>
                        <input type="radio" class="coefficient" name="coefficient" value="-1" <?php if($coefficient == -1){ ?>checked<?php } ?>>不限
                        <input type="radio" class="coefficient" name="coefficient" value="10" <?php if($coefficient == 10){ ?>checked <?php } ?>>按整数10
                        <input type="radio" class="coefficient" name="coefficient" value="100" <?php if($coefficient == 100){ ?>checked <?php } ?>>按整数100
                        <input type="radio" class="coefficient" name="coefficient" value="1000" <?php if($coefficient == 1000){ ?>checked <?php } ?>>按整数1000
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>转换规则：</dt>
                    <dt style="width:100px;margin-left: 0px!important;">零钱:货款 = 1:</dt>
                    <dd>
                        <input type="text" id="change_rule" name="change_rule" value="<?php echo $change_rule;?>"/>（可输入等于大于0的数字，可保留小数点后两位数字）
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>转换说明：</dt>
                    <dd>
                        <textarea id="comment" name="comment" ><?php echo $comment;?></textarea>
                    </dd>
                </dl>
        </form>
        <div class="submit_div">
            <input type="button" class="WSY_button" value="提交" onclick="return saveData(this);" style="cursor:pointer;">
        </div>
    </div>
</div>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script>
    function saveData() {
        var is_open_change = $("#is_open_change").val();
        var min_change_price = $("#min_change_price").val();
        var coefficient = $("input[name='min_change_price']:checkbox").val();
        var change_rule = $("#change_rule").val();
        var $comment = $("#comment").val();
        if(min_change_price.length==0 || min_change_price==0){
            alert('最低转换金额不能为空，且大于0');
            return false;
        }
        if(change_rule.length==0 || change_rule==0){
            alert('转换规则不能为空，且大于0');
            return false;
        }
        if($comment.length>200){
            alert('转换说明不能超过200个字');
            return false;
        }
        document.getElementById("saveFrom").submit();
        return true ;
    }
    function set_is_open_change(v) {
        $('#is_open_change').val(v);
    }

    $("#change_rule").on('keyup', function (event) {
        var $amountInput = $(this);
        //响应鼠标事件，允许左右方向键移动
        event = window.event || event;
        if (event.keyCode == 37 | event.keyCode == 39) {
            return;
        }
        //先把非数字的都替换掉，除了数字和.
        $amountInput.val($amountInput.val().replace(/[^\d.]/g, "").
        //只允许一个小数点
        replace(/^\./g, "").replace(/\.{2,}/g, ".").
        //只能输入小数点后两位
        replace(".", "$#$").replace(/\./g, "").replace("$#$", ".").replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3'));
    });
    $("#min_change_price").on('keyup', function (event) {
        var $amountInput = $(this);
        //响应鼠标事件，允许左右方向键移动
        event = window.event || event;
        if (event.keyCode == 37 | event.keyCode == 39) {
            return;
        }
        if($amountInput.val()=='-' || $amountInput.val()=='-1'){
            return true;
        }
        //先把非数字的都替换掉，除了数字和.
        $amountInput.val($amountInput.val().replace(/[^\d.]/g, "").
        //只允许一个小数点
        replace(/^\./g, "").replace(/\.{2,}/g, ".").
        //只能输入小数点后两位
        replace(".", "$#$").replace(/\./g, "").replace("$#$", ".").replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3'));
    });
    $("#min_change_price,#change_rule").on('blur', function () {
        var $amountInput = $(this);
        //最后一位是小数点的话，移除
        $amountInput.val(($amountInput.val().replace(/\.$/g, "")));
    });
</script>
</body>
</html>