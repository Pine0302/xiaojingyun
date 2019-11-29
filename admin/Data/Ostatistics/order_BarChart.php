<script>
	var provinces = new Array();
	var counts = new Array();
			
</script> 

<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");
$query="SELECT name from weixin_commonshops where isvalid=true and customer_id =".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$shopname=$row->name;
	break;
}
$begintime="";
$endtime ="";
$province ="";
$city ="";
$area ="";
$search_status=-1;
$ktype=0;
$PCAtype=1;
$ktype=$configutil->splash_new($_GET["type"]);
if(!empty($_GET["province"])){
	   $province = $configutil->splash_new($_GET["province"]);	   
	  // $query = $query." and a.location_p='".$province."'";
	   if(!empty($_GET["city"])){
		   $city = $configutil->splash_new($_GET["city"]);
		  // $query = $query." and a.location_c='".$city."'";
		   if(!empty($_GET["area"])){
			   $area = $configutil->splash_new($_GET["area"]);
			  // $query = $query." and a.location_a='".$area."'";
		   }
	   }
		
}
$Pmap=array();
$Pmap=$_SESSION['Pmap']; 
if($ktype==1){
$PCprovince=$_SESSION["PCprovince"];
$PCAcity=$_SESSION["PCcity"]; 
}
if($ktype==1 && strlen($province)==0){
	$Pmap=array();
}
$Cmap=array();
$Cmap=$_SESSION['Cmap']; 

