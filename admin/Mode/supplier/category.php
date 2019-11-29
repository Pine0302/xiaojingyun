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
$head=7;//头部文件  0基本设置,1提现记录,2供应商管理
require('../../../../weixinpl/auth_user.php');
$op="";



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

$sql = "SELECT * from weixin_commonshop_area_category where isvalid=true AND customer_id='{$customer_id}' limit {$start} , {$end}";
$data = $database->getData($sql);

$sql = "SELECT count(1) from weixin_commonshop_area_category where isvalid=true AND customer_id='{$customer_id}'";
$rcount_q2 = $database->getField($sql);
// var_dump($data);

$op = 3;
?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>合作商-品牌商户管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script charset="utf-8" src="inputexcel.js"></script>
<script src="../../../common/js/floatBox.js"></script>
<title>品牌合作商管理</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
.topName{font-size: 20px;margin: 20px;color: #323232;font-weight: bold;width: 70%;display: inline-block;vertical-align: middle;}
.topBtn{width: 200px;vertical-align: middle;}
.list{display: inline-block;vertical-align: top;margin: 50px;height: 400px;}
.list1{width: 50%;}
.list2{width: 30%; border:1px solid #d8d8d8;}
.list2 .name{width: 95%;margin: 10px auto;font-size: 15px;}
.list2 .cell{width: 95%;margin: 20px auto;}
.list2 .cell .cellname{width: 25%;font-size: 15px;display: inline-block;}
.list2 .cell input{width: 60%;border: 1px solid #d8d8d8;border-radius: 3px;height: 25px;}
.list2 .cell .hint{width: 70%;position: relative;left: 25%;font-size: 15px;color: #ff0000;margin: 5px;}
.list2Btn{width: 150px;margin: 50px auto;display: block;}
</style>
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
                

                <p class="topName">类目设置</p>
                <div class="list list1">
                    <table width="97%" class="WSY_table WSY_t2" id="WSY_t1" style="margin-left:0;">
                        <thead class="WSY_table_header">
                           <th width="13%">类目名称</th>       
                           <th width="10%">排序</th>
                           <th width="13%">创建时间</th>       
                           <th width="8%">操作</th>
                        </thead>
                        <?php foreach ($data as $key => $value) { ?>
                        <tr>
                            <td ><?php echo $value['name'] ?></td>
                            <td ><?php echo $value['sort'] ?></td>
                            <td ><?php echo $value['createtime'] ?></td>
                            <td>
                            <a class="btn1" onclick='compile("<?php echo $value['id'] ?>","<?php echo $value['name'] ?>","<?php echo $value['sort'] ?>")' title="编辑">
                                <img src="../../../common/images_V6.0/operating_icon/icon05.png" align="absmiddle"/>
                            </a>
                            <a class="btn1" onclick='del("<?php echo $value['id'] ?>")'><img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除"></a>
                            
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <!-- <div class="list list2"> -->
                <form action="area_operation.php" id='upform' class="list list2" method="get" accept-charset="utf-8">
                    <input type="hidden" name="op" value="<?php echo $op ?>">
                    <input type="hidden" name="id" value="">
                    <p class="name">新经营类目</p>
                    <div class="cell">
                        <p class="cellname">类目名称</p>
                        <input type="text" name="name" maxlength="5">
                        <p class="hint">提示:名称不能重复,5字符内</p>
                    </div>
                    <div class="cell">
                        <p class="cellname">类目排序</p>
                        <input type="number" name="sort">
                        <p class="hint">提示:值大排前</p>
                    </div>
                    <button class="list2Btn" id='submit' onclick="return false;">保存</button>
                </form>
                <!-- </div> -->



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
 var rcount_q2 = '<?php echo $rcount_q2 ?>';
 var end = '<?php echo $end ?>';
  var count =Math.ceil(rcount_q2/end);//总页数
    //pageCount：总页数
    //current：当前页
    $(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
             // var search_user_id = document.getElementById("search_user_id").value; 
             // var search_brandstatus = document.getElementById("search_brandstatus").value; 
             // var search_name = document.getElementById("search_name").value; 
             // var search_phone = document.getElementById("search_phone").value; 
             document.location= "category.php?pagenum="+p+"&pagecount="+end+"&customer_id=<?php echo $customer_id_en;?>";
       }
    }); 
    var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
    var a=parseInt($("#WSY_jump_page").val());
    if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
        return false;
    }else{
             // var search_user_id = document.getElementById("search_user_id").value; 
             // var search_brandstatus = document.getElementById("search_brandstatus").value; 
             // var search_name = document.getElementById("search_name").value; 
             // var search_phone = document.getElementById("search_phone").value; 
             document.location= "category.php?pagenum="+a+"&pagecount="+end+"&customer_id=<?php echo $customer_id_en;?>";
         }
    }
    $('#submit').click(function(){
        var name = $.trim($('[name="name"]').val())
        var id = $('[name="id"]').val()
        if( name == '' ){
            alert('类目名称不能为空!')
            return false;
        }
        $.get('area_operation.php',{'op':5,'name':name,'id':id},function(data){
            if( data > 0 ){
                alert('类目名称重复!')
                return false;
            }else{
                $.post('area_operation.php',$('#upform').serialize(),function(data){
                    window.location.href = location;
                })
            }
        })
        

        // $.post('area_operation.php',$('#upform').serialize(),function(data){
        //     console.log(data)
        // })
    })

    function compile(id,name,sort){
        $('[name="id"]').val(id)
        $('[name="name"]').val(name)
        $('[name="sort"]').val(sort)
        $('[name="op"]').val(4)
    }

    function del(id){
        if(confirm("确定要删除此类目吗？")){
            $.post('area_operation.php',{'op':6,'id':id},function(data){
                if(data>0){
                    alert('已有区域批发商关联此类目,暂无法删除!')    
                }else{
                    window.location.href = location;
                }
            })
        }
    }
  </script>

<?php mysql_close($link);?> 
 <script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>