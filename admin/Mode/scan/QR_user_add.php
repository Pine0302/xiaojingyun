<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
  
	$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	_mysql_query("SET NAMES UTF8");
	require('../../../../weixinpl/proxy_info.php');

	$keyid = -1;
	$op = "";
	$stu="";
  
	if(!empty($_GET["stu"])){
		$stu = $configutil->splash_new($_GET["stu"]);
	}

	if(!empty($_GET["keyid"])){
		$keyid = $configutil->splash_new($_GET["keyid"]);
	}
 
	if(!empty($_GET["op"])){

	$op = $configutil->splash_new($_GET["op"]); 
  
   if($op=="del"){         
     $query = 'update weixin_commonshop_ticketclerk set isvalid=false where id='.(int)$keyid;
	 _mysql_query($query);
	 $error =mysql_error();
	 mysql_close($link);
	 //echo $error;
	 echo "<script>location.href='QR_user.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
	 return;
  }
} 
		if($keyid>0&&$stu=='rs'){
			if(!empty($_GET["keyid"])){
			$keyid = $configutil->splash_new($_GET["keyid"]);
		}
		
		$newpassword=888888;
		$query="update weixin_commonshop_ticketclerk set password='".md5($newpassword)."'  where id=".$keyid."";
		_mysql_query($query) or die('Query failed1: ' . mysql_error()); 
		echo "<script>alert('密码重置为888888')</script>";
		echo "<script>location.href='QR_user.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
		return;
		
		}elseif($keyid>0&&$stu==""){
			$query='select name,password,logo_img from weixin_commonshop_ticketclerk where isvalid=true and customer_id='.$customer_id.' and id='.$keyid;
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
			while ($row = mysql_fetch_object($result)) {				
				$name=  $row->name;
				$password=   $row->password;
				$logo_img= $row->logo_img;
			}
		}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<title>兑票员设置</title>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<script>
 function check(num){
	var check_num=/^[0-9]*$/.test(num);
	return check_num;
}	

 function submitV(a){
	if($(a).hasClass("disable")){
        return;
    }
	var name = document.getElementById("name").value;
	if(name==""){
	    alert('请输入用户名!');
	   return;
	}
	
	var password = document.getElementById("password").value;
	if(password==""){
	    alert('请输入密码!');
	   return;
	}

	document.getElementById("upform").submit();	   
 } 

</script>
<style type="text/css">
.WSY_member textarea {
width: 350px;
height: 150px;
}
dt{
	margin-top:6px;
}
.spa{
  position: relative;
  right: 32px;
}
</style>
<body>
<div class="div_new_content">
<form action="QR_user_save.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
	<input type="hidden" name="keyid" value="<?php echo $keyid ?>" />
    <div class="WSY_content">
		<div class="WSY_columnbox">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1"><?php if($keyid>0){echo "添加";}else{echo "修改";} ?>兑票员</a>
				</div>
			</div>

			<div class="WSY_data">
				<dl class="WSY_member">
					<dt>用户名</dt>
					<dd class="spa"><span ><?php echo $customer_id."-";?></span><input type="text" value="<?php echo $name ?>" name="name" id="name" style="width:250px;" onkeyup="this.value=this.value.replace(/^ +| +$/g,'')" /></dd><dd>长度为1~16位字符</dd>
				</dl>
				<dl class="WSY_member">
					<dt>密码</dt>
					<dd><input type=text value="<?php echo $password ?>" name="password" id="password" style="width:250px;" <?php if($password!=""){echo "disabled='disabled'";}?>/></dd><dd>长度为6~16位字符，可以为“数字/字母/中划线/下划线”组成</dd>
				</dl>
				<?php   if($keyid>0){?>
				<dl class="WSY_member">
					<dt>修改密码</dt>
					<dd><input type=text value="" name="newpassword" id="newpassword" style="width:250px;" /></dd>
				</dl>
				<?php }?>
				<dl class="WSY_member">
                    <dt>添加logo图片</dt>
                    <div class="WSY_memberimg">
						<?php if($logo_img!=""){?>
                        <img src="<?php echo $logo_img; ?>" style="width:170px;height:auto;">
						<?php }else{ ?>
						<img src="../../../pic/uniqlo.png" style="width:64px;height:64px;">
						<?php } ?>
                        <span>(图片尺寸：170px*50px）</span>
                        <!--上传文件代码开始-->
                        <div class="uploader white">
                            <input type="text" class="filename" readonly/>
                            <input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="upfile1" id="upfile1" type=file value="<?php echo $logo_img ?>">
							<input type=hidden value="<?php echo $logo_img ?>" name="logo_img" id="logo_img" /> 
                        </div>
                        <!--上传文件代码结束-->
                    </div>
                </dl>
				<div class="WSY_text_input01">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
				</div>
			</div>
	
		</div>
		
	</div>
 </form>

<div style="width:100%;height:20px;">
</div>
</div>	
<!--内容框架结束-->
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>

<?php mysql_close($link);?>	
</html>