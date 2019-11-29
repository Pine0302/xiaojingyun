<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");

$pageindex = 0;
$pagenum = 1;
$pagesize = 20;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagesize;
$end = $pagesize;

$query = "select id,pro_id,asort from weixin_commonshop_guess_you_like where isvalid=true and customer_id=".$customer_id." order by asort desc ";

$query2 = $query;
$query .= " limit ".$start.", ".$end ."";

  /* 输出数量开始 */
$result = _mysql_query($query2) or die('L26 Query failed2: ' . mysql_error());
$rcount_q = mysql_num_rows($result);
$page=ceil($rcount_q/$end); 


 /* 输出数量结束 */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>购物车猜您喜欢设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<style>
.WSY_left .flo_left a:hover {
    background-color: #fff;
    border-bottom: solid 2px #06a7e1;
}
td img{
	width:100px;
	margin: 5px;
}
.btn{
	font-size: 14px;
    line-height: 20px;
    padding-left: 15px;
    padding-right: 15px;
    border-radius: 3px 3px 3px 3px;
    margin-right: 30px;
    color: #fff;
}
.btn:hover{
	color: #fff;

}
.search{
	width: 180px;
    height: 25px;
	border: 1px solid #ccc;
    border-radius: 5px;
    text-indent: 10px;
}
#sel{
	margin-top: 4px;

}
.WSY_righticon_li02{
	margin-right: 30px;
    line-height: 30px;
}
	
.top{

	width: 108px;
    height: 16px;
}	
</style>
</head>


<body>

	<div class="WSY_content">
		<div class="WSY_columnbox">
		<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">猜您喜欢设置</a>
				</div>
		</div>
		<div class="WSY_data">

        <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
 
			<ul class="WSY_righticon" >
			
				<li>
					<div>
					<span class="btn WSY-skin-bg" onclick="location.href='add_guess_you_like.php?customer_id=<?php echo $customer_id_en ;?>'">添加</span>
					</div>
				</li>
			</ul>	
			<ul class="WSY_righticon">	
				<!-- <li><a href="add_index.php?customer_id=<?php echo $customer_id_en ;?>">添加参赛者</a></li> -->
						   
			</ul> 
        <br class="WSY_clearfloat">

        <table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
          <thead class="WSY_table_header">
            	<tr style="border:none">
					<th width="4%" >ID</th>
					<th width="8%" >排序</th>
					<th width="12%">产品名称</th>
					<th width="12%">产品图片</th>
					<th width="12%">编辑</th>
					
				</tr>
          </thead>
		  <?php
		    
		
					$keyid 	= 0;
					$asort  = 0;
					$pro_id = 0;
					$result = _mysql_query($query) or die('L26 Query failed2: ' . mysql_error());					
					while ($row = mysql_fetch_object($result)) {
						$keyid 	= $row->id;
						$pro_id = $row->pro_id;
						$asort  = $row->asort;
						
					//查询产品信息
					$query1 = "select name,default_imgurl from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and id=".$pro_id." ";
					//echo $query1;
					$name 				= '';
					$default_imgurl 	= '';
					$result1=_mysql_query($query1)or die('L102 Query failed'.mysql_error());
					while($row1=mysql_fetch_object($result1)){
						$name 			= $row1->name;
						$default_imgurl = $row1->default_imgurl;
						break;
					}
				
				
				
		?>
          
		  
		    <tr class="WSY_q1">
					<td align="center"><?php echo $keyid;?></td>
					<td align="center">
						<a  style="cursor:pointer;" class="WSY_operation"  title="修改确认"><img style="float: right;width: 20px; display:none;" class="edit_fonfirm" src="../../../common/images_V6.0/operating_icon/icon23.png"></a>
						<a  style="cursor:pointer;" class="WSY_operation"  title="修改票数"><img style="float: right;width: 20px;" class="edit_top" src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
						<input style="border:1px solid #CCC;height: 22px;background: white;"  type="text" name="top" class="top" value="<?php echo $asort; ?>" disabled="true"/>
						<input type="hidden" name="ed_id" class="ed_id" value="<?php echo $keyid; ?>" />
						<input type="hidden" name="user_id" class="user_id" value="<?php echo $user_id; ?>" />
					</td>
					<td align="center"><?php echo $name;?></td>
					<td align="center"><img src="<?php echo "//".CLIENT_HOST.$default_imgurl ;?>"></td>
					<td class="WSY_t4">
						<a href="add_guess_you_like.php?keyid=<?php echo $keyid ?>&customer_id=<?php echo $customer_id_en; ?>" style="cursor:pointer;" class="WSY_operation" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>						
						<a href="javascript: G.ui.tips.confirm('您确定删除吗？','add_guess_you_like.php?keyid=<?php echo $keyid ?>&op=del&customer_id=<?php echo $customer_id_en; ?>&obj_id=<?php echo $obj_id; ?>');" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a> 
				   
					</td>
			</tr>
			
		  <?php
  
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

<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
 var pagenum = <?php echo $pagenum;	 ?>;
  var count =<?php echo $page ?>;//总页数
	//pageCount：总页数
	//current：当前页
	
	$(".WSY_page").createPage({
		pageCount:count,
		current:pagenum,
		backFn:function(p){
		 document.location= "guess_you_like.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+p;
	   }
	});

  var page = <?php echo $page ?>;
  
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "guess_you_like.php?customer_id=<?php echo $customer_id_en ?>&pagenum="+a;
	}
  }	
		  
		  
		  
$('.edit_top').click(function(){
	$(this).parent().parent().find('.edit_fonfirm').show();
	$(this).parent().parent().find('.top').removeAttr("disabled");
	
	});	
	
$('.edit_fonfirm').click(function(){
	var self = $(this);
	var _val = self.parent().parent().find('.top').val();
	console.log(_val);
	console.log(self);
	var num_reg = /^[0-9]*$/;		
	if(!num_reg.test(_val)){alert('请输入数字!');return false;}
	var ed_id = self.parent().parent().find('.ed_id').val();
	//console.log(ed_id);
	$.post(
		"save_guess_you_like.php?keyid="+ed_id+"&customer_id=<?php echo $customer_id ?>&op=asort_edit",
		{asort:_val},
		function(res){
			if(res.code=='10001'){
				alert('修改成功！');
				self.parent().parent().find('.top').attr('disabled',true);				
				self.hide();				
			}else{
				alert('修改失败，请重试！');
			}
		}
	,'json'); 
	});
	
</script>		   

<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">

<?php 

mysql_close($link);
?>

</body>
</html>
