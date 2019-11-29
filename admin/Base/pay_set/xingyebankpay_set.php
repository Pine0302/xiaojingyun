<?php
header("Content-type: text/html; charset=utf-8");
require_once('../../../../weixinpl/back_newshops/config.php');
require_once('../../../../weixinpl/back_newshops/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once('../../../../weixinpl/back_newshops/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require_once('../../../../weixinpl/back_newshops/proxy_info.php');
require_once('../../../../weixinpl/back_newshops/Base/pay_set/pay_config.class.php');
$head=9;//
$pay_config = new pay_config();
$xingyebankpay_config = $pay_config->get_alipay_config($customer_id);
session_start();
$_SESSION['xingye_secret']= $xingyebankpay_config['xingye_secret'];
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="../common/css/pay_set/alipay_set.css">
    <script type="text/javascript" src="../../common/js/jquery-2.1.0.min.js"></script>


    <title>兴业银行支付</title>

    <meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
    <div class="WSY_columnbox">
        <?php
        include("../../../../weixinpl/back_newshops/Base/pay_set/pay_head.php");
        ?>
        <form action="save_xingyebankpay_set.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
            <input type=hidden name="xingye_id" value="<?php echo $xingyebankpay_config['xingye_id'] ?>" />
            <div class="WSY_remind_main">

                <dl class="WSY_remind_dl02">
                    <dt>商户号：</dt>
                    <dd>
                        <input type="text" name="xingye_account" value="<?php echo $xingyebankpay_config['xingye_account']; ?>">
                    </dd>
                </dl>
                <dl class="WSY_remind_dl02">
                    <dt>商户密钥：</dt>
                    <dd>
                        <input type="text" value="<?php if(!empty($xingyebankpay_config['xingye_secret'])){echo substr_replace($xingyebankpay_config['xingye_secret'],"***************",2,10);} ?>" name="xingye_secret" id="xingye_secret" data-password>
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>显示名称：</dt>
                    <dd>
                        <input type="text" value="<?php echo $xingyebankpay_config['title'] ?>" name="title" id="title" >
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>显示排序：</dt>
                    <dd>
                        <input type="text" value="<?php echo $xingyebankpay_config['px']?>" name="px" id="px">
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>默认图标：</dt>
                    <dd>
                        <img src='<?php echo $xingyebankpay_config['icon'];?>' name="icon" style="height: 80px;width: 80px">
                    </dd>
                </dl>

                <dl class="WSY_remind_dl02">
                    <dt>描述：</dt>
                    <dd>
                        <input type="text" value="<?php ?>" name="description" id="description" value="<?php echo $xingyebankpay_config['description']?>" >
                    </dd>
                </dl>

            </div>
        </form>
        <div class="submit_div">
            <input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;">
        </div>
    </div>
</div>
<script type="text/javascript" src="../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../common/js/pay_set/alipay_set.js"></script>
<script>
    document.getElementById('description').value = '<?php echo $xingyebankpay_config['description']?>';
</script>
</body>
</html>