if($ktype==1 && strlen($province)>0 && strlen($city)==0){
	$Cmap=array();
}
$Amap=array();
$Amap=$_SESSION['Amap']; 
if($ktype==1 && strlen($province)>0 && strlen($city)>0){
	$Amap=array();
}
if($ktype==2){
	$Pmap=array();
	$Cmap=array();
	$Amap=array();
	$province="";
	$_SESSION['Pmap']=$Pmap;
	$_SESSION['Cmap']=$Cmap;
	$_SESSION['Amap']=$Amap;
	$PCprovince="";
	$PCAcity="";
	$_SESSION["PCprovince"]=$PCprovince;
	$_SESSION["PCcity"]=$PCAcity; 
}
if(count($Pmap)==0 && strlen($province)==0){
if(!empty($_GET["search_status"])){
   $search_status=$configutil->splash_new($_GET["search_status"]);
}

$query="SELECT location_p,count(distinct o.batchcode) as num,location_c,location_a  from weixin_commonshop_order_addresses a inner join 
weixin_commonshop_orders o on a.batchcode = o.batchcode where o.isvalid = true and o.customer_id =".$customer_id;
if(empty($_GET["begintime"]) && empty($_GET["endtime"]))
{
	$query = $query." and month(o.createtime) = '".date('m')."' ";
}
else{
	$begintime = $configutil->splash_new($_GET["begintime"]);
   	$endtime = $configutil->splash_new($_GET["endtime"]);
    $query = $query." and date(o.createtime) between '{$begintime}' and '{$endtime}' ";
	// if(!empty($_GET["begintime"])){
	//    	$begintime = $configutil->splash_new($_GET["begintime"]);
	//     $query = $query." and o.createtime)>=".strtotime($begintime);
	// }
	// if(!empty($_GET["endtime"])){
	//    	$endtime = $configutil->splash_new($_GET["endtime"]);
	//     $query = $query." and UNIX_TIMESTAMP(o.createtime)<=".strtotime($endtime);
	// }
}
switch($search_status){
	case 1:
	//已确认
		$query = $query." and o.status=1";					   
		break;
	case 2:
	//未确认
		$query = $query." and o.status=0";					   
		break;
	case 3:
	//未确认
		$query = $query." and o.paystatus=1";					   
		break;
	case 4:
	//未确认
		$query = $query." and o.paystatus=0";					   
		break;
	case 5:
	//已发货
		$query = $query." and (o.sendstatus=1 or o.sendstatus=2)";		 			
		break;
	case 6:
	//未确认
		$query = $query." and o.sendstatus=0";					   
		break;
	case 7:
	//已退货
		$query = $query." and o.sendstatus=3";					   
		break;
	case 8:
	//已取消
		$query = $query." and o.status=-1";
		break;

}
if(!empty($_GET["province"])){
	 //  $province = $configutil->splash_new($_GET["province"]);	   
	   $query = $query." and a.location_p='".$province."'";
	   if(!empty($_GET["city"])){
		 //  $city = $configutil->splash_new($_GET["city"]);
		   $query = $query." and a.location_c='".$city."'";
		   if(!empty($_GET["area"])){
			//   $area = $configutil->splash_new($_GET["area"]);
			   $query = $query." and a.location_a='".$area."'";
		   }
	   }
		
}
$query = $query." group by location_p";
//echo $query;
$num=-1;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$Pmap = array();
while ($row = mysql_fetch_object($result)) {
	$location_p=$row->location_p;
	$num=$row->num;	
	$b = array();
	array_push($b,$location_p,$num);
	array_push($Pmap,$b);
	} 
	$PCAtype=1;
}
if(count($Cmap)==0  && strlen($province)>0 && strlen($city)==0){
$PCprovince=$province;
if(!empty($_GET["search_status"])){
   $search_status=$configutil->splash_new($_GET["search_status"]);
}

$query="SELECT location_p,count(distinct o.batchcode) as num,location_c,location_a  from weixin_commonshop_order_addresses a inner join 
weixin_commonshop_orders o on a.batchcode = o.batchcode where o.isvalid = true and o.customer_id =".$customer_id;
if(empty($_GET["begintime"]) && empty($_GET["endtime"]))
{
	$query = $query." and month(o.createtime) = '".date('m')."' ";
}
else{
	$begintime = $configutil->splash_new($_GET["begintime"]);
   	$endtime = $configutil->splash_new($_GET["endtime"]);
    $query = $query." and date(o.createtime) between '{$begintime}' and '{$endtime}' ";
	// if(!empty($_GET["begintime"])){
	//    	$begintime = $configutil->splash_new($_GET["begintime"]);
	//     $query = $query." and o.createtime)>=".strtotime($begintime);
	// }
	// if(!empty($_GET["endtime"])){
	//    	$endtime = $configutil->splash_new($_GET["endtime"]);
	//     $query = $query." and UNIX_TIMESTAMP(o.createtime)<=".strtotime($endtime);
	// }
}
switch($search_status){
	case 1:
	//已确认
		$query = $query." and o.status=1";					   
		break;
	case 2:
	//未确认
		$query = $query." and o.status=0";					   
		break;
	case 3:
	//未确认
		$query = $query." and o.paystatus=1";					   
		break;
	case 4:
	//未确认
		$query = $query." and o.paystatus=0";					   
		break;
	case 5:
	//已发货
		$query = $query." and (o.sendstatus=1 or o.sendstatus=2)";		 			
		break;
	case 6:
	//未确认
		$query = $query." and o.sendstatus=0";					   
		break;
	case 7:
	//已退货
		$query = $query." and o.sendstatus=3";					   
		break;
	case 8:
	//已取消
		$query = $query." and o.status=-1";
		break;

}
if(!empty($_GET["province"])){
	 //  $province = $configutil->splash_new($_GET["province"]);	   
	   $query = $query." and a.location_p='".$province."'";
	   if(!empty($_GET["city"])){
		 //  $city = $configutil->splash_new($_GET["city"]);
		   $query = $query." and a.location_c='".$city."'";
		   if(!empty($_GET["area"])){
			//   $area = $configutil->splash_new($_GET["area"]);
			   $query = $query." and a.location_a='".$area."'";
		   }
	   }
		
}
$query = $query." group by location_c";
//echo $query;
$num=-1;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$Cmap = array();
while ($row = mysql_fetch_object($result)) {
	$location_c=$row->location_c;
	$num=$row->num;	
	$map=array();
	array_push($map,$location_c,$num);
	array_push($Cmap,$map);
} 
$PCAtype=2;	
}
if(count($Amap)==0 && strlen($city)>0){
$PCAcity=$province.$city;

if(!empty($_GET["search_status"])){
   $search_status=$configutil->splash_new($_GET["search_status"]);
}

$query="SELECT location_p,count(distinct o.batchcode) as num,location_c,location_a  from weixin_commonshop_order_addresses a inner join 
weixin_commonshop_orders o on a.batchcode = o.batchcode where o.isvalid = true and o.customer_id =".$customer_id;
if(empty($_GET["begintime"]) && empty($_GET["endtime"]))
{
	$query = $query." and month(o.createtime) = '".date('m')."' ";
}
else{
	$begintime = $configutil->splash_new($_GET["begintime"]);
   	$endtime = $configutil->splash_new($_GET["endtime"]);
    $query = $query." and date(o.createtime) between '{$begintime}' and '{$endtime}' ";
	// if(!empty($_GET["begintime"])){
	//    	$begintime = $configutil->splash_new($_GET["begintime"]);
	//     $query = $query." and o.createtime)>=".strtotime($begintime);
	// }
	// if(!empty($_GET["endtime"])){
	//    	$endtime = $configutil->splash_new($_GET["endtime"]);
	//     $query = $query." and UNIX_TIMESTAMP(o.createtime)<=".strtotime($endtime);
	// }
}
switch($search_status){
	case 1:
	//已确认
		$query = $query." and o.status=1";					   
		break;
	case 2:
	//未确认
		$query = $query." and o.status=0";					   
		break;
	case 3:
	//未确认
		$query = $query." and o.paystatus=1";					   
		break;
	case 4:
	//未确认
		$query = $query." and o.paystatus=0";					   
		break;
	case 5:
	//已发货
		$query = $query." and (o.sendstatus=1 or o.sendstatus=2)";		 			
		break;
	case 6:
	//未确认
		$query = $query." and o.sendstatus=0";					   
		break;
	case 7:
	//已退货
		$query = $query." and o.sendstatus=3";					   
		break;
	case 8:
	//已取消
		$query = $query." and o.status=-1";
		break;

}
if(!empty($_GET["province"])){
	 //  $province = $configutil->splash_new($_GET["province"]);	   
	   $query = $query." and a.location_p='".$province."'";
	   if(!empty($_GET["city"])){
		 //  $city = $configutil->splash_new($_GET["city"]);
		   $query = $query." and a.location_c='".$city."'";
		   if(!empty($_GET["area"])){
			//   $area = $configutil->splash_new($_GET["area"]);
			   $query = $query." and a.location_a='".$area."'";
		   }
	   }
		
}
$query = $query." group by location_a";
//echo $query;
$num=-1;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$Amap = array();
while ($row = mysql_fetch_object($result)) {
	$location_a=$row->location_a;
	$num=$row->num;	
	$mapl=array();
	array_push($mapl,$location_a,$num);
	array_push($Amap,$mapl);
} 
$PCAtype=3;
}
?>
<?php 
$_SESSION["Pmap"] = $Pmap;
$_SESSION["Cmap"] = $Cmap;
$_SESSION["Amap"] = $Amap;
$_SESSION["PCprovince"] = $PCprovince;
$_SESSION["PCcity"] = $PCAcity;

