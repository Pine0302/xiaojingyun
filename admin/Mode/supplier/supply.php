<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
include('../../../../weixinpl/common/phpqrcode/phpqrcode.php');
require('../../../../weixinpl/common/tupian/CreateExpQR.php');
require_once ROOT_DIR.'weixinpl/common/utility_qrcode.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$db = new \Key\DB();

$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=2;//头部文件  0基本设置,1提现记录,2供应商管理
require('../../../../weixinpl/auth_user.php');
$op="";
require('../../../../weixinpl/common/utility_shop.php');

$shopMessage= new shopMessage_Utlity();
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);

   $id = $configutil->splash_new($_GET["id"]);
   $user_id = $configutil->splash_new($_GET["user_id"]);
   $user_ids = $configutil->splash_new($_GET["user_ids"]);
   $parent_id = $configutil->splash_new($_GET["parent_id"]);
   if($op=="status"){
      $status = $configutil->splash_new($_GET["status"]);
      $isAgent = $configutil->splash_new($_GET["isAgent"]);
      if($status==1){
          //1.查询用户状态是否为0
          $query="select status from weixin_commonshop_applysupplys where isvalid=true and user_id =".$user_id."";
          $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
           while ($row = mysql_fetch_object($result)) {
              $status = $row->status;
          }
          if( $status == 0 ){
            $parent_id = $configutil->splash_new($_GET["parent_id"]);
            $reason = $configutil->splash_new($_GET["reason"]);
            $sql="update weixin_qrs set status=".$status.",reason='".$reason."' where id=".$id;
            _mysql_query($sql);
            $status = 1;
          } else {
            echo "<script>alert('数据未同步,请刷新页面重试');</script>";
            // $status = 0;
          }
          
          if($isAgent==3){
              $Cstatus = 3;
            $sql="update weixin_commonshop_applysupplys set status=".$status." where user_id=".$user_id;
            _mysql_query($sql);

            $sql="update promoters set status=1,isAgent=3 where user_id=".$user_id." and isvalid=true and customer_id=".$customer_id;
            _mysql_query($sql);

            /* 2015-11-01 阿鸿 供应商不需要删除他的上级 --------start
            $sql="update promoters set parent_id=-1,status=1,isAgent=3 where user_id=".$user_id." and isvalid=true and customer_id=".$customer_id;
            _mysql_query($sql);

            //取消上下级关系 代理不需要上级
            $sql="update weixin_qr_scans set isvalid=false where  user_id=".$user_id." and customer_id=".$customer_id." and scene_id=".$parent_id;
            _mysql_query($sql);
            $sql="update weixin_users set parent_id=-1 where id=".$user_id;
            _mysql_query($sql);
            //取消上下级关系 代理不需要上级
            //减少上级的粉丝数和推广员数
            $sql="update promoters set fans_count= fans_count-1,promoter_count=promoter_count-1 where isvalid=true and user_id=".$parent_id;
            _mysql_query($sql);
            2015-11-01 供应商不需要删除他的上级 -------- end */
          }
          if($isAgent==-1){
              $Cstatus = 4;
              $status = -1;
                $sql="update weixin_commonshop_applysupplys set status=".$status." where user_id=".$user_id;
                _mysql_query($sql);
          }
          $shopMessage->ChangeRelationLog($customer_id,$user_id,'供应商',$Cstatus);    //生命周期
      }elseif(2 == $status){
           $applysupplys="update weixin_commonshop_applysupplys set is_admin_closed=1 where user_id=".$user_id;
           _mysql_query($applysupplys);//平台开启供应商复业
      }elseif(3 == $status){
           $applysupplys="update weixin_commonshop_applysupplys set is_admin_closed=0 where user_id=".$user_id;
           _mysql_query($applysupplys);//平台开启供应商复业
      }

      // $shopMessage->ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,3,$Cstatus);
   }else if($op=="del"){
      $sql="update promoters set isAgent=0 where user_id=".$user_id;
      _mysql_query($sql);
      $sql="update weixin_commonshop_applysupplys set isvalid=false where user_id=".$user_id;
      _mysql_query($sql);//删掉供应商申请
      $sql="update weixin_commonshop_products set isout=1,isout_status=0 where is_supply_id=".$user_id;
      _mysql_query($sql);//将供应商的产品下架
      $applysupplys="update weixin_commonshop_applysupplys set isbrand_supply=false where user_id=".$user_id;
      _mysql_query($applysupplys);//取消品牌供应商标识
      $brandsupplys="update weixin_commonshop_brand_supplys set isvalid=false where user_id=".$user_id;
      _mysql_query($brandsupplys);//删除品牌供应商
      // $shopMessage->ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,3,5);
      $shopMessage->ChangeRelationLog($customer_id,$user_id,'供应商',5);   //生命周期
   }else if($op=="mul_del"){
      $userid_arr=explode(',',$user_ids);
      foreach($userid_arr as $val){
          $sql="update promoters set isAgent=0 where user_id=".$val;
          _mysql_query($sql);
          $sql="update weixin_commonshop_applysupplys set isvalid=false where user_id=".$val;
          _mysql_query($sql);//删掉供应商申请
          $sql="update weixin_commonshop_products set isout=1,isout_status=0 where is_supply_id=".$val;
          _mysql_query($sql);//将供应商的产品下架
          $applysupplys="update weixin_commonshop_applysupplys set isbrand_supply=false where user_id=".$val;
          _mysql_query($applysupplys);//取消品牌供应商标识
          $brandsupplys="update weixin_commonshop_brand_supplys set isvalid=false where user_id=".$val;
          _mysql_query($brandsupplys);//删除品牌供应商
          // $shopMessage->ChangeRelation_new($user_id,$parent_id,$parent_id,$customer_id,3,5);
          $shopMessage->ChangeRelationLog($customer_id,$val,'供应商',5);   //生命周期
      }
   }else if($op=="brand_status"){ //品牌供应商操作
         $status = $configutil->splash_new($_GET["status"]);
        $sql="update weixin_commonshop_applysupplys set brand_status=".$status." where user_id=".$user_id;
        _mysql_query($sql);

   }
   if($op=="resetpwd"){ //重置供应商密码
       $user_id = $configutil->splash_new($_GET["user_id"]);
       $sql="update promoters set pwd='888888' where user_id=".$user_id." and customer_id=".$customer_id;
       _mysql_query($sql);
   }
}
$exp_user_id=-1;

if(!empty($_GET["exp_user_id"])){
    $exp_user_id = $configutil->splash_new($_GET["exp_user_id"]);
}
$search_status=-1;  //普通供应商搜索
if(!empty($_GET["search_status"])){
    $search_status = $configutil->splash_new($_GET["search_status"]);
}
if(!empty($_POST["search_status"])){
    $search_status = $configutil->splash_new($_POST["search_status"]);
}

$search_brandstatus=-1;  //品牌供应商搜索
if(!empty($_GET["search_brandstatus"])){
    $search_brandstatus = $configutil->splash_new($_GET["search_brandstatus"]);
}
if(!empty($_POST["search_brandstatus"])){
    $search_brandstatus = $configutil->splash_new($_POST["search_brandstatus"]);
}

