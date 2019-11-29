<?php
/*
修改4M模板请在 common/utility_4m.php 的$allow_4M_template
*/
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$head=0;
require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/auth_user.php');

require('../../../../../weixinpl/common/utility_4m.php');
_mysql_query("SET NAMES UTF8");

$template_id=-1;
if(!empty($_GET["template_id"])){
    $template_id=$configutil->splash_new($_GET["template_id"]);
	$query="update weixin_commonshops set template_id=".$template_id." where customer_id=".$customer_id;
	_mysql_query($query);
	//每次操作都清空模板缓存 xj
	clear_template_cache("/tmp/weixin_platform/$customer_id");

	//贴牌oem编号
	$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);

}else{
	$query ="select template_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $template_id = $row->template_id;
	}
}
$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	}

$u4m = new Utiliy_4m_new();
$rearr = $u4m->is_4M_new($customer_id);

//是4m分销
$is_shopgeneral = $rearr[0]  ;
//厂家编号
$adminuser_id = $rearr[1] ;
//是否是厂家总店
$is_samelevel = $rearr[2] ;
//总店模板编号
$general_template_id = $rearr[3] ;
//总店商家编号
$general_customer_id = $rearr[4] ;

//1：厂家总店； 2：代理商总店
$owner_general = $rearr[5] ;

$orgin_adminuser_id = $rearr[6];

// var_dump($rearr);
if($is_shopgeneral){
	$getAllSubCustomers = $u4m->getAllSubCustomers_new($customer_id,2);//获取下级商家
}

$is_4m_template = 0;										//是否使用4M模板
if($is_shopgeneral == 1 and $is_samelevel == 1){			//必须是厂家总店
	if($u4m->check_allow_4M_template($template_id)){		//判断厂家现在的模板是否是4M模板，是则更新下级商城模板
		$is_4m_template = 1;
		if(!empty($_GET["general_customer_id"])){
			$general_customer_id_2 = $configutil->splash_new($_GET["general_customer_id"]);
			if($general_customer_id_2>0 and $template_id>0){
			   //是总部商家，则更新所有下级商城的模板编号
			   $query="update weixin_commonshops set template_id=".$template_id." where customer_id in (".$getAllSubCustomers.")";
			   _mysql_query($query);
			   $arr = explode(',',$getAllSubCustomers);
			   foreach($arr as $k=>$v){
				   //每次操作都清空模板缓存 xj
				   clear_template_cache("/tmp/weixin_platform/$v");
			   }
			}
		}
	}
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

$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed19: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}

$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and paytime>='".$cur_date_begin."' and paytime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);

$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}

$query="select count(1) as new_qr_count from promoters where status=1 and isvalid=true and customer_id=".$customer_id." and createtime>='".$cur_date_begin."' and createtime<='".$cur_date_end."'";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
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
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../Common/css/Base/home_decoration/m-style.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme;?>.css">
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGreen.css">--><!--内容CSS配色·绿色-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentOrange.css">--><!--内容CSS配色·橙色-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentbgreen.css">--><!--内容CSS配色·蓝绿-->
<!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGGreen.css">--><!--内容CSS配色·草绿-->

<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../../common/js_V6.0/jscolor.js"></script><!--拾色器js-->
<script type="text/javascript" src="../../../Common/js/Base/personalization/shop.js"></script>
<script type="text/javascript" src="../../../../common/js/inside.js"></script>
<script language="javascript">$(document).ready(shop_obj.skin_init);</script>
<script>
  var customer_id = '<?php echo $customer_id_en; ?>';
  var general_customer_id = <?php echo $general_customer_id; ?>;
  var adminuser_id = <?php echo $adminuser_id; ?>;
</script>

<!--富文本JS结束-->
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content">
<style type="text/css">
<?php
	switch($theme){

		case blue:
		echo "		.input_butn{margin-top:30%}
		.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
		.input_butn input:hover{background:#017ca9;cursor:pointer;}
		.input_butn01 input{width:220px;}
		.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
		.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
		.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}

		";
		break;

		case Green:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#32b16c;border:solid 1px #0e9f50;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#0e9f50;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#32b16c;border:solid 1px #0e9f50;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0e9f50;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#0e9f50;color:#fff;}";
		break;

		case Orange:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#e74f31;border:solid 1px #d43d1f;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#d43d1f;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#e74f31;border:solid 1px #d43d1f;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#d43d1f;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#e74f31;color:#fff;}";
		break;

		case bgreen:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#0faa9a;border:solid 1px #20b3a4;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#20b3a4;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#0faa9a;border:solid 1px #20b3a4;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#20b3a4;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#20b3a4;color:#fff;}";
		break;

		case GGreen:
		echo ".input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#096733;border:solid 1px #146e3c;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#146e3c;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#096733;border:solid 1px #146e3c;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#146e3c;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#096733;color:#fff;}";
		break;



	}

?>

/*蓝色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#06a7e1;border:solid 1px #0b91c2;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#017ca9;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#07a7e1;border:solid 1px #0b91c2;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0b91c2;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#06a7e1;color:#fff;}
*/
/*绿色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#32b16c;border:solid 1px #0e9f50;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#0e9f50;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#32b16c;border:solid 1px #0e9f50;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#0e9f50;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#0e9f50;color:#fff;}*/

/*橙色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#e74f31;border:solid 1px #d43d1f;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#d43d1f;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#e74f31;border:solid 1px #d43d1f;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#d43d1f;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#e74f31;color:#fff;}*/