if($num==-1){
	$num=0;
?>
<script>
provinces.push('<?php echo $province.$city.$area;?>');
counts.push(<?php echo $num;?>);
</script>
<?php }?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<!--<script type="text/javascript" src="//www.ichartjs.com/ichart.latest.min.js"></script>-->
<script language="javascript">

</script>

</head>
<style>
.search_time,.excel_orders{
	background: #1584D5;
	color: white;
	border: none;
	height: 22px;
	line-height: 22px;
	width: 80px;
}
.display{display:none}
.WSY_search_q li{font-size:14px;margin-left: 0px;}
</style>
<body>
<!-- 修改产品分类 begin -->

<!-- 修改产品分类 end -->

<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">

       <!--列表内容大框开始-->
	<div class="WSY_columnbox" style="min-height: 560px;">
    	<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a  class="typf white1" onclick="ClickType1(this)">各省（地区）订单分析</a>
				<a  class="typf" onclick="ClickType1(this)"><?php echo $PCprovince; ?>订单分析</a>
				<a  class="typf" onclick="ClickType1(this)"><?php echo $PCAcity; ?>订单分析</a>
				<input id="PCprovince" class="display"    value="<?php echo $PCprovince?>">
				<input id="PCAcity" class="display"    value="<?php echo $PCAcity?>">
			</div>
			<script>
			var Ktitle1="各省（地区）订单分析";
			function ClickType1(obj){
				var PCprovince=$("#PCprovince").val();
				var PCAcity=$("#PCAcity").val();
				Ktitle=$(obj).html();
				Ktitle1=Ktitle;
				Ktitle=Ktitle.substring(0,Ktitle.length-4);
				$(".typf").removeClass("white1");
				$(obj).addClass("white1");
				if(Ktitle!=PCprovince && Ktitle!=PCAcity ){
					$("#PCAtype").val(1);
					Ichartjs();
				}
				if(Ktitle!=null && Ktitle!=''){
					if(PCprovince.length>0 && Ktitle==PCprovince && Ktitle!=PCAcity ){
						$("#PCAtype").val(2);
						Ichartjs();
					}
					if(PCAcity.length>0 && Ktitle!=PCprovince && Ktitle==PCAcity ){
							$("#PCAtype").val(3);
							Ichartjs();
					}
				}else{
						alert("请选择地区！");
				}
			}
			</script>
		</div>
    
	
	
    <!--产品管理代码开始-->
    <div class="WSY_data">
    	<div class="WSY_agentsbox" ">
			<div style="text-align: center;"><span style="font-size: 15px;color: red;">温馨提示：默认初始页面只统计本月的订单量</span></div>
			<div class="WSY_search_q" style="display:block;" >
        <li style="width:38%;">
        	<span style="color: red;font-weight: bold;">注意：搜索时间跨度不能大于3个月</span>
			下单时间：&nbsp;
			<a>
			<input type="text" readonly="readonly" class="Wdate" style="width:20%;border: 1px solid #ccc;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="begintime" name="AccTime_S" value="<?php echo $begintime; ?>" >
			</a>-<a>
			<input type="text" readonly="readonly" class="Wdate" style="width:20%;border: 1px solid #ccc;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
			</a>
		</li>
		<li style="width:10%;"> 
		省:&nbsp;<select name="province" id="province"style="width:70%;border: 1px solid #ccc;height: 26px;border-radius: 3px;" ></select>
		</li>
		<li style="width:10%;"> 
		市:&nbsp;<select name="city" id="city" style="width:70%;border: 1px solid #ccc;height: 26px;border-radius: 3px;"></select>
		</li>
		<li style="width:10%;"> 
		区:&nbsp;<select name="area" id="area" style="width:70%;border: 1px solid #ccc;height: 26px;border-radius: 3px;"></select>
		</li>
		
		<!--<script src="../../Common/js/Data/js/region_select.js"></script>-->
		<script src="../../../common/region_select.js"></script>
		<script type="text/javascript">
	
			new PCAS('province', 'city', 'area', '<?php echo $province;?>', '<?php echo $city;?>', '<?php echo $area;?>',1);
		</script>
		
		<li style="width:16%;"> 
		订单状态：
		<select name="search_status" id="search_status" style="width:60%;border: 1px solid #ccc;height: 26px;border-radius: 3px;">
			<option value="-1">--请选择--</option>
			<option value="1" <?php if($search_status==1){ ?>selected <?php } ?>>已确认</option>
			<option value="2" <?php if($search_status==2){ ?>selected <?php } ?>>待确认</option>
			<option value="3" <?php if($search_status==3){ ?>selected <?php } ?>>已支付</option>
			<option value="4" <?php if($search_status==4){ ?>selected <?php } ?>>未支付</option>
			<option value="5" <?php if($search_status==5){ ?>selected <?php } ?>>已发货</option>
			<option value="6" <?php if($search_status==6){ ?>selected <?php } ?>>未发货</option>
			<option value="7" <?php if($search_status==7){ ?>selected <?php } ?>>申请退货</option>
			<option value="8" <?php if($search_status==8){ ?>selected <?php } ?>>已取消</option>			
		</select>
		</li>
		<li style="width:16%;" class="WSY_bottonliss">
		<input id="search_bar" type="button"  onclick="search_bar();" value="搜 索"  style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer;" />	
		<input  type="button"  onclick="search_bar1();" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer;" />
		</li>			
          </div>
		  <div>
			<input id="type" class="display"    value="<?php echo $type ?>">
			<input id="PCAtype" class="display"    value="<?php echo $PCAtype ?>">
		  
			<input id="Psize" class="display"    value="<?php echo count($Pmap)?>">
			<?php 
			for  ($i=0;  $i<count($Pmap);  $i++)  {  
			?>
			<input id="Pdate<?php echo $i?>" class="display"    value="<?php echo $Pmap[$i][0]?>">
			<input id="Pdata<?php echo $i?>" class="display"    value="<?php echo $Pmap[$i][1]?>" >
			<?php } ?>
		  </div>
		  <div>
		  <input id="Csize" class="display"    value="<?php echo count($Cmap)?>">
			<?php 
			for  ($i=0;  $i<count($Cmap);  $i++)  {  
			?>
			<input id="Cdate<?php echo $i?>" class="display"    value="<?php echo $Cmap[$i][0]?>">
			<input id="Cdata<?php echo $i?>" class="display"   value="<?php echo $Cmap[$i][1]?>" >
			<?php } ?>
		  </div>
		  <div>
			<input id="Asize"  class="display"    value="<?php echo count($Amap)?>">
			<?php 
			for  ($i=0;  $i<count($Amap);  $i++)  {  
			?>
			<input id="Adate<?php echo $i?>" class="display"    value="<?php echo $Amap[$i][0]?>">
			<input id="Adata<?php echo $i?>" class="display"    value="<?php echo $Amap[$i][1]?>" >
			<?php } ?>
		  </div>
    </div>

			<script type="text/javascript">
		var PCAtype=0;
		$(function() {
			
			PCAtype = $("#PCAtype").val();
			$(".typf").removeClass("white1");
			if(PCAtype==1){
				$(".WSY_columnnav").children("a").eq(0).addClass("white1");
			}
			if(PCAtype==2){
				$(".WSY_columnnav").children("a").eq(1).addClass("white1");
			}
			if(PCAtype==3){
				$(".WSY_columnnav").children("a").eq(2).addClass("white1");
			}
			
			Ichartjs();
		});
		
		
		
		function Ichartjs(){
			var PCAtype=$("#PCAtype").val();
			var Ksize;
			var oldArr=new Array();
			var newCounts = new Array();
			var totalCount=0;
			var PCprovince1=$("#PCprovince").val();
			if(PCAtype==1){
				Ksize=$("#Psize").val();
				for(var i = 0 ; i< Ksize ; i++){
					var k1=$("#Pdate"+i).val();
					var k2=$("#Pdata"+i).val();
					if(k2!=null){
						totalCount=totalCount+parseInt(k2);
					}
				oldArr.push(k1);
				newCounts.push(k2);
				}
				
			}
			if(PCAtype==2){
				Ksize=$("#Csize").val();
				for(var i = 0 ; i< Ksize ; i++){
					var k1=$("#Cdate"+i).val();
					var k2=$("#Cdata"+i).val();
					if(k2!=null){
						totalCount=totalCount+parseInt(k2);
					}
					
				oldArr.push(k1);
				newCounts.push(k2);
				}
				
			}
			if(PCAtype==3){
				Ksize=$("#Asize").val();
				for(var i = 0 ; i< Ksize ; i++){
					var k1=$("#Adate"+i).val();
					var k2=$("#Adata"+i).val();
					if(k2!=null){
						totalCount=totalCount+parseInt(k2);
					}
				oldArr.push(k1);
				newCounts.push(k2);
				}
				
			}
			//排序
			for(var i=0;i<Ksize;i++){
				for(var j=i;j<Ksize;j++){
					
					if(parseInt(newCounts[i])<parseInt(newCounts[j])){
						var temp=newCounts[i];
						newCounts[i]=newCounts[j];
						newCounts[j]=temp;
						var temp_2=oldArr[i];
						oldArr[i]=oldArr[j];
						oldArr[j]=temp_2;						
						}
				}
			}
			oldArr.push('其他');
			newCounts.push(0);
			//========================================
			
			
			var beginTime=$("#begintime").val();
			var endTime=$("#endtime").val();
			var title1="各省订单数分析";
			if(Ktitle1.length>0){
				title1=$(".WSY_columnnav").children("a").eq(PCAtype-1).html();
			}
			
			
			/*if(beginTime=="" && endTime!="" ){
				
				title1="截止到"+endTime+title1;
			}
			if(beginTime!="" && endTime=="" ){
				
				title1=beginTime+"到目前为止"+title1;
			}
			if(beginTime!="" && endTime!="" ){
				
				title1=beginTime+"~"+endTime+title1;
			}*/
	
			var data = [];
			for(var i=0;i<newCounts.length;i++){
				var k={name : oldArr[i],value : newCounts[i],color:'#06A7E1'}
				data.push(k);
			}
			
			var kData=(newCounts[0]/5);
			if(kData%10>0){
			kData=(parseInt(kData/10)+1)*10;
			}
			var endData=kData*5;
			
			var chart = new iChart.Column2D({
				render : 'canvasDiv',
				data : data,
				title : {
					text : title1,
					font : '微软雅黑',
					color : '#333333'
				},
				subtitle : {
					text : '总订单数：'+totalCount+'单',
					font : '微软雅黑',
					color : '#333333'
				},
		
				width : 800,
				height : 400,
				label : {
					fontsize:11,
					textAlign:'right',
					textBaseline:'middle',
					rotate:-45,
					color : '#666666'
				},
				tip:{
					enable:true,
					listeners:{
						 //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
						parseText:function(tip,name,value,text,i){
							//将数字进行千位格式化
							var f = new String(value);
							f = f.split("").reverse().join("").replace(/(\d{3})/g,"$1,").split("").reverse();
							if(f[0]==','){
								f.shift();
							}	
							f = f.join("");
							
							return name+"订单数为"+f+"单<br/>所占比重:"+(value/this.get('total') * 100).toFixed(2)+ '%';
						}
					}
				},
				animation : true,//开启过渡动画
				animation_duration:400,//800ms完成动画
				shadow : true,
				shadow_blur : 2,
				shadow_color : '#aaaaaa',
				shadow_offsetx : 1,
				shadow_offsety : 0,
				column_width : 62,
				sub_option : {
					label : false,
					border : {
						width : 2,
						color : '#ffffff'
					}
				},
				coordinate : {
					background_color : null,
					grid_color : '#dadada',
					width : 660,
					height:240,
					axis : {
						color : '#9f9f9f',
						width : [0, 0, 1, 0]
					},
					scale : [{
						position : 'left',
						start_scale : 0,
						end_scale : endData,
						scale_space : kData,
						scale_enable : false,
						label : {
							fontsize:11,
							color : '#333333'
						},
						listeners:{
							parseText:function(t,x,y){
								return {text:t}
							}
						 }
					}]
				},
				sub_option:{
					listeners:{
						/**
						 * r:iChart.Rectangle2D对象
						 * e:eventObject对象
						 * m:额外参数
						 */
						click:function(r,e,m){
							if(PCAtype==1){
								$("#begintime").val(beginTime);
								$("#endtime").val(endTime);
								//$("#province").val(r.get('name'));
								new PCAS('province', 'city', 'area', r.get('name'), '', '',1);
								$("#search_bar").click();
							}
							if(PCAtype==2){
								$("#begintime").val(beginTime);
								$("#endtime").val(endTime);
								//$("#province").val(r.get('name'));
								new PCAS('province', 'city', 'area', PCprovince1, r.get('name'), '',1);
								$("#search_bar").click();
							}
							if(PCAtype==3){
								$("#begintime").val(beginTime);
								$("#endtime").val(endTime);
								//$("#province").val(r.get('name'));
								new PCAS('province', 'city', 'area', '', '', '',1);
								$(".typf").removeClass("white1");
								$("#PCAtype").val(1);
								$(".WSY_columnnav").children("a").eq(0).addClass("white1");
								Ichartjs();	
								
							}
						}
					}
				}
			});
			
			//利用自定义组件构造左侧说明文本
			chart.plugin(new iChart.Custom({
					drawFn:function(){
						//计算位置
						var coo = chart.getCoordinate(),
							x = coo.get('originx'),
							y = coo.get('originy');
						//在左上侧的位置，渲染一个单位的文字
						chart.target.textAlign('start')
						.textBaseline('bottom')
						.textFont('600 11px 微软雅黑')
						.fillText('订单数（单）',x-40,y-10,false,'#333333');
						
					}
			}));
			var mwidth=800;
			var mheight=400;
			$("#canvasDiv").children().children().attr("width",mwidth);
			$("#canvasDiv").children().children().attr("height",mheight);
			chart.draw();
			
		}
		</script>
			
			
			<div style="margin-top:20px;margin-left: 100px">
            <div id='canvasDiv' style="float:left;width:800;height:400"></div>
			</div>
			<div style="float:left;width:20%">
				<ul>
				<li style="margin-top:10px" class="WSY_bottonliss"><input  type="button" class="search_btn" onclick="search_area();" value="地区列表"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
				<li style="margin-top:10px" class="WSY_bottonliss"><input  type="button" class="search_btn" onclick="excel_orders();" value="导出订单"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
				<li style="margin-top:10px" class="WSY_bottonliss"><input  type="button" class="search_btn" onClick="exportFeiDouRecord();" value="导出飞豆"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
				</ul>
			</div>
			<div style="height:20px"></div>
	<!--	 <div class="WSY_weixinbox" style="width: 65%;margin-left: 15%;">
			<div class="WSY_weixin">
                    <a>公众号登记</a>每个设置项标题
                </div>
            <div class="WSY_little001box">
    	</div>
		</div>
    <!--产品管理代码结束-->
	</div>
   </div>

	
