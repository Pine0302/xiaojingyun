<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);//print_r($customer_id);die();
require('../../../../weixinpl/back_init.php');
$keyid = 0;
$len = count($_GET);
$del = "";
if($len>0){
 if(!empty($_GET["keyid"])){
   $keyid = $configutil->splash_new($_GET["keyid"]);
 }
 if($len>1){
   if(!empty($_GET["del"])){
	  $del = $configutil->splash_new($_GET["del"]);
   }
 }
}
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
//error_reporting(0);
  /* 删除快递模板 */
  if($del=="isok"){
     $query = 'update  weixin_expresses set isvalid=false where id='.(int)$keyid;
	 _mysql_query($query);
	 mysql_close($link);
	 echo "<script>location.href='express.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
	 return;
  }
	$name 		   = "";	//快递名称
	$type		   =  1;	//计价方式。1：按件数；2：按重量
	$FreeNum       =  0;	//免邮设置。按件免邮/按重免邮
	$FirstNum      =  1;	//首件件数/首重重量
	$ContinueNum   =  1;	//续件件数/续重重量
	$price 		   =  0;	//首件/首重费用
	$ContinuePrice =  0;	//续件费用/续重费用
	$is_include	   =  1;	//运送范围:0.范围之内 1.范围之外
	$region 	   = "";	//是否允许快递的地区
	$cost 		   =  0;	//快递所需商品总费用
	$expressCode   = "";	//快递100代码
	$region_array  = array();	//快递地区数组
	$print_temp_id = 0; 	//快递打印模板ID
	$city_area 	   = ""; 	//关联的市和区
  if($keyid>0){
	$query = 'SELECT id,name,is_include,region,cost,expressCode,type,FirstNum,ContinueNum,price,ContinuePrice,FreeNum,print_temp_id,city_area FROM weixin_expresses where id='.$keyid;
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	    $name		   = $row->name;
	    $type		   = $row->type;			//计价方式
		$FirstNum      = $row->FirstNum;	    //首件件数/首重重量
		$ContinueNum   = $row->ContinueNum;		//续件件数/续重重量
		$price         = $row->price;			//首件费用/首重费用
		$ContinuePrice = $row->ContinuePrice;	//续件费用/续重费用
		$FreeNum       = $row->FreeNum;
		$is_include    = $row->is_include;
		$region        = $row->region;
		$cost          = $row->cost;
		$expressCode   = $row->expressCode;
		$print_temp_id = $row->print_temp_id;
		$city_area 	   = $row->city_area;
		if(!empty($region)){$region_array  = explode(",",$region);}else{$region_array = array();} //这里不加判断，字段为空的话，会出现警告提示；

	}
	// $region = "广东省,北京市";
	// $city_area = "东莞市_南城区,东莞市_东坑镇";

  }
	// $sql_print_temp = "SELECT id,print_name from weixin_print_temp WHERE isvalid=1 AND is_supply=0 AND customer_id=".$customer_id;
	// $obj_print_temp = _mysql_query($sql_print_temp); $array_print_temp = array();
	// while ($row_print_temp = mysql_fetch_object($obj_print_temp)){
		// $array_print_temp[] = $row_print_temp;
	// }


//list in select 检测是否等于$val值是否相等，如果等于就返回$selected值，默认为selected
function l_s($val_0, $val_1, $selected='selected'){
	//if(in_array($array_val,$array)){return $selected;}else{return '';}
	if($val_0==$val_1){return $selected;}else{return '';}
}

?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link href="../../../css/chosen.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" href="css/area_select.css" />
<script type="text/javascript" src="js/jquery-1.12.1.min.js" ></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>

.add_content_one{
  width:90%;
  padding-left: 20px;
  margin:0;
  margin-top:10px;
  height:30px;
  line-height:30px;
  font-size:13px;
  text-align:left;
  font-weight:bolder;
}
.split_line{
	width:100%;
	/*border-bottom: 1px solid  #d4d4d4;
	border-top:none;*/
	border-bottom:1px solid #ccc;
	border-top:1px solid #fff;
}
.add_content_con_one{
   width:90%;
   margin:0 auto;
   margin-top:10px;
   height:40px;
   line-height:20px;
}
.add_content_con_l{

   width:10%;
   float:left;
   heigth:100%;
   text-align:right;

}
.add_content_con_r{

   width:90%;
   float:left;
   heigth:100%;
   text-align:left;
}

.add_content_con_two{
   width:90%;
   margin:0 auto;
   margin-top:10px;
   height:auto;
}
.add_content_con_four{
   width:90%;
   margin:0 auto;
   margin-top:30px;
   height:250px;

}
.add_content_con_three{

  width:90%;
  margin:0 auto;
  text-align:left;
  height:100px;;
}

