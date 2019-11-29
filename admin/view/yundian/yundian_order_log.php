<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>云店奖励－订单管理</title>
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/contentblue.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/js/layer/V2_1/skin/layer.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
    <script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/weixinpl/common/js/layer/layer.js"></script>
    <script type="text/javascript" src="/weixinpl/common/js/layer/V2_1/layer.js"></script>
    <style type="text/css">
        .pages-list{width:80%;min-width:500px;border:solid 1px #ddd;margin:15px;padding:10px;}
        .pages-list>dd{margin-top:5px;}
        .pages-list>dd>b{display:inline-block;vertical-align:top;width:80px;text-align:center;background-color:#eee;color:#333;font-size:12px;line-height:20px;}
        .pages-list>dd>span{display:inline-block;vertical-align:top;line-height:20px;margin:0 8px;}
    </style>
</head>
<body>
<!--内容框架开始-->
<div class="WSY_content">
    <!--列表内容大框开始-->
    <div class="WSY_columnbox">
        <div class="WSY_column_header">
            <div class="WSY_columnnav">       
                <a href="javascript:;" class="white1">订单日志</a>
            </div>
        </div>
        <!-- 订单日志 -->
        <div class="WSY_modifydiv " id="log_<?php echo $o_batchcode; ?>" >
            <dl class="pages-list">
                <?php

                foreach ($res23 as $k => $row_log)
                {
                    if($o_batchcode == $row_log['batchcode'])
                    {
                        $operation_user = $row_log['operation_user'];
                        if(!$operation_user)
                        {
                            continue;
                        }

                        foreach ($res24 as $k => $v)
                        {
                            if($operation_user == $v['weixin_fromuser'])
                            {
                                if ($v['weixin_name'] !='')
                                {
                                    $operation_user = $v['weixin_name'];
                                }
                                else
                                {
                                    $operation_user = '';
                                }
                            }
                        }

                        foreach($res2 as $k => $v)
                        {
                            if($buy_user_id == $v['id'])
                            {
                                $operation_user = $v['weixin_name'];					//注册-电话
                            }
                        }
                        ?>
                        <dd>
                            <b>时间：</b><span><?php echo $row_log['createtime'];?></span>
                            <b>操作：</b>
                            <span>
        							<?php
                                    $op_str = "";
                                    $op = $row_log['operation'];									//0：下单；1：取消；2：支付；3：修改价格；4：发货：5：申请延期；6：确认延期；7：确认收货；8：退货；9：退货审批；10：退款；11：退款审批；12：退款；13：用户退货填单；14：商家确认退货；';
                                    //获取用户售后上传的图片
                                    if($op == 8 || $op == 18 )
                                    {
                                        $images_ref_img     = '';
                                        $images_ref_img_arr = array();
                                        foreach($res26 as $k => $v)
                                        {
                                            if($o_batchcode == $v['batchcode'])
                                            {
                                                $images_ref_img = $v['images'];
                                            }
                                        }

                                        if(!empty($images_ref_img))
                                        {
                                            $images_ref_img_arr   = explode('|',$images_ref_img);
                                            $images_ref_img_count = count($images_ref_img_arr);
                                        }
                                    }
                                    switch($op){
                                        case 0 :$op_str = "下单";break;
                                        case 1 :$op_str = "取消";break;
                                        case 2 :$op_str = "支付";break;
                                        case 3 :$op_str = "修改价格";break;
                                        case 4 :$op_str = "发货";break;
                                        case 5 :$op_str = "申请延期";break;
                                        case 6 :$op_str = "确认延期";break;
                                        case 7 :$op_str = "确认收货";break;
                                        case 8 :$op_str = "退货";break;
                                        case 9 :$op_str = "退货审批";break;
                                        case 10 :$op_str = "退款";break;
                                        case 11 :$op_str = "退款审批";break;
                                        case 12 : $op_str = "退款操作";break;
                                        case 13 :$op_str = "用户退货填单";break;
                                        case 14 :$op_str = "商家确认退货";break;
                                        case 15 :$op_str = "退货完成";break;
                                        case 16 :$op_str = "确认完成";break;
                                        case 17 :$op_str = "订单评价";break;
                                        case 18 :$op_str = "申请维权";break;
                                        case 19 :$op_str = "维权审批";break;
                                        case 20 :$op_str = "维权处理";break;
                                        case 21 :$op_str = "微信退款";break;
                                        case 22 :$op_str = "订单删除";break;
                                        case 23 :$op_str = "维权扣除合作商款项";break;
                                        case 30 :$op_str = "系统派单";break;
                                        case 31 :$op_str = "运费修改";break;
                                        case 34 :$op_str = "修改快递单号";break;
                                    }
                                    echo $op_str;
                                    ?>
        								</span>
                            <b>描述：</b><span><?php echo $row_log['descript'];?></span>
                            <b>操作人：</b><span><?php echo $operation_user;?></span>
                        </dd>
                        <?php
                    }//订单日志if判断
                }//订单日志if循环
                ?>
            </dl>
        </div>
        <!-- 订单日志End -->
    </div>
</div>
</body>
</html>