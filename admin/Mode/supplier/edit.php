<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');


$user_id   = $configutil->splash_new($_GET["user_id"]);
$category  = $configutil->splash_new($_GET["category"]);

if(empty($user_id) || empty($category))
{
	header("location:supply.php");
}	

$sql       = " select u.id,u.name,u.weixin_name from weixin_commonshop_applysupplys as he left join weixin_users as u on u.id=he.user_id where he.isvalid=1 and he.user_id=$user_id";
$result    = _mysql_query($sql) or die('Query failed: ' . mysql_error());
while($row = mysql_fetch_object($result)) 
{
	$id	  		 = $row->id;
	$name		 = $row->name;
	$weixin_name = $row->weixin_name;
}

$p_name = "";
$p_weixin_name = "";

$pare_sql  = "select parent_id from weixin_attract_investment_user where inverst_id = $user_id and category = $category limit 1";
$pare_res  = _mysql_query($pare_sql) or die('Query failed3: ' . mysql_error());
while($row = mysql_fetch_object($pare_res)) 
{
	$p_parent_id = $row->parent_id;	
}
if($p_parent_id>0){
	$pare_sql2  = "select name,weixin_name from weixin_users where id=$p_parent_id and isvalid=1 limit 1";
	$user_res  = _mysql_query($pare_sql2) or die('Query failed4: ' . mysql_error());
	while($row = mysql_fetch_object($user_res)) 
	{	
		$p_name = $row->name;
		$p_weixin_name = $row->weixin_name;	
	}
}


mysql_close($link);
?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>修改推荐人</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<style type="text/css">
	.content{padding-left:50px; padding-top: 50px}
	.xinxi{height: 30px;padding: 5px 20px; font-size: 16px;}
	.pr_input{ border:1px solid #333; height: 20px; width: 100px; font-size: 14px; padding-left: 2px; }
	.xinxi span{ display: inline-block;; padding-left: 15px;  font-size: 14px; }
	.xinxi span.xiugai{ display: inline-block;; padding-left: 25px; color: #f00; font-size: 14px; }
	.submit{ border-radius: 3px ;color: #fff; font-size: 16px;  width: 150px; height: 40px; background: #06a7e1;line-height: 40px; text-align: center;margin-bottom: 50px;margin-left: 20px;margin-top: 25px;cursor: pointer; }
</style>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a href="javascript:void(0)">招商推荐人</a>
				</div>
			</div>
			<!--列表头部切换结束-->
			<form method="post" action="edit_save.php?customer_id=<?php echo $customer_id_en; ?>">
				<div class="content">
					<div class="xinxi">合作商编号：<?php echo $id ?></div>
					<div class="xinxi">合作商姓名：<?php echo $name ?>(<?php echo $weixin_name ?>)</div>
					<div class="xinxi">招商推荐人：<input id="textID" placeholder="输入推广员ID" type="number" name="p_parent_id" class="pr_input" value="<?php echo $p_parent_id!=-1 && $p_parent_id!='' ? $p_parent_id : '' ?>"> 
					<span class="tui"></span> 
					<a style="color:#000000">(-1或空表示没有推荐人)</a>
					<span class="xiugai"><a style="color:#f00" href="edit_detail.php?customer_id=<?php echo $customer_id_en ?>&inverst_id=<?php echo $id ?>&category=<?php echo $category ?>">(更改推荐人详情)</a></span></div>
					<input type="hidden" value="<?php echo $category ?>" name="category" class="category" />
					<input type="hidden" value="<?php echo $id ?>" name="inverst_id" class="inverst_id" />
					<div class="submit">保存</div>
				</div>
			</form>	
		</div>
	</div>
<script type="text/javascript" src="../../Common/js/Reward/Commission/investment.js?131"></script>
<script type="text/javascript">
var customer_id = '<?php echo $customer_id ?>';
var p_weixin_name = '<?php echo $p_weixin_name ?>';
var p_name = '<?php echo $p_name ?>';
init($('.pr_input'),p_name,p_weixin_name);
// $(".pr_input").blur(function(){
// 		var parent_id = $('.pr_input').val();
	
// 	var obj = $('.pr_input');
// 	check_promoter(parent_id,customer_id,obj);
	
	

// });


$(".submit").on("click",function()
{	 
	var act = document.activeElement.id;
if(act == "textID" ){
	alert("true");
}else{
	var parent_id = $('.pr_input').val();
	
	var obj = $('.pr_input');
    check_promoter(parent_id,customer_id,obj);

}

	var parent_id = $('.pr_input').val();
	var category  = $(".category").val();
	var inverst_id   = $(".inverst_id").val();

	if(category == '' || inverst_id == '')
	{
		alert('参数丢失');
		return false;
	}
	// $(this).unbind();//解绑防止重复提交
	
	var op  = 'change';
	var url = '/weixinpl/back_newshops/Mode/supplier/edit_invest.php?customer_id='+customer_id;
	$.post(url,{op:op,parent_id:parent_id},function(da){
		if(da.status == 0){	
			alert(da.msg);
			return false;
		}else{
			$(this).unbind();//解绑防止重复提交
			$("form").submit()
			}
	},'json');
		
	

});
</script>
</body>
</html>