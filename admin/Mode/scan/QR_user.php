<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);  //解密

require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require('../../../../weixinpl/proxy_info.php');
$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $_GET["pagenum"];
}

$start = ($pagenum-1) * 20;
$end = 20;
  $query = 'select id,name,password,confirm_name from weixin_commonshop_ticketclerk where isvalid=true and customer_id='.$customer_id." order by id desc limit ".$start.",".$end; 
  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
  
  $query_num = 'select count(1) as rcount from weixin_commonshop_ticketclerk where isvalid=true and customer_id='.$customer_id; 
  $result_num = _mysql_query($query_num) or die('Query failed_num: ' . mysql_error());
	while ($row = mysql_fetch_object($result_num)) {
		$rcount_num =$row->rcount; 
		}
 
$page=ceil($rcount_num/$end);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>兑换员</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
</head>

<body>

	<div class="WSY_content">

		<div class="WSY_columnbox">
			
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="index.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >二维码发送设置</a>
					<a href="QR_user.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" class="white1">兑票员设置</a>
					<a href="QR_user_login.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >兑票员登录日志</a>
					<a href="QR_user_check.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" >兑票员扫码日志</a> 
				</div>
			</div>			

		<div class="WSY_data">

              <div class="WSY_list" id="WSY_list" style="min-height: 500px;">
                    <div class="WSY_left" ><a>用户列表</a>
                    </div>
                    
                    <ul class="WSY_righticon">
                       <li><a href="QR_user_add.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>">添加用户</a></li>
                        <!--<li class="WSY_inputicon"><input type="button" value="批量删除"></li>-->  
                    </ul>
                    <br class="WSY_clearfloat">

        <table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
          <thead class="WSY_table_header">
            <th width="3%"><input id="s" onclick="$(this).attr('checked')?checkAll():uncheckAll()" type="checkbox"></th>
            <th width="5%">ID</th>
            <th width="10%">用户名</th>
		<!-- 	<th width="20%">密码</th> -->
        
            <th width="25%">操作</th>
          </thead>
		  <?php
			$keyid = -1;
	
		   while ($row = mysql_fetch_object($result)) {
				
				$keyid= $row->id;
				//$name=  $row->name;
				$confirm_name=$row->confirm_name;
				
		?>
          <tr>
            <td><input type="checkbox" name="code_Value" value="<?php echo $keyid; ?>"></td>
			 <td align="center"><?php echo $keyid; ?></td>
            <td align="center"><?php echo $confirm_name; ?></td>
			
            <td class="WSY_t4">

				<a href="QR_user_add.php?keyid=<?php echo $keyid ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" style="cursor:pointer;" class="WSY_operation" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
				
				<a href="QR_user_add.php?keyid=<?php echo $keyid ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&stu=rs" style="cursor:pointer;" class="WSY_operation" title="重置密码"><img src="../../../common/images_V6.0/operating_icon/icon01.png"></a>
			
                <a href="javascript: G.ui.tips.confirm('您确定删除吗？','QR_user_add.php?keyid=<?php echo $keyid ?>&op=del&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>');" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>   
				
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
<script>
 /* var pagenum = <?php echo $pagenum ?>;
  var count =<?php echo $page ?>;//总页数
  	//pageCount：总页数
	//current：当前页
	
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 document.location= "QR_user.php?pagenum="+p+"&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>";
	   }
    });

  var page = <?php echo $page ?>;
  
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "QR_user.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;
	}
  }	*/
  
  <!-- 分页 --start--> 
	var customer_id = "<?php echo $customer_id;?>";
	var pagenum = <?php echo $pagenum ?>;
	var count =<?php echo $page ?>;//总页数
	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
		pageCount:count,
		current:pagenum,
		backFn:function(p){
		document.location= "QR_user.php?customer_id=<?php echo $customer_id; ?>&pagesize=<?php echo $pagesize; ?>&pagenum="+p;
	   }
	});

  var page = <?php echo $page ?>;
  
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "QR_user.php?customer_id=<?php echo $customer_id; ?>&pagesize=<?php echo $pagesize; ?>&pagenum="+a;
	}
  }
<!-- 分页 --end-->
</script>	
</body>
</html>
