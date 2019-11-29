<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST, DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head = 2;//头部文件  0基本设置,1提现记录,2代理商管理
require('../../../../weixinpl/auth_user.php');
$op = "";
require('../../../../weixinpl/common/utility_shop.php');

$shopMessage= new shopMessage_Utlity();
if (!empty($_GET["op"])) {
    $op = $configutil->splash_new($_GET["op"]);
    if (!empty($_GET["id"])) {
        $id = $configutil->splash_new($_GET["id"]);
    }
    $user_id = $configutil->splash_new($_GET["user_id"]);
    if ($op == "status") {
        $status = $configutil->splash_new($_GET["status"]);
        $isAgent = $configutil->splash_new($_GET["isAgent"]);
        if ($status == 1) {
            $parent_id = $configutil->splash_new($_GET["parent_id"]);
            $sql = "update weixin_qrs set status=" . $status . ",reason='' where id=" . $id;
            _mysql_query($sql);
            if ($isAgent == 1) {
				$Cstatus = 5;
                $sql = "update weixin_commonshop_applyagents set status=1 where user_id=" . $user_id;
                _mysql_query($sql);
                //插入申请代理金额日志 start
                $query = "select id from weixin_commonshop_agentfee_records where isvalid=true and type=3 and user_id=" . $user_id;
                $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
                $record_id = -1;
                while ($row = mysql_fetch_object($result)) {
                    $record_id = $row->id;
                    break;
                }

                $query1 = "select agent_price from weixin_commonshop_applyagents where isvalid=true and user_id=" . $user_id;
                $result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
                $agent_price = 0;
                while ($row1 = mysql_fetch_object($result1)) {
                    $agent_price = $row1->agent_price;
                    break;
                }
                $stringtime = date("Y-m-d H:i:s", time());
                $batchcode = strtotime($stringtime);
                $batchcode = $user_id . $batchcode;
                if ($record_id < 0) {
                    $sql = "insert into weixin_commonshop_agentfee_records(user_id,batchcode,price,detail,type,isvalid,createtime,after_inventory) values(" . $user_id . ",'" . $batchcode . "'," . $agent_price . ",'库存增加',3,true,now()," . $agent_price . ")";
                    _mysql_query($sql);
                    $sql = "update promoters set agent_inventory=" . $agent_price . " where user_id=" . $user_id . " and isvalid=true and customer_id=" . $customer_id;
                    _mysql_query($sql);
                }

                $sql = "update promoters set status=1,isAgent=1 where user_id=" . $user_id . " and isvalid=true and customer_id=" . $customer_id;
                _mysql_query($sql);
                /*
                //取消上下级关系 代理不需要上级
                $sql="update weixin_qr_scans set isvalid=false where  user_id=".$user_id." and customer_id=".$customer_id." and scene_id=".$parent_id;
                _mysql_query($sql);
                $sql="update weixin_users set parent_id=-1 where id=".$user_id;
                _mysql_query($sql);
                //取消上下级关系 代理不需要上级
                //插入申请代理金额日志 end

                //减少上级的粉丝数和推广员数
                $sql="update promoters set fans_count= fans_count-1,promoter_count=promoter_count-1 where isvalid=true and user_id=".$parent_id;
                _mysql_query($sql);
                 */
            }
            if ($isAgent == -1) {
				$Cstatus = 3;
				$reason = '';
				if( !empty( $_GET['reason'] ) ){
					$reason = $_GET['reason'];
				}
                $sql = "update weixin_commonshop_applyagents set status=-1,reason='".$reason."' where user_id=" . $user_id;
                _mysql_query($sql);
            }
			$shopMessage->ChangeRelationLog($customer_id,$user_id,'代理商',$Cstatus);	//生命周期
        }

		// $shopMessage->ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,2,$Cstatus);

    } else if ($op == "del") {
		$isAgent = $configutil->splash_new($_GET["isAgent"]);
		$parent_id = $configutil->splash_new($_GET["parent_id"]);
        $sql = "update promoters set isAgent=0,agent_inventory=0,agent_getmoney=0 where user_id=" . $user_id;
        _mysql_query($sql);
        $sql = "update weixin_commonshop_applyagents set isvalid=false where user_id=" . $user_id;
        _mysql_query($sql);//删掉代理商申请
        $sql = "update weixin_commonshop_agentfee_records set isvalid=false where user_id=" . $user_id;
        _mysql_query($sql);//消费记录
		if( $isAgent==1){
			// $shopMessage->ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,2,4);
			$shopMessage->ChangeRelationLog($customer_id,$user_id,'代理商',4);	//生命周期
		}

    }
}

$query = "select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=" . $customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $isOpenPublicWelfare = $row->isOpenPublicWelfare;
}
$exp_user_id = -1;


if (!empty($_GET["exp_user_id"])) {
    $exp_user_id = $configutil->splash_new($_GET["exp_user_id"]);
}
$search_status = -1;
if (!empty($_GET["search_status"])) {
    $search_status = $configutil->splash_new($_GET["search_status"]);
}
if (!empty($_POST["search_status"])) {
    $search_status = $configutil->splash_new($_POST["search_status"]);
}