$search_name="";
if(!empty($_GET["search_name"])){
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
if(!empty($_POST["search_name"])){
    $search_name = $configutil->splash_new($_POST["search_name"]);
}

$search_user_id="";
if(!empty($_GET["search_user_id"])){
    $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
if(!empty($_POST["search_user_id"])){
    $search_user_id = $configutil->splash_new($_POST["search_user_id"]);
}


$search_phone="";
if(!empty($_GET["search_phone"])){
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
if(!empty($_POST["search_phone"])){
    $search_phone = $configutil->splash_new($_POST["search_phone"]);
}

$search_begintime="";
if(!empty($_GET["search_begintime"])){
    $search_begintime = $configutil->splash_new($_GET["search_begintime"]);
}
if(!empty($_POST["search_begintime"])){
    $search_begintime = $configutil->splash_new($_POST["search_begintime"]);
}

$search_endtime="";
if(!empty($_GET["search_endtime"])){
    $search_endtime = $configutil->splash_new($_GET["search_endtime"]);
}
if(!empty($_POST["search_endtime"])){
    $search_endtime = $configutil->splash_new($_POST["search_endtime"]);
}

$op="";
if(!empty($_GET["op"])){
   $op     = $configutil->splash_new($_GET["op"]);
   if( $op == "resetpwd" ){
       $keyid   = $configutil->splash_new($_GET["keyid"]);
       $user_id = $configutil->splash_new($_GET["user_id"]);
       $sql     = "update promoters set pwd='888888' where user_id=".$user_id;
       _mysql_query( $sql );

   }
}

$exp_name="推广员";
$query="select exp_name,shop_card_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 while ($row = mysql_fetch_object($result)) {
    $shop_card_id = $row->shop_card_id;
    $exp_name     = $row->exp_name;
    break;
}
$pagecount = 10;
if(!empty($_GET["pagecount"])){
    $pagecount = intval($_GET["pagecount"]);
}
$pagenum = 1;
if (!empty($_GET["pagenum"])) {
    $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagecount;
$end = $pagecount;

//查询是否开启品牌供应商
$isOpenBrandSupply=-1;
$brand_supply="select isOpenBrandSupply from weixin_commonshops where customer_id=".$customer_id."";
$brand_result=_mysql_query($brand_supply) or die ('brand_supply faild' .mysql_error());
while($row=mysql_fetch_object($brand_result)){
    $isOpenBrandSupply=$row->isOpenBrandSupply;
}

// 获取推广二维码
function getQr($customer_id,$user_id){
    $query="select id,watertype,name,exp_pic_text1,exp_pic_text2,exp_pic_text3,promoter_bg_imgurl,parent_ps,is_autoupgrade from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
    $watertype=1;
    $is_autoupgrade=0;
    $text3 = "消费变成投资 人人都是老板";
    $text4 = "长按此图片识别图中二维码搞定";
    $text5 = "奖励送不停,别人消费你还有奖励";
    $promoter_bg_imgurl = "";
    $result = _mysql_query($query) or die('Query failed22: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
        $shop_id   = $row->id;
        $watertype = $row->watertype;
        $shop_name = mysql_real_escape_string($row->name);          //微商城名称
        $text3 = empty($row->exp_pic_text1) ? $text3 : $row->exp_pic_text1;  //推广图片自定义文字1
        $text4 = empty($row->exp_pic_text2) ? $text4 : $row->exp_pic_text2;  //推广图片自定义文字2
        $text5 = empty($row->exp_pic_text3) ? $text5 : $row->exp_pic_text3;  //推广图片自定义文字3
        $promoter_bg_imgurl = $row->promoter_bg_imgurl;  //推广二维码图片背景图
        $parent_ps = $row->parent_ps;  //不是推广员，申请推广图片是的提示
        $is_autoupgrade = $row->is_autoupgrade;
    }
    $watertype=$watertype>0?$watertype:1;
    $water_change = 0;
    $query2 = "select id,watertype,exp_pic_text1,exp_pic_text2,exp_pic_text3 from weixin_commonshop_waters where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id;

    $result2 = _mysql_query($query2) or die('Query22222 failed: ' . mysql_error());
    $water_id = -1;
    $old_watertype = "";
    $old_pic_text1 = "";
    $old_pic_text2 = "";
    $old_pic_text3 = "";
    while ($row2 = mysql_fetch_object($result2)) {
        $water_id = $row2->id;  //用户更新
        $old_watertype = $row2->watertype;  //选择推广图片风格
        $old_pic_text1 = $row2->exp_pic_text1;  //推广图片自定义文字1
        $old_pic_text2 = $row2->exp_pic_text2;  //推广图片自定义文字2
        $old_pic_text3 = $row2->exp_pic_text3;  //推广图片自定义文字3
    }

    $watertype=$watertype>0?$watertype:1;
    if($water_id<0){
        $sql1="insert into weixin_commonshop_waters(user_id,customer_id,watertype,exp_pic_text1,exp_pic_text2,exp_pic_text3,isvalid,createtime) values(".$user_id.",".$customer_id.",".$watertype.",'".$text3."','".$text4."','".$text5."',true,now())";

        _mysql_query($sql1) or die('sql144444 failed: ' . mysql_error());

    }

    if($old_watertype!=$watertype or $old_pic_text1!=$text3 or $old_pic_text2!=$text4 or $old_pic_text3!=$text5){
        $water_change = 1;
    }
    $createExpQr =  new createExpQrUtility();
    $exp_map_url = $createExpQr->waterMark($weixin_headimgurl, "我是".$weixin_name, "我为".$shop_name."代言", $text3 , $text4, $text5,$watertype,$user_id,$customer_id,$promoter_bg_imgurl,$for_or,$op);
    $query="update promoters set exp_map_url='".$exp_map_url."' where user_id=".$user_id;
    _mysql_query($query);
    $sql1="update weixin_commonshop_waters set watertype=".$watertype.",exp_pic_text1='".$text3."',exp_pic_text2='".$text4."',exp_pic_text3='".$text5."' where user_id=".$user_id." and customer_id=".$customer_id;
    _mysql_query($sql1);
    // return str_replace('exp_','',$exp_map_url);
}

?>
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>合作商-商户管理</title>
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="/weixinpl/common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/weixinpl/js/tis.js"></script>
<script type="text/javascript" src="/weixinpl/common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="/weixinpl/js/WdatePicker.js"></script>
<script type="text/javascript" src="/weixinpl/common/js/jquery.blockUI.js"></script>
<script charset="utf-8" src="/weixinpl/common/js/jquery.jsonp-2.2.0.js"></script>
<script charset="utf-8" src="inputexcel.js"></script>
<script src="/weixinpl/common/js/floatBox.js"></script>
<script>
function inputtext(table,filename){

    /*导出自行安装订单筛选框*/
    var excelArray = [
                        ["1","合作商编号"],
                        ["2","排序(降序)"],
                        ["3","姓名"],
                        ["4","个人申请信息"],
                        ["5","直接会员人数"],
                        ["6","账单记录"],
                        ["7","普通合作商申请状态"],
                        ["8","咨询电话"],
                        ["9","虚拟粉丝"],
                        ["10","个人总消费金额"],
                        ["11","申请时间"]
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
            if(i!=12 && (dataIntArr.indexOf(i)!=-1)){
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
            if(i!=11 && (dataIntArr.indexOf(i)!=-1)){
                if(i==2){
                    excel += '<td>';
                    excel += $(this).find('input:text').val()||' ';
                    excel += '</td>';
                }else{
                    excel += '<td>';
                    excel += $(this).html();
                    excel += '</td>';
                }
            }
        })
        excel += '</tr>';
    })
    excel += '</table>';
    excel=excel.replace(/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/g ,"$2").replace(/[\s]+/g," ").replace(/<i[^>]*class="([^"]*)"[^>]*>(.*?)<\/i>/g,"");
    console.log(excel);
    //构建excel内容结束
    form = $("<form></form>");
    form.attr('action','inputexl.php');
    form.attr('method','post');
    input1 = $("<input type='hidden' name='excel' />");
    input1.attr('value',excel);
    input2 = $("<input type='text' name='filename' />");
    input2.attr('value','合作商');
    form.append(input1);
    form.append(input2);
    form.appendTo("body");
    form.css('display','none');
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
.inventory{
    color:#06A7E1;
}
input.search_btn{
    margin-bottom:5px;
}
</style>
<title>合作商管理</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
    <!--内容框架-->
    <div class="WSY_content">
        <!--列表内容大框-->
        <div class="WSY_columnbox">
            <!--列表头部切换开始-->
            <?php
            include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/supplier/basic_head.php");
            ?>
            <!--列表头部切换结束-->
            <div class="WSY_remind_main">
                <form class="search" id="search_form" style="margin-left:18px; margin-top: 18px;">
                <li class="WSY_position_date tate001" style="display: flex;margin-bottom: 12px;">
                <p>申请时间：<input class="date_picker" type="text" name="AccTime_E" id="search_begintime" value="<?php echo $search_begintime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'search_endtime\')}'});"></p>
                <p>&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="AccTime_B" id="search_endtime" value="<?php echo $search_endtime ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'search_begintime\')}'});"></p>
                </li>
                        普通合作商状态:<select name="search_status" id="search_status"  style="width:100px;" >
                        <option value="-1">--请选择--</option>
                        <option value="2" <?php if($search_status==2){ ?>selected <?php } ?>>待审核</option>
                        <option value="1" <?php if($search_status==1){ ?>selected <?php } ?>>已确认</option>
                        <option value="-2" <?php if($search_status==-2){ ?>selected <?php } ?>>已驳回/暂停</option>
                        </select>
                        合作商类型:<select name="search_brandstatus" id="search_brandstatus"  style="width:100px;" >
                        <option value="-1">--请选择--</option>
                        <option value="2" <?php if($search_brandstatus==2){ ?>selected <?php } ?>>普通合作商</option>
                        <option value="1" <?php if($search_brandstatus==1){ ?>selected <?php } ?>>品牌合作商</option>
                        <option value="3" <?php if($search_brandstatus==3){ ?>selected <?php } ?>>区域代理合作商</option>
                        </select>

                        &nbsp;合作商编号:<input type=text name="search_user_id" id="search_user_id" value="<?php echo $search_user_id; ?>" style="width:80px;" />
                        &nbsp;姓名:<input type=text name="search_name" id="search_name" value="<?php echo $search_name; ?>" style="width:80px;" />
                        &nbsp;电话:<input type=text name="search_phone" id="search_phone" value="<?php echo $search_phone; ?>"  style="width:80px;" />
                    每页记录数：<input type=text name="pagecount" id="pagecount" value="<?php echo $pagecount; ?>"  style="width:80px;border: 1px solid #ccc; border-radius: 2px;height: 24px;margin-left: 10px;padding-left: 8px;" />

                        <input type="button" class="search_btn" onclick="searchForm();" value="搜 索">
                        <!--    <input class="search_btn" value="导出本页信息" onclick="javascript:inputtext('WSY_t1','合作商')" style="cursor:hand" type="button"> -->
                        <input class="search_btn" value="导出全部信息" onclick="exportExcel()" style="cursor:hand" type="button">
                    <div class="WSY_search_q" style="display: block;">
                    <div class="WSY_search_div">
                        <ul>
                            <li class="WSY_bottonliss left" ><input type="button" style="width:100px" id="mul_del" value="删除"></li>
                            <input type=text name="mul_virtual_fans_nums" id="mul_virtual_fans_nums" value="0" style="width:80px;float: left;margin-top: 9px;margin-left: 20px;" />
                            <li class="WSY_bottonliss left" ><input type="button" style="width:100px" id="mul_virtualfans" value="批量设置虚拟粉丝"></li>
                            <?php 
                                $query="select is_open_suning from ".WSY_SHOP.".suning_setting where isvalid=true and customer_id=".$customer_id;
                                $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
                                while ($row = mysql_fetch_object($result)) {
                                    $is_open_suning = $row->is_open_suning;
                                }
                                
                                if ($is_open_suning == 1) {
                            ?>
                            <li class="WSY_bottonliss left" ><input type="button" style="width:100px" id="is_open_batch" value="批量开启苏宁开关"></li>
                            <?php } ?>
                        </ul>
                    </div>
                    </div>
                </form>
                <form action="/weixin/plat/app/index.php/Excel/js_excel" method="p"></form>
                <table width="97%" class="WSY_table" id="WSY_t1">
                    <thead class="WSY_table_header">
                        <th width="3%"><input id="ck_all"  type="checkbox"></th>
                        <th width="8%">合作商编号</th>
                        <th width="4%">排序(降序)</th>
                        <th width="15%">姓名</th>
                        <th width="12%">个人申请信息</th>
                    <!--    <th width="13%">推广二维码</th> -->

                        <th width="8%">直接会员人数</th>
                        <th width="8%">账单记录</th>
                        <th width="4%">普通合作商申请状态</th>
                        <th width="140px">咨询电话</th>
                        <th width="140px">虚拟粉丝</th>
                        <?php if($is_open_suning == 1){ ?>
                        <th width="140px">苏宁开关</th>
                        <?php } ?>
                        <th width="8%">个人总消费金额</th>
                        <th width="8%">招商推荐人</th>
                        <th width="8%">申请时间</th>
                        <th width="8%">操作</th>
                    </thead>
                    <tbody>
                       <?php

                       
                        $weixin_fromuser="";
                        $query = "select
                        distinct(wq.id) as id,
                        qr_info_id,
                        wq.reason as reason,
                        wu.id as user_id,
                        wu.name as name,
                        wu.weixin_name as weixin_name,
                        wu.phone as phone,
                        wu.parent_id as parent_id,
                        wq.imgurl_qr,wcas.id as supplysid,
                        wcas.status as supplystatus,
                        wcas.isbrand_supply as isbrand_supply,
                        wcas.isarea_supply as isarea_supply,
                        wcas.deposit,wcas.supply_money,
                        wcas.advisory_telephone,
                        wcas.advisory_flag,
                        wcas.virtual_fans_flag,
                        wcas.virtual_fans_nums,
                        wcas.createtime as supplycreatetime,
                        wcas.asort_value,
                        wcas.is_admin_closed,
                        wcas.user_name,
                        wcas.user_phone,
                        wq.status,
                        reward_score,reward_money,
                        wq.createtime,
                        promoter.isAgent,
                        weixin_fromuser ";

                        $query_num = "select count(DISTINCT wq.id) as num ";

                        $where = " FROM weixin_qrs wq inner join weixin_qr_infos wqi
                        inner join weixin_users wu
                        inner join ".WSY_SHOP.".weixin_commonshop_applysupplys wcas
                        inner join ".WSY_PUB.".promoters promoter
                        on wq.qr_info_id=wqi.id
                        WHERE promoter.status=1
                        and promoter.user_id=wu.id
                        and promoter.user_id=wcas.user_id
                        and wcas.isvalid=true
                        and promoter.isvalid=true
                        and wq.isvalid=true
                        and wqi.isvalid=true
                        and wqi.user_type=1
                        and wqi.foreign_id = wu.id
                        and wu.isvalid=true
                        and wq.isvalid=true
                        and wq.type=1
                        and wq.customer_id=".$customer_id;

                         if($exp_user_id>0){
                             $where = $where." and wqi.foreign_id=".$exp_user_id;
                         }
                         switch($search_status){
                            case 2:
                               $where = $where." and wcas.status=0";
                               break;
                            case 1:
                               $where = $where." and wcas.status=1";
                               break;
                            case -2:
                               $where = $where." and wcas.status=-1";
                               break;
                         }
                         switch($search_brandstatus){
                            case 2:
                               $where = $where." and wcas.isbrand_supply=false and wcas.isarea_supply=false ";
                               break;
                            case 3:
                               $where = $where." and wcas.isarea_supply=true ";
                               break;
                            case 1:
                               $where = $where." and wcas.isbrand_supply=true";
                               break;

                         }

                         if(!empty($search_name)){

                            $where = $where." and (wu.name like '%".$search_name."%' or wu.weixin_name like '%".$search_name."%')";
                         }

                         if(!empty($search_phone)){

                            $where = $where." and wu.phone like '%".$search_phone."%'";
                         }
                         if(!empty($search_begintime)){

                            $where = $where." and UNIX_TIMESTAMP(wcas.createtime)>=".strtotime($search_begintime);
                         }
                         if(!empty($search_endtime)){

                            $where = $where." and UNIX_TIMESTAMP(wcas.createtime)<=".strtotime($search_endtime);
                         }
                         if(!empty($search_user_id)){

                            $where = $where." and wu.id like '%".$search_user_id."%'";
                         }

                         /* 输出数量开始 */
                         //$query2 = $query2.' order by wcas.createtime';
                         $query2 = $query_num.$where;
                         $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                         while ($row = mysql_fetch_object($result2)) {
                            $rcount_q2 = $row->num;
                         }

                         /* 输出数量结束 */
                         //$where .= " GROUP BY wqi.foreign_id ";   //防止出现多条数据
                         //$query = $query." order by wcas.asort_value desc,wcas.createtime desc"." limit ".$start.",".$end;
                         $query = $query.$where." order by wcas.asort_value desc,wcas.createtime desc"." limit ".$start.",".$end;
                          // echo $query;
                         $data = $db->getData($query);
                         foreach ($data as $key => $value) {
                            $weixin_fromuser    = $value['weixin_fromuser'];
                            $qr_info_id         = $value['qr_info_id'];
                            $user_id            = $value['user_id'];
                            $id                 = $value['id'];
                            $reward_score       = $value['reward_score'];
                            $reward_money       = $value['reward_money'];
                            $isAgent            = $value['isAgent'];
                            $reason             = $value['reason'];
                            $advisory_telephone = $value['advisory_telephone'];
                            $supplysid          = $value['supplysid'];
                            $advisory_flag      = $value['advisory_flag'];
                            $virtual_fans_flag  = $value['virtual_fans_flag'];
                            $virtual_fans_nums  = $value['virtual_fans_nums'];
                            $weixin_name        = $value['weixin_name'];
                            $userphone          = $value['phone'];
                            $imgurl_qr          = $value['imgurl_qr'];
                            $supplycreatetime   = $value['supplycreatetime'];
                            $isbrand_supply     = $value['isbrand_supply'];
                            $isarea_supply      = $value['isarea_supply'];
                            $deposit            = $value['deposit'];
                            $supply_money       = $value['supply_money'];
                            $asort_value        = $value['asort_value'];
                            $is_admin_closed    = $value['is_admin_closed'];
                            $user_name          = $value['user_name'];
                            $user_phone         = $value['user_phone'];
                            $username           = $value['name'];
                            $reward_money       = round($value['reward_money'], 2);
                            $username           = $value['username']."(".$value['weixin_name'].")";

                            if(!$imgurl_qr){
                                //获取二维码url
                                $qr_Utlity_fun = new qr_Utlity();
                                $qr_info = $qr_Utlity_fun->get_qr_info($user_id,$customer_id);
                                $imgurl_qr =$qr_info['img_url'];
                            }

                            /* if(!file_exists(str_replace('admin.weisanyun.cn/weixinpl', '../../..', $imgurl_qr))){
                                getQr($customer_id,$user_id);
                            } */

                            $team_fans = 0 ;
                            $team_prom = 0;
                            $query2="select team_fans,team_prom from promoters where user_id=".$user_id." and isvalid=true";
                            $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                            while ($row2 = mysql_fetch_object($result2)) {
                             //总的推广员数跟粉丝数
                                $team_fans = $row2->team_fans;
                                $team_prom = $row2->team_prom;
                               break;
                            }
                            // $status       = $row->status;
                            // $supplystatus = $row->supplystatus;
                            $status         = $value['status'];
                            $supplystatus   = $value['supplystatus'];

                            $statusstr    = "待审核";
                            switch($supplystatus){
                               case 1:
                                 $statusstr = "已确认";
                                 break;
                               case -1:
                                 $statusstr = "已驳回/暂停";
                                 break;
                            }
                            $parent_name = "";
                            $query2="select parent_id,createtime,isAgent,agent_inventory,agent_getmoney from promoters where  status=1 and isvalid=true and user_id=".$user_id;
                            // echo $query2;
                            $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                            $parent_id       = -1;
                            $isAgent         = 0;
                            $agent_inventory = 0;
                            $agent_getmoney  = 0;
                            while ($row2 = mysql_fetch_object($result2)) {
                                $parent_id  = $row2->parent_id;
                                $createtime = $row2->createtime;
                                $isAgent    = $row2->isAgent;
                                break;
                            }
                            $is_identity = 0;
                            if( $isAgent == 1 ){ $is_identity = 1; } //是代理商
                            if( $isAgent == 3 ){ $is_identity = 1; } //是供应商,则赋值可以进行审核

                            //查找账户和支付宝

                            $query2="SELECT account,account_type,bank_open,bank_address,bank_name,phone,email from weixin_card_members where checked=true and isvalid=true and user_id=".$user_id;
                            $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                            $account      = "暂未填写";
                            $account_type = "";
                            $bank_open    = "";
                            $bank_name    = "";
                            $account_type_str = "暂未填写";
                            while ($row2 = mysql_fetch_object($result2)) {
                                $account        = $row2->account;
                                $account_type   = $row2->account_type;
                                $bank_open      = $row2->bank_open;
                                $bank_address   = $row2->bank_address;
                                $bank_name      = $row2->bank_name;
                                $phone          = $row2->phone;
                                $email          = $row2->email;
                            
                                switch($account_type){
                                    case 1:
                                       $account_type_str = "支付宝";
                                       $account = $email;
                                       break;
                                    case 2:
                                       $account_type_str = "财付通";
                                       $account = $email;
                                       break;
                                    case 3:
                                       $account_type_str = "银行账户";
                                       break;
                                    case 4:
                                       $account_type_str = "环迅账户";
                                       break;
                                    case 0:
                                       $account_type_str = "微信";
                                       $account = $phone;
                                       break;
                                    default:
                                       $account_type_str = "无";
                                       break;
                                }
                            }

                            //查找招商推荐人
                            $pare_sql  = "select parent_id from weixin_attract_investment_user where inverst_id = $user_id and category = 1 limit 1";
                            $pare_res  = _mysql_query($pare_sql) or die('Query failed3: ' . mysql_error());
                            $p_parent_id = -1;
                            while($row = mysql_fetch_object($pare_res))
                            {
                                $p_parent_id = $row->parent_id;
                            }
                            if($p_parent_id>0){
                                $pare_sql2  = "select name,weixin_name from weixin_users where id=$p_parent_id and isvalid=1 limit 1";
                                $user_res  = _mysql_query($pare_sql2) or die('Query failed4: ' . mysql_error());
                                $p_name = "";
                                $p_weixin_name = "";
                                while($row = mysql_fetch_object($user_res))
                                {
                                    $p_name = $row->name;
                                    $p_weixin_name = $row->weixin_name;
                                }
                            }


                            //查找推广员的会员卡号
                            $Membership_Card=-1;
                            $query_m="SELECT id from weixin_card_members where isvalid=true and card_id=".$shop_card_id." and user_id=".$user_id;
                            $result_m = _mysql_query($query_m) or die('Query failed: ' . mysql_error());
                            while ($row_m = mysql_fetch_object($result_m)) {
                               $Membership_Card = $row_m->id;
                               break;
                            }

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

                            $query2="select title,online_qq from weixin_commonshop_owners where isvalid=true and user_id=".$user_id;
                            $mystore_title = "";
                            $mystore_qq    = "";
                            $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());

                            while ($row2 = mysql_fetch_object($result2)) {
                                $mystore_title = $row2->title;
                                $mystore_qq    = $row2->online_qq;
                                break;
                            }

                        //代理进账出账费用
                         $query2 = "select id,batchcode,price,detail,type from weixin_commonshop_agentfee_records where isvalid=true and  user_id=".$user_id;
                         $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
                         $price =0;
                         $total_in_money = 0;
                         $total_out_money = 0;
                         while ($row2 = mysql_fetch_object($result2)) {
                            $price =$row2->price;
                            $type =$row2->type;
                            switch($type){
                                case 1:
                                    $total_out_money = $total_out_money + $price;
                                    break;
                                case 2:
                                    $total_in_money = $total_in_money + $price;
                                    break;
                            }
                         }

                        $querys="select export_suning_product,create_suning_order from ".WSY_SHOP.".suning_supplys_setting where isvalid=true and customer_id=".$customer_id." and supply_id= ".$user_id;
                        $results = _mysql_query($querys) or die('Query failed: ' . mysql_error());
                        while ($rows = mysql_fetch_object($results)) {
                           $export_suning_product    = $rows->export_suning_product;
                           $create_suning_order      = $rows->create_suning_order;
                        }
                       ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="user_ids" value="<?php echo $user_id; ?>">
                            </td>
                           <td><?php echo $user_id; ?><?php if($isbrand_supply){?><span class="am-btn am-btn-danger am-radius ui-btn-up-undefined">品牌</span><?php }?><?php if($isarea_supply){?><span class="am-btn am-btn-danger am-radius ui-btn-up-undefined">区域</span><?php }?></td>
                           <td><input type="text" style="border:1px solid #ccc;border-radius:5px;text-align:center;" value="<?php echo $asort_value;?>" class="ch_sort" sid="<?php echo $supplysid;?>"></td>
                           <td style="text-align:left;"><a title="会员卡号:<?php echo $Membership_Card; ?>" href="../../../card_member.php?card_id=<?php echo $shop_card_id; ?>&card_member_id=<?php echo $Membership_Card; ?>&customer_id=<?php echo passport_encrypt((string)$customer_id);?>"><?php echo $username; ?></a>
                           <?php if(!empty($weixin_fromuser)){
                                     ?>
                                       <a  class="btn"  href="../../../weixin_inter/send_to_msg.php?fromuserid=<?php echo $weixin_fromuser; ?>&customer_id=<?php echo passport_encrypt($customer_id)?>"  title="对话"><i  class="icon-comment"></i></a>
                                    <?php
                                   }  ?>
                           <br/>
                               <?php echo $userphone; ?><br/>
                               收款类型:<?php echo $account_type_str; ?><br/>
                               收款账户:<?php echo $account; ?>
                               <?php if($account_type==3){ ?>
                               <br/>开户银行：<?php echo $bank_open; ?>
                               <br/>开户支行：<?php echo $bank_address; ?>
                               <br/>开户姓名：<?php echo $bank_name; ?>
                               <?php } ?>
                               <?php if(!empty($mystore_title)){ ?>
                                 <br/>微店名称:<?php echo $mystore_title; ?><br/>
                                 在线QQ:<?php echo $mystore_qq; ?>
                               <?php } ?>
                           </td>
                           <td>姓名：<?php if(empty($user_name)){echo "/";}else{ echo $user_name;} ?><br/>电话：<?php if(empty($user_phone)){echo "/";}else{ echo $user_phone;} ?></td>
                          <!--  <td><a href="<?php echo $imgurl_qr; ?>" target="_blank"><img src="<?php echo $imgurl_qr; ?>" style="width:40px;height:40px;" /></a></td> -->
                           <td>
                           粉丝数:&nbsp;<a href="../../Users/promoter/qrsell_detail_member.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&rcount=<?php echo $team_fans; ?>"><?php echo $team_fans; ?></a><br/>
                           推广员数:&nbsp;<a href="../../Users/promoter/qrsell_detail.php?customer_id=<?php echo $customer_id_en; ?>&scene_id=<?php echo $user_id; ?>&rcount=<?php echo $team_prom; ?>"><?php echo $team_prom; ?></a>
                           </td>
                           <!--<td>押金:<!?php echo $deposit; ?>元<br/>提成比例:<!?php echo $commission; ?>%</td>-->
                           <!-- lml echo substr(sprintf("%.3f",$supply_money),0,-1); 小数位处理-->
                           <td><a href="supplycost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&istype=3&detail=1"><span id="span_inventory_<?php echo $id;?>" class="inventory"><?php echo substr(sprintf("%.3f",$supply_money),0,-1);?></span>元</a></td>
                           <td>
                             <?php echo $statusstr; ?><br/>
                             <?php if(!empty($reason) && $supplystatus==-1){ ?>
                             (<span style="font-size:12px;"><?php echo $reason; ?></span>)
                             <?php } ?>
                           </td>

                            <td>
                           <input type="hidden" value="<?php echo $supplysid; ?>"/>
                           <div style="border-radius:25px;width:140px;height:30px;padding:0px" >
                            <?php if($advisory_flag==0){?>
                            <input type='button' onclick='open_advisory(this)' class="WSY-skin-bg" style='width:120px;height:31px;border-radius:25px 25px 25px 25px;display:inline-table;line-height:25px;color:#fff' value='开启'/>
                            <?php }else{?>
                            <input type='button' onclick='open_advisory(this)' style='width:40px;height:31px;background-color: #FF7170;border: 1px solid #FF7170;border-radius:25px 0px 0px 25px;margin:0px;display:inline-table;line-height:25px;color:#fff' value='关闭'/><input type='text' id='advisory_telephone' onblur='save_advisory(this)' style='width:80px;height:29px;background-color: #fff;border: 1px solid #ff7170;border-radius:0px 25px 25px 0px;margin:0px;display:inline-table;line-height:25px' value="<?php echo $advisory_telephone ?>"/>
                            <?php }?>
                            </div>

                           </td>
                           <td>
                           <input type="hidden" value="<?php echo $user_id; ?>"/>
                           <div style="border-radius:25px;width:140px;height:30px;padding:0px" >
                            <?php if($virtual_fans_flag==0){?>
                            <input type='button' onclick='open_virtualfans(this)' class="WSY-skin-bg" style='width:120px;height:31px;border-radius:25px 25px 25px 25px;display:inline-table;line-height:25px;color:#fff' value='开启'/>
                            <?php }else{?>
                            <input type='button' onclick='open_virtualfans(this)' style='width:40px;height:31px;background-color: #FF7170;border: 1px solid #FF7170;border-radius:25px 0px 0px 25px;margin:0px;display:inline-table;line-height:25px;color:#fff' value='关闭'/><input type='text' id='virtual_fans_nums' onblur='save_virtualfans(this)' style='width:80px;height:29px;background-color: #fff;border: 1px solid #ff7170;border-radius:0px 25px 25px 0px;margin:0px;display:inline-table;line-height:25px' value="<?php echo $virtual_fans_nums ?>"/>
                            <?php }?>
                            </div>

                           </td>
                           <?php if($is_open_suning == 1){ ?>
                           <td>
                           <input type="hidden" value="<?php echo $user_id; ?>"/>
                           <div style="border-radius:25px;width:140px;height:30px;padding:0px" >
                            <?php if($export_suning_product == 0 && $create_suning_order == 0){?>
                            <input type='button' onclick='open_suning_set(this)' class="WSY-skin-bg" style='width:120px;height:31px;border-radius:25px 25px 25px 25px;display:inline-table;line-height:25px;color:#fff' value='开启'/>
                            <?php }else{?>
                            <input type='button' onclick='open_suning_set(this)' style='width:120px;height:31px;background-color: #FF7170;border: 1px solid #FF7170;border-radius:25px 25px 25px 25px;margin:0px;display:inline-table;line-height:25px;color:#fff' value='关闭'/>
                            <?php }?>
                            </div>

                           </td>
                           <?php } ?>
                           <td><a href="../../Users/promoter/customers.php?search_user_id=<?php echo $user_id; ?>"><?php echo round($s_totalprice,2); ?></a></td>
                           <td><?php if($p_parent_id>0){ ?>
                               <a href="/wsy_user/admin/user/promoter/promoter.php?exp_user_id=<?php echo $p_parent_id; ?>&customer_id=<?php echo $customer_id_en; ?>" style="word-wrap: break-word;"><?php echo $p_name."<br>(".$p_weixin_name.")"; ?></a><?php }else{echo "";} ?>
                           </td>

                           <td><?php echo $supplycreatetime; ?></td>
                           <td>
                             <!--<a class="btn1"  href="supply.php?customer_id=<!?php echo $customer_id; ?>&keyid=<!?php echo $id; ?>&op=resetpwd&user_id=<!?php echo $user_id; ?>&pagenum=<!?php echo $pagenum; ?>" onclick="if(!confirm(&#39;重置后密码为：888888。继续？&#39;)){return false};"><img src="../../../common/images_V6.0/operating_icon/icon01.png" align="absmiddle" alt="重置密码" title="重置密码"></a>
                             <!?php if($isAgent==3){?>
                             <a  class="btn1" href="javascript:inventory_recharge(<!?php echo $id;?>,<!?php echo $user_id; ?>);"title="充值">
                              <img src="../../../common/images_V6.0/operating_icon/icon22.png" align="absmiddle"/>
                             </a>
                             <!?php }?>-->
                             <?php if($supplystatus == 1){?>
                                <a class="btn1" title="编辑推荐人" href="edit.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&category=1" ><img src="../../../common/images_V6.0/operating_icon/icon53.png" align="absmiddle" alt="编辑推荐人"></a>

                             <?php if(0 == $is_admin_closed){  ?>
                                 <a class="btn1" title="复业" href="supply.php?customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>&op=status&status=2&user_id=<?php echo $user_id; ?>&qr_info_id=<?php echo $qr_info_id; ?>&pagenum=<?php echo $pagenum; ?>&parent_id=<?php echo $parent_id; ?>" onclick="if(!confirm(&#39;确定复业本店铺吗？&#39;)){return false};"><img src="../../../common/images_V6.0/operating_icon/icon75.png" align="absmiddle" alt="复业"></a>
                             <?php }else{ ?>
                                 <a class="btn1" title="停业" href="supply.php?customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>&op=status&status=3&user_id=<?php echo $user_id; ?>&qr_info_id=<?php echo $qr_info_id; ?>&pagenum=<?php echo $pagenum; ?>&parent_id=<?php echo $parent_id; ?>" onclick="if(!confirm(&#39;确定停业本店铺吗？&#39;)){return false};"><img src="../../../common/images_V6.0/operating_icon/icon74.png" align="absmiddle" alt="停业"></a>
                             <?php }} ?>
                             <a href="supply.php?customer_id=<?php echo $customer_id_en; ?>&op=resetpwd&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>"  class="btn1" onclick="if(!confirm(&#39;重置后密码为：888888。继续？&#39;)){return false};"><img src="../../../common/images_V6.0/operating_icon/icon01.png" align="absmiddle" alt="重置密码" title="重置密码"></a>
                            <?php if($supplystatus==0){?> <!--推广员情况下-->
                                <?php if($is_identity!=1 and $supplystatus!=-1){?>
                                 <a  class="btn1"  href="supply.php?op=status&id=<?php echo $id; ?>&status=1&isAgent=3&user_id=<?php echo $user_id; ?>&parent_id=<?php echo $parent_id; ?>&pagenum=<?php echo $pagenum; ?>"  title="通过普通合作商申请">
                                  <img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle"/>
                                 </a>

                                <a  class="btn1"  href="javascript:showReason('supply.php?op=status&id=<?php echo $id; ?>&status=1&isAgent=-1&parent_id=<?php echo $parent_id; ?>&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>');"  title="驳回/暂停普通合作商申请">
                                  <img src="../../../common/images_V6.0/operating_icon/icon03.png" align="absmiddle"/>
                                </a>
                                <?php }?>
                            <?php }?>

                            <a class="btn1" href="supply.php?customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>&op=del&user_id=<?php echo $user_id; ?>&qr_info_id=<?php echo $qr_info_id; ?>&pagenum=<?php echo $pagenum; ?>&parent_id=<?php echo $parent_id; ?>" onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};"><img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除"></a>
                            <a class="btn1" href="<?php echo $imgurl_qr; ?>" target="_blank">
                                <img src="../../../common/images_V6.0/operating_icon/icon09.png" align="absmiddle" alt="二维码" title="二维码" onMouseOver="toolTip('<img src=<?php echo $imgurl_qr; ?>>')" onMouseOut="toolTip()"/>
                            </a>
                            <a class="btn1" title="合作商详情" href="supply_detail.php?user_id=<?php echo $user_id;?>&id=<?php echo $id; ?>&parent_id=<?php echo $parent_id; ?>" ><img src="../../../common/images_V6.0/operating_icon/icon44.png" align="absmiddle" alt="合作商详情"></a>
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
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/Base/mall_setting/ToolTip.js"></script>
<script src="/weixinpl/js/fenye/jquery.page1.js"></script>

