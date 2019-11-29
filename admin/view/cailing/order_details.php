<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>彩铃订购－订单详情</title>
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
    <style>
        .order_div{margin-right: 0px;}
    	li{font-size: 14px;margin: 15px 0px 15px 60px;}
    	.WSY_list{margin-bottom: 0px;}
    </style>
</head>
<body>
	<!--内容框架开始-->
	<div class="WSY_content" id="WSY_content_height">
	    <!--列表内容大框开始-->
		<div class="WSY_columnbox">	
			<div class="WSY_column_header">
                <div class="WSY_columnnav">
                    <a class="white1">订单详情</a>
                </div>  
            </div>

		<div class="WSY_remind_main" style="padding-bottom: 50px;">
			<div class="WSY_list">
                <li class="WSY_left">
                	<a>订单信息</a>
                </li>
            </div>
            <div class="order_div">
            	<ul>
            		<li style="margin-top: 0px;">订单号：<?php echo $res[0]['batchcode'];?></li>
            		<li>订单金额：￥<?php echo $res[0]['money'];?></li>
            		<li>下单时间：<?php echo $res[0]['cot_createtime'];?></li>
                    <?php if ($status == 1 || $status == 3) { ?>
            		<li>完成时间：<?php echo $res[0]['confirmtime'];?></li>
                    <?php } ?>
                    <?php if ($status == 3) { ?>
                    <li>退款时间：<?php echo $res[0]['dabletime'];?></li>
                    <?php } ?>
            	</ul>
            </div>
            <div class="order_div">
                <ul>
                    <li style="margin-top: 0px;color: red;">
                        <?php
                            $status = $res[0]['status'];
                            $paystatus = $res[0]['paystatus'];
                            $recoverytime = $res[0]['recoverytime'];
                            if($status == -1){
                                echo '取消订单';
                            } else if($status == 1){
                                echo '已完成';
                            } else if($status == 2){
                                if ($paystatus == 0) {
                                    if (strtotime($recoverytime) >= time()) {
                                        echo '待付款';
                                    } else {
                                        echo '订单失效';
                                    }
                                } else if($paystatus == 1){
                                    echo '待完成';
                                }
                            } else if($status == 3){
                                echo '已退款';
                            } else if($status == 4){
                                echo '订单失效';
                            } else {
                                echo '未知状态';
                            }
                        ?>
                    </li>
                </ul>
            </div>
            <?php if ($paystatus == 1) { ?>
            <div class="order_div">
            	<ul>
            		<li style="margin-top: 0px;">支付类型：<?php echo $res[0]['paystyle'];?></li>
            		<li>实付金额：￥<?php echo $res[0]['money'];?></li>
            		<li>支付时间：<?php echo $res[0]['paytime'];?></li>
            	</ul>
            </div>
            <?php } ?>
			<div class="WSY_list">
                <li class="WSY_left">
                	<a>商品信息</a>
                </li>
            </div>
            <div style="float: left;margin-left: 60px;">
	            <img src="<?php echo $res[0]['img_url']; ?>" width="150px" height="150px">
            </div>
            <div class="order_div" style="width: 30%;">
            	<ul>
            		<li>彩铃名称：<?php echo $res[0]['cp_name'];?></li>
            		<li>彩铃价格：￥<?php echo $res[0]['price'];?></li>
            		<li>彩铃标签：<?php echo $res[0]['tip'];?></li>
            	</ul>
            </div>
			<div class="WSY_list">
                <li class="WSY_left">
                	<a>用户信息</a>
                </li>
            </div>
            <div>
            	<ul>
            		<li style="margin-top: 0px;">用户名：<?php echo $res[0]['wu_name'];?></li>
            		<li>用户编号：<?php echo $res[0]['id'];?></li>
            		<li>手机号码：<?php echo $res[0]['use_phone'];?></li>
            	</ul>
            </div>
			<div class="WSY_list">
                <li class="WSY_left">
                	<a>历史备注</a>
                </li>
            </div>
            <table width="97%" class="WSY_table" id="WSY_t1">
				<thead style="background-color: #ccc">
					<th width="10%" nowrap="nowrap" align="center">操作人</th>
					<th width="20%" nowrap="nowrap" align="center">时间</th>
					<th width="10%" nowrap="nowrap" align="center">类型</th>
					<th width="60%" nowrap="nowrap" align="center">内容</th>
				</thead>
				<tbody class="tbody-main">
					<?php 
					foreach ($res as $key => $row) {
					if($row['operator'] != ''){ 
                        $type = $row['type'];
                        if ($type == 1) {
                            $type_str = '订单备注';
                        } else if ($type == 2) {
                            $type_str = '后台支付备注';
                        } else if ($type == 3) {
                            $type_str = '完成备注';
                        } else if ($type == 4) {
                            $type_str = '退款备注';
                        } else {
                            $type_str = '未知备注';
                        }
                    ?>
					<tr>
						<td style="text-align:center;"><?php echo $row['operator'];?></td>
						<td style="text-align:center;"><?php echo $row['col_createtime'];?></td>
						<td style="text-align:center;"><?php echo $type_str;?></td>
						<td style="text-align:center;"><?php if ($row['content'] == '') {
                            echo '无';} else {echo htmlspecialchars($row['content']);}?></td>
					</tr>
					<?php }} ?>
				</tbody>
				
			</table>
		</div>

	</div>
	</div>
	<!--内容框架结束-->
</body>
</html>