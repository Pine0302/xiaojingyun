<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

//$customer_id =$configutil->splash_new($_POST["customer_id"]);  //前面引入的文件中已经获取了
$id =$configutil->splash_new($_POST["id"]);
$tcount=0;

switch($id){
    case 1:
        //分销商数
        $query= "select count(1) as tcount from promoters where isvalid=true and customer_id=".$customer_id." and isAgent>-1";
        //echo $query;
        break;
    case 2:
        //待审核分销商数
        $query= "select count(1) as tcount from promoters where isvalid=true and customer_id=".$customer_id." and status=0";
        break;
    case 3:
        //出售中的商品数
        $query= "select count(1) as tcount from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and isout=0";
        break;
    case 4:
        //仓库中商品数
        $query= "select count(1) as tcount from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and isout=1";
        break;
    case 5:
        //已售罄的商品数
        $tcount=0;
        $tcount1=0;
        $tcount2=0;
        $query= "SELECT count(distinct p.id) as tcount1 FROM weixin_commonshop_products p join weixin_commonshop_product_prices pr on p.customer_id=".$customer_id." and p.isvalid=1 and p.isout=0 and p.id=pr.product_id and pr.storenum<1 and p.storenum<1";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        $query= "SELECT count(1) as tcount2 FROM weixin_commonshop_products where isvalid=1 and isout=0 and customer_id=".$customer_id." and storenum<1 and (SELECT count(1) FROM weixin_commonshop_product_prices where product_id=weixin_commonshop_products.id)<1";
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }
        $tcount=$tcount1+$tcount2;
        echo $tcount;
        return;
        break;
    case 6:
        //已支出佣金
        $query= "select sum(reward) as tcount from weixin_commonshop_order_promoters where paytype in(1,3) and isvalid=true and customer_id=".$customer_id;
        break;
    case 7:
        //待提现佣金笔数
        //$query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and sendstatus=2 and paystatus=1";
        $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and sendstatus=2 and paystatus=1";
        break;
    case 8:
        //待付款订单数
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=0";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and paystatus=0";
        break;
    case 9:
        //待发货订单数
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=1 and sendstatus=0";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and paystatus=1 and sendstatus=0";
        break;
    case 10:
        //库存提醒
        $tcount=0;
        $tcount1=0;
        $tcount2=0;
        $stock_remind=0;
        $query="select stock_remind from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
        $result = _mysql_query($query) or die('Query failed1: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $stock_remind = $row->stock_remind;
        }
        $query= "select count(1) as tcount1 from weixin_commonshop_products where isvalid=true and storenum<=".$stock_remind." and isout=0 and customer_id=".$customer_id;
        $result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }

        $query="SELECT count(distinct wcpp.product_id) as tcount2 FROM weixin_commonshop_products wcp inner join weixin_commonshop_product_prices wcpp on wcp.isvalid=true and wcp.isout=0 and wcp.customer_id=".$customer_id." and wcp.id=wcpp.product_id and wcp.storenum>".$stock_remind." and wcpp.storenum<".$stock_remind;
        $result = _mysql_query($query) or die('Query failed3: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }


    /*  $query= "select id,propertyids from weixin_commonshop_products where isvalid=true and isout=0 and storenum>".$stock_remind." and customer_id=".$customer_id;
        $result = _mysql_query($query) or die('Query failed3: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $p_id = $row->id;
            $propertyids = $row->propertyids;
            if(!empty($propertyids)){
                $query1="SELECT count(distinct product_id) as tcount2 FROM weixin_commonshop_product_prices WHERE storenum<".$stock_remind." and product_id=".$p_id;
                //echo $query1;
                $result1 = _mysql_query($query1) or die('Query failed4: ' . mysql_error());
                while ($row1 = mysql_fetch_object($result1)) {
                    $tcount2 = $row1->tcount2;
                }
            }
        }    */
        $tcount=$tcount1+$tcount2;
        echo $tcount;
        return;
        break;
    case 11:
        //退货订单数
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=1 and sendstatus in (3,5)";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and paystatus=1 and sendstatus in (3,5)";
        break;
    case 12:
        //已完成订单数
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and paystatus=1 and status=1";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and paystatus=1 and status=1";
        break;
    case 13:
        //当天总订单数
        $begintime=date("Y-m-d");
        $endtime=date("Y-m-d",strtotime("+1 day"));
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        break;
    case 14:
        //当天总消费金额
        $tcount=0;
        $begintime=date("Y-m-d");
        $endtime=date("Y-m-d",strtotime("+1 day"));

        $query= "select IFNULL(sum(totalprice),0) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id."  and paystatus=1 and paytime>='".$begintime."' and paytime<'".$endtime."'";
        //echo $query;
        break;
    case 15:
        //本月订单
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        $beginThismonth_t = date("Y-m-d H:i:s",$beginThismonth);
        $endThismonth_t = date("Y-m-d H:i:s",$endThismonth);

        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$beginThismonth_t."' and createtime<'".$endThismonth_t."'";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$beginThismonth_t."' and createtime<'".$endThismonth_t."'";     
        
        break;
    case 16:
        //本月总消费金额
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        $beginThismonth_t = date("Y-m-d H:i:s",$beginThismonth);
        $endThismonth_t = date("Y-m-d H:i:s",$endThismonth);
        $query= "select IFNULL(sum(totalprice),0) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id."  and paystatus=1 and paytime>='".$beginThismonth_t."' and paytime<'".$endThismonth_t."'";
//echo  $query;return;
        break;
    case 17:
        //当天订单增幅
        $tcount1=0;
        $tcount2=0;
        $tcount=0;
        $begintime=date("Y-m-d");   //今天时间开始
        $endtime=date("Y-m-d",strtotime("+1 day")); //今天时间结束
        $begintime1=date("Y-m-d",strtotime("-1 day"));  //昨天时间开始
        //今天
        $query= "select count(distinct batchcode) as tcount1 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        // $query = "select count(1) as tcount1 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        //echo "11111".$query."</br>";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        //昨天
        $query= "select count(distinct batchcode) as tcount2 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime1."' and createtime<'".$begintime."'";

        // $query = "select count(1) as tcount2 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime1."' and createtime<'".$begintime."'";
        
        //echo "22".$query."</br>";
        //return;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }
        $tcount=$tcount1-$tcount2;
        if($tcount2==0){
            $tcount2="--";
            echo $tcount2;
            return;
        }
        $amplitude=$tcount/$tcount2;
        $amplitude=round($amplitude,4);
        echo $amplitude;
        return;
        break;
    case 18:
        //当天消费增幅
        $tcount1=0;
        $tcount2=0;
        $tcount=0;
        $begintime=date("Y-m-d");   //今天时间开始
        $endtime=date("Y-m-d",strtotime("+1 day")); //今天时间结束
        $begintime1=date("Y-m-d",strtotime("-1 day"));  //昨天时间开始
        //今天
        $query= "select sum(totalprice) as tcount1 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id."  and paystatus=1 and paytime>='".$begintime."' and paytime<'".$endtime."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        //昨天
        $query= "select sum(totalprice) as tcount2 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id."  and paystatus=1 and paytime>'".$begintime1."' and paytime<='".$begintime."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }

        $tcount=$tcount1-$tcount2;
        if($tcount2==0){
            $tcount2="--";
            echo $tcount2;
            return;
        }
        $amplitude=$tcount/$tcount2;
        $amplitude=round($amplitude,4);
        echo $amplitude;
        return;
        break;
    case 19:
        //本月订单增幅
        $tcount1=0;
        $tcount2=0;
        $tcount=0;
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));//当月开始
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));//当月结束

        $beginThismonth_t = date("Y-m-d H:i:s",$beginThismonth);
        $endThismonth_t = date("Y-m-d H:i:s",$endThismonth);


        $beginLastmonth = date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y')));//上个月开始
        $beginLastmonth_t = date("Y-m-d H:i:s",$beginLastmonth);
        
        _file_put_contents ( "ljy111.txt", "=====beginThismonth==2==".$beginThismonth. "\r\n", FILE_APPEND );
        _file_put_contents ( "ljy111.txt", "=====endThismonth==2==".$endThismonth. "\r\n", FILE_APPEND );
        _file_put_contents ( "ljy111.txt", "=====beginLastmonth==2==".$beginLastmonth. "\r\n", FILE_APPEND );
        //当月
        //$query= "select count(distinct batchcode) as tcount1 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$beginThismonth_t."' and createtime<'".$endThismonth_t."'";

        $query = "select count(1) as tcount1 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$beginThismonth_t."' and createtime<'".$endThismonth_t."'";
        

        _file_put_contents ( "ljy111.txt", "=====query==2==".$query. "\r\n", FILE_APPEND );
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        _file_put_contents ( "ljy111.txt", "=====tcount1==1==".$tcount1. "\r\n", FILE_APPEND );
        //上个月
        //$query= "select count(distinct batchcode) as tcount2 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$beginLastmonth_t."' and createtime<'".$beginThismonth_t."'";

        $query = "select count(1) as tcount2 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$beginLastmonth_t."' and createtime<'".$beginThismonth_t."'";

        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }

        $tcount=$tcount1-$tcount2;
        if($tcount2==0){
            $tcount2="--";
            echo $tcount2;
            return;
        }
        _file_put_contents ( "ljy111.txt", "=====tcount==2==".$tcount. "\r\n", FILE_APPEND );
        _file_put_contents ( "ljy111.txt", "=====tcount2==2==".$tcount2. "\r\n", FILE_APPEND );
        $amplitude=$tcount/$tcount2;
        $amplitude=round($amplitude,4);
        echo $amplitude;
        return;
        break;
    case 20:
        //本月消费增幅
        $tcount1=0;
        $tcount2=0;
        $tcount=0;
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));//当月开始
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));//当月结束

        $beginThismonth_t = date("Y-m-d H:i:s",$beginThismonth);
        $endThismonth_t = date("Y-m-d H:i:s",$endThismonth);

        $beginLastmonth = date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y')));//上个月开始
        $beginLastmonth_t = date("Y-m-d H:i:s",$beginLastmonth);
        //当月
        $query= "select sum(totalprice) as tcount1 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id."  and paystatus=1 and paytime>='".$beginThismonth_t."' and paytime<'".$endThismonth_t."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        //上个月
        $query= "select sum(totalprice) as tcount2 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id."  and paystatus=1 and paytime>='".$beginLastmonth_t."' and paytime<'".$beginThismonth_t."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }
        $tcount=$tcount1-$tcount2;
        if($tcount2==0){
            $tcount2="--";
            echo $tcount2;
            return;
        }
        $amplitude=$tcount/$tcount2;
        $amplitude=round($amplitude,4);
        echo $amplitude;
        return;
        break;
    case 21:
        $tcount1=0;
        $tcount2=0;
        $tcount3=0;
        $tcount4=0;
        $tcount5=0;
        $tcount6=0;
        $tcount7=0;
        $ome=date("Y-m-d"); //今天时间开始
        $endtime=date("Y-m-d",strtotime("+1 day")); //今天时间结束
        $two=date("Y-m-d",strtotime("-1 day")); //昨天时间开始
        $there=date("Y-m-d",strtotime("-2 day"));
        $four=date("Y-m-d",strtotime("-3 day"));
        $five=date("Y-m-d",strtotime("-4 day"));
        $six=date("Y-m-d",strtotime("-5 day"));
        $seven=date("Y-m-d",strtotime("-6 day"));
        $eight=date("Y-m-d",strtotime("-7 day"));

        //今天
        $query= "select count(distinct batchcode) as tcount1 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$ome."' and createtime<'".$endtime."'";
        // $query = "select count(1) as tcount1 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$ome."' and createtime<'".$endtime."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        //昨天
        $query= "select count(distinct batchcode) as tcount2 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$two."' and createtime<'".$ome."'";
        // $query = "select count(1) as tcount2 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$two."' and createtime<'".$ome."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }
        //前天
        $query= "select count(distinct batchcode) as tcount3 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$there."' and createtime<'".$two."'";
        // $query = "select count(1) as tcount3 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$there."' and createtime<'".$two."'";

        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount3 = $row->tcount3;
        }
        //大前天
        $query= "select count(distinct batchcode) as tcount4 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$four."' and createtime<'".$there."'";
        // $query = "select count(1) as tcount4 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$four."' and createtime<'".$there."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount4 = $row->tcount4;
        }
        //大大前天
        $query= "select count(distinct batchcode) as tcount5 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$five."' and createtime<'".$four."'";
        // $query = "select count(1) as tcount5 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$five."' and createtime<'".$four."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount5 = $row->tcount5;
        }
        //大大大前天
        $query= "select count(distinct batchcode) as tcount6 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$six."' and createtime<'".$five."'";
        // $query = "select count(1) as tcount6 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$six."' and createtime<'".$five."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount6 = $row->tcount6;
        }
        //大大大大前天
        $query= "select count(distinct batchcode) as tcount7 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$eight."' and createtime<'".$seven."'";
        // $query = "select count(1) as tcount7 from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$seven."' and createtime<'".$six."'";

        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount7 = $row->tcount7;
        }
        $tcount=array();
        array_push($tcount,$tcount7,$tcount6,$tcount5,$tcount4,$tcount3,$tcount2,$tcount1);
        //$tcount=$tcount1.",".$tcount2.",".$tcount3.",".$tcount4.",".$tcount5.",".$tcount6.",".$tcount7;
        echo json_encode($tcount);
        return;
        break;
    case 22:
        //当天总订单数
        $endtime=date("Y-m-d");
        $begintime=date("Y-m-d",strtotime("-1 day"));
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        break;
    case 23:
        //今日付款订单数（笔）
        $begintime=date("Y-m-d");
        $endtime=date("Y-m-d",strtotime("+1 day"));
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        break;
    case 24://昨日付款订单数（笔）
        $endtime=date("Y-m-d");
        $begintime=date("Y-m-d",strtotime("-1 day"));
        $query= "select count(distinct batchcode) as tcount from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        // $query = "select count(1) as tcount from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        break;
    case 25://付款订单笔数趋势图
        $tcount1=0;
        $tcount2=0;
        $tcount3=0;
        $tcount4=0;
        $tcount5=0;
        $tcount6=0;
        $tcount7=0;
        $ome=date("Y-m-d"); //今天时间开始
        $endtime=date("Y-m-d",strtotime("+1 day")); //今天时间结束
        $two=date("Y-m-d",strtotime("-1 day")); //昨天时间开始
        $there=date("Y-m-d",strtotime("-2 day"));
        $four=date("Y-m-d",strtotime("-3 day"));
        $five=date("Y-m-d",strtotime("-4 day"));
        $six=date("Y-m-d",strtotime("-5 day"));
        $seven=date("Y-m-d",strtotime("-6 day"));
        $eight=date("Y-m-d",strtotime("-7 day"));

        //今天
        $query= "select count(distinct batchcode) as tcount1 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$ome."' and createtime<'".$endtime."'";

        // $query = "select count(1) as tcount1 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$ome."' and createtime<'".$endtime."'";       
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        //昨天
       $query= "select count(distinct batchcode) as tcount2 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$two."' and createtime<'".$ome."'";
        // $query = "select count(1) as tcount2 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$two."' and createtime<'".$ome."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }
        //前天
        $query= "select count(distinct batchcode) as tcount3 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$there."' and createtime<'".$two."'";
        // $query = "select count(1) as tcount3 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$there."' and createtime<'".$two."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount3 = $row->tcount3;
        }
        //大前天
        $query= "select count(distinct batchcode) as tcount4 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$four."' and createtime<'".$there."'";
        // $query = "select count(1) as tcount4 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$four."' and createtime<'".$there."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount4 = $row->tcount4;
        }
        //大大前天
        $query= "select count(distinct batchcode) as tcount5 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$five."' and createtime<'".$four."'";
        // $query = "select count(1) as tcount5 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$five."' and createtime<'".$four."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount5 = $row->tcount5;
        }
        //大大大前天
        $query= "select count(distinct batchcode) as tcount6 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$six."' and createtime<'".$five."'";
        // $query = "select count(1) as tcount6 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$six."' and createtime<'".$five."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount6 = $row->tcount6;
        }
        //大大大大前天
       $query= "select count(distinct batchcode) as tcount7 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$eight."' and createtime<'".$seven."'";
        // $query = "select count(1) as tcount7 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$seven."' and createtime<'".$six."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount7 = $row->tcount7;
        }
        $tcount=array();
        array_push($tcount,$tcount7,$tcount6,$tcount5,$tcount4,$tcount3,$tcount2,$tcount1);
        echo json_encode($tcount);
        return;
        break;
    case 26://订单金额统计
        $tcount1=0;
        $tcount2=0;
        $tcount3=0;
        $tcount4=0;
        $tcount5=0;
        $tcount6=0;
        $tcount7=0;
        $ome=date("Y-m-d"); //今天时间开始
        $endtime=date("Y-m-d",strtotime("+1 day")); //今天时间结束
        $two=date("Y-m-d",strtotime("-1 day")); //昨天时间开始
        $there=date("Y-m-d",strtotime("-2 day"));
        $four=date("Y-m-d",strtotime("-3 day"));
        $five=date("Y-m-d",strtotime("-4 day"));
        $six=date("Y-m-d",strtotime("-5 day"));
        $seven=date("Y-m-d",strtotime("-6 day"));
        $eight=date("Y-m-d",strtotime("-7 day"));

        /*所有订单，包括未支付的，开始*/
        //今天
        $query= "select IFNULL(sum(totalprice),0) as tcount1 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$ome."' and createtime<'".$endtime."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        //昨天
        $query= "select IFNULL(sum(totalprice),0) as tcount2 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$two."' and createtime<'".$ome."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }
        //前天
        $query= "select IFNULL(sum(totalprice),0) as tcount3 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$there."' and createtime<'".$two."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount3 = $row->tcount3;
        }
        //大前天
        $query= "select IFNULL(sum(totalprice),0) as tcount4 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$four."' and createtime<'".$there."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount4 = $row->tcount4;
        }
        //大大前天
        $query= "select IFNULL(sum(totalprice),0) as tcount5 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$five."' and createtime<'".$four."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount5 = $row->tcount5;
        }
        //大大大前天
        $query= "select IFNULL(sum(totalprice),0) as tcount6 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$six."' and createtime<'".$five."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount6 = $row->tcount6;
        }
        //大大大大前天
        $query= "select IFNULL(sum(totalprice),0) as tcount7 from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$eight."' and createtime<'".$seven."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount7 = $row->tcount7;
        }
        $tcount_1=array();
        array_push($tcount_1,$tcount7,$tcount6,$tcount5,$tcount4,$tcount3,$tcount2,$tcount1);
        /*所有订单，包括未支付的，结束*/

        /*支付了的订单，开始*/
        // $query= "select IFNULL(sum(totalprice),0) as tcount1 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and UNIX_TIMESTAMP(createtime)>=".strtotime($ome)." and UNIX_TIMESTAMP(createtime)<".strtotime($endtime);
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        // while ($row = mysql_fetch_object($result)) {
            // $tcount1 = $row->tcount1;
        // }
       
        $query = "select IFNULL(sum(NoExpPrice),0) as tcount1,
                       IFNULL(sum(pay_currency),0) as tpay_currency
                  from weixin_commonshop_order_prices
                 where isvalid= 1
                   and paystatus=1
                   and batchcode IN(
                        select batchcode
                          from weixin_commonshop_orders
                         where customer_id= ".$customer_id."
                           and isvalid= 1
                           and sendstatus!=4
                           and sendstatus!=6
                           and paystatus=1
                           and paytime>= '".$ome."'
                           and paytime< '".$endtime."' group by batchcode)";

        // $query = "select IFNULL(sum(NoExpPrice),0) as tcount1,IFNULL(sum(pay_currency),0) as tpay_currency from weixin_commonshop_order_prices where customer_id= ".$customer_id." and isvalid= 1 and paystatus=1 and sendstatus!=4 and sendstatus!=6 and paytime>= '".$ome."' and paytime< '".$endtime."'";     


        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1        = $row->tcount1;
            $pay_currency   = $row->pay_currency;

            $tcount1 -= $pay_currency;
        }
        
        //昨天
        // $query= "select IFNULL(sum(totalprice),0) as tcount2 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and UNIX_TIMESTAMP(createtime)>=".strtotime($two)." and UNIX_TIMESTAMP(createtime)<".strtotime($ome);
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        // while ($row = mysql_fetch_object($result)) {
            // $tcount2 = $row->tcount2;
        // }
        $query = "select IFNULL(sum(NoExpPrice),0) as tcount2,
                       IFNULL(sum(pay_currency),0) as tpay_currency
                  from weixin_commonshop_order_prices
                 where isvalid= 1
                   and paystatus=1
                   and batchcode IN(
                        select batchcode
                          from weixin_commonshop_orders
                         where customer_id= ".$customer_id."
                           and isvalid= 1
                           and sendstatus!=4
                           and sendstatus!=6
                           and paystatus=1
                           and paytime>= '".$two."'
                           and paytime< '".$ome."' group by batchcode)";
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());

        // $query = "select IFNULL(sum(NoExpPrice),0) as tcount2,IFNULL(sum(pay_currency),0) as tpay_currency from weixin_commonshop_order_prices where customer_id= ".$customer_id." and isvalid= 1 and paystatus=1 and sendstatus!=4 and sendstatus!=6 and paytime>= '".$two."' and paytime< '".$ome."'";

        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2        = $row->tcount2;
            $pay_currency   = $row->pay_currency;

            $tcount2 -= $pay_currency;
        }
        //前天
        // $query= "select IFNULL(sum(totalprice),0) as tcount3 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and UNIX_TIMESTAMP(createtime)>=".strtotime($there)." and UNIX_TIMESTAMP(createtime)<".strtotime($two);
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        // while ($row = mysql_fetch_object($result)) {
            // $tcount3 = $row->tcount3;
        // }
        
        $query = "select IFNULL(sum(NoExpPrice),0) as tcount3,
                       IFNULL(sum(pay_currency),0) as tpay_currency
                  from weixin_commonshop_order_prices
                 where isvalid= 1
                   and paystatus=1
                   and batchcode IN(
                        select batchcode
                          from weixin_commonshop_orders
                         where customer_id= ".$customer_id."
                           and isvalid= 1
                           and sendstatus!=4
                           and sendstatus!=6
                           and paystatus=1
                           and paytime>= '".$there."'
                           and paytime< '".$two."' group by batchcode)";

        // $query = "select IFNULL(sum(NoExpPrice),0) as tcount3,IFNULL(sum(pay_currency),0) as tpay_currency from weixin_commonshop_order_prices where customer_id= ".$customer_id." and isvalid= 1 and paystatus=1 and sendstatus!=4 and sendstatus!=6 and paytime>= '".$there."' and paytime< '".$two."'";  
        

        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount3        = $row->tcount3;
            $pay_currency   = $row->pay_currency;

            $tcount3 -= $pay_currency;
        }
        //大前天
        // $query= "select IFNULL(sum(totalprice),0) as tcount4 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and UNIX_TIMESTAMP(createtime)>=".strtotime($four)." and UNIX_TIMESTAMP(createtime)<".strtotime($there);
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        // while ($row = mysql_fetch_object($result)) {
            // $tcount4 = $row->tcount4;
        // }
        $query = "select IFNULL(sum(NoExpPrice),0) as tcount4,
                       IFNULL(sum(pay_currency),0) as tpay_currency
                  from weixin_commonshop_order_prices
                 where isvalid= 1
                   and paystatus=1
                   and batchcode IN(
                        select batchcode
                          from weixin_commonshop_orders
                         where customer_id= ".$customer_id."
                           and isvalid= 1
                           and sendstatus!=4
                           and sendstatus!=6
                           and paystatus=1
                           and paytime>= '".$four."'
                           and paytime< '".$there."' group by batchcode)";

        // $query = "select IFNULL(sum(NoExpPrice),0) as tcount4,IFNULL(sum(pay_currency),0) as tpay_currency from weixin_commonshop_order_prices where customer_id= ".$customer_id." and isvalid= 1 and paystatus=1 and sendstatus!=4 and sendstatus!=6 and paytime>= '".$four."' and paytime< '".$there."'";  
                           
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount4        = $row->tcount4;
            $pay_currency   = $row->pay_currency;

            $tcount4 -= $pay_currency;
        }
        //大大前天
        // $query= "select IFNULL(sum(totalprice),0) as tcount5 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and UNIX_TIMESTAMP(createtime)>=".strtotime($five)." and UNIX_TIMESTAMP(createtime)<".strtotime($four);
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        // while ($row = mysql_fetch_object($result)) {
            // $tcount5 = $row->tcount5;
        // }
        $query = "select IFNULL(sum(NoExpPrice),0) as tcount5,
                       IFNULL(sum(pay_currency),0) as tpay_currency
                  from weixin_commonshop_order_prices
                 where isvalid= 1
                   and paystatus=1
                   and batchcode IN(
                        select batchcode
                          from weixin_commonshop_orders
                         where customer_id= ".$customer_id."
                           and isvalid= 1
                           and sendstatus!=4
                           and sendstatus!=6
                           and paystatus=1
                           and paytime>='".$five."'
                           and paytime< '".$four."' group by batchcode)";

          // $query = "select IFNULL(sum(NoExpPrice),0) as tcount5,IFNULL(sum(pay_currency),0) as tpay_currency from weixin_commonshop_order_prices where customer_id= ".$customer_id." and isvalid= 1 and paystatus=1 and sendstatus!=4 and sendstatus!=6 and paytime>= '".$five."' and paytime< '".$four."'";  
                            
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount5        = $row->tcount5;
            $pay_currency   = $row->pay_currency;

            $tcount5 -= $pay_currency;
        }
        //大大大前天
        // $query= "select IFNULL(sum(totalprice),0) as tcount6 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and UNIX_TIMESTAMP(createtime)>=".strtotime($six)." and UNIX_TIMESTAMP(createtime)<".strtotime($five);
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        // while ($row = mysql_fetch_object($result)) {
            // $tcount6 = $row->tcount6;
        // }
        $query = "select IFNULL(sum(NoExpPrice),0) as tcount6,
                       IFNULL(sum(pay_currency),0) as tpay_currency
                  from weixin_commonshop_order_prices
                 where isvalid= 1
                   and paystatus=1
                   and batchcode IN(
                        select batchcode
                          from weixin_commonshop_orders
                         where customer_id= ".$customer_id."
                           and isvalid= 1
                           and sendstatus!=4
                           and sendstatus!=6
                           and paystatus=1
                           and paytime>='".$six."'
                           and paytime<'".$five."' group by batchcode)";

         // $query = "select IFNULL(sum(NoExpPrice),0) as tcount6,IFNULL(sum(pay_currency),0) as tpay_currency from weixin_commonshop_order_prices where customer_id= ".$customer_id." and isvalid= 1 and paystatus=1 and sendstatus!=4 and sendstatus!=6 and paytime>= '".$six."' and paytime< '".$five."'";  
         

        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount6        = $row->tcount6;
            $pay_currency   = $row->pay_currency;

            $tcount6 -= $pay_currency;
        }
        //大大大大前天
        // $query= "select IFNULL(sum(totalprice),0) as tcount7 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and UNIX_TIMESTAMP(createtime)>=".strtotime($seven)." and UNIX_TIMESTAMP(createtime)<".strtotime($eight);
        // $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        // while ($row = mysql_fetch_object($result)) {
            // $tcount7 = $row->tcount7;
        // }
        $query = "select IFNULL(sum(NoExpPrice),0) as tcount7,
                       IFNULL(sum(pay_currency),0) as tpay_currency
                  from weixin_commonshop_order_prices
                 where isvalid= 1
                   and paystatus=1
                   and batchcode IN(
                        select batchcode
                          from weixin_commonshop_orders
                         where customer_id= ".$customer_id."
                           and isvalid= 1
                           and sendstatus!=4
                           and sendstatus!=6
                           and paystatus=1
                           and paytime>= '".$seven."'
                           and paytime< '".$six."' group by batchcode)";
                           
        // $query = "select IFNULL(sum(NoExpPrice),0) as tcount7,IFNULL(sum(pay_currency),0) as tpay_currency from weixin_commonshop_order_prices where customer_id= ".$customer_id." and isvalid= 1 and paystatus=1 and sendstatus!=4 and sendstatus!=6 and paytime>= '".$seven."' and paytime< '".$eight."'";  

        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount7        = $row->tcount7;
            $pay_currency   = $row->pay_currency;

            $tcount7 -= $pay_currency;
        }
        $tcount_2=array();
        array_push($tcount_2,$tcount7,$tcount6,$tcount5,$tcount4,$tcount3,$tcount2,$tcount1);

        /*支付了的订单，结束*/
        $tcount=array();
        array_push($tcount,$tcount_2,$tcount_1);

        echo json_encode($tcount);
        return;
        break;
    case 27://订单统计
        $begintime=date("Y-m-d");
        $endtime=date("Y-m-d",strtotime("+1 day"));
        /*未支付订单*/
        //$query= "select count(distinct batchcode) as tcount1 from weixin_commonshop_orders where isvalid=true and paystatus=0 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        $query = "select count(1) as tcount1 from weixin_commonshop_order_prices where isvalid=true and paystatus=0 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount1 = $row->tcount1;
        }
        /*已支付订单*/
        //$query= "select count(distinct batchcode) as tcount2 from weixin_commonshop_orders where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        $query = "select count(1) as tcount2 from weixin_commonshop_order_prices where isvalid=true and paystatus=1 and customer_id=".$customer_id." and createtime>='".$begintime."' and createtime<'".$endtime."'";
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $tcount2 = $row->tcount2;
        }
        $tcount=array();
        array_push($tcount,$tcount2,$tcount1);
        echo json_encode($tcount);
        return;
        break;
    case 28://标签投放订单统计
        //今天总订单数
        if($_POST["search_tags_id"] == -1){
            $condition = "";
        }else{
            $search_tags_id = $configutil->splash_new($_POST["search_tags_id"]);
            $condition = " and from_type=".$search_tags_id." ";
        }
        $begintime=date("Y-m-d");
        $endtime=date("Y-m-d",strtotime("+1 day"));
        $query = "select sum(new_order_num) as tcount from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$begintime."' and statisticaltime<'".$endtime."'".$condition;
        break;
    case 29://标签投放订单统计
        //昨天总订单数
        if($_POST["search_tags_id"] == -1){
            $condition = "";
        }else{
            $search_tags_id = $configutil->splash_new($_POST["search_tags_id"]);
            $condition = " and from_type=".$search_tags_id." ";
        }
        $begintime=date("Y-m-d",strtotime("-1 day"));
        $endtime=date("Y-m-d");
        $query = "select sum(new_order_num) as tcount from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$begintime."' and statisticaltime<'".$endtime."'".$condition;
        break;
    case 30://标签投放订单统计
        $tcount1=0;
        $tcount2=0;
        $tcount3=0;
        $tcount4=0;
        $tcount5=0;
        $tcount6=0;
        $tcount7=0;
        $ome=date("Y-m-d"); //今天时间开始
        $endtime=date("Y-m-d",strtotime("+1 day")); //明天时间结束
        $two=date("Y-m-d",strtotime("-1 day"));
        $there=date("Y-m-d",strtotime("-2 day"));
        $four=date("Y-m-d",strtotime("-3 day"));
        $five=date("Y-m-d",strtotime("-4 day"));
        $six=date("Y-m-d",strtotime("-5 day"));
        $seven=date("Y-m-d",strtotime("-6 day"));
        $eight=date("Y-m-d",strtotime("-7 day"));
        if($_POST["search_tags_id"] == -1){
            $condition = "";
        }else{
            $search_tags_id = $configutil->splash_new($_POST["search_tags_id"]);
            $condition = " and from_type=".$search_tags_id." ";
        }

        //今天
        $query= "select sum(new_order_num) as tcount1 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$ome."' and statisticaltime<'".$endtime."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount1)){
                $tcount1 = $row->tcount1;
            }
        }
        //昨天
        $query= "select sum(new_order_num) as tcount2 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$two."' and statisticaltime<'".$ome."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount2)){
                $tcount2 = $row->tcount2;
            }
        }
        //前天
        $query= "select sum(new_order_num) as tcount3 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$there."' and statisticaltime<'".$two."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount3)){
                $tcount3 = $row->tcount3;
            }
        }
        //大前天
        $query= "select sum(new_order_num) as tcount4 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$four."' and statisticaltime<'".$there."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount4)){
                $tcount4 = $row->tcount4;
            }
        }
        //大大前天
        $query= "select sum(new_order_num) as tcount5 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$five."' and statisticaltime<'".$four."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount5)){
                $tcount5 = $row->tcount5;
            }
        }
        //大大大前天
        $query= "select sum(new_order_num) as tcount6 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$six."' and statisticaltime<'".$five."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount6)){
                $tcount6 = $row->tcount6;
            }
        }
        //大大大大前天
        $query= "select sum(new_order_num) as tcount7 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$seven."' and statisticaltime<'".$eight."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount7)){
                $tcount7 = $row->tcount7;
            }
        }
        $tcount=array();
        array_push($tcount,$tcount7,$tcount6,$tcount5,$tcount4,$tcount3,$tcount2,$tcount1);
        //$tcount=$tcount1.",".$tcount2.",".$tcount3.",".$tcount4.",".$tcount5.",".$tcount6.",".$tcount7;
        echo json_encode($tcount);
        return;
        break;
    case 31://标签拒收订单统计
        //今天天拒收订单数
        if($_POST["search_tags_id"] == -1){
            $condition = "";
        }else{
            $search_tags_id = $configutil->splash_new($_POST["search_tags_id"]);
            $condition = " and from_type=".$search_tags_id." ";
        }
        $begintime=date("Y-m-d");
        $endtime=date("Y-m-d",strtotime("+1 day"));
        $query = "select sum(rejected_order_num) as tcount from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$begintime."' and statisticaltime<'".$endtime."'".$condition;
        break;
    case 32://标签拒收订单统计
        //昨天天拒收订单数
        if($_POST["search_tags_id"] == -1){
            $condition = "";
        }else{
            $search_tags_id = $configutil->splash_new($_POST["search_tags_id"]);
            $condition = " and from_type=".$search_tags_id." ";
        }
        $begintime=date("Y-m-d",strtotime("-1 day"));
        $endtime=date("Y-m-d");
        $query = "select sum(rejected_order_num) as tcount from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$begintime."' and statisticaltime<'".$endtime."'".$condition;
        break;
    case 33://标签拒收订单统计
        $tcount1=0;
        $tcount2=0;
        $tcount3=0;
        $tcount4=0;
        $tcount5=0;
        $tcount6=0;
        $tcount7=0;
        $ome=date("Y-m-d"); //今天时间开始
        $endtime=date("Y-m-d",strtotime("+1 day")); //明天时间结束
        $two=date("Y-m-d",strtotime("-1 day"));
        $there=date("Y-m-d",strtotime("-2 day"));
        $four=date("Y-m-d",strtotime("-3 day"));
        $five=date("Y-m-d",strtotime("-4 day"));
        $six=date("Y-m-d",strtotime("-5 day"));
        $seven=date("Y-m-d",strtotime("-6 day"));
        $eight=date("Y-m-d",strtotime("-7 day"));
        if($_POST["search_tags_id"] == -1){
            $condition = "";
        }else{
            $search_tags_id = $configutil->splash_new($_POST["search_tags_id"]);
            $condition = " and from_type=".$search_tags_id." ";
        }

        //今天
        $query= "select sum(rejected_order_num) as tcount1 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$ome."' and statisticaltime<'".$endtime."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount1)){
                $tcount1 = $row->tcount1;
            }
        }
        //昨天
        $query= "select sum(rejected_order_num) as tcount2 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$two."' and statisticaltime<'".$ome."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount2)){
                $tcount2 = $row->tcount2;
            }
        }
        //前天
        $query= "select sum(rejected_order_num) as tcount3 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$there."' and statisticaltime<'".$two."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount3)){
                $tcount3 = $row->tcount3;
            }
        }
        //大前天
        $query= "select sum(rejected_order_num) as tcount4 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$four."' and statisticaltime<'".$there."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount4)){
                $tcount4 = $row->tcount4;
            }
        }
        //大大前天
        $query= "select sum(rejected_order_num) as tcount5 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$five."' and statisticaltime<'".$four."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount5)){
                $tcount5 = $row->tcount5;
            }
        }
        //大大大前天
        $query= "select sum(rejected_order_num) as tcount6 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$six."' and statisticaltime<'".$five."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount6)){
                $tcount6 = $row->tcount6;
            }
        }
        //大大大大前天
        $query= "select sum(rejected_order_num) as tcount7 from weixin_advertise_data_statistics where isvalid=true and customer_id=".$customer_id." and statisticaltime>='".$seven."' and statisticaltime<'".$eight."'".$condition;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            if (!empty($row->tcount7)){
                $tcount7 = $row->tcount7;
            }
        }
        $tcount=array();
        array_push($tcount,$tcount7,$tcount6,$tcount5,$tcount4,$tcount3,$tcount2,$tcount1);
        //$tcount=$tcount1.",".$tcount2.",".$tcount3.",".$tcount4.",".$tcount5.",".$tcount6.",".$tcount7;
        echo json_encode($tcount);
        return;
        break;
    case 34://标签投放成交订单占比
        $query_tag = "select ats.confirm_order_num,at.tag_name,at.color from weixin_advertise_tag_statistics ats inner join weixin_advertise_tags as at where at.customer_id=".$customer_id." and ats.customer_id=".$customer_id." and ats.from_type=at.id and ats.confirm_order_num>0";
        $result_tag = _mysql_query($query_tag) or die('Query failed 100: ' . mysql_error());
        $confirm_order_nums = array();
        $tag_names = array();
        $color = array();
        $i = 0;
        while ($row_tag = mysql_fetch_object($result_tag)) {
            $confirm_order_nums[$i] = $row_tag->confirm_order_num;
            $tag_names[$i] = $row_tag->tag_name;
            $color[$i] = $row_tag->color;
            $i++;
        }

        $tcount = array(
                "confirm_order_nums"  => $confirm_order_nums,
                "tag_names"           => $tag_names,
                "color"               => $color,
                "count"               => $i
            );
        echo json_encode($tcount);
        return;
        break;
    case 35://标签投放拒收订单占比
        $query_tag = "select ats.rejected_order_num,at.tag_name,at.color from weixin_advertise_tag_statistics ats inner join weixin_advertise_tags as at where at.customer_id=".$customer_id." and ats.customer_id=".$customer_id." and ats.from_type=at.id and ats.rejected_order_num>0";
        $result_tag = _mysql_query($query_tag) or die('Query failed 100: ' . mysql_error());
        $rejected_order_num = array();
        $tag_names = array();
        $color = array();
        $i = 0;
        while ($row_tag = mysql_fetch_object($result_tag)) {
            $rejected_order_num[$i] = $row_tag->rejected_order_num;
            $tag_names[$i] = $row_tag->tag_name;
            $color[$i] = $row_tag->color;
            $i++;
        }

        $tcount = array(
                "rejected_order_num"  => $rejected_order_num,
                "tag_names"           => $tag_names,
                "color"               => $color,
                "count"               => $i
            );
        echo json_encode($tcount);
        return;
        break;
}


$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $tcount = $row->tcount;
}
$tcount=round($tcount,2);
echo $tcount;
?>