<script>
function excel_orders(){
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var province = document.getElementById("province").value;
	var city = document.getElementById("city").value;
	var area = document.getElementById("area").value;
	var search_status = document.getElementById("search_status").value;
	var url='/weixin/plat/app/index.php/Excel/area_order_excel/customer_id/<?php echo $customer_id ?>/status/'+search_status+'/';
	
	if(begintime !=""){
		url=url+'begintime/'+begintime+'/';
	}
	if(endtime !=""){
		url=url+'endtime/'+endtime+'/';
	}
	if(province !=""){
		url=url+'province/'+province+'/';
	}
	if(city !=""){
		url=url+'city/'+city+'/';
	}
	if(area !=""){
		url=url+'area/'+area+'/';
	}
	var flag = checkTime(begintime,endtime);
	if(flag)
	{
		document.location=url;
	}
}
function exportFeiDouRecord(){
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var province = document.getElementById("province").value;
	var city = document.getElementById("city").value;
	var area = document.getElementById("area").value;
	var search_status = document.getElementById("search_status").value;
	var url='/weixin/plat/app/index.php/Excel/area_order_feidou_excel/shopname/<?php echo $shopname;?>/customer_id/<?php echo $customer_id ?>/status/'+search_status+'/';
	
	if(begintime !=""){
		url=url+'begintime/'+begintime+'/';
	}
	if(endtime !=""){
		url=url+'endtime/'+endtime+'/';
	}
	if(province !=""){
		url=url+'province/'+province+'/';
	}
	if(city !=""){
		url=url+'city/'+city+'/';
	}
	if(area !=""){
		url=url+'area/'+area+'/';
	}
	var flag = checkTime(begintime,endtime);
	if(flag)
	{
		document.location=url;
	}
}


