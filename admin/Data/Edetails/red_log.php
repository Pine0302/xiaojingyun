<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');

_mysql_query("SET NAMES UTF8");


/*if(!empty($_GET["customer_id"])){
   $customer_id = $configutil->splash_new($_GET["customer_id"]);
}*/ //前面引用的文件中有获取
$pagenum = 1;
$pagesize = 20;
$begintime="";
$endtime ="";
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagesize;
$end = $pagesize;


$change_type=-1;
//ktype页面选项卡
$ktype=0;

//接受页面参数
if(!empty($_GET["search_red"])){
   $search_red = $configutil->splash_new($_GET["search_red"]);
}
if(!empty($_GET["red_order"])){
   $red_order = $configutil->splash_new($_GET["red_order"]);
}
if(!empty($_GET["deal_id"])){
   $deal_id = $configutil->splash_new($_GET["deal_id"]);
}
if(!empty($_GET["name"])){
   $name = $configutil->splash_new($_GET["name"]);
}
if(!empty($_GET["begintime"])){
   $begintime = $configutil->splash_new($_GET["begintime"]);
}
if(!empty($_GET["endtime"])){
   $endtime = $configutil->splash_new($_GET["endtime"]);
}
if(!empty($_GET["change_type"])){
   $change_type = $configutil->splash_new($_GET["change_type"]);
}
if(!empty($_GET["ktype"])){
   $ktype = $configutil->splash_new($_GET["ktype"]);
}
if($ktype>0){
if(empty($_GET["endtime"])){
   $endtime = date("Y-m-d");
}	
}
$a_time = strtotime($endtime);
if($ktype>0){
	$b_time = strtotime('-1 Month',$a_time);
	$begintime = date('Y-m-d',$b_time);
}
$total_money=0;
$query="select r.id,u.name,u.weixin_name,customer_red_id,weixin_red_id,r.remark,user_id,r.type,r.deal_id,r.createtime,red_money from weixin_red_log r inner join weixin_users u on r.user_id=u.id where r.isvalid=true and r.customer_id=".$customer_id."";
if(!empty($search_red)){			   
	$query = $query." and r.weixin_red_id like '%".$search_red."%'";
}
if(!empty($red_order)){			   
	$query = $query." and r.customer_red_id like '%".$red_order."%'";
}
if(!empty($deal_id)){			   
	$query = $query." and r.deal_id like '%".$deal_id."%'";
}
if(!empty($name)){			   
	$query = $query." and u.name like '%".$name."%'";
}
if(!empty($begintime)){			   
	$query = $query." and UNIX_TIMESTAMP(r.createtime)>".strtotime($begintime);
}
if(!empty($endtime)){			   
	$query = $query." and UNIX_TIMESTAMP(r.createtime)<".strtotime($endtime);
}
if(!empty($name)){			   
	$query = $query." and u.weixin_name like '%".$name."%'";
}
if($change_type!=-1){			   
	$query = $query." and r.type=".$change_type;
}

 //echo $query;
  /* 输出数量开始 */
$result = _mysql_query($query) or die('Query failed2: ' . mysql_error());
$rcount_q = mysql_num_rows($result);
$page=ceil($rcount_q/$end); 
 /* 输出数量结束 */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>商户支出明细</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>

<script src="../../Common/js/Data/js/echarts/echarts.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<style>
table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}

</style>

</head>

<body id="bod" style="min-height: 580px;">
	<!--内容框架-->
	<div class="WSY_content" style="height: 100%;">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="typf" onclick="ClickType(this)">商户支出明细</a>
					<a class="typf" onclick="ClickType(this)">每日支出分析</a>
					<a class="typf" onclick="ClickType(this)">会员奖励统计</a>
				</div>
			</div>
			<!--列表头部切换结束-->
