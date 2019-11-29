<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

// 数据库操作类
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();

// 连接数据库
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=8;//头部文件  0基本设置,1提现记录,2供应商管理
require('../../../../weixinpl/auth_user.php');

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

$user_id = $database->init($_REQUEST['user_id']);

$sql = "SELECT * 
        from weixin_commonshop_wholesaler_logs l
        left join weixin_commonshop_wholesalers w on w.user_id = l.user_id
        where l.customer_id='{$customer_id}' and l.user_id='{$user_id}'
        limit {$start},{$end}";
$data = $database->getData($sql);
// var_dump($data);
// echo $sql;
$op="";

$sql = "SELECT count(1) 
        from weixin_commonshop_wholesaler_logs l
        left join weixin_commonshop_wholesalers w on w.user_id = l.user_id
        where l.customer_id='{$customer_id}' and l.user_id='{$user_id}' ";
$rcount_q2 = $database->getField($sql);

?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>合作商-区域批发商户管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<link rel="stylesheet" type="text/css" href="../../../css/inside.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script charset="utf-8" src="inputexcel.js"></script>
<script src="../../../common/js/floatBox.js"></script>
<style> 

tr {
    line-height: 22px;
}
.inventory{
    color:#06A7E1;
}
</style>
<title>区域批发商日志</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>  
    <!--内容框架-->
    <div class="WSY_content"> 
        <!--列表内容大框-->
        <div class="WSY_columnbox">
            <!--列表头部切换开始-->
            <div class="WSY_column_header">
                <div class="WSY_columnnav">
                <a href="javascript:;" class='white1'>区域代理商管理日志</a>    
                </div>
            </div>
            <!--列表头部切换结束-->
            <div class="WSY_remind_main"> 
                <form action="/weixin/plat/app/index.php/Excel/js_excel" method="p"></form>
                <table width="97%" class="WSY_table" id="WSY_t1">
                    <thead class="WSY_table_header">
                        <th width="8%">商家编号</th>
                        <th width="8%">公司名称</th>
                        <th width="8%">类型</th>
                        <th width="8%">日志描述</th>
                        <th width="8%">操作时间</th> 
                    </thead>
                    <tbody>
                       <?php foreach ($data as $key => $value) {  
                        $switch = array('审核中','通过','驳回');
                        $value['type_str'] = $database->switchReplace($value['type'],$switch);
                        ?>

                        <tr>
                            <td><?php echo $value['user_id']; ?></td>
                            <td><?php echo $value['wholesaler_intro'];?></td>
                            <td><?php echo $value['type_str'];?></td>
                            <td><?php echo $value['remark'];?></td>
                            <td><?php echo $value['createtime'];?></td>
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
<script type="text/javascript" src="../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>

<script>

var pagenum = '<?php echo $pagenum ?>';
var user_id = '<?php echo $user_id ?>';
 var rcount_q2 = '<?php echo $rcount_q2 ?>';
 var end = '<?php echo $end ?>';
  var count =Math.ceil(rcount_q2/end);//总页数
    //pageCount：总页数
    //current：当前页
    $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
             document.location= "area_supply_log.php?pagenum="+p+"&user_id="+user_id+"&customer_id=<?php echo $customer_id_en;?>";
       }
    }); 
    var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
    var a=parseInt($("#WSY_jump_page").val());
    if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
        return false;
    }else{
             document.location= "area_supply_log.php?pagenum="+a+"&user_id="+user_id+"&customer_id=<?php echo $customer_id_en;?>";
         }
  }

  </script>

<?php mysql_close($link);?> 
 <script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>