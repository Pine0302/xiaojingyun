<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

$pagenum = 1;
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

?>
<!DOCTYPE html>
<!-- saved from url=(0047)//www.ptweixin.com/member/?m=shop&a=orders -->
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">	
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
</head>

<body>
<div id="WSY_content">
	<div class="WSY_columnbox" style="min-height: 300px;">
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">会员邀请人锁定模式修改日志</a>
			</div>
		</div>
		<div  class="WSY_data">
		<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			<thead class="WSY_table_header">
				<tr>
					<th width="25%" nowrap="nowrap">更改前的模式</th>
					<th width="25%" nowrap="nowrap">更改后的模式</th>
					<th width="25%" nowrap="nowrap">更改时间</th>
					<th width="25%" nowrap="nowrap">说明</th>
					
				</tr>
			</thead>
			<tbody>
			    <?php 
				
				$query = "select orgin_mode,change_mode,createtime,remark from weixin_commonshop_lockmode_change_logs where isvalid=true and customer_id=".$customer_id;
				
				 /* 输出数量开始 */
				 $rcount_q2=1;
				 $result2 = _mysql_query($query) or die('Query failed: ' . mysql_error());
				 $rcount_q2 = mysql_num_rows($result2);
				 /* 输出数量结束 */
				 
				 $query = $query." order by id desc"." limit ".$start.",".$end;
				 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	             while ($row = mysql_fetch_object($result)) {
				 
				    $orgin_mode =$row->orgin_mode;
					$change_mode = $row->change_mode;					
					$createtime=$row->createtime;
					$remark = $row->remark;
					
					$orgin_str = "";
					$change_str = "";
					switch($orgin_mode){
						case 1:
							$orgin_str = "下单后锁定";
							break;
						case 2:
							$orgin_str = "第一次邀请人锁定";
							break;
					}
					switch($change_mode){
						case 1:
							$change_str = "下单后锁定";
							break;
						case 2:
							$change_str = "第一次邀请人锁定";
							break;
					}
			   ?>
                <tr>
				   <td align="center"><?php echo $orgin_str; ?></td>
				   <td align="center"><?php echo $change_str; ?></td>
				   <td align="center"><?php echo $createtime; ?></td>
				   <td align="center"><?php echo $remark; ?></td>
				  
                </tr>				
			   <?php } ?>
			</tbody>
		</table>
		<div class="blank20"></div>
		<div id="turn_page"></div>
		</div>	
			<!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
	</div>
</div>
	

<?php 

mysql_close($link);
?>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
var customer_id = '<?php echo $customer_id_en ?>';
var user_id = <?php echo $user_id ?>;

var pagenum = <?php echo $pagenum ?>;
var rcount_q2 = <?php echo $rcount_q2 ?>;
var end = <?php echo $end ?>;
var count = Math.ceil(rcount_q2/end);//总页数
console.log(count);

var page = count;

  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			
		document.location= "lockmode_change_log.php?customer_id="+customer_id+"&pagenum="+p;
	   }
    });

  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val()); 
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
		document.location= "lockmode_change_log.php?customer_id="+customer_id+"&pagenum="+a;
		
	}
  }
</script>

</body></html>