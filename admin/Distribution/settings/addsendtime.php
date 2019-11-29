<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');
  $customer_id = passport_decrypt($customer_id);
  require('../../../../weixinpl/back_init.php');
 	$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
 	mysql_select_db(DB_NAME) or die('Could not select database');
  require('../../../../weixinpl/proxy_info.php');
 	_mysql_query("SET NAMES UTF8");
  $keyid = 0;
  $len = count($_GET);
  $op = "";
  if($len>0){
     if(!empty($_GET["keyid"])){
	   $keyid = $_GET["keyid"];
	 }
     if($len>1){
       if(!empty($_GET["op"])){
	      $op = $configutil->splash_new($_GET["op"]);
	   }
	 }
  }
  if($op=="del"){
     $query = 'update weixin_sendtimes set isvalid=false where id='.(int)$keyid;
	 _mysql_query($query);
	 //$error =mysql_error();
	 echo "<script>location.href='sendtimes.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
	 return;
  }
  $title = "";
  if($keyid>0){
	$query = 'SELECT id,title FROM weixin_sendtimes where id='.$keyid;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$title =  $row->title ;
	}
  }
  mysql_close($link);
 
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../js/tis.js"></script>

<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
a:hover{text-decoration: none;}   
.button_blue{cursor: pointer;margin-left: 10px;font-size: 14px;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:20px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.name{  margin-top: 10px;height: 30px;line-height: 30px;font-size: 13px;text-align: left;font-weight: bolder;margin-left: 19px;}
.button_box{width: 296px;display: block;text-align: right;}
.button_box .WSY_button{border-radius:2px;border:none;}
</style>
</head>

<script>
 function submitV(){
    var title = document.getElementById("title").value;
	title_s = title.replace(/\s/g, "");
	if(title_s==""){
	    alert('请输入时间!'); 
	   return; 
	} 
    document.getElementById("keywordFrm").submit();
 }


</script>
<body>
<div >  
    <div class="WSY_content">
		<div class="WSY_columnbox">
		<div class="WSY_column_header">
					<div class="WSY_columnnav">
						<a  class="white1">添加送货时间</a>
					</div>
		</div>  
<form action="savesendtime.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>" enctype="multipart/form-data" id="keywordFrm" method="post">
    <!--<div class="name">
	    添加送货时间
	</div>-->
	<div id="products" class="r_con_wrap">
		<div style="margin-top:20px">
			<label>时间：</label>
			<span class="input">
			<input type=text value="<?php echo $title ?>" style="width:250px;height:24px;" name="title" id="title" />&nbsp;如：（7:00~18:00）
			</span>
		</div>
			<span class="button_box">
			<input type=button class="WSY_button"  value="提交" onclick="submitV();"  style="float:none"/>
			&nbsp;	
			<input type=button class="WSY_button"  value="取消" onclick="document.location='sendtimes.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>';" style="float:none" />
			</span>
		</div> 
		<input type=hidden name="keyid" value="<?php echo $keyid ?>" />
	 </div>
	</div>
 </form>
<div style="width:100%;height:20px;">
</div>
</div>
</div>
</div>
<script>
function setIssn(obj){
    if(obj.checked){
       document.getElementById("issn").value=1;
	}else{
	   document.getElementById("issn").value=0;
	}
 }
</script>
</body>
</html>