<script>
		var  ktype=0;
		//点击选项卡事件
		function ClickType(obj){
			var endtime=$("#endtime").val();
			$(".typf").removeClass("white1");
			$(obj).addClass("white1");
			var type=$(obj).html();
			if(type=="商户支出明细"){
				ktype=0;
				$("#type1").removeClass("display");
				$("#type2").addClass("display");
				$("#type3").addClass("display");
				$("#searchtype").removeClass("display");
				$("#searchtype1").removeClass("display");
				$("#searchtype4").addClass("display");
				$("#searchtype3").removeClass("display");
			}
			if(type=="每日支出分析"){
				ktype=1;
				$("#type2").removeClass("display");
				$("#type1").addClass("display");
				$("#type3").addClass("display");
				$("#searchtype").addClass("display");
				$("#searchtype1").addClass("display");
				$("#searchtype4").removeClass("display");
				$("#searchtype3").addClass("display");
				if(endtime=='' || endtime==null){
				search_redname();
				}else{
				search_type2();}
			}
			if(type=="会员奖励统计"){
				ktype=2;
				$("#type3").removeClass("display");
				$("#type1").addClass("display");
				$("#type2").addClass("display");
				$("#searchtype").addClass("display");
				$("#searchtype1").addClass("display");
				$("#searchtype4").removeClass("display");
				$("#searchtype3").addClass("display");
				if(endtime=='' || endtime==null){
				search_redname();
				}else{
				search_type3();}
			}
			
		}		
