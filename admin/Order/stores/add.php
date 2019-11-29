<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/back_newshops/Order/stores/config.php');
  $customer_id = passport_decrypt($customer_id);
  require('../../../../weixinpl/back_newshops/Order/stores/back_init.php');  
  
  require('../../../../weixinpl/back_newshops/Order/stores/common/utility.php');  
  $keyid = 0;
  $len = count($_GET);
  $del = "";
  $card_id = $configutil->splash_new($_GET["card_id"]);
  
  if($card_id<0){
     echo "<script>alert('请先填写会员卡基本信息!');location.href='addcard.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
     exit;
  }
  $keyvalue = "";
   if($len>0){
     if(!empty($_GET["keyid"])){
	   $keyid = $configutil->splash_new($_GET["keyid"]);
	 }
	 
     if($len>1){
       if(!empty($_GET["op"])){
	      $del = $configutil->splash_new($_GET["op"]);
	   }
	 }
  }
   $link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
     mysql_select_db(DB_NAME) or die('Could not select database');
	 _mysql_query("SET NAMES UTF8");
	//echo "del========".$del; 
	
	$is_auth_user = 0;
	$auth_user_id = -1;
	$shopids="";
	if(!empty($_SESSION["is_auth_user"])){
	   $is_auth_user = $_SESSION["is_auth_user"];
	   if(!empty($_SESSION["user_id"])){
			$auth_user_id = $_SESSION["user_id"]; 
	   }
	}
 
  if($del=="del"){
         
     //$query = 'delete from weixin_card_shops where id='.(int)$keyid;
	 $query = 'update weixin_card_shops set isvalid = false where id='.(int)$keyid;
	 //echo $query;
	 _mysql_query($query);
	 $error =mysql_error();
	 mysql_close($link);
	 //echo $error;
	 echo "<script>location.href='card_shop.php?customer_id=".passport_encrypt((string)$customer_id)."&card_id=".$card_id."';</script>";
	 return;
  }
  $name = "";
  $phone = "";
  $address = "";
  $type=1;
  $category_id = -1;
  $shop_id = -1;
  $store_number = -1;
  $mini_websiteurl="";
  $contactname="";
  $imgurl="";
  $description="";
  if($keyid>0){
	  
	$query = 'SELECT id,imgurl,description,name,phone,address,type,category_id,mini_websiteurl,contactname,store_number,location_p,location_c,location_a FROM weixin_card_shops where id='.$keyid;
	$result = _mysql_query($query) or die('Query failed68: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$name =  $row->name ;
		$phone =  $row->phone ;
		$address = $row->address;
		$shop_id = $row->id;
		$type = $row->type;
		$category_id = $row->category_id;
		$mini_websiteurl=$row->mini_websiteurl;
		$contactname = $row->contactname;
		$imgurl = $row->imgurl;
		$description = $row->description;
		$store_number = $row->store_number;
		$location_p=$row->location_p;
		$location_c=$row->location_c;
		$location_a=$row->location_a;
	}
  }
  
  if(empty($imgurl) or $imgurl==""){
     $imgurl="pic/shop.jpg";
  }else{
      $pos = strpos($imgurl,"//");
	  
      if($pos===0){
	  }else{
	      $imgurl = BaseURL.$imgurl;
	  }
  }
  $ulst = new ArrayList();
  $query="select customer_user_id from weixin_card_shop_auths where isvalid=true and card_shop_id=".$keyid;
  $result = _mysql_query($query) or die('Query failed96: ' . mysql_error());  
  $init_uids="";
  while ($row = mysql_fetch_object($result)) {
      $u_id = $row->customer_user_id;
	  $ulst->Add($u_id);
	  $init_uids = $init_uids.$u_id.",";
  }
  if($init_uids!=""){
     $init_uids = substr($init_uids,0,strlen($init_uids)-1);
  }
  
?>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">

<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="/weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束--->

</head>

<script>
 function submitV(){
    var name = document.getElementById("name").value;
	if(name==""){
	    alert('请输入名称!');
	   return;
	}
	var phone = document.getElementById("phone").value;
	    
	if(phone==""){
	   alert('请输入电话号码!');
	   return;
	}
	
	var address = document.getElementById("address").value;
	if(address==""){
	    alert('请输入地址!');
	   return;
	}
	var location_p = document.getElementById("location_p").value;    
	if(location_p==""){
	   alert('请选择所在地区-省!');
	   return;
	}
	var location_c = document.getElementById("location_c").value;    
	if(location_c==""){
	   alert('请选择所在地区-市!');
	   return;
	}

    document.getElementById("upform").submit();
 }
 
 var i;
