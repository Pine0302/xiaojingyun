<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

 $shop_subscribe_id=-1;
  if(!empty($_GET["shop_subscribe_id"])){
     $shop_subscribe_id = $configutil->splash_new($_GET["shop_subscribe_id"]);
  }  
 //$customer_id = $configutil->splash_new($_GET["customer_id"]);  //去掉，前面的引入的文件里已经获取了customer_id
  $need_score=0;
  $subscribe_id = -1;
  //是否需要会员身份
  $is_needmember=0;
  $imgurl="";
  if($shop_subscribe_id>0){
    
	$query = 'SELECT id,subscribe_id,need_score,is_needmember,imgurl FROM weixin_commonshop_subscribes where isvalid=true and id='.$shop_subscribe_id;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$subscribe_id = $row->subscribe_id;
		$need_score =$row->need_score;
		$is_needmember = $row->is_needmember;
		$imgurl = $row->imgurl;
	}
  }
  
  //图文信息
if(empty($imgurl) or $imgurl==""){
     //$imgurl="../pic/gift.png";
  }else{
      $pos = strpos($imgurl,"//");
	  
      if($pos===0){
	  }else{
	      $imgurl = $imgurl;
	  }	
  }	
?>
<!doctype html>
<html>	
<head>
<meta charset="utf-8">
<title>微商城功能项列表</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../../js/tis.js"></script>
<script type="text/javascript" src="../../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../../common/js/layer/layer.js"></script>
<script type="text/javascript" src="../../../../common/js/jscolor.js" ></script>
<script> 
 function submitV(){
    var subscribe_id = document.getElementById("subscribe_id").value;
	if(subscribe_id==-1){
	    alert('请选择一个功能!');
	   return;
	}
	
	
    document.getElementById("upform").submit();
 }
</script>
<script>
var i;
function showMediaMap(customer_id){
	i = $.layer({
		type : 2,
		shadeClose: true,
		offset : ['10px' , '80px'],
		time : 0,
		iframe : {
			src : '../mediamap.php?customer_id='+customer_id
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

 function setIsmember(obj){
    if(obj.checked){
       document.getElementById("is_needmember").value=1;
	}else{
	   document.getElementById("is_needmember").value=0;
	}
 }
</script>

</head>

<body>
<!--内容框架开始-->
<div class="WSY_content">  
<div class="div_new_content">
<form action="saveshop_subscribe.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data" id="upform" name="upform">
		<input type="hidden" name="item_color" id="item_color" value="<?php echo $item_color; ?>" />
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">微商城功能项</a>
				</div>
			</div>
			<!--列表头部切换结束-->
			<div class="WSY_data">
				<dl class="WSY_member">
					<dt>功能项</dt>
					<dd>	
					<span class="input">
			<select name="subscribe_id" id="subscribe_id">
			 <option value=-1>--请选择一个图文--</option>
			<?php 
			$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and coupon_id<=0 and customer_id='.$customer_id;
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result)) {
			  $sub_id =  $row->id ;
			  $title = $row->title;
			  ?>
			   <option value="<?php echo $sub_id; ?>" <?php if($subscribe_id==$sub_id){ ?> selected	<?php } ?>><?php echo $title; ?></option>
		<?php } ?>	
			</select>
					</span>
					</dd>
				</dl>
				<dl class="WSY_member">
                    <dt>图片</dt>
                    <div class="WSY_memberimg">
						<?php if($imgurl!=""){?>
                        <img src="<?php echo $imgurl; ?>" style="width:80px;height:80px;">
						<?php }else{ ?>	
						<img src=	"../../../Common/images/Base/personal_center/gift.png" style="width:126px;height:120px;">
						<?php } ?>
                        <span>(尺寸要求：宽度80，高度80）</span>
                        <!--上传文件代码开始-->
                        <div class="uploader white">
                            <input type="text" class="filename" readonly/>
                            <input type="button" name="file" class="button" value="上传..."/>
							<input size="17" name="upfile" id="upfile" type=file value="<?php echo $imgurl ?>">
							<input type=hidden value="<?php echo $imgurl ?>" name="imgurl" id="imgurl" /> 
                        </div>
                        <!--上传文件代码结束-->
                    </div>
                </dl>
				
				<dl class="WSY_member">
					<dt>是否需要推广员</dt>
					<!-- <dd><label><input type=radio value=0 name="is_vote" <?php if(!$is_vote){?>checked<?php } ?>>否</label></dd>
					<dd><label><input type=radio value=1 name="is_vote" <?php if($is_vote){?>checked<?php } ?>>是</label></dd> -->
					<dd>
						<input type=checkbox  id="chkmember" <?php if($is_needmember){?>checked<?php } ?> onclick="setIsmember(this);" /> (推广员才能够使用该功能)
						<input type=hidden name="is_needmember" id="is_needmember" value=<?php if($is_needmember){echo "1";}else{echo "0";}?> />
					</dd>
					
				</dl>
				<div class="WSY_text_input01">
				  <div class="WSY_text_input"><input type="button" class="WSY_button"  value="提交" onclick="submitV();" /></div>
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);"/></div>
				</div>
				<input type=hidden name="keyid" value="<?php echo $shop_subscribe_id ?>" />
				<input type=hidden name="need_score" value="0" />
			</div>
		</div>
	</form>	
	<div style="width:100%;height:20px;"></div>
</div>
</div>
<!--内容框架结束-->
<script type="text/javascript" src="../../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
<?php

mysql_close($link);
?>
</body>
</html>