</script>
<!--门店列表开始-->
  <div  class="WSY_data">
	 <!--列表按钮开始-->
      <div class="WSY_list" id="WSY_list">
        	<div class="WSY_left" style="background-image:url('');width:95%;margin-top: 1px;padding:0px">
			<div >
			<span id="searchtype" class="display">
			<a  style="margin-top: 0px;display:  inline-block;">
			微信商户单号
			<span class="WSY_generalize_dl08" >
			<input type=text name="search_red" id="search_red" value="<?php echo $search_red; ?>" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"/>
			</span>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			<a style="margin-top: 0px;display:  inline-block;">
			红包单号
			<span class="WSY_generalize_dl08" >
			<input type=text name="red_order" id="red_order" value="<?php echo $red_order; ?>" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"/>
			</span>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			<a style="margin-top: 0px;display:  inline-block;">
			订单号
			<span class="WSY_generalize_dl08" >
			<input type=text name="deal_id" id="deal_id" value="<?php echo $deal_id; ?>" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"/>
			</span>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			<a style="margin-top: 0px;display:  inline-block;">
			名称
			<span class="WSY_generalize_dl08" >
			<input type=text name="name" id="name" value="<?php echo $name; ?>" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;" />
			</span>
			</a>
			<a style="width:1%;margin-top: 0px;display:  inline-block;"></a>
			</span>
			<a style="margin-top: 0px;display:  inline-block;">
			时间
			<span class="WSY_generalize_dl08" >
			<span id="searchtype3" class="display">
			<input type="text" class="input Wdate" style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="begintime" name="AccTime_A" value="<?php echo $begintime; ?>" maxlength="21" id="K_1389249066532" />
			-
			</span>
			<input type="text" class="input  Wdate"  style="border: 1px solid #CFCBCB;height: 24px;margin-bottom: 5px;border-radius: 2px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="endtime" name="AccTime_B" value="<?php echo $endtime; ?>" maxlength="20" id="K_1389249066580" />
			</span>
			</a>
			<span id="searchtype4" class="display">
			<a   style="width:20%;display:  inline-block;padding:0px" class="WSY_bottonliss">
            <input  type="button" class="search_btn" onclick="search_redname();" value="搜 索" style="width:40%;margin-left:9%;border-radius: 2px;height:24px;color:#fff;cursor: pointer; " >  
            </a>
			</span>
			</div>
			<div id="searchtype1" class="display">
			 <a style="width:16%;margin-top: 0px;display: inline-block;">红包类型
			<span class="WSY_generalize_dl08" >
   		    <select id="change_type" name="change_type" class="WSY_generalize_dl08_07" style="width:60%;padding: 0px 0px;border-radius: 3px;background-color:#fff"  onchange="change_type(this.value);">
				<option value=-1 <?php if($change_type==-1){ ?>selected<?php } ?>>--请选择--</option>
				<option value=1 <?php if($change_type==1){ ?>selected<?php } ?>>佣金红包</option>
				<option value=2 <?php if($change_type==2){ ?>selected<?php } ?>>微信零钱</option>
				<option value=3 <?php if($change_type==3){ ?>selected<?php } ?>>用户组红包</option>
			</select>
			</span>
			</a>
			<script>
			var PageURL="";
			$(function(){
				//跳转选项卡
				$("#change_type").click(function(){
        		if($("#change_type").val()!=-1){
        			$("#change_type").css({"background-color":"#FFF"});
        		}
        		if($("#change_type").val()==-1){
        			$("#change_type").css({"background-color":"#E4E4E4"});
        		}
        	});
			var liuheight = $(window).height(); 
			if ( liuheight <= 500 )  
			{ 
			liuheight= 580; 
			} 
			else 
			{ 
			liuheight= $(window).height(); 
			}  
			$("#bod").height(liuheight); 
			
			//onload开始时加载选项卡
			ktype = <?php echo $ktype ?>;
			$(".typf").removeClass("white1");
			if(ktype==0){
				$(".WSY_columnnav").children("a").eq(0).addClass("white1");
				$("#type1").removeClass("display");
				$("#type2").addClass("display");
				$("#type3").addClass("display");
				$("#searchtype").removeClass("display");
				$("#searchtype1").removeClass("display");
				$("#searchtype4").addClass("display");
				$("#searchtype3").removeClass("display");
			}
			if(ktype==1){
				$(".WSY_columnnav").children("a").eq(1).addClass("white1");
				$("#type2").removeClass("display");
				$("#type1").addClass("display");
				$("#type3").addClass("display");
				$("#searchtype").addClass("display");
				$("#searchtype1").addClass("display");
				$("#searchtype4").removeClass("display");
				$("#searchtype3").addClass("display");
				search_type2();
				
			}
			if(ktype==2){
				$(".WSY_columnnav").children("a").eq(2).addClass("white1");
				$("#type3").removeClass("display");
				$("#type1").addClass("display");
				$("#type2").addClass("display");
				$("#searchtype").addClass("display");
				$("#searchtype1").addClass("display");
				$("#searchtype4").removeClass("display");
				$("#searchtype3").addClass("display");
				search_type3();
			}
			//记录翻页条件
			var search_red = document.getElementById("search_red").value;
			var change_type = document.getElementById("change_type").value;
			var name = document.getElementById("name").value;
			var deal_id = document.getElementById("deal_id").value;
			var red_order = document.getElementById("red_order").value;
			var begintime = document.getElementById("begintime").value;
			var endtime = document.getElementById("endtime").value;
			 PageURL="search_red="+search_red+"&name="+name+"&deal_id="+deal_id+"&red_order="+red_order+"&customer_id=<?php echo $customer_id_en;?>";
			if(change_type!=-1){
				PageURL=PageURL+"&change_type="+change_type;
			}
			if(begintime !=""){
				PageURL=PageURL+"&begintime="+begintime;
			}
			if(endtime !=""){
				PageURL=PageURL+"&endtime="+endtime;
			}
			});
			
			
			</script>
			<a style="width:20%;display:  inline-block;padding:0px" class="WSY_bottonliss">
            <input   type="button" class="search_btn" onclick="search_redname();" value="搜 索" style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >  
			<input  type="button" class="search_btn" onclick="exportRed();" value="导出数据"  style="width:40%;margin-left:9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	
            </a>
			</div>
          </div>
             <br class="WSY_clearfloat";>
        </div>
        <!--列表按钮开始-->
		
        <!--表格开始-->
		<div class="WSY_data" id="type1" style="margin-left: 1.5%;">
		
		<table class="WSY_t2"  width="97%"  style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			<thead class="WSY_table_header">
			<?php 
				 /* $query1="select sum(red_money) as total_money from weixin_red_log where isvalid=true and customer_id=".$customer_id; */
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$red_money=$row->red_money;
					$total_money=$total_money+$red_money;
					
				}
			?>
			<!--	<tr style="background: #fff;">
					<td colspan="9">
					总共发出红包:<span style="color:red;font-size:22px;"><?php echo $total_money;?></span>元
					</td>
				</tr>
				-->
				<tr style="border:none">
					<th width="4%" >ID</th>
					<th width="8%" >红包单号</th>
					<th width="12%">微信商户单号</th>
					<th width="8%">订单号/会员卡号</th>
					<th width="8%">名称(微信名称)</th>
					<th width="8%">类型</th>
					<th width="14%">确认时间</th>
					<th width="6%">红包金额</th>
					<th width="12%">备注</th>
				</tr>
			</thead>
			<tbody>
			<?PHP
				
				$query = $query." order by id desc"." limit ".$start.",".$end;
				
				//echo $query;
				$result1 = _mysql_query($query) or die('Query failed1: ' . mysql_error());
				
				$sun_money=0;
				$tcount_1=array();
				$tcount_2=array();
				$flag=0;
				$Qtime9=date('Y-m-d',strtotime('-30 day',$a_time ));
				if($Qtime9=$begintime){
					//$flag=8;
				}
				for($i=0;$i<35;$i++){
					$k=$i;
					$cd='-'.$k.' day';
				$b_time1 = strtotime($cd,$a_time );
				$Qtime1=date('Y-m-d',$b_time1);
				if($flag<1){
					$tcount1=0;
					$tcount2=0;
					array_push($tcount_1,$flag);
					array_push($tcount_2,$Qtime1);
				if($Qtime1==$begintime){
					$flag=1;
				}
				}
				}
				
				
				$tcount=array();
					while ($row = mysql_fetch_object($result1)) {
						$map=array();
						$log_id=$row->id;
						$customer_red_id=$row->customer_red_id;//红包单号
						$weixin_red_id=$row->weixin_red_id;//微信商户单号
						$remark=$row->remark;//备注
						$user_id=$row->user_id;
						$type=$row->type;//红包类型
						$deal_id=$row->deal_id;//订单号/会员卡号
						$createtime=$row->createtime;
						$red_money=$row->red_money;
						$username=$row->name;
						$weixin_name=$row->weixin_name;
						$sun_money=$sun_money+$red_money;
						$type_name="佣金红包";
						if($type==1){
							$type_name="佣金红包";
						}else if($type==2){
							$type_name="微信零钱";
						}else if($type==3){
							$type_name="用户组红包";
						}
						/* $query2="select name,weixin_name from weixin_users where isvalid=true and id=".$user_id;
						$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
						while ($row2 = mysql_fetch_object($result2)) {
							$username=$row2->name;
							$weixin_name=$row2->weixin_name;
						} */
			?>
				<tr style="border:1px solid #D8D8D8">
					<td><?php echo $log_id;?></td>
					<td><?php echo $customer_red_id;?></td>
					<td><?php echo $weixin_red_id;?></td>
					<td><?php echo $deal_id;?></td>
					<td><?php echo $username;?>(<?php echo $weixin_name;?>)</td>
					<td><?php echo $type_name;?></td>
					<td><?php echo $createtime;?></td>
					<td><?php echo $red_money;?></td>
					<td><?php echo $remark;?></td>
				</tr>
			<?PHP }?> 
			
			</tbody>
			
			</table>
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
		<script src="../../../js/fenye/jquery.page1.js"></script>
		<script type="text/javascript">
		 var pagenum = <?php echo $pagenum ?>;
		  var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页
			
			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "red_log.php?pagenum="+p+"&"+PageURL;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "red_log.php?pagenum="+a+"&"+PageURL;
			}
		  }	
		</script>
		
        <div id="type2" class="WSY_data display">
		<?PHP 
		//===========================（每天支出分析:红包）=====================================//
		$monthTotalRedMoney=0;//当月总数
		if($ktype>0){
			for($i=0;$i<(count($tcount_2));$i++){
				if($i==0){
					$STime=$tcount_2[$i];
					$b_time1 = strtotime('1 day',$a_time );
					$Qtime1=date('Y-m-d',$b_time1);
					$ETime=$Qtime1;
				}else{
					$STime=$tcount_2[$i];
					$ETime=$tcount_2[$i-1];
				}

				
		$query1="select sum(red_money) sum from weixin_red_log where isvalid=true and customer_id=".$customer_id."  and UNIX_TIMESTAMP(createtime)>".strtotime($STime)." and UNIX_TIMESTAMP(createtime)<=".strtotime($ETime);
				
				$tcount7;
				$result2 = _mysql_query($query1) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result2)) {
				$tcount7 = $row->sum;
				}
				if($tcount7>0){
					$monthTotalRedMoney=$monthTotalRedMoney+$tcount7;
					$tcount_1[$i]=$tcount7;
				}else{
					$tcount7=0;
					$tcount_1[$i]=$tcount7;
				}
				
			}
		}	
		?> 
		<div>
		
		<input id="monthTotalRedMoney"  class="display" value="<?php echo $monthTotalRedMoney ?>">
		<input id="ksize"  class="display" value="<?php echo count($tcount_2)?>">
		<?php 
		 for  ($i=0;  $i<count($tcount_2);  $i++)  {  
		?>
		<input id="date<?php echo $i?>"  class="display" value="<?php echo $tcount_2[$i]?>">
		<input id="data<?php echo $i?>"  class="display" value="<?php echo $tcount_1[$i]?>" >
		<?php } ?>
		
		

		<script>
		function search_type2(){
			var monthTotalRedMoney=$("#monthTotalRedMoney").val();
			var kbegintime=$("#begintime").val();
			var kendtime=$("#endtime").val(); 
			var ksize=$("#ksize").val();
			
			var data1= new Array();
			var data2= new Array();
			//data1=[5, 20, 30, 10, 10, 20];
			//总页面加载数据
			var Total=0;
			var max=0;
			var Tlabels=[];
			for(var i=0;i<ksize;i++){
			var k=$("#data"+i).val();
			Total+=parseInt(k);
				if(max<parseInt(k)){
					max=parseInt(k);
				}
			data1.push(k);
			 k=$("#date"+i).val();
			 Tlabels.push(k);
			data2.push(k.substring(k.lastIndexOf("-")+1));
			}
			data1.reverse();//数组倒序
			Tlabels.reverse();
			data2.reverse();//数组倒序
			var step=1;
			var Tmax=max;
			var T=0;
			var Kmax=1;
			for(var i=0;i<100;i++){
				Tmax=parseInt(Tmax/10);
				if(Tmax<10){
					T=i;
					break;
				}
			}
			Tmax=(Tmax+1)*10;
			for(var i=0;i<T;i++){
				Kmax=Kmax*10;
			}
			var step=(parseInt(Tmax/5)*Kmax);
			if(step==0){
				step=1;
			}
//***********************************************************************************************
				var data = [
				         	{
				         		name : 'PV',
				         		value:data1,
				         		color:'#0d8ecf',
				         		line_width:2
				         	}
				         ];
		         
				var labels = data2;
				
				var chart = new iChart.LineBasic2D({
					render : 'canvasDiv',
					data: data,
					align:'center',
					shadow_color : '#202020',
					animation : true,//开启过渡动画
					animation_duration:600,//600ms完成动画
					shadow_blur : 8,
					shadow_offsetx : 0,
					shadow_offsety : 0,
					border: '2px solid #FBFBFB',
					background_color:'#FBFBFB',
					title : {
						text:'当月总支出:'+Total+'元',
						font : '微软雅黑',
						color:'#333333'
					},
					width : 800,
					height : 400,
					sub_option:{
						smooth : true,
						label:false,
						hollow:false,
						hollow_inside:false,
						point_size:6
					},
					tip:{
						enable:true,
						shadow:true,
						listeners:{
							 //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
							parseText:function(tip,name,value,text,i){
								return "<span style='color:#005268;font-size:12px;'>"+Tlabels[i]+"<br/>"+
								" </span><span style='color:#005268;font-size:12px;'>支出:"+value+"元</span>";
							}
						}
					},
					legend : {
						enable : false
					},
					crosshair:{
						enable:true,
						line_color:'#dadada'
					},
					coordinate:{
						width:640,
						height:260,
						striped_factor : 0.10,
						grid_color:'#dadada',
						axis:{
							color:'#9f9f9f',
							width:[0,0,2,2]
						},
						grids:{
							vertical:{
								way:'share_alike',
						 		value:12
							}
						},
						scale:[{
							 position:'left',	
							 start_scale:0,
							 end_scale:step*5,
							 scale_space:step,
							 scale_size:2,
							 scale_enable : false,
							 label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
							 scale_color:'#333333'
						},{
							position:'bottom',	
							label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
							scale_enable : true,
							labels:labels
						}]
					}
				});
							//利用自定义组件构造左侧说明文本
			chart.plugin(new iChart.Custom({
					drawFn:function(){
						//计算位置
						var coo = chart.getCoordinate(),
							x = coo.get('originx'),
							y = coo.get('originy'),
							w = coo.width,
							h = coo.height;
						//在左上侧的位置，渲染一个单位的文字
						chart.target.textAlign('start')
						.textBaseline('bottom')
						.textFont('600 11px 微软雅黑')
						.fillText('支出(元)',x-25,y-12,false,'#333333')
						.textBaseline('top')
						.fillText('(号)',x+w+12,y+h+7,false,'#333333');
						
					}
			}));
			//开始画图
			chart.draw();
		}
		</script>
		<div class="WSY_weixinbox" style="width: 800px;margin-left: 15%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;">
			<div class="WSY_weixin">
                    <a><?php echo $begintime?>到<?php echo $endtime?>一个月支出分析</a>
                </div>
			<div style="padding:1px">
			<div class="WSY_little001box" id="canvasDiv" style="width: 800px;border-radius: 5px;background-color: transparent;cursor: default;height: 300px;margin-top: 8px;height:400px">
			</div>
			</div>
		</div>
		
		
		
		</div>
		</div>
		<div id="type3" class="WSY_data display">
		<?PHP 
		if($ktype>0){
		//===========================(会员月前十奖励者)=====================================\\	
				
		$query2="select sum(red_money) sum ,r.user_id from weixin_red_log r  where r.isvalid=true and r.customer_id=".$customer_id."  and UNIX_TIMESTAMP(r.createtime)>".strtotime($begintime)." and UNIX_TIMESTAMP(r.createtime)<=".strtotime($endtime)." group by r.user_id order by sum(red_money) desc";
		//echo $query2;
		$nameSum=array();	
		$kcount=0;//总金额
		$ik=0;//
				$result3 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result3)) {
				$ik=$ik+1;
				$map=array();
				$sum = $row->sum;
				$user_id=$row->user_id;
				$uname="";
				if($ik<11){
				$query3="select weixin_name,name from  weixin_users where id=".$user_id." limit 0,1";
				$result4 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
				while ($row1 = mysql_fetch_object($result4)) {
					$weixin_name = $row1->weixin_name;
					$name = $row1->name;
					$uname=$name.'('.$weixin_name.')';
				}
				}
				$monthTotalRedMoney=$monthTotalRedMoney+$sum;
				if($ik<10){
					array_push($map,$uname,$sum);
					array_push($nameSum,$map);
				}else{
				$kcount=$kcount+$sum;	
				}
				
				}	
				if($kcount>0){
					$map=array();
					array_push($map,'其他',$kcount);
					array_push($nameSum,$map);
				}
		}	
		?> 
		<div>
		<input id="lmonthTotalRedMoney" class="display"  value="<?php echo $monthTotalRedMoney?>">
		<input id="lsize" class="display"  value="<?php echo count($nameSum)?>">
		<?php 
		 for  ($i=0;  $i<count($nameSum);  $i++)  {  
		?>
		<input id="name<?php echo $i?>" class="display"  value="<?php echo $nameSum[$i][0]?>">
		<input id="datat<?php echo $i?>" class="display"  value="<?php echo $nameSum[$i][1]?>" >
		<?php } ?>
		<script>
		function search_type3(){
			var lmonthTotalRedMoney=$("#lmonthTotalRedMoney").val();
			var kbegintime=$("#begintime").val();
			var kendtime=$("#endtime").val(); 
			var lsize=$("#lsize").val();
			
		var data1= new Array();
		var data2= new Array();
		var data=[];
		var Total=0;
		var max=0;
		//data1=[5, 20, 30, 10, 10, 20];
		if(lsize<20){
			lsize=20;
		}
		for(var i=0;i<lsize;i++){
		var k=$("#datat"+i).val();
		
		if(k==null){
			data1.push(0);
			data2.push("会员("+(i+1)+")");
			data.push({name : "会员("+(i+1)+")",value : 0,color:'#0d8ecf'});
		
		}else{
		data1.push(k);
		Total+=parseInt(k);
				if(max<parseInt(k)){
					max=parseInt(k);
				}
		 var k1=$("#name"+i).val();
		 
		data2.push(k1);
		if(i<20){
			data.push({name : k1,value : k,color:'#0d8ecf'});
		}
		
		
		
		}
		}
		var step=1;
			var Tmax=max;
			var T=0;
			var Kmax=1;
			for(var i=0;i<100;i++){
				Tmax=parseInt(Tmax/10);
				if(Tmax<10){
					T=i;
					break;
				}
			}
			Tmax=(Tmax+1)*10;
			for(var i=0;i<T;i++){
				Kmax=Kmax*10;
			}
			var step=(parseInt(Tmax/5)*Kmax);
			if(step==0){
				step=1;
			}
//***********************************************************************************************

			
			var chart = new iChart.Column2D({
				render : 'canvasDiv1',
				data : data,
				title : {
					text : '本月总奖励支出：'+Total+"元",
					color : '#333333'
				},
				width : 800,
				height : 400,
				animation : true,//开启过渡动画
				animation_duration:600,//600ms完成动画
				border: '2px solid #FBFBFB',
				background_color:'#FBFBFB',
				label : {
					fontsize:11,
					textAlign:'right',
					textBaseline:'middle',
					rotate:-60,
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
							
							return name+"会员:<br/>获取奖励:"+value+"(元)<br/>占百分比:"+(value/this.get('total') * 100).toFixed(2)+ '%';
						}
					}
				},
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
					grid_color : '#c0c0c0',
					width : 660,
					height:240,
					axis : {
						color : '#c0d0e0',
						width : [0, 0, 1, 0]
					},
					scale : [{
						position : 'left',
						start_scale : 0,
						end_scale : step*5,
						scale_space : step,
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
						.textFont('600 11px Verdana')
						.fillText('奖励支出(元)',x-40,y-10,false,'#333333');
						
					}
			}));
			
			chart.draw();
			var mwidth=800;
			var mheight=400;
			$("#canvasDiv").children().children().attr("width",mwidth);
			$("#canvasDiv").children().children().attr("height",mheight);
			//alert("0101"+$("#canvasDiv").children().html());

		}
		</script>
		<div class="WSY_weixinbox" style="width: 800px;margin-left: 15%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;">
			<div class="WSY_weixin">
                    <a><?php echo $begintime?>到<?php echo $endtime?>一个月支出分析</a>
                </div>
			<div>
			<div class="WSY_little001box" id="canvasDiv1" style="width: 800px;border-radius: 5px;background-color: transparent;cursor: default;height: 300px;margin-top: 8px;height:400px">
			</div>
			</div>
		</div>
		</div>
		</div>
	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<!--<script src="../../js/fenye/jquery.page.js"></script>-->