<script>

var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
  var count =Math.ceil(rcount_q2/end);//总页数

  //导出
    function exportExcel(){
        var search_user_id = document.getElementById("search_user_id").value;
        var search_status = document.getElementById("search_status").value;
        var search_brandstatus = document.getElementById("search_brandstatus").value;
        var search_name = document.getElementById("search_name").value;
        var search_phone = document.getElementById("search_phone").value;
        var search_begintime = document.getElementById("search_begintime").value;
        var search_endtime = document.getElementById("search_endtime").value;
        var url='/weixin/plat/app/index.php/Excel/supply_excel/customer_id/<?php echo passport_decrypt($customer_id); ?>';

        if( search_user_id != '' && search_user_id > 0 ){
            url += '/search_user_id/'+search_user_id;
        }
        if( search_name != '' ){
            url += '/search_name/'+search_name;
        }
        if( search_phone != '' ){
            url += '/search_phone/'+search_phone;
        }
        if (search_begintime != "") {
            url += '/search_begintime/'+search_begintime;
        }
        if (search_endtime != "") {
            url += '/search_begintime/'+search_endtime;
        }
        if( search_status > 0 ){
            url += '/search_status/'+search_status;
        }
        if( search_brandstatus > 0 ){
            url += '/search_brandstatus/'+search_brandstatus;
        }

        document.location = url;
    }
    //pageCount：总页数
    //current：当前页
    $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
             var search_user_id = document.getElementById("search_user_id").value;
             var search_status = document.getElementById("search_status").value;
              var search_brandstatus = document.getElementById("search_brandstatus").value;
             var search_name = document.getElementById("search_name").value;
             var search_phone = document.getElementById("search_phone").value;
             var search_begintime = document.getElementById("search_begintime").value;
             var search_endtime = document.getElementById("search_endtime").value;
             document.location= "supply.php?pagenum="+p+"&pagecount="+end+"&search_status="+search_status+"&search_brandstatus="+search_brandstatus+"&search_name="+search_name+"&search_phone="+search_phone+"&search_begintime="+search_begintime+"&search_endtime="+search_endtime+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
       }
    });
    var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
    var a=parseInt($("#WSY_jump_page").val());
    if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
        return false;
    }else{
             var search_user_id = document.getElementById("search_user_id").value;
             var search_status = document.getElementById("search_status").value;
            var search_brandstatus = document.getElementById("search_brandstatus").value;
             var search_name = document.getElementById("search_name").value;
             var search_phone = document.getElementById("search_phone").value;
             var search_begintime = document.getElementById("search_begintime").value;
             var search_endtime = document.getElementById("search_endtime").value;
             document.location= "supply.php?pagenum="+a+"&pagecount="+end+"&search_status="+search_status+"&search_brandstatus="+search_brandstatus+"&search_name="+search_name+"&search_phone="+search_phone+"&search_begintime="+search_begintime+"&search_endtime="+search_endtime+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
         }
  }

    function searchForm(){
        var search_user_id = document.getElementById("search_user_id").value;
        var search_status = document.getElementById("search_status").value;
        var search_brandstatus = document.getElementById("search_brandstatus").value;
         var search_name = document.getElementById("search_name").value;
         var search_phone = document.getElementById("search_phone").value;
         var search_begintime = document.getElementById("search_begintime").value;
         var search_endtime = document.getElementById("search_endtime").value;
         if(search_endtime<search_begintime){
             alert("申请时间有误，结束时间不能比开始时间早！");
            return;
         }
        var pagecount = document.getElementById("pagecount").value;
         document.location= "supply.php?issearch=1&pagenum=1&search_status="+search_status+"&search_brandstatus="+search_brandstatus+"&pagecount="+pagecount+"&search_name="+search_name+"&search_phone="+search_phone+"&search_begintime="+search_begintime+"&search_endtime="+search_endtime+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
    }

  function showReason(url){

    var str=prompt("请输入驳回/暂停理由","您不符合合作商条件，请联系客服");
    if(str)
    {
       document.location = url+"&reason="+str;
    }
  }

   /*  function inventory_recharge(id,user_id){ //充值
        var str_m=prompt("确认充值吗?请输入充值金额","");
        if(str_m==null){
            //alert("不能为空");
            return;
        }else if(str_m==""){
            alert("不能为空");
            return;
        }
        isNum = /^[0-9]*$/;
        if(isNum.test(str_m)){
           url='agent_inventory_recharge.php?callback=jsonpCallback_inrecharge&user_id='+user_id+'&customer_id='+customer_id+"&money="+str_m+'&id='+id;
           console.log(url);
             $.jsonp({
                url:url,
                callbackParameter: 'jsonpCallback_inrecharge'
            });
        }else{
            alert("请输入数字");
            return;
        }

  }
  function jsonpCallback_inrecharge(results){
       var agent_inventory = results[0].agent_inventory;
       var id = results[0].id;
      console.log(agent_inventory);
        document.getElementById("span_inventory_"+id).innerHTML="<span id='span_inventory_"+id+"'>"+agent_inventory+"</span>";
  }
  */



                           function open_advisory(obj){
                               var button_val=$(obj).val();
                               var advisory_parent=$(obj).parent();
                               var supplys_id=advisory_parent.parent().children("input").eq(0).val();
                               var val_1="<input type='button' onclick='open_advisory(this)' style='width:40px;height:31px;background-color: #FF7170;border: 1px solid #FF7170;border-radius:25px 0px 0px 25px;margin:0px;display:inline-table;line-height:25px;color:#fff' value='关闭'/><input type='text' id='advisory_telephone' onblur='save_advisory(this)' style='width:80px;height:29px;background-color: #fff;border: 1px solid #ff7170;border-radius:0px 25px 25px 0px;margin:0px;display:inline-table;line-height:25px'  />";
                               var val_0="<input type='button' onclick='open_advisory(this)' class='WSY-skin-bg' style='width:120px;height:31px;border-radius:25px 25px 25px 25px;display:inline-table;line-height:25px;color:#fff' value='开启'/>";
                               if(button_val=="开启"){
                                   $(obj).parent().html(val_1);

                                   $.ajax({
                                        type: "post",
                                        url: "ajax_advisory.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,advisory_flag:3},
                                        success: function (result) {
                                            if(result==0){
                                                result="";
                                            }
                                            advisory_parent.children("input").eq(1).val("").val(result);
                                        }
                                    });
                               }
                               if(button_val=="关闭"){

                                    var advisory_telephone=advisory_parent.children("input").eq(1).val();
                                    $(obj).parent().html(val_0) ;
                                   $.ajax({
                                        type: "post",
                                        url: "ajax_advisory.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,advisory_flag:0,advisory_telephone:advisory_telephone},
                                        success: function (result) {

                                        }
                                    });
                               }
                           }
                           function save_advisory(obj){
                                var advisory_parent=$(obj).parent();
                                 var supplys_id=advisory_parent.parent().children("input").eq(0).val();
                                var advisory_telephone=$(obj).val();
                                var patrn=/^((1[3-9])\d{9})|(\d{3,4}-\d{7,8})$/;
                                var patrn1 = /^[48]00\d{7}$/;
                                var patrn2 = /^((0\d{2,3}))(\d{7,8})?$/;
                                if(advisory_telephone!=""){
                                if (!patrn.exec(advisory_telephone) && !patrn1.exec(advisory_telephone) && !patrn2.exec(advisory_telephone)){
                                    alert("请输入正确的电话号码");
                                    $(obj).val("");
                                    return;
                                }else{
                                    $.ajax({
                                        type: "post",
                                        url: "ajax_advisory.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,advisory_flag:1,advisory_telephone:advisory_telephone},
                                        success: function (result) {
                                        alert(result);
                                        }
                                    });

                                }
                                }

                           }
                           function open_virtualfans(obj){
                               var button_val=$(obj).val();
                               var vf_parent=$(obj).parent();
                               var supplys_id=vf_parent.parent().children("input").eq(0).val();
                               var val_1="<input type='button' onclick='open_virtualfans(this)' style='width:40px;height:31px;background-color: #FF7170;border: 1px solid #FF7170;border-radius:25px 0px 0px 25px;margin:0px;display:inline-table;line-height:25px;color:#fff' value='关闭'/><input type='text' id='virtual_fans_nums' onblur='save_virtualfans(this)' style='width:80px;height:29px;background-color: #fff;border: 1px solid #ff7170;border-radius:0px 25px 25px 0px;margin:0px;display:inline-table;line-height:25px'  value='0'/>";
                               var val_0="<input type='button' onclick='open_virtualfans(this)' class='WSY-skin-bg' style='width:120px;height:31px;border-radius:25px 25px 25px 25px;display:inline-table;line-height:25px;color:#fff' value='开启'/>";
                               if(button_val=="开启"){
                                   $(obj).parent().html(val_1);

                                   $.ajax({
                                        type: "post",
                                        url: "ajax_virtualfans.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,vf_flag:3},
                                        success: function (result) {
                                            /* if(result==0){
                                                result="";
                                            } */
                                            vf_parent.children("input").eq(1).val("").val(result);
                                        }
                                    });
                               }
                               if(button_val=="关闭"){

                                    var virtual_fans_nums=vf_parent.children("input").eq(1).val();
                                    $(obj).parent().html(val_0) ;
                                   $.ajax({
                                        type: "post",
                                        url: "ajax_virtualfans.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,vf_flag:0,virtual_fans_nums:virtual_fans_nums},
                                        success: function (result) {

                                        }
                                    });
                               }
                           }
                           function open_suning_set(obj){
                               var button_val=$(obj).val();
                               var suning_parent=$(obj).parent();
                               var supplys_id=suning_parent.parent().children("input").eq(0).val();
                               var val_1="<input type='button' onclick='open_suning_set(this)' style='width:120px;height:31px;background-color: #FF7170;border: 1px solid #FF7170;border-radius:25px 25px 25px 25px;margin:0px;display:inline-table;line-height:25px;color:#fff' value='关闭'/>";
                               var val_0="<input type='button' onclick='open_suning_set(this)' class='WSY-skin-bg' style='width:120px;height:31px;border-radius:25px 25px 25px 25px;display:inline-table;line-height:25px;color:#fff' value='开启'/>";
                               if(button_val=="开启"){
                                   $(obj).parent().html(val_1);

                                    $.ajax({
                                        type: "post",
                                        url: "ajax_suning_set.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,op:'open'},
                                        success: function (result) {

                                        }
                                    });
                               }
                               if(button_val=="关闭"){
                                    $(obj).parent().html(val_0) ;

                                    $.ajax({
                                        type: "post",
                                        url: "ajax_suning_set.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,op:'close'},
                                        success: function (result) {

                                        }
                                    });
                               }
                           }

                           function save_virtualfans(obj){
                                var vf_parent=$(obj).parent();
                                 var supplys_id=vf_parent.parent().children("input").eq(0).val();
                                var virtual_fans_nums=$(obj).val();
                                // 正整数var patrn= /^[0-9]*[1-9][0-9]*$/ ;
                                // 0和正整数
                                var patrn= /^([1-9]\d*|[0]{1,1})$/;
                                if(virtual_fans_nums!=""){
                                if (!patrn.exec(virtual_fans_nums)){
                                    alert("请输入0或正整数！");
                                    $(obj).val("0");
                                    return;
                                }else{
                                    $.ajax({
                                        type: "post",
                                        url: "ajax_virtualfans.php",
                                        dataType: "json",
                                        //begintime:begintime,endtime:endtime,
                                        data: {supplys_id: supplys_id,vf_flag:1,virtual_fans_nums:virtual_fans_nums},
                                        success: function (result) {
                                        alert(result);
                                        }
                                    });

                                }
                                }

                           }
                           $(".ch_sort").on("blur",function(i){
                                var sid     = $(this).attr('sid');
                                var ch_sort = $(this).val();
                                var op      = "cha_sort";
                                //alert("id:"+sid+"值:"+ch_sort);
                                $.ajax({
                                    url:"./save_set.php?customer_id=<?php echo $customer_id_en;?>",
                                    type:"post",
                                    data:{op:op,so_id:sid,ch_sort:ch_sort},
                                    success:function(result){
                                        if(result=="ok"){
                                            //alert("修改成功");
                                        }else{
                                            //alert("修改失败");
                                        }

                                    }

                                });
                           });

    $("#mul_del").click(function(){
        var ckIds = $("input[name='user_ids']:checked");
        if(ckIds.length == 0){
            alert("请先勾选要删除的合作商！");
            return;
        }
        if(confirm("是否确定删除选中的"+ckIds.length+"个合作商？")){
            var idsStr = "";
            ckIds.each(function(i,n){
                //console.log(" i : "+i+" n.value : "+n.value);
                if(i > 0){
                    idsStr += ",";
                }
                idsStr = idsStr + n.value;
            });
            var url = "supply.php?customer_id=<?php echo $customer_id_en;?>&user_ids="+idsStr+"&op=mul_del";
            location.href=url;
        }
    });

    $("#mul_virtualfans").click(function(){
        var ckIds = $("input[name='user_ids']:checked");
        if(ckIds.length == 0){
            alert("请先勾选要设置虚拟粉丝的合作商！");
            return;
        }
        //var virtual_fans_nums=$(obj).val();
        var mul_virtual_fans_nums = document.getElementById("mul_virtual_fans_nums").value;
        // 正整数var patrn= /^[0-9]*[1-9][0-9]*$/ ;
        // 0和正整数
        var patrn= /^([1-9]\d*|[0]{1,1})$/;
        if (!patrn.exec(mul_virtual_fans_nums)){
            alert("请输入0或正整数！");
            $(obj).val("0");
            return;
        }
        if(confirm("是否确定批量设置选中的"+ckIds.length+"个合作商的虚拟粉丝？")){
            var idsStr = "";
            ckIds.each(function(i,n){
                //console.log(" i : "+i+" n.value : "+n.value);
                if(i >= 0){
                    idsStr += ",";
                    $.ajax({
                        type: "post",
                        url: "ajax_virtualfans.php",
                        dataType: "json",
                        //begintime:begintime,endtime:endtime,
                        data: {supplys_id: n.value,vf_flag:1,virtual_fans_nums:mul_virtual_fans_nums},
                        success: function (result) {
                        }
                    });
                }
            });
            alert("设置成功！")
            var url = "supply.php?customer_id=<?php echo $customer_id_en;?>";
            location.href=url;
        }
    });

    $("#is_open_batch").click(function(){
        var ckIds = $("input[name='user_ids']:checked");
        if(ckIds.length == 0){
            alert("请先勾选要开启苏宁开关的合作商！");
            return;
        }
        if(confirm("是否确定批量开启选中的"+ckIds.length+"个合作商的苏宁开关？")){
            ckIds.each(function(i,n){
                //console.log(" i : "+i+" n.value : "+n.value);
                if(i >= 0){
                    $.ajax({
                        type: "post",
                        url: "ajax_suning_set.php",
                        dataType: "json",
                        //begintime:begintime,endtime:endtime,
                        data: {supplys_id: n.value,op:'open'},
                        success: function (result) {

                        }
                    });
                }
            });
            alert("开启成功！")
            var url = "supply.php?customer_id=<?php echo $customer_id_en;?>";
            location.href=url;
        }
    });

    $("#ck_all").click(function(){
        $("input[name='user_ids']").attr("checked",this.checked);
    });
    $("input[name='user_ids']").click(function(){
        if(!this.checked){
            $("#ck_all").attr("checked",this.checked);
        }
    });
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

<?php mysql_close($link);?>
 <script type="text/javascript" src="/weixinpl/common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="/weixinpl/common/js_V6.0/content.js"></script>
</body>
</html>