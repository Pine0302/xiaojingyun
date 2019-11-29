<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");
$head=4;		
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

$op = "";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]); 
   $keyid  =-1;
   if(!empty($_GET["keyid"])){
	   $keyid = $configutil->splash_new($_GET["keyid"]);
	 }
   if($op=="del"){
       $query = 'update weixin_commonshop_subscribes  set isvalid=false where id='.(int)$keyid;
	   _mysql_query($query);
   }
}
//$customer_id = $configutil->splash_new($_GET["customer_id"]);  //引入文件中已获取
$rcount_q = 0;

 $query="select id,subscribe_id,need_score,imgurl,is_needmember from weixin_commonshop_subscribes where isvalid=true and customer_id=".$customer_id;

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$rcount_q = mysql_num_rows($result);

$query_num = "select count(1) as rcount from weixin_commonshop_subscribes where isvalid=true and customer_id=".$customer_id; 
$result_num = _mysql_query($query_num) or die('Query failed_num: ' . mysql_error());
while ($row = mysql_fetch_object($result_num)) {
		$rcount_num =$row->rcount;
		}
 
$page=ceil($rcount_num/$end);
//echo $rcount_q;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<style>
#caozuo a img{
	width: 18px;
    height: 18px;
    vertical-align: baseline;
    display: inline-block;
    float: none;	
}
#caozuo{
	height: 80px;
	padding-top: 20px !important;
    padding-bottom: 20px !important;
	
}
#caozuo a{
	display: inline-block;
	margin-right: 10px;
}
</style>
</head>
<body>

	<div class="WSY_content">


		<div class="WSY_columnbox">

		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
		?>		
			<div class="WSY_data">

              <div class="WSY_list" id="WSY_list" >
                    <div class="WSY_left" ><a></a>
                    </div>
                    
                    <ul class="WSY_righticon">
                       <li><a href="addshop_subscribe.php?customer_id=<?php echo $customer_id_en; ?>">添加微商城功能项</a></li>
                      
                    </ul>
                    <br class="WSY_clearfloat">

			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			  <thead class="WSY_table_header">
				<th width="3%"><input id="s" onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()" type="checkbox"></th>
				<th width="5%">ID</th>
				<th width="10%">图文编号</th>
				<th width="10%">小图标</th>
				<th width="8%">是否需要推广员身份</th>
			   
				<th width="8%">操作</th>
			  </thead>
<?php
			
  if(!empty($result)){
   while ($row = mysql_fetch_object($result)) {
	   $keyid =  $row->id ;
	   $subscribe_id = $row->subscribe_id;
	   $need_score = $row->need_score;
	   $imgurl = $row->imgurl;
	  
	   //是否需要推广员身份
	   $is_needmember = $row->is_needmember;
	   
	   $isneedstr="不需要";
	   if($is_needmember){
	     $isneedstr="需要";
	   }
	   
	   $query2="select title from weixin_subscribes where isvalid=true and id=".$subscribe_id;
	    $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());  
	  $title="";
	  while ($row2 = mysql_fetch_object($result2)) {
	      $title = $row2->title;
		  break;
	  }
	 
?>
		
          <tr >
            <td><input type="checkbox" name="code_Value" value="<?php echo $keyid; ?>"></td>
			 <td align="center"><?php echo $keyid; ?></td>
            <td ><?php echo $title; ?></td>
            <td ><img src="<?php echo $imgurl ?>" style="width:80px;height:80px;" /></td>
            <td ><?php echo $isneedstr; ?></td>
            
            <td id="caozuo">

				<a href="addshop_subscribe.php?shop_subscribe_id=<?php echo $keyid ?>&customer_id=<?php echo $customer_id_en; ?>" style="cursor:pointer;" class="WSY_operation" title="编辑"><img src="../../../../common/images_V6.0/operating_icon/icon05.png"></a>
			
                <a href="javascript: G.ui.tips.confirm('您确定删除吗？','shop_subscribes.php?keyid=<?php echo $keyid ?>&op=del&customer_id=<?php echo $customer_id_en; ?>&pagenum=<?php echo $pagenum;?>');" title="删除"><img src="../../../../common/images_V6.0/operating_icon/icon04.png"></a> 
				
            </td>
          </tr>
		  <?php
  
				}
			}
			mysql_close($link);
			?>
        </table>
        <!--表格结束-->
        
        <!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
        </div>
		</div>
		</div>
		<div style="width:100%;height:20px;"></div>
	</div>

<script src="../../../../js/fenye/jquery.page1.js"></script>
<script>
  var pagenum = <?php echo $pagenum ?>;
  var count =<?php echo $page ?>;//总页数
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 document.location= "shop_subscribes.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+p;
	   }
    });
</script>	
<script>
  var pagenum = <?php echo $pagenum ?>;
   var page = <?php echo $page ?>;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "shop_subscribes.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+a;
	}
  }
</script>	
<script type="text/javascript" src="../../../../promotion/ZeroClipboard.js"></script>
<script>

</script>
</body>
</html>