/*蓝绿色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#0faa9a;border:solid 1px #20b3a4;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#20b3a4;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#0faa9a;border:solid 1px #20b3a4;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#20b3a4;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#20b3a4;color:#fff;}*/

/*草绿色*/
/*.input_butn{margin-top:30%}
.input_butn input{display:block;width:192px;background:#096733;border:solid 1px #146e3c;height:32px;line-height:30px;border-radius:3px;font-size:14px;color:#fff;}
.input_butn input:hover{background:#146e3c;cursor:pointer;}
.input_butn01 input{width:220px;}
.leftA01 .leftA01_dl dd .tj{background:#096733;border:solid 1px #146e3c;color:#fff;}
.leftA01 .leftA01_dl dd .tj:hover{background:#146e3c;}
.WSY_homeright .WSY_homeright_nav li .blueAA{background:#096733;color:#fff;}*/
</style>

    <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    	<!--列表头部切换开始-->
		<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Base/personalization/basic_head.php");
		?>
        <!--列表头部切换结束-->
        <!--风格设置代码开始-->
		<div class="WSY_data">
			<?php if($is_shopgeneral == 1 and $is_samelevel == 1 ){?>
			<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02">
					<dt>4M模板</dt>
						 <dd style="float:left;margin-left: 10px;margin-right: 10px;">
							<?php if($is_4m_template==1){ //开?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;"></p>
								<li onclick="change_4m_template(1)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_4m_template(0)" class="WSY_bot2" id="WSY_bot2" style="display: none; left: 0px;"></span>
							</ul>
							<?php }else{ //关 ?>
							<ul style="background-color: rgb(39, 230, 209);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;"></p>
								<li onclick="change_4m_template(1)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_4m_template(0)" class="WSY_bot2" id="WSY_bot2" style="display: block; left: 30px;"></span>
							</ul>
							<?php } ?>
						</dd>
						<dt style="float:left">通用模板</dt>

			</dl>
			</div>
			<?php }?>
			<div class="WSY_stylebox" id="skin">

			<?php
				$query="select id,name,imgurl from weixin_commonshop_templates where isvalid=true";

				if($is_shopgeneral  and $template_id>0){
					//总部模板只有37号
					if($is_samelevel == 1){		//厂家总店
						//$query = $query." and id in (".implode(',',$u4m->allow_4M_template).")";
					}else{						//下级商家
						$query = $query." and id=".$template_id;
					}
				}
				//echo $query;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$tid=$row->id;
					$tname = $row->name;
					$imgurl = $row->imgurl;
                    $imgurl = WeixinManager.$imgurl;
			?>
				<a class="<?php if($template_id==$tid){?>cur<?php } ?> <?php if($u4m->check_allow_4M_template($tid)){echo 'u4m_template';}else{echo'common_template';}?>"  sid="<?php echo $tid; ?>" >
					<li class="<?php if($template_id==$tid){?>cur<?php } ?>">
					<div class="item" sid="<?php echo $tid; ?>" title="点击选择微商城风格" >
						<div class="img"><img src="<?php echo $imgurl; ?>"></div>
						<div class="title"><?php echo $tname; ?>
						</div>
					</div>
					</li>
				</a>

				<!--<a class="<?php if($template_id==$tid){?>cur<?php } ?>" sid="<?php echo $tid; ?>"><img src="<?php echo $imgurl; ?>"><li><?php echo $tname; ?></li></a>-->
			<?php
				}
			?>
			</div>
		</div>
		<!--风格设置代码结束-->

	</div>
</div>
<!--内容框架结束-->

<?php
if($is_shopgeneral == 1 and $is_samelevel == 1 ){
?>
<script>
var is_4m_template = '<?php echo $is_4m_template?>'; //是否是4M模板
$(function(){

	if(is_4m_template == '1' ){
		$('.common_template').hide();
		$('.u4m_template').show();
	}else{
		$('.common_template').show();
		$('.u4m_template').hide();
	}

	$(".WSY_bot").click(function(){
		$(this).animate({left : '30px'});
		$(this).parent().find(".WSY_bot2").animate({left : '30px'});
		$(this).hide();
		$(this).parent().find(".WSY_bot2").show();
		$(this).parent().find("p").animate({margin : '0 0 0 13px'}, 500);

		//$(this).parent().find("p").html('关');
		$(this).parent().css({backgroundColor : 'rgb(39, 230, 209)'});
		$(this).parent().find("p").css({color : '#7f8a97'});
		})

	$(".WSY_bot2").click(function(){
		$(this).parent().find(".WSY_bot").animate({left : '0px'});
		$(this).animate({left : '0px'});
		$(this).parent().find(".WSY_bot").show();
		$(this).hide();
		$(this).parent().find("p").animate({margin : '0 0 0 27px'}, 500);

		//$(this).parent().find("p").html('开');
		$(this).parent().css({backgroundColor : '#ff7170'});
		$(this).parent().find("p").css({color : '#fff'});
		})
});

function change_4m_template(a){
	if( a == 1 ){	//显示4M模板
		$('.common_template').show();
		$('.u4m_template').hide();
	}else{			//显示普通模板
		$('.common_template').hide();
		$('.u4m_template').show();
	}
}

</script>
<?php
}
?>

</body>
</html>
<?php

mysql_close($link);
?>