.add_content_con_three_con{
  height:40px;
  width:100%;
}
.button_blue{font-size: 14px;display: block;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:5px;color: #fff;}
.r_con_form{border:0 none;}
.r_con_form .rows{border: 0 none;}
.r_con_form .rows .input{border: 0 none;}
.r_con_form .rows .input2{border: 0 none;}
.r_con_wrap{background: rgb(251, 251, 251);}
.border_left{border: 0 none;}
.r_con_form .rows > label{line-height:24px;text-align: left;width: 11%;font-size:14px;}
.r_con_form .rows:hover{background:#fbfbfb;}
.rows input[type="text"]{padding-left:5px;height:24px;border-radius:2px;}
</style>
</head>

<script>
function submitV(){
  var name =$('#name').val();
  if(name==""){
	  alert('请输入快递公司名称');
	  return;
  }
 var type =$("input[name='type'][checked]").val(); 	//计价方式
 var FirstNum =$('#FirstNum').val();				//首件件数/首重重量
 var ContinueNum =$('#ContinueNum').val();			//续件件数/续重重量
 var price =$('#price').val();						//首件费用/首重费用
 var ContinuePrice =$('#ContinuePrice').val();		//续件费用/续重费用
 var cost =$('#cost').val();						//所需金额
 var Costtype_str1 = $('#Costtype_str1').text();
 var Costtype_str2 = $('#Costtype_str2').text();
 var Costtype_str3 = $('#Costtype_str3').text();
 var Costtype_str4 = $('#Costtype_str4').text();
 var  re =   new RegExp("^[0-9]*[1-9][0-9]*$");

  if(FirstNum.match(re)==null && type==1){
	  alert(Costtype_str1+"请输入大于零的整数!");
	  return;
  }
  if(FirstNum==""){
	  alert('请输入'+Costtype_str1);
	  return;
  }
  if(isNaN(FirstNum) || FirstNum<0){
	  alert('请输入正确'+Costtype_str1);
	  return;
  }
  if(ContinueNum.match(re)==null && type==1){
	  alert(Costtype_str2+"请输入大于零的整数!");
	  return;
  }
  if(ContinueNum==""){
	  alert('请输入'+Costtype_str2);
	  return;
  }
  if(isNaN(ContinueNum) || ContinueNum<0){
	  alert('请输入正确'+Costtype_str2);
	  return;
  }

  if(price==""){
	  alert('请输入'+Costtype_str3);
	  return;
  }
  if(isNaN(price) || price<0){
	 alert('请输入正确'+Costtype_str3);
	  return;
  }

  if(ContinuePrice==""){
	  alert('请输入'+Costtype_str4);
	  return;
  }
  if(isNaN(ContinuePrice) || ContinuePrice<0){
	  alert('请输入正确'+Costtype_str4);
	  return;
  }

  if(cost==""){
	  alert('请输入所需金额');
	  return;
  }
  if(isNaN(cost) || cost<0){
	  alert('所需金额请输入正确金额');
	  return;
  }
  //if($('#print_temp_id').val() == '0'){ alert('请选择要关联的打印模板'); return;}

	// if( !getSelected(1) ){
		// alert('请选择区域！');
		// return;
	// }
  getSelected(1);
  document.getElementById("keywordFrm").submit();
}
</script>

<body>

<div >
    <div class="WSY_content">
		<div class="WSY_columnbox">
		<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">添加运费规则</a>
				</div>
		</div>
<form action="saveexpress.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" id="keywordFrm" method="post">
    <div class="add_content_one">
	    添加运费规则
	</div>

	<div id="products" class="r_con_wrap">
	  <div class="r_con_form" >
		<div class="rows">
			<label>运费规则名称</label>
			<span class="input">
			<input type=text value="<?php echo $name ?>" name="name" id="name" />
			</span>
			<div class="clear"></div>
		</div>
		<!--<div class="rows">
			<label>关联打印模板</label>
			<span class="input">
			<select name="print_temp_id" id="print_temp_id">
				<option value="0">选择模板绑定</option>
				<?php foreach($array_print_temp as $val){ ?>
				<option value="<?php echo $val->id ?>" <?php echo l_s($val->id,$print_temp_id) ?>><?php echo $val->print_name ?></option>
				<?php } ?>
			</select>


			<a href="add_delivery_temp.php?customer_id=<?php echo $customer_id ?>">添加运单模板</a>
			<a id="edit_print_temp_url" style="display:none;" href="">编辑运单模板</a>
			<a id="del_print_temp_url" style="display:none;" href="" onClick="return confirm('确认要删除吗？');">删除运单模板</a>
			</span>
			<div class="clear"></div>
		</div>-->
		<!-- <div class="rows">
			<label>快递100编码</label>
			<span class="input">
			<input type=text value="<?php echo $expressCode ?>" name="kuaiDiName" id="kuaiDiName" />
			<a style="color:blue" href="//www.kuaidi100.com/download/api_kuaidi100_com(20140729).doc" class="aco" target="_blank">API URL 所支持的快递公司及参数说明</a>
			</span>
			<div class="clear"></div>
		</div> -->
		<div class="rows">
			<label>计价方式</label>
			<span class="input">
			   <label><input type="radio" name="type" id="type1" value="1" <?php if($type==1){?> checked="checked" <?php } ?>  onClick="chktype(1);">按件数&nbsp;</label>
			   <label><input type="radio" name="type" id="type2" value="2" <?php if($type==2){?> checked="checked" <?php } ?>  onClick="chktype(2);">按重量&nbsp;</label>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows" >
			<label style="line-height:19px;">快递件数设置(<span id="unit">件</span>)</label>
			<span class="input_bx border_left"><span id="Costtype_str1">首件件数</span>：
				<input type="text" name="FirstNum" id="FirstNum" onkeyup="clearNoNum(this,1);" value="<?php echo floatval($FirstNum);?>" >
			</span>
			<span class="input_bx"><span id="Costtype_str2">续件件数</span>：
				<input type="text" name="ContinueNum" id="ContinueNum" onkeyup="clearNoNum(this,1);" value="<?php echo floatval($ContinueNum);?>" >
			</span>
			<span class="input_bx"><span id="Costtype_Free">按件免邮</span>：
				<input type="text" name="FreeNum" id="FreeNum" onkeyup="clearNoNum(this,1);" value="<?php echo floatval($FreeNum);?>" >
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows" >
			<label>快递配送费用(元)</label>
			<span class="input_bx border_left"><span id="Costtype_str3">首件费用</span>：
				<input type="text" name="price" id="price" onkeyup="clearNoNum(this,2);" value="<?php echo round($price,2);?>" >
			</span>
			<span class="input_bx"><span id="Costtype_str4">续件费用</span>：
				<input type="text" name="ContinuePrice" id="ContinuePrice" onkeyup="clearNoNum(this,2);" value="<?php echo round($ContinuePrice,2);?>" >&nbsp;(0表示无运费)
			</span>
			<div class="clear"></div>
		</div>

		<!--
		<div class="rows">
			<label>运费</label>
			<span class="input">
			<input type=text value="<!?php echo $price ?>" name="price" id="price" />元&nbsp;(0表示无运费)
			</span>
			<div class="clear"></div>
		</div>
		-->
		<div class="rows">
			<label style="line-height:19px;">选择此快递所需金额(元)</label>
			<span class="input">
			<input type=text value="<?php echo $cost ?>" name="cost" onkeyup="clearNoNum(this,2);" id="cost" />(0表示无限制)
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label>区域模式</label>
			<span class="input">
			   <label><input type="radio" name="include"../../../../weixinpl/back_newshops/Distribution/express/ value="0" <?php if($is_include==0){?> checked="checked" <?php } ?>>区域之内&nbsp;</label>
			   <label><input type="radio" name="include"../../../../weixinpl/back_newshops/Distribution/express/ value="1" <?php if($is_include==1){?> checked="checked" <?php } ?>>区域之外&nbsp;</label>
			</span>
			<div class="clear"></div>
		</div>
		<div class="rows">
			<label>区域选择</label>
			<div class="area_select_btn WSY-skin-bg">选择</div>
			<div class="province" style="display:none;">
				<div class="box">
					<div class="boxContent">
						<div class="header">
							<div class="searchBox">
								<span>关键字搜索 :</span>
								<input class="searchVal" placeholder="请输入关键字" onkeydown="if(event.keyCode==13){searchArea();}" />
								<div class="searchBtn" onclick="searchArea()">搜索</div>
							</div>
							<div class="confirmBtn">
								<span>确定</span>
							</div>
						</div>
						<div class="footer">
							<div class="left">
								<div class="all-select"><img class="select" src="img/select1.png" /><span>全选</span></div>
								<div class="top" id="province">
								<!--省-->
								</div>
								<div class="bottom" id="openProvince" onclick="openProvince()">
									<img src="img/arrow3.png" />
								</div>
							</div>
							<div class="right" id="city_area">
								<div class="top">
									<img class="arrowLeft" src="img/arrow4.png" />
									<img class="arrowRight" src="img/arrow5.png" />
									<div class="cityBox">
									<!--市-->
									</div>
									<!--区-->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="selected-areaBox">
				<label>已选</label>
				<div>省：<span class="selected-province hidden-content" data-province_num="0"></span><label class="showMore moreProvince">↓更多</label><label class="showMore hidden-btn hiddenProvince">↑隐藏</label></div>
				<div>市：<span class="selected-city hidden-content" data-city_num="0"></span><label class="showMore moreCity">↓更多</label><label class="showMore hidden-btn hiddenCity">↑隐藏</label></div>
				<div>区/县/镇：<span class="selected-area hidden-content" data-area_num="0"></span><label class="showMore moreArea">↓更多</label><label class="showMore hidden-btn hiddenArea">↑隐藏</label></div>
				<div class="checked-div" style="width:72%;display:inline-block;"></div>
			</div>
			<div class="clear"></div>
		</div>
		<input type="hidden" name="region" id="region" value="<?php echo $region;?>" />
		<input type="hidden" name="city_area" id="region_city_area" value="<?php echo $city_area;?>" />
		<div class="rows">
			<label> </label>
			<span class="input">
			<input type=button class="WSY_button"  value="提交" onclick="submitV();" style="border:0 none;border-radius: 3px;float:left;margin-right: 10px;"/>
			<input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="border:0 none;border-radius: 3px;float:left;"/>
			</span>
			<div class="clear"></div>
		</div>

	  </div>
			<div class="add_content_con_four">
			</div>
	</div>

<input type=hidden name="keyid" value="<?php echo $keyid ?>" />
</form>
<div style="width:100%;height:20px;">
</div>
</div>
</div>
</div>
<script type="text/javascript" src="../../../js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../../js/chosen.jquery.min.js" ></script>
<script type="text/javascript" src="../../../common/region_select.js" ></script>
<script type="text/javascript" src="js/area_select.js?v=<?php echo time();?>" ></script>
<script>
var province_str = "<?php echo $region;?>";
var city_area_str = "<?php echo $city_area;?>";
var city_arr = new Array();
province_arr = province_str.split(',');
city_area_arr = city_area_str.split(',');
for( i in city_area_arr ){
	city_arr.push(city_area_arr[i].split('_')[0]);
}

$(function() {
	$('.chosen-select').chosen({
	  no_results_text: '没有找到匹配的区域!',
	  width: '250px'
	});
	var v = $("input[name='type'][checked]").val();
	chktype(v);

	$("#print_temp_id").change( function() {
		if($("#print_temp_id").val() == '0'){
			$('#edit_print_temp_url').hide();
			$('#del_print_temp_url').hide();
		}else{
			show_url($("#print_temp_id").val());
		}
	});

	<?php if($print_temp_id>0){?>
	show_url($("#print_temp_id").val());
	<?php } ?>
	//select_relist();


});

function show_url(print_temp_id){
	$('#edit_print_temp_url').show();
	$('#edit_print_temp_url').attr('href','add_delivery_temp.php?id='+print_temp_id);
	$('#del_print_temp_url').show();
	$('#del_print_temp_url').attr('href','save_delivery.php?do=del&id='+print_temp_id);
}


 function chktype(v){
	    v = parseInt(v);
       switch(v){
			case 1:
				$('#unit').text("件");
				$('#Costtype_str1').text("首件件数");
				$('#Costtype_str1').next().attr("onkeyup","clearNoNum(this,1)");
				$('#Costtype_str2').text("续件件数");
				$('#Costtype_str2').next().attr("onkeyup","clearNoNum(this,1)");
				$('#Costtype_str3').text("首件费用");
				$('#Costtype_str4').text("续件费用");
				$('#Costtype_Free').next().attr("onkeyup","clearNoNum(this,1)");
				$('#Costtype_Free').text("按件免邮");
				$('#type1').attr("checked",true);
				$('#type2').removeAttr("checked");
			break;

			case 2:
				$('#unit').text("千克");
				$('#Costtype_str1').text("首重重量");
				$('#Costtype_str1').next().attr("onkeyup","clearNoNum(this,2)");
				$('#Costtype_str2').text("续重重量");
				$('#Costtype_str2').next().attr("onkeyup","clearNoNum(this,2)");
				$('#Costtype_str3').text("首重费用");
				$('#Costtype_str4').text("续重费用");
				$('#Costtype_Free').next().attr("onkeyup","clearNoNum(this,2)");
				$('#Costtype_Free').text("续重免邮");
				$('#type2').attr("checked",true);
				$('#type1').removeAttr("checked");
			break;
	   }
}

</script>
<?php mysql_close($link); ?>
</body>
</html>