<script>
	
function search_redname(){
	var search_red = document.getElementById("search_red").value;
	var change_type = document.getElementById("change_type").value;
	var name = document.getElementById("name").value;
	var deal_id = document.getElementById("deal_id").value;
	var red_order = document.getElementById("red_order").value;
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var url="red_log.php?pagenum=1&ktype="+ktype+"&search_red="+search_red+"&name="+name+"&deal_id="+deal_id+"&red_order="+red_order+"&customer_id=<?php echo $customer_id_en;?>";
	if(change_type!=-1){
		url=url+"&change_type="+change_type;
	}
	if(begintime !=""){
		url=url+"&begintime="+begintime;
	}
	if(endtime !=""){
		url=url+"&endtime="+endtime;
	}
	document.location= url;
}
function change_type(change_type){
	var search_red = document.getElementById("search_red").value;
	document.location= "red_log.php?pagenum=1&change_type="+change_type+"&search_red="+search_red+"&customer_id=<?php echo $customer_id_en;?>";	

	
}
function exportRed(){
	var change_type = document.getElementById("change_type").value;
	var begintime = document.getElementById("begintime").value;
	var endtime = document.getElementById("endtime").value;
	var search_red = document.getElementById("search_red").value;
	var red_order = document.getElementById("red_order").value;
	var deal_id = document.getElementById("deal_id").value;
	var name = document.getElementById("name").value;
	var url='/weixin/plat/app/index.php/Excel/red_excel/customer_id/<?php echo $customer_id; ?>/change_type/'+change_type;
	if(begintime !=""){
		url=url+'/begintime/'+begintime+'/';
	}
	if(endtime !=""){
		url=url+'/endtime/'+endtime+'/';
	}
	if(search_red !=""){
		url=url+'/search_red/'+search_red+'/';
	}
	if(red_order !=""){
		url=url+'/red_order/'+red_order+'/';
	}
	if(deal_id !=""){
		url=url+'/deal_id/'+deal_id+'/';
	}
	if(name !=""){
		url=url+'/name/'+name+'/';
	}
	console.log(url);
	document.location=url;
	//goExcel(url,1,'//<?php echo $http_host;?>/weixinpl/');
}
</script>

<?php 

mysql_close($link);
?>

</body>
</html>