function showMediaMap(customer_id){
	i = $.layer({
		type : 2,
		shadeClose: true,
		offset : ['10px' , '80px'],
		time : 0,
		iframe : {
			src : 'mediamap.php?customer_id='+customer_id
		},
		title : "图片库(双击获取图片)",
		//fix : true,
		zIndex : 2,
		border : [5 , 0.3 , '#437799', true],
		area : ['500px','500px'],
		closeBtn : [0,true],
		success : function(){ //层加载成功后进行的回调
			//layer.shift('right-bottom',1000); //浏览器右下角弹出
		},
		end : function(){ //层彻底关闭后执行的回调
			/*$.layer({
				type : 2,
				offset : ['100px', ''],
				iframe : {
					src : '//sentsin.com/about/'
				},	
				area : ['960px','500px']
			})*/
		}
	});
}

function setMapValue(imgurl){
   document.getElementById("img_v").src=imgurl;
   document.getElementById("imgurl").value=imgurl;
   try{
     layer.close(i);
   }catch(e){
      //alert(e);
   }
}

</script>
<body>
<!--内容框架-->
<div class="WSY_content">
<form action="savecardshop.php?card_id=<?php echo $card_id ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" method="post"  enctype="multipart/form-data" id="upform" name="upform">

			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">门店信息</a>
				</div>
			</div>
			<!--列表头部切换结束-->
		<div class="WSY_data">
	 <div class="WSY_membebox">
		<dl class="WSY_member">
			<dt>名称</dt>
			<input type="text" value="<?php echo $name ?>" name="name" id="name" />		
			<div class="clear"></div>
		</dl>
		<dl class="WSY_member">
			<dt>门店编号</dt>

			<input type="text" value="<?php echo $store_number ?>" name="store_number" id="store_number" />		

			<div class="clear"></div>
		</dl>	
		<dl class="WSY_member">
			<dl>电话</dl>

			<input type="text" value="<?php echo $phone ?>" name="phone" id="phone" />

			<div class="clear"></div>
		</dl>	
		
		<dl class="WSY_member">
			<dl>联系人</dl>

			<input type="text" value="<?php echo $contactname ?>" name="contactname" id="contactname" />

			<div class="clear"></div>
		</dl>	
		
		<dl class="WSY_member">
			<dl>地址</dl>

			<input type="text" value="<?php echo $address ?>" name="address" id="address" />此地址与下面所在地区无关，还是要填省市区

			<div class="clear"></div>
		</dl>	
		
		<dl class="WSY_member">
			<dl>所在地区</dl>

			<select name="location_p" id="location_p"></select><select name="location_c" id="location_c"></select><select name="location_a" id="location_a"></select>此地区仅用于搜索，与上面的地区没关

			<div class="clear"></div>
		</dl>	
		<script type="text/javascript">
			new PCAS('location_p', 'location_c', 'location_a', '<?php echo $location_p;?>', '<?php echo $location_c;?>', '<?php echo $location_a;?>');
		</script>
		
		<dl class="WSY_member">
			<dl>级别</dl>

			  <select name=type id="type">
			     <option value=1 <?php if($type==1){ ?> selected <?php } ?>>总店</option>
				 <option value=2 <?php if($type==2){ ?> selected <?php } ?>>分店</option>
			  </select>

			<div class="clear"></div>
		</dl>	
		
		<dl class="WSY_member">
			<dl>类别</dl>

			  <select name=category_id id="category_id">
			     <?php 
				    $query="select id,name from categorys where isvalid=true";
					$result = _mysql_query($query) or die('Query failed254: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
					   $cate_id = $row->id;
					   $cate_name = $row->name;
				 ?>
				   <option value=<?php echo $cate_id; ?> <?php if($category_id==$cate_id){ ?> selected <?php } ?>><?php echo $cate_name; ?></option>
				 <?php } ?>
			  </select>

			<div class="clear"></div>
		</dl>	
		
		<dl class="WSY_member">
			<dl>图片</dl>

			<div class="WSY_memberimg">
		    <?php if($imgurl!=""){?>
			   <img src="<?php echo $imgurl; ?>" id="img_v" style="width:240px;height:160px;" /><br/>
			   <input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18" id="upfile" size="17" name="upfile" type=file value="<?php echo $imgurl ?>">
			   <input type=hidden value="<?php echo $imgurl ?>" name="imgurl" id="imgurl" />
			<?php }else{ ?>
			    <img src="pic/shop.jpg" id="img_v" style="width:240;height:160px;" /><br/>
				<input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18" size="17" name="upfile" id="upfile" type=file value="<?php echo $imgurl ?>">
				<input type=hidden value="<?php echo $imgurl ?>" name="imgurl" id="imgurl" />
			 <?php } ?>
			  <span>(尺寸要求：宽度480，高度320 ，大小30K以内）</span>
		    </div> 
			
			 <p class="WSY_imgkup WSY_public">图片库</p>
			<div class="clear"></div>
		</dl>
                <div class="WSY_imgkubox WSY_expansion">
                	<h3>图片库(双击获取图片)<span><img src="../images/contenticon/shanchu_img.png"></span></h3>
                    <div class="WSY_imgkucontent_topbox">
                    <dl class="WSY_imgkucontent_top">
                    	<dd>
                        	<select>
                            	<option>选择尺寸</option>
                            </select>
                        </dd>
                        <dd>
                        	<select>
                            	<option>选择标签</option>
                            </select>
                        </dd>
                        <dd>
                        	<select>
                            	<option>选择行业</option>
                            </select>
                        </dd>
                        <dd>
                        	<select>
                            	<option>选择发布者</option>
                            </select>
                        </dd>
                    </dl>
                    <dl class="WSY_imgkucontent">
                    	<dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                        <dd><img src="../images/contenticon/pic.png"></dd>
                    </dl>
                    <dl class="WSY_fannext">
                    	<dd class="WSY_previous"><input type="button" value="上一页"></dd>
                        <dd class="WSY_next"><input type="button" value="下一页"></dd>
                    </dl>
                </div>
                </div>		
		<dl class="WSY_member">
			<dl>微网站地址</dl>
			<input type="text" value="<?php echo $mini_websiteurl ?>" style="width:400px;" name="mini_websiteurl" id="mini_websiteurl" />
			<div class="clear"></div>
		</dl>	
		<?php if($auth_user_id<0){ ?>
		<dl class="WSY_member">
			<dl>门店管理员</dl>
			  <?php 
			    $query="select id,confirm_name from customer_users where C_id=".$customer_id;
				$result= _mysql_query($query) or die('Query failed302: ' . mysql_error());
			    while ($row = mysql_fetch_object($result)) {
		            $u_id = $row->id;
					$u_name = $row->confirm_name;
			  ?>
			      <dl>&nbsp;<input type=checkbox value="<?php echo $u_id; ?>" id="shop_<?php echo $u_id; ?>" <?php if($ulst->Contains($u_id)){ ?>checked<?php } ?> onclick="choose_shop(this.checked,<?php echo $u_id; ?>)" /><?php echo $u_name; ?>&nbsp;</dl>
			  <?php } ?>			 
			<div class="clear"></div>
		</dl>	
		<?php } ?>
		<dl class="WSY_member">
			<dl>描述</dl>
			<textarea id="editor1"   name="description"><?php echo $description; ?></textarea>
			<div class="clear"></div>
		</dl>		
		<dl class="WSY_member">
			<dl></dl>
			<input type=button class="button"  value="提交" onclick="submitV();" />
			&nbsp;	
		</dl>	
		<input type=hidden name="keyid" value="<?php echo $keyid ?>" />
	 </div>
	</div>
	<input type=hidden name="chk_users" id="chk_users" value="<?php echo $init_uids; ?>" />
 </form>
<div style="width:100%;height:20px;">
</div>
</div>
	<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../weixin/plat/Public/ckfinder/ckfinder.js"></script>

<script>
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});


function choose_shop(ckd,u_id){
   var v = document.getElementById("chk_users").value;
   
   if(ckd){
       if(v!=""){
	      v = v+","+u_id;
	   }else{
	      v = v +u_id;
	   }
   }else{
      if(v!=""){
	     var vs = v.split(",");
	     var str = "";	  
		 for(i=0;i<vs.length;i++){
		     if(vs[i]!=u_id){
			    str = str + u_id+",";
			 }
		 }
		 if(str!=""){
		    str = str.substring(0,str.length-1);
		 }
		 v = str;
	  }
   }
   document.getElementById("chk_users").value = v;
}
</script>
<?php

mysql_close($link);
?>
</body>
</html>