$search_name = "";
if (!empty($_GET["search_name"])) {
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
if (!empty($_POST["search_name"])) {
    $search_name = $configutil->splash_new($_POST["search_name"]);
}

$search_user_id = "";
if (!empty($_GET["search_user_id"])) {
    $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
if (!empty($_POST["search_user_id"])) {
    $search_user_id = $configutil->splash_new($_POST["search_user_id"]);
}


$search_phone = "";
if (!empty($_GET["search_phone"])) {
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
if (!empty($_POST["search_phone"])) {
    $search_phone = $configutil->splash_new($_POST["search_phone"]);
}


//新增客户
$new_customer_count = 0;
//今日销售
$today_totalprice = 0;
//新增订单
$new_order_count = 0;
//新增推广员
$new_qr_count = 0;

$nowtime = time();
$year = date('Y', $nowtime);
$month = date('m', $nowtime);
$day = date('d', $nowtime);

$cur_date = date('Y-m-d');
$cur_date_begin = $cur_date." 00:00:00";
$cur_date_end = $cur_date." 23:59:59";

$query = "select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=" . $customer_id . " and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
//  echo $query;
while ($row = mysql_fetch_object($result)) {
    $new_order_count = $row->new_order_count;
    break;
}

$query = "select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=" . $customer_id . " and paytime>='".$cur_date_begin."' and paytime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
//  echo $query;
while ($row = mysql_fetch_object($result)) {
    $today_totalprice = $row->today_totalprice;
    break;
}
$today_totalprice = round($today_totalprice, 2);

$query = "select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=" . $customer_id . " and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
//  echo $query;
while ($row = mysql_fetch_object($result)) {
    $new_customer_count = $row->new_customer_count;
    break;
}

$query = "select count(1) as new_qr_count from promoters where status=1 and isvalid=true and customer_id=" . $customer_id . " and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
//  echo $query;
while ($row = mysql_fetch_object($result)) {
    $new_qr_count = $row->new_qr_count;
    break;
}

$op = "";
if (!empty($_GET["op"])) {
    $op = $configutil->splash_new($_GET["op"]);
    if ($op == "resetpwd") {
        $keyid = $configutil->splash_new($_GET["keyid"]);
        $user_id = $configutil->splash_new($_GET["user_id"]);
        $sql = "update promoters set pwd='888888' where user_id=" . $user_id;
        _mysql_query($sql);

    }
}

$exp_name = "推广员";
$query = "select exp_name,shop_card_id from weixin_commonshops where isvalid=true and customer_id=" . $customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $shop_card_id = $row->shop_card_id;
    $exp_name = $row->exp_name;
    break;
}


$is_distribution = 0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1 = "select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=" . $customer_id . " and c.filename='scdl' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
$dcount = mysql_num_rows($result1);
if ($dcount > 0) {
    $is_distribution = 1;
}
$is_supplierstr = 0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1 = "select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=" . $customer_id . " and c.filename='scgys' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
$dcount = mysql_num_rows($result1);
if ($dcount > 0) {
    $is_supplierstr = 1;
}

$begintime = "";
$endtime = "";
if (!empty($_GET["begintime"])) {
    $begintime = $configutil->splash_new($_GET["begintime"]);
}
if (!empty($_GET["endtime"])) {
    $endtime = $configutil->splash_new($_GET["endtime"]);
}
$pagecount = 20;
if(!empty($_GET["pagecount"])){
    $pagecount = intval($_GET["pagecount"]);
}
$pagenum = 1;
if (!empty($_GET["pagenum"])) {
    $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagecount;
$end = $pagecount;

?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
    <link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
    <link rel="stylesheet" type="text/css" href="../../Common/css/Mode/agent/set.css">
    <script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../../../js/tis.js"></script>
    <script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
    <script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
    <script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
    <script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
    <script type="text/javascript" src="../../../js/WdatePicker.js"></script>
    <!--script type="text/javascript" src="inputexcel.js"></script-->
	<script src="../../../common/js/floatBox.js"></script>
	<script>
		function inputtext(table,filename){

		/*导出自行安装订单筛选框*/
		var excelArray = [
							["0","代理商编号"],
							["1","姓名"],
							["2","个人申请信息"],
							["3","直接会员人数"],
							["4","代理级别"],
							["5","库存记录"],
							["6","进账记录"],
							["7","状态"],
							["8","个人总消费金额"],
							["9","申请时间"]
						 ];
		exportBox(excelArray);
		$(".floatbox").show();
		$(".floatinputs").click(function(){
			var str="";
			$("input[name='excel_field[]']:checkbox").each(function(){ 
				if($(this).is(':checked')){
					str += $(this).val()+","
				}
			})
			str = str.substring(0,str.length-1);	
			sstr = str.split(",");
			dataIntArr=sstr.map(function(data){  
				return +data;  
			}); 
		
		//构建excel内容	
		var table = $('#WSY_t1');
		var excel="<table>";
		//表头开始
		table.children('thead').children('tr').each(function(){
			excel += '<tr>';
			$(this).children('th').each(function(i){
				//清除第9列
				if(i!=11 && (dataIntArr.indexOf(i)!=-1)){
					excel += '<th>';
					excel += $(this).text();
					excel += '</th>';
				}
			})
			excel += '</tr>';
		})
		//表头结束，内容开始
		table.children('tbody').children('tr').each(function(){
			excel += '<tr>';
			$(this).children('td').each(function(i){
				if(i!=10 && (dataIntArr.indexOf(i)!=-1)){
					excel += '<td>';
					excel += $(this).html();
					excel += '</td>';
				}
			})
			excel += '</tr>';
		})
		excel += '</table>';
		excel=excel.replace(/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/g ,"$2").replace(/[\s]+/g," ").replace(/<i[^>]*class="([^"]*)"[^>]*>(.*?)<\/i>/g,"");
		//构建excel内容结束
		form = $("<form></form>")
		form.attr('action','inputexl.php')
		form.attr('method','post')
		input1 = $("<input type='hidden' name='excel' />")
		input1.attr('value',excel)
		input2 = $("<input type='text' name='filename' />")
		input2.attr('value','代理商')
		form.append(input1)
		form.append(input2)
		form.appendTo("body")
		form.css('display','none')
		form.submit()
			$(".floatbox").hide();
			$(".floatbox").remove();
		});		
	}
	</script>
    <style>

        tr {
            line-height: 22px;
        }

        .inventory {
            color: #06A7E1;
        }
		input.search_btn{
			margin-bottom:5px;
		}
    </style>
    <title>代理商管理</title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>

<!--内容框架-->
<div class="WSY_content">
    <!--列表内容大框-->
    <div class="WSY_columnbox">
        <!--列表头部切换开始-->
        <?php
        // include("../../../../weixinpl/back_newshops/Mode/agent/basic_head.php");
        include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/agent/basic_head.php");
        ?>
        <!--列表头部切换结束-->
        <div class="WSY_remind_main">
            <form class="search" id="search_form" style="margin-left:18px; margin-top: 18px;">
                状态：<select name="search_status" id="search_status" style="width:100px;">
                    <option value="-1">--请选择--</option>
                    <option value="2" <?php if ($search_status == 2){ ?>selected <?php } ?>>待审核</option>
                    <option value="1" <?php if ($search_status == 1){ ?>selected <?php } ?>>已确认</option>
                    <option value="-2" <?php if ($search_status == -2){ ?>selected <?php } ?>>已驳回/暂停</option>
                </select>
                &nbsp;代理商编号:<input type=text name="search_user_id" id="search_user_id"
                                   value="<?php echo $search_user_id; ?>" style="width:80px;"/>
                &nbsp;姓名:<input type=text name="search_name" id="search_name" value="<?php echo $search_name; ?>"
                                style="width:80px;"/>
                &nbsp;电话:<input type=text name="search_phone" id="search_phone" value="<?php echo $search_phone; ?>"
                                style="width:80px;"/>
                申请时间：
						<span class="WSY_generalize_dl08">
							<span id="searchtype3" class="display">
								<input type="text" class="input Wdate"
                                       style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"
                                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="begintime" name="AccTime_A"
                                       value="<?php echo $begintime; ?>" maxlength="21" id="K_1389249066532"/>
								-
							</span>
								<input type="text" class="input  Wdate"
                                       style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"
                                       onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_B"
                                       value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580"/>
						</span>&nbsp;
                每页记录数：<input type=text name="pagecount" id="pagecount" value="<?php echo $pagecount; ?>"  style="width:80px;border: 1px solid #ccc; border-radius: 2px;height: 24px;margin-left: 10px;padding-left: 8px;" />
                <input type="button" class="search_btn" onclick="searchForm();" value="搜 索">
                <input type="button" class="search_btn" value="导出金额详情+" onClick="exportRecord();" class="button"
                       style="cursor:hand">
                <!--	<input class="search_btn" value="导出本页信息" onclick="javascript:inputtext('WSY_t1','合作商')" style="cursor:hand" type="button"> -->
				<input class="search_btn" value="导出全部信息" onclick="exportExcel()" style="cursor:hand" type="button">
                <!--<input type="button" class="search_btn" value="导出推广员+" onClick="exportRecord();" class="button" style="cursor:hand">

                -->
            </form>
            <table width="97%" class="WSY_table" id="WSY_t1">
                <thead class="WSY_table_header">
                <th width="8%">代理商编号</th>
                <th width="15%">姓名</th>
				<th width="10%">个人申请信息</th>
                <!-- <th width="10%">推广二维码</th> -->
                <th width="8%">直接会员人数</th>
                <th width="11%">代理级别</th>
                <th width="8%">库存记录</th>
                <th width="8%">进账记录</th>
                <th width="8%">状态</th>
                <th width="8%">个人总消费金额</th>
                <th width="8%">申请时间</th>
                <th width="8%">操作</th>
                </thead>
                <tbody>

                <?php


                $weixin_fromuser = "";
                $query = "select
									distinct(wq.id) as id,
									ag.id as ag_id,
									qr_info_id,
									wq.reason as reason,
									ag.reason as agreason,
									wu.id as user_id,
									wu.name as name,
									wu.weixin_name as weixin_name,
									wu.phone as phone,
									wu.parent_id as parent_id ,
									imgurl_qr,
									ag.status as agstatus,
									ag.createtime as agcreatetime,
									wq.status,
									reward_score,
									reward_money,
									wq.createtime,
									promoter.isAgent,
									weixin_fromuser";
				$query3 = " from weixin_qrs wq inner join weixin_qr_infos wqi inner join weixin_users wu inner join weixin_commonshop_applyagents ag inner join promoters promoter  on wq.qr_info_id=wqi.id and promoter.status=1 and promoter.user_id=wu.id and promoter.user_id=ag.user_id and ag.isvalid=true and promoter.isvalid=true and wq.isvalid=true and wqi.isvalid=true and wqi.user_type=1 and  wqi.foreign_id = wu.id and wu.isvalid=true and  wq.isvalid=true and wq.type=1 and wq.customer_id=" . $customer_id . " and wu.customer_id=" . $customer_id;


                if ($exp_user_id > 0) {
                    $query3 = $query3 . " and wqi.foreign_id=" . $exp_user_id;
                }
                switch ($search_status) {
                    case 2:
                        $query3 = $query3 . " and ag.status=0";
                        break;
                    case 1:
                        $query3 = $query3 . " and ag.status=1";
                        break;
                    case -2:
                        $query3 = $query3 . " and ag.status=-1";
                        break;


                }

                if (!empty($search_name)) {
                    $query3 = $query3 . " and (wu.name like '%" . $search_name . "%' or wu.weixin_name like '%" . $search_name . "%')";
                }

                if (!empty($search_phone)) {
                    $query3 = $query3 . " and wu.phone like '%" . $search_phone . "%'";
                }

                if (!empty($search_user_id)) {
                    $query3 = $query3 . " and wu.id like '%" . $search_user_id . "%'";
                }

                if ($begintime != "") {
                    $query3 = $query3 . " and UNIX_TIMESTAMP(ag.createtime)>" . strtotime($begintime);
                }

                if ($endtime != "") {
                    $query3 = $query3 . " and UNIX_TIMESTAMP(ag.createtime)<" . strtotime($endtime);
                }
				// $query3 .= " GROUP BY wq.id ";		//防止出现多条数据
				$query3 .= " GROUP BY wqi.foreign_id ";		//防止出现多条数据
				$query .= $query3;

                /* 输出数量开始 */
                $query2  = "select count(rcount) as rcount_q2 from (select count(distinct(wq.id)) as rcount ". $query3.") as a";
                $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                /* $rcount_q2 = mysql_num_rows($result2); */
				$rcount_q2 = 0;
				while ($row2 = mysql_fetch_object($result2)) {
					$rcount_q2 = $row2->rcount_q2;
				}

                /* 输出数量结束 */
                $query = $query . " order by ag.createtime desc" . " limit " . $start . "," . $end;
                //$query = $query . " order by case when ag.status=0 then 1 when ag.status=1 then 2 when ag.status=-1 then 3 end,ag.createtime desc" . " limit " . $start . "," . $end;
                $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
                $rcount_q = mysql_num_rows($result);
                while ($row = mysql_fetch_object($result)) {
                    $weixin_fromuser = $row->weixin_fromuser;
                    $qr_info_id = $row->qr_info_id;
                    $user_id = $row->user_id;
                    $id = $row->id;
                    $reward_score = $row->reward_score;
                    $reward_money = $row->reward_money;
                    $isAgent = $row->isAgent;
                    $reward_money = round($reward_money, 2);
                    $reason = $row->reason;
                    $agreason = $row->agreason;

                    $ag_id = $row->ag_id;

                    $username = $row->name;
                    $weixin_name = $row->weixin_name;
                    $username = $username . "(" . $weixin_name . ")";
                    $userphone = $row->phone;
                    $imgurl_qr = $row->imgurl_qr;
                    $agcreatetime = $row->agcreatetime;


                    /*$rcount = 0;            //会员数
                    $query2 = "select count(1) as rcount from weixin_qr_scans wqs inner join weixin_users wu on wu.id = wqs.user_id  and wqs.isvalid=true and wu.isvalid=true and wqs.customer_id=" . $customer_id . " and scene_id=" . $user_id;
                    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                    while ($row2 = mysql_fetch_object($result2)) {
                        $rcount = $row2->rcount;
                    }*/
					$team_fans = 0 ;
					$team_prom = 0;

					$query2="select team_fans,team_prom from promoters where user_id=".$user_id." and isvalid=true";
					$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {

    					 //总的推广员数跟粉丝数
                         //----------------------begin

                         //重新统计粉丝数
                        $query_check_fans="select id from weixin_users w where isvalid=true and customer_id=".$customer_id." and match(gflag) against (',".$user_id.",')";

                        $result_check_fans = _mysql_query($query_check_fans) or die('Query failed5: ' . mysql_error());
                        $rcount_check_fans = mysql_num_rows($result_check_fans);

                        //重新统计推广员数
                        $query_check_prom="SELECT w.id FROM promoters p INNER JOIN weixin_users w ON w.id=p.user_id  WHERE  w.isvalid=true and p.isvalid=TRUE AND p.status=1 and w.customer_id=".$customer_id." AND match(w.gflag) against (',".$user_id.",')";
                        //echo $query;
                        $result_check_prom = _mysql_query($query_check_prom) or die('Query failed: ' . mysql_error());
                        $rcount_check_prom = mysql_num_rows($result_check_prom);

                        if( $rcount_check_fans != $row2->team_fans ){
                            //更新总的粉丝数
                            $query_update="update promoters set team_fans=".$rcount_check_fans." where isvalid=true  and customer_id=".$customer_id." and user_id=".$user_id."";
                            _mysql_query($query_update)or die('Query failed_up01'.mysql_error());
                        }

                        if( $query_check_prom != $row2->team_prom ){
                           //更新总的推广员数
                           $query_update="update promoters set team_prom=".$rcount_check_prom." where isvalid=true  and customer_id=".$customer_id." and user_id=".$user_id."";
                           _mysql_query($query_update)or die('Query failed_up01'.mysql_error());
                        }

    					$team_fans = $rcount_check_fans;
    				    $team_prom = $rcount_check_prom;
    					   break;
                    //-------------------------end
					}
                    $status = $row->status;
                    $agstatus = $row->agstatus;
                    $statusstr = "待审核";
                    switch ($agstatus) {
                        case 1:
                            $statusstr = "已确认";
                            break;
                        case -1:
                            $statusstr = "已驳回/暂停<br>".$agreason;
                            break;
                    }
                    $parent_name = "";
                    $query2 = "select parent_id,createtime,isAgent,agent_inventory,agent_getmoney from promoters where  status=1 and isvalid=true and user_id=" . $user_id;
                    // echo $query2;
                    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                    $parent_id = -1;
                    $isAgent = 0;
                    $agent_inventory = 0;
                    $agent_getmoney = 0;
                    while ($row2 = mysql_fetch_object($result2)) {
                        $parent_id = $row2->parent_id;
                        $createtime = $row2->createtime;
                        $isAgent = $row2->isAgent;
                        $agent_inventory = $row2->agent_inventory;//代理库存余额
                        $agent_getmoney = $row2->agent_getmoney;//售出得到的金额
                        break;
                    }
                   /* //推广员数量
                    $promoter_count = 0;
                   $query2 = "select count(distinct p.user_id) as promoter_count from promoters p inner join weixin_users  w on w.id=p.user_id where p.status=1 and p.isvalid=true  and w.isvalid=true and w.parent_id=" . $user_id . " and w.customer_id=" . $customer_id;
                    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                    while ($row2 = mysql_fetch_object($result2)) {
                        $promoter_count = $row2->promoter_count;
                        break;
                    }*/

                    //查找账户和支付宝

                    $query2 = "select account,account_type,bank_open,bank_name,email from weixin_card_members where isvalid=true and checked=true and user_id=" . $user_id;
                    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                    $account = "";
                    $account_type = "";
                    $bank_open = "";
                    $bank_name = "";
                    while ($row2 = mysql_fetch_object($result2)) {
                        $account_type = $row2->account_type;
                        if( $account_type==1 || $account_type==2 ){
                            $account = $row2->email;
                        }else{
                            $account = $row2->account;
                        }
                        $bank_open = $row2->bank_open;
                        $bank_name = $row2->bank_name;
                    }
                    $account_type_str = "";
                    switch ($account_type) {
                        case 1:
                            $account_type_str = "支付宝";
                            break;
                        case 2:
                            $account_type_str = "财付通";
                            break;
                        case 3:
                            $account_type_str = "银行账户";
                            break;
                    }

                    //查找推广员的会员卡号
                    $Membership_Card = -1;
                    $query_m = "SELECT id from weixin_card_members where isvalid=true and card_id=" . $shop_card_id . " and user_id=" . $user_id;
                    $result_m = _mysql_query($query_m) or die('Query failed: ' . mysql_error());
                    while ($row_m = mysql_fetch_object($result_m)) {
                        $Membership_Card = $row_m->id;
                        break;
                    }

                    //显示该推广员已经购买的商品总金额(已经付款的)
                    /* $query2 = "select sum(totalprice) as s_totalprice from weixin_commonshop_orders where isvalid=true and paystatus=1 and  user_id=" . $user_id;
                    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                    $s_totalprice = 0;;
                    while ($row2 = mysql_fetch_object($result2)) {
                        $s_totalprice = $row2->s_totalprice;
                    }

                    $s_totalprice = round($s_totalprice, 2); */
					$s_totalprice=0;
					$query2 = "SELECT total_money FROM my_total_money WHERE isvalid=true AND user_id=$user_id LIMIT 1";
					$result2 = _mysql_query($query2) or die('Query failed23: ' . mysql_error());
					while( $row2 = mysql_fetch_object($result2) ){
						$s_totalprice = $row2->total_money;
					}

					if($s_totalprice==''){
						$s_totalprice = 0;
					}else{
						$s_totalprice = sprintf("%.3f", $s_totalprice);
					}

                    $query2 = "select title,online_qq from weixin_commonshop_owners where isvalid=true and user_id=" . $user_id;
                    $mystore_title = "";
                    $mystore_qq = "";
                    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());

                    while ($row2 = mysql_fetch_object($result2)) {
                        $mystore_title = $row2->title;
                        $mystore_qq = $row2->online_qq;
                        break;
                    }

                    //代理进账出账费用
                    $query2 = "select id,batchcode,price,detail,type from weixin_commonshop_agentfee_records where isvalid=true and  user_id=" . $user_id;
                    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                    $price = 0;
                    $total_in_money = 0;
                    $total_out_money = 0;
                    while ($row2 = mysql_fetch_object($result2)) {
                        $price = $row2->price;
                        $type = $row2->type;
                        switch ($type) {
                            case 1:
                                $total_out_money = $total_out_money + $price;
                                break;
                            case 2:
                                $total_in_money = $total_in_money + $price;
                                break;
                        }
                    }
                    $query1 = "select agent_name,agent_discount,agent_price,user_name,user_phone from weixin_commonshop_applyagents where isvalid=true and user_id=" . $user_id;
                    $result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
                    $agent_price = 0;
                    $agent_discount = 0;
                    $agent_name = "代理商";
                    while ($row1 = mysql_fetch_object($result1)) {
                        $agent_price = $row1->agent_price;
                        $agent_discount = $row1->agent_discount;
                        $agent_name = $row1->agent_name;
						$user_name = $row1->user_name;
						$user_phone = $row1->user_phone;
                        break;
                    }
					
					/*判断使用临时二维码还是永久二维码 start*/
					if(!empty($imgurl_qr)){
						$imgurl_qr = $imgurl_qr;
					}else{
						$query2 = "select imgurl_qr from weixin_qr_scene where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id;
						$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
						while($row = mysql_fetch_object($result2)){
							$imgurl_qr = $row->imgurl_qr;
							break;
						}
					}
					/*判断使用临时二维码还是永久二维码 end*/
					/********************************************************/
					/* $query23 = "SELECT 
					wu.id as user_id,
					wu.name as name,
					wu.weixin_name as weixin_name,
					wu.phone as phone,
					ag.reason as agreason,
					ag.status as agstatus,
					ag.createtime as agcreatetime,
					ag.agent_name as agent_name,
					ag.agent_discount as agent_discount,
					ag.agent_price as agent_price,
					ag.user_name as user_name,
					ag.user_phone as user_phone,
					pr.agent_getmoney as agent_getmoney,
					pr.agent_inventory as agent_inventory,
					pr.team_fans as team_fans,
					pr.team_prom as team_prom,
					tm.total_money as total_money,
					cm.account_type as account_type,
					cm.account as account 
					FROM weixin_users wu inner join weixin_commonshop_applyagents ag inner join promoters pr inner join my_total_money tm inner join weixin_card_members cm 
					on tm.user_id=wu.id and tm.isvalid=true and cm.user_id=wu.id and cm.isvalid=true and pr.user_id=wu.id and pr.user_id=ag.user_id and pr.isvalid=true and wu.isvalid=true and ag.isvalid=1 and pr.status=1 and wu.customer_id=".$customer_id;
					echo $query23;
					$res = _mysql_query($query23);
					echo $res."<br/>"; */
					
					
                    ?>
                    <tr>
                        <td><?php echo $user_id; ?></td>
                        <td><a title="会员卡号:<?php echo $Membership_Card; ?>"
                                                        href="../../../card_member.php?card_id=<?php echo $shop_card_id; ?>&card_member_id=<?php echo $Membership_Card; ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>"><?php echo $username; ?></a>
                            <?php if (!empty($weixin_fromuser)) {
                                ?>
                                <a class="btn"
                                   href="../../../weixin_inter/send_to_msg.php?fromuserid=<?php echo $weixin_fromuser; ?>&customer_id=<?php echo passport_encrypt($customer_id) ?>"
                                   title="对话"><i class="icon-comment"></i></a>
                                <?php
                            } ?>
                            <br/>
                            电话：<?php echo $userphone; ?><br/>
                            收款类型:<?php echo $account_type_str; ?><br/>
                            收款账户:<?php echo $account; ?>
                            <?php if ($account_type == 3) { ?>
                                <br/>开户银行：<?php echo $bank_open; ?>
                                <br/>开户姓名：<?php echo $bank_name; ?>
                            <?php } ?>
                            <?php if (!empty($mystore_title)) { ?>
                                <br/>微店名称:<?php echo $mystore_title; ?><br/>
                                在线QQ:<?php echo $mystore_qq; ?>
                            <?php } ?>
                        </td>
						<td>姓名：<?php if(empty($user_name)){echo "/";}else{ echo $user_name;} ?><br/>电话：<?php if(empty($user_phone)){echo "/";}else{ echo $user_phone;} ?></td>
                        <!-- <td><a href="<?php echo $imgurl_qr; ?>" target="_blank"><img src="<?php echo $imgurl_qr; ?>" style="width:40px;height:40px;" /></a></td> -->
                        <td>
                            粉丝数:&nbsp;<a
                                href="../../Users/promoter/qrsell_detail_member.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&rcount=<?php echo $team_fans; ?>"><?php echo $team_fans; ?></a><br/>
                            推广员数:&nbsp;<a
                                href="../../Users/promoter/qrsell_detail.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&rcount=<?php echo $team_prom; ?>"><?php echo $team_prom; ?></a>
                        </td>
                        <td>代理级别:<?php echo $agent_name; ?><br/>
                            代理折扣:<?php echo $agent_discount; ?>%<br/>
                            代理金额:<?php echo $agent_price; ?>元
                        </td>
                        <td>
                            <a href="agentcost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=1"><span
                                    class="inventory"
                                    id="span_inventory_<?php echo $id; ?>"><?php echo round($agent_inventory,2); ?></span>元</a>
                        </td>
                        <td>
                            <a href="agentcost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=2"><?php echo round($agent_getmoney,2); ?>
                                元</a></td>
                        <td>
                            <?php echo $statusstr; ?><br/>
                             <?php if(!empty($reason) && $supplystatus==-1){ ?>
                                (<span style="font-size:12px;"><?php echo $reason; ?></span>)
                            <?php } ?>
                        </td>
                        <!--<td>
							 <a href="qrsell.php?exp_user_id=<?php echo $parent_id; ?>&customer_id=<?php echo $customer_id; ?>"><?php echo $parent_name; ?></a>
						   </td>-->
                        <td>
                            <a href="../../Users/promoter/customers.php?search_user_id=<?php echo $user_id; ?>"><?php echo round($s_totalprice,2); ?></a>
                        </td>
                        <td><?php echo $agcreatetime; ?></td>
                        <td>

                            <a href="agent.php?customer_id=<?php echo $customer_id_en; ?>&keyid=<?php echo $id; ?>&op=resetpwd&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>"
                               class="btn1" onclick="if(!confirm(&#39;重置后密码为：888888。继续？&#39;)){return false};"><img
                                    src="../../../common/images_V6.0/operating_icon/icon01.png" align="absmiddle"
                                    alt="重置密码" title="重置密码"></a>
                            <?php if ($isAgent == 1) { ?>
                                <a class="btn1"
                                   href="javascript:inventory_recharge(<?php echo $id; ?>,<?php echo $user_id; ?>);"
                                   title="充值">
                                    <img src="../../../common/images_V6.0/operating_icon/icon22.png" align="absmiddle"/>
                                </a>
                            <?php } ?>
                            <?php if ($status == 1) { ?>    <!--推广员情况下-->
                                <?php if ($isAgent != 1 and $agstatus != 1) { ?>
                                    <a class="btn1"
                                       href="agent.php?op=status&id=<?php echo $id; ?>&status=1&isAgent=1&user_id=<?php echo $user_id; ?>&parent_id=<?php echo $parent_id; ?>&pagenum=<?php echo $pagenum; ?>"
                                       title="通过">
                                        <img src="../../../common/images_V6.0/operating_icon/icon23.png"
                                             align="absmiddle"/>
                                    </a>

                                    <a class="btn1"
                                       href="javascript:showReason('agent.php?op=status&id=<?php echo $id; ?>&status=1&isAgent=-1&parent_id=<?php echo $parent_id; ?>&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>');"
                                       title="驳回/暂停">
                                        <img src="../../../common/images_V6.0/operating_icon/icon03.png"
                                             align="absmiddle"/>
                                    </a>
                                <?php } ?>
                            <?php } else if ($status == -1) { ?>    <!--粉丝情况下-->
                                <?php if ($isAgent != 1 and $agstatus != 1) { ?>
                                    <a class="btn1"
                                       href="agent.php?op=status&id=<?php echo $id; ?>&status=1&isAgent=1&user_id=<?php echo $user_id; ?>&parent_id=<?php echo $parent_id; ?>&pagenum=<?php echo $pagenum; ?>"
                                       title="通过">
                                        <img src="../../../common/images_V6.0/operating_icon/icon23.png"
                                             align="absmiddle"/>
                                    </a>

                                    <a class="btn1"
                                       href="javascript:showReason('../../Users/promoter/promoter.php?op=status&id=<?php echo $id; ?>&status=1&isAgent=-1&parent_id=<?php echo $parent_id; ?>&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>');"
                                       title="驳回/暂停">
                                        <img src="../../../common/images_V6.0/operating_icon/icon03.png"
                                             align="absmiddle"/>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                            <a href="agent.php?customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>&op=del&user_id=<?php echo $user_id; ?>&qr_info_id=<?php echo $qr_info_id; ?>&pagenum=<?php echo $pagenum; ?>&isAgent=<?php echo $isAgent; ?>&parent_id=<?php echo $parent_id; ?>"
                               class="btn1" onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};"><img
                                    src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle"
                                    alt="删除"></a>			
                            <a class="btn1" href="<?php echo $imgurl_qr; ?>" target="_blank">
                                <img src="../../../common/images_V6.0/operating_icon/icon09.png" align="absmiddle"
                                     alt="二维码" title="二维码" onMouseOver="toolTip('<img src=<?php echo $imgurl_qr; ?>>')"
                                     onMouseOut="toolTip()"/>
                            </a>
                        </td>

                    </tr>

                <?php } ?>

                </tbody>
            </table>
            <div class="blank20"></div>
            <div id="turn_page"></div>
            <!--翻页开始-->
            <div class="WSY_page">

            </div>
            <!--翻页结束-->
        </div>
    </div>
</div>

<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script>
    var customer_id = '<?php echo $customer_id_en ?>';
    var pagenum = <?php echo $pagenum ?>;
    var rcount_q2 = <?php echo $rcount_q2 ?>;
    var end = <?php echo $end ?>;
    var count = Math.ceil(rcount_q2 / end);//总页数

    //pageCount：总页数
    //current：当前页
	//导出
	function exportExcel(){
		var search_user_id = document.getElementById("search_user_id").value;
		var search_status = document.getElementById("search_status").value;
		var search_name = document.getElementById("search_name").value;
		var search_phone = document.getElementById("search_phone").value;
		var begintime = document.getElementById("begintime").value;
		var endtime = document.getElementById("endtime").value;
		var url='/weixin/plat/app/index.php/Excel/agents_excel/customer_id/<?php echo passport_decrypt($customer_id); ?>';
		
		if( search_user_id != '' && search_user_id > 0 ){
			url += '/search_user_id/'+search_user_id;
		}
		if( search_name != '' ){
			url += '/search_name/'+search_name;
		}
		if( search_phone != '' ){
			url += '/search_phone/'+search_phone;
		}
		if (begintime != "") {
			url += '/begintime/'+begintime;
		}
		if (endtime != "") {
			url += '/endtime/'+endtime;
		}
		if( search_status > 0 ){
			url += '/search_status/'+search_status;
		}
		
		document.location = url;
	}
    //pageCount：总页数
    //current：当前页

    $(".WSY_page").createPage({
        pageCount: count,
        current: pagenum,
        backFn: function (p) {
            var search_user_id = document.getElementById("search_user_id").value;
            var search_status = document.getElementById("search_status").value;
            var search_name = document.getElementById("search_name").value;
            var search_phone = document.getElementById("search_phone").value;
			var begintime = document.getElementById("begintime").value;
			var endtime = document.getElementById("endtime").value;

			var url = "agent.php?pagenum=" + p +"&pagecount="+end+ "&search_status=" + search_status + "&search_name=" + search_name + "&search_phone=" + search_phone + "&search_user_id=" + search_user_id + "&customer_id=<?php echo $customer_id_en;?>";

			if (begintime != "") {
				url = url + "&begintime=" + begintime;
			}
			if (endtime != "") {
				url = url + "&endtime=" + endtime;
			}
			document.location = url;
            // document.location = "agent.php?pagenum=" + p +"&pagecount="+end+ "&search_status=" + search_status + "&search_name=" + search_name + "&search_phone=" + search_phone + "&search_user_id=" + search_user_id + "&customer_id=<?php echo $customer_id_en;?>";
        }
    });

    var pagenum = <?php echo $pagenum ?>;
    var page = count;
    function jumppage() {
        var a = parseInt($("#WSY_jump_page").val());
        if ((a < 1) || (a == pagenum) || (a > page) || isNaN(a)) {
            return false;
        } else {
            var search_user_id = document.getElementById("search_user_id").value;
            var search_status = document.getElementById("search_status").value;
            var search_name = document.getElementById("search_name").value;
            var search_phone = document.getElementById("search_phone").value;
			var begintime = document.getElementById("begintime").value;
			var endtime = document.getElementById("endtime").value;

			var url = "agent.php?pagenum=" + a +"&pagecount="+end+ "&search_status=" + search_status + "&search_name=" + search_name + "&search_phone=" + search_phone + "&search_user_id=" + search_user_id + "&customer_id=<?php echo $customer_id_en;?>";

			if (begintime != "") {
				url = url + "&begintime=" + begintime;
			}
			if (endtime != "") {
				url = url + "&endtime=" + endtime;
			}
			document.location = url;
            // document.location = "agent.php?pagenum=" + a +"&pagecount="+end+ "&search_status=" + search_status + "&search_name=" + search_name + "&search_phone=" + search_phone + "&search_user_id=" + search_user_id + "&customer_id=<?php echo $customer_id_en;?>";
        }
    }

    function exportRecord() {
        var begintime = document.getElementById("begintime").value;
        var endtime = document.getElementById("endtime").value;
        var url = '../../../../weixin/plat/app/index.php/Excel/agent_excel/customer_id/<?php echo $customer_id ?>/';
        if (begintime != "") {
            url = url + 'begintime/' + begintime + '/';
        }
        if (endtime != "") {
            url = url + 'endtime/' + endtime + '/';
        }

	/*导出自行安装订单筛选框*/
	var excelArray = [
						["name","代理商名称"],
						["user_id","代理商编号"],
						["batchcode","订单号"],
						["price","进账记录"],
						["price1","成本"],
						["price2","利润"],
						["after_getmoney","每次结算余额"],
						["createtime","时间"],
						["remark","消费说明"]
					 ];
	exportBox(excelArray);
	$(".floatbox").show();

	$(".floatinputs").click(function(){
		var str="";
		$("input[name='excel_field[]']:checkbox").each(function(){
            if($(this).is(':checked')){
                str += $(this).val()+","
            }
        })
        str = str.substring(0,str.length-1);
		if(str != ""){
			url = url + "/excel_fields/" + str + '/';
		}

        goExcel(url, 1, '//<?php echo $http_host;?>/weixinpl/');

		$(".floatbox").hide();
		$(".floatbox").remove();
	});
    }
    function agentexcel(star, end) {
        var begintime = document.getElementById("begintime").value;
        var endtime = document.getElementById("endtime").value;
        var url = '../../../../weixin/plat/app/index.php/Excel/agentname_excel/customer_id/<?php echo $customer_id ?>/';
        if (begintime != "") {
            url = url + 'begintime/' + begintime + '/';
        }
        if (endtime != "") {
            url = url + 'endtime/' + endtime + '/';
        }
        goExcel(url, 1, '//<?php echo $http_host;?>/weixinpl/');
    }

    function searchForm() {
        var search_user_id = document.getElementById("search_user_id").value;
        var search_status = document.getElementById("search_status").value;
        var search_name = document.getElementById("search_name").value;
        var search_phone = document.getElementById("search_phone").value;
        var begintime = document.getElementById("begintime").value;
        var endtime = document.getElementById("endtime").value;
        var pagecount = document.getElementById("pagecount").value;
        var url = "agent.php?&issearch=1&pagenum=1&search_status=" + search_status +"&pagecount="+pagecount+ "&search_name=" + search_name + "&search_phone=" + search_phone + "&search_user_id=" + search_user_id + "&customer_id=<?php echo $customer_id_en;?>";

        if (begintime != "") {
            url = url + "&begintime=" + begintime;
        }
        if (endtime != "") {
            url = url + "&endtime=" + endtime;
        }
        document.location = url;
    }

    function showReason(url) {

        var str = prompt("请输入驳回/暂停理由", "您不符合代理商条件，请联系客服");
        if (str) {
            document.location = url + "&reason=" + str;
        }
    }

    function inventory_recharge(id, user_id) {	//充值
        var str_m = prompt("确认充值吗?请输入充值金额", "");
        if (str_m == null) {
            //alert("不能为空");
            return;
        } else if (str_m == "") {
            alert("不能为空");
            return;
        }
        //isNum = /^[0-9]*$/;
        //isNum =/^\d+(\.\d{2})?$/; //可以输入正负数 不包括0
        isNum = /^-?\d+\.?\d{0,2}$/; //可以输入正负数 包括0
        if (isNum.test(str_m)) {
            if (str_m != 0) {
                url = 'agent_inventory_recharge.php?callback=jsonpCallback_inrecharge&user_id=' + user_id + '&customer_id=' + customer_id + "&money=" + str_m + '&id=' + id;
                console.log(url);
                $.jsonp({
                    url: url,
                    callbackParameter: 'jsonpCallback_inrecharge'
                });
            } else {
                alert("不能为0！");
            }
        } else {
            alert("请输入两位小数");
            return;
        }

    }
    function jsonpCallback_inrecharge(results) {
		if(results[0].errcode == 0){
			var agent_inventory = results[0].agent_inventory;
			var id = results[0].id;
			console.log(agent_inventory);
			document.getElementById("span_inventory_" + id).innerHTML = "<span class='inventory' id='span_inventory_" + id + "'>" + agent_inventory + "</span>";
		}else{
			alert(decodeURIComponent(results[0].errmsg));
			return false;
		}

    }

</script>

<script type="text/javascript" language=JavaScript charset="UTF-8">

document.onkeydown=function(event)   //ENTER键盘按键触发事件
{
    var e = event || window.event || arguments.callee.caller.arguments[0];
    if (e && e.keyCode==13) 
    {
        searchForm();
    }
}
</script>

<?php mysql_close($link); ?>

<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>