<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$head=3;

$op = '';
$business_id = -1;
if(!empty($_GET['op'])){
	if(!empty($_GET['business_id'])){
		$business_id = $_GET['business_id'];
	}
	$op = $_GET['op'];
	switch($op){
		case 'pass':
			$sql = "update pcshop_merchants_settled_member set status=1 where isvalid=true and customer_id=".$customer_id." and id=".$business_id;
		break;
		case 'fail':
			$sql = "update pcshop_merchants_settled_member set status=-1 where isvalid=true and customer_id=".$customer_id." and id=".$business_id;
		break;
		case 'delect':
			$sql = "update pcshop_merchants_settled_member set isvalid=0 where isvalid=true and customer_id=".$customer_id." and id=".$business_id;
		break;		
	}
		_mysql_query($sql) or die('SQL failed: ' . mysql_error());
}

//分页---start
$pagenum = 1;
$pagesize = 20;
$begintime="";
$endtime ="";
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * $pagesize;
$end = $pagesize;

$query = "select id,user_id,name,phone,email,company_name,company_management,status from pcshop_merchants_settled_member where isvalid=true and customer_id=".$customer_id." limit ".$start.",".$end."";
$query_num = "select count(1) as pcount from pcshop_merchants_settled_member where isvalid=true and customer_id=".$customer_id;
$pcount = 0;
$result_num = _mysql_query($query_num) or die('Query failed: ' . mysql_error());
while($row = mysql_fetch_object($result_num)){
	$pcount = $row->pcount;
}
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());

$page = ceil($pcount/$pagesize);

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商家入驻资料</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script src="../../Common/js/Data/js/echarts/echarts.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<style>

table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
table td img{width: 20px;height: 20px;margin-left: 5px;}
.mlt12{margin-left: 15px;margin-top: 22px;}
.WSY_position_date select {
    width: 130px;
    height: 24px;
    background: #fefefe;
    border: 1px solid #ccc;
    color: #333;
    border-radius: 2px;
    padding-left: 5px;
}
</style>

</head>

<body id="bod" style="min-height: 580px;">
	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			
				<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/merchants_settled/basic_head.php");
			?>
		
			<!--列表头部切换结束-->
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list">


        </div> 
        <!--列表按钮开始-->
		
        <!--表格开始-->
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;">
		
		<table class="WSY_t2"  width="97%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
				<tr style="border:none">
					<th width="2%" >ID</th>
					<th width="4%" >编号</th>
					<th width="6%">联系人姓名</th>
					<th width="6%">联系人手机号</th>
					<th width="6%">联系人电子邮箱</th>
					<th width="6%">公司名称</th>
					<th width="5%">公司经营</th>
					<th width="5%">状态</th>
					<th width="6%">操作</th>
				</tr>
			</thead>
			<tbody>	
				<?php 
					$id 				= -1;
					$user_id  			= -1;//用户id
					$name			  	= '';//联系人姓名
					$phone 				= '';//联系人电话
					$email 				= '';//联系人电子邮箱
					$company_name		= '';//公司名称
					$company_management = '';//公司经营
					$status 			= -1;//申请状态：0:申请中，1:已确认，-1:已驳回

					while ($row = mysql_fetch_object($result)) { 
						$id 				= $row->id;
						$user_id 			= $row->user_id;
						$name 				= $row->name;
						$phone 				= $row->phone;
						$email 				= $row->email;
						$company_name 		= $row->company_name;
						$company_management = $row->company_management;
						$status				= $row->status;
						if( $status == 0 ){
							$status_str = '待审核';
						} elseif( $status == 1 ){
							$status_str = '已通过';
						} else{
							$status_str = '未通过';
						}
				?>
				<tr>
					<td><?php echo $id;?></td>
					<td><?php echo $user_id;?></td>
					<td><?php echo $name;?></td>
					<td><?php echo $phone;?></td>
					<td><?php echo $email;?></td>
					<td><?php echo $company_name;?></td>
					<td><?php echo $status_str;?></td>
					<td><?php echo $company_management;?></td>					
					<td class="images">
					
						<a href="./edit_business_information.php?business_id=<?php echo $id;?>&customer_id=<?php echo $customer_id_en;?>">
							<img src="../../../common/images_V6.0/operating_icon/icon05.png" align="absmiddle" alt="编辑" title="编辑">
						</a>
						<?php if($status==0){ ?>
							<a href="./business_information.php?business_id=<?php echo $id;?>&customer_id=<?php echo $customer_id_en;?>&op=pass">
								<img src="../../../common/images_V6.0/operating_icon/icon07.png" align="absmiddle" alt="通过" title="通过">
							</a>
							<a href="./business_information.php?business_id=<?php echo $id;?>&customer_id=<?php echo $customer_id_en;?>&op=fail">
								<img src="../../../common/images_V6.0/operating_icon/icon03.png" align="absmiddle" alt="驳回" title="驳回">
							</a>
						<?php }elseif($status==1){ ?>
							<!-- <img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle" alt="已通过" title="已通过"> -->
						<?php }else{ ?>
							<!-- <img src="../../../common/images_V6.0/operating_icon/icon25.png" align="absmiddle" alt="未通过" title="未通过"> -->
							<a href="./business_information.php?business_id=<?php echo $id;?>&customer_id=<?php echo $customer_id_en;?>&op=pass">
								<img src="../../../common/images_V6.0/operating_icon/icon07.png" align="absmiddle" alt="通过" title="通过">
							</a>
						<?php } ?>
						<a href="./business_information.php?business_id=<?php echo $id;?>&customer_id=<?php echo $customer_id_en;?>&op=delect">
							<img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除" title="删除">
						</a>
					</td>
				</tr>
					<?php } ?>
			</tbody>
			
			</table>
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
		<script src="../../../js/fenye/jquery.page1.js"></script>
		<script type="text/javascript">
			var customer_id_en = '<?php echo $customer_id_en ?>';
			var pagenum = <?php echo $pagenum ?>;
			var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页

			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "business_information.php?customer_id="+customer_id_en+"&pagenum="+p;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());

			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "business_information.php?customer_id="+customer_id_en+"&pagenum="+a;
			}
		  }	
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">


<?php 

mysql_close($link);
?>

</body>
</html>