function search_bar(){
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var province = document.getElementById("province").value;
	var city = document.getElementById("city").value;
	var area = document.getElementById("area").value;
	var search_status = document.getElementById("search_status").value;
	var url="order_BarChart.php?type=1&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&search_status="+search_status;
	
 	if(province !=""){
		url=url+'&province='+province;
	}
	if(city !=""){
		url=url+'&city='+city;
	}
	if(area !=""){
		url=url+'&area='+area;
	}
	if(begintime !=""){
		url=url+'&begintime='+begintime;
	}
	if(endtime !=""){
		url=url+'&endtime='+endtime;
	}
	var flag = checkTime(begintime,endtime);
	if(flag)
	{
		document.location=url;
	}
}
function search_bar1(){
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var province = document.getElementById("province").value;
	var city = document.getElementById("city").value;
	var area = document.getElementById("area").value;
	var search_status = document.getElementById("search_status").value;
	var url="order_BarChart.php?type=2&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&search_status="+search_status;
	document.location=url;
}

function search_area(){
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var province = document.getElementById("province").value;
	var city = document.getElementById("city").value;
	var area = document.getElementById("area").value;
	var search_status = document.getElementById("search_status").value;
	var url="order_BarChart_detailed.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&search_status="+search_status;
	if(province ==""){
		alert('请选择地区！');
		return;
	}
 	if(province !=""){
		url=url+'&province='+province;
	}
	if(city !=""){
		url=url+'&city='+city;
	}
	if(area !=""){
		url=url+'&area='+area;
	}
	if(begintime !=""){
		url=url+'&begintime='+begintime;
	}
	if(endtime !=""){
		url=url+'&endtime='+endtime;
	}
	var flag = checkTime(begintime,endtime);
	if(flag)
	{
		document.location=url;
	}
}
function checkTime(begintime,endtime){
	var time1 = new Date(begintime).getTime();
	var time2 = new Date(endtime).getTime();
	if(begintime==''){
	    alert("开始时间不能为空");
	    return false;
	}
	if(endtime==''){
	    alert("结束时间不能为空");
	    return false;
	}
	if(time1 > time2){
	    alert("开始时间不能大于结束时间");
	    return false;
	}

	//判断时间跨度是否大于3个月
	var arr1 = begintime.split('-');
	var arr2 = endtime.split('-');
	arr1[1] = parseInt(arr1[1]);
	arr1[2] = parseInt(arr1[2]);
	arr2[1] = parseInt(arr2[1]);
	arr2[2] = parseInt(arr2[2]);
	var flag = true;
	if(arr1[0] == arr2[0]){//同年
	    if(arr2[1]-arr1[1] > 3){ //月间隔超过3个月
	        flag = false;
	    }else if(arr2[1]-arr1[1] == 3){ //月相隔3个月，比较日
	        if(arr2[2] > arr1[2]){ //结束日期的日大于开始日期的日
	            flag = false;
	        }
	    }
	}else{ //不同年
	    if(arr2[0] - arr1[0] > 1){
	        flag = false;
	    }else if(arr2[0] - arr1[0] == 1){
	        if(arr1[1] < 10){ //开始年的月份小于10时，不需要跨年
	            flag = false;
	        }else if(arr1[1]+3-arr2[1] < 12){ //月相隔大于3个月
	            flag = false;
	        }else if(arr1[1]+3-arr2[1] == 12){ //月相隔3个月，比较日
	            if(arr2[2] > arr1[2]){ //结束日期的日大于开始日期的日
	                flag = false;
	            }
	        }
	    }
	}
	if(!flag){
	    alert("时间跨度不得超过3个月！");
	    return false;
	}
	return true;
}
</script>
<?php 

mysql_close($link);
?>
</body>
</html>