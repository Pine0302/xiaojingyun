<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/auth_user.php');
$key_id=$configutil->splash_new($_GET["key_id"]);	
if($key_id){
	$query="select * from weixin_commonshop_distributor_article where id=$key_id";
	$re=_mysql_query($query);
	while($row=mysql_fetch_object($re)){
		$title=$row->title;
		$description=$row->description;
		$share_description=$row->share_description;
		$share_img=$row->share_img;
		$p_id=$row->p_id;
	}
}	
$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	}
$query = 'SELECT id,appid,appsecret,access_token FROM weixin_menus where isvalid=true and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
$access_token="";
while ($row = mysql_fetch_object($result)) {
	$keyid =  $row->id ;
	$appid =  $row->appid ;
	$appsecret = $row->appsecret;
	$access_token = $row->access_token;
	break;
}
//新增客户
$new_customer_count =0;
//今日销售
$today_totalprice=0;
//新增订单
$new_order_count =0;
//新增推广员
$new_qr_count =0;

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$cur_date = date('Y-m-d');
$cur_date_begin = $cur_date." 00:00:00";
$cur_date_end = $cur_date." 23:59:59";

// $query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}

//$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and year(paytime)=".$year." and month(paytime)=".$month." and day(paytime)=".$day;
$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." paytime>='".$cur_date_begin."' and paytime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);

//$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}

// $query="select count(1) as new_qr_count from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$query="select count(1) as new_qr_count from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}
$search_user_id=-1;
if(!empty($_GET["search_user_id"])){
   $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
$search_name="";
if(!empty($_GET["search_name"])){
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
if(!empty($_POST["search_name"])){
    $search_name = $configutil->splash_new($_POST["search_name"]);
}
$search_phone="";
if(!empty($_GET["search_phone"])){
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
if(!empty($_POST["search_phone"])){
    $search_phone = $configutil->splash_new($_POST["search_phone"]) ;
}
$search_name_type=1;	//1为搜索微信名称 2为搜索收货名称
if(!empty($_GET["search_name_type"])){		
    $search_name_type = $configutil->splash_new($_GET["search_name_type"]);
}
if(!empty($_POST["search_name_type"])){
    $search_name_type = $configutil->splash_new($_POST["search_name_type"]);
}
$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}
?>  
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<link href="../../../../css/global.css" rel="stylesheet" type="text/css">
<link href="../../../../css/main.css" rel="stylesheet" type="text/css">
<link href="../../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css"> 
<script type="text/javascript" src="../../../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../js/product.js"></script>
<script type="text/javascript" src="../../../../common/utility.js"></script>
<script type="text/javascript" src="../../../../js/tis.js"></script>
<script type="text/javascript" src="../../../../js/WdatePicker.js"></script>

<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script type="text/javascript" src="../../../common_shop/jiushop/js/region_select.js"></script><!--跟商场的地区js相同-->
<meta http-equiv="content-type" content="text/html;charset=UTF-8">


<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="/weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="/weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束--->
<script src="../../../common/ckeditor/ckeditor.js"></script>				<!--这个是整个富文本的JS样式需要链接对-->
<!--富文本JS结束-->
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
 

</script>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<form action="save_distritor_article.php?card_id=<?php echo $card_id ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" method="post"  enctype="multipart/form-data" id="upform" name="upform">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">添加门店</a>
				</div>
			</div>
			<!--列表头部切换结束-->

  <!--关注用户开始-->
	<div class="WSY_data">
        <dl class="WSY_bulkbox"> 
        	<dt>文章标题：</dt>
			<dd><input type="text" value="<?php echo $name ?>" name="name" id="name" /></dd></dd>		
        </dl>
       <dl class="WSY_bulkbox">
        	<dt>分享描述：</dt>
			<dd><input type="text" value="<?php echo $store_number ?>" name="store_number" id="store_number" /></dd>		
        </dl>
        <dl class="WSY_member" >
			<dt>分享图片：</dt>
			<input type="hidden" id="imgurl" name="imgurl" value="<?php echo $imgurl;?>"/>
            <iframe id="frm_shopImage" src="addcardshop_image.php?customer_id=<?php echo $customer_id_en; ?>&shop_id=<?php echo $keyid; ?>&shop_imgurl=<?php echo $imgurl; ?>" height=300 width=1024 FRAMEBORDER=0 SCROLLING=no></iframe>
        </dl>
		<dl class="WSY_bulkbox">
			<dt>关联产品：</dt>
			<?php 
				$query = "select id,name from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());  
			?>
			<select name="p_id" id="p_id" style="font-size:12px;height:24px;border-radius:2px;border:solid 1px #dadada;padding:3px;">
				<option value="" >--请选择一个单品--</option>
				<?php 
				while($row = mysql_fetch_object($result)) {
					$r_p_id = $row->id;
					$r_name = $row->name;	
				?>
				<option value="<?php echo $r_p_id; ?>" <?php if($p_id == $r_p_id){ ?>selected<?php } ?>><?php echo $r_name; ?></option>
				<?php } ?>
			</select>
		</dl>
		
		
        <dl class="WSY_bulkdl">
            <dt>编辑单品文章：</dt>
			<div style="width:750px!important;">
				<div class="text_box">
					<textarea id="editor1"   name="description"><?php echo $product_description; ?></textarea>
				</div> 
			</div> 
		</dl>
            <div class="WSY_text_input01">
                <div class="WSY_text_input"><button class="WSY_button" type="button" onclick="submitV();">提交保存</button></div>
                <div class="WSY_text_input"><button class="WSY_button" type="button" onclick="javascript:history.go(-1);">返回</button></div>
            </div>
			<input type=hidden name="keyid" value="<?php echo $keyid ?>" />
		</div>
	</div>
		<input type=hidden name="chk_users" id="chk_users" value="<?php echo $init_uids; ?>"/>

 </form>
</div>


	<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>

<script>
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
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
	$("#frm_shopImage").contents().find("#img_v").attr("src",imgurl);
   //document.getElementById("img_v").src=imgurl;
   document.getElementById("imgurl").value=imgurl;
   try{
     layer.close(i);
   }catch(e){
      //alert(e);
   }
}

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
function setParentShopImage(url){
	$("#imgurl").val(url);
}
</script>
<?php

mysql_close($link);
?>
</body>
</html>
