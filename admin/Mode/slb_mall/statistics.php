<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php');
$customer_id = passport_decrypt($customer_id);  //解密
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$INDEX = $configutil->splash_new($_GET["INDEX"]);
$Itype = $configutil->splash_new($_GET["Itype"]);
$id =$configutil->splash_new($_POST["id"]);//switch选择器
$type=$configutil->splash_new($_POST["type"]);//时间类型1：月2：季度3：年4：星期5：本田
$SOtype=$configutil->splash_new($_POST["SOtype"]);//在同switch选择器下的辅助选择器
$Ptype=$configutil->splash_new($_POST["Ptype"]);//商品类型
$tcount=0;
switch($id){
	case 1:
		$begintime="";
		$endtime ="";
		if(empty($_POST["begintime"])){
			$begintime = date("Y-m-d");
		}else{
			$begintime = $configutil->splash_new($_POST["begintime"]);
		}
		$cond="";
		$grup="";
	//=================================(算时间段)=================================
			if($type==1){
				$cond="%Y-%m-%d";
				$grup="days";
				$a_time = strtotime($begintime);
				$begintime = date('Y-m-01', $a_time);
				$a_time = strtotime($begintime);
				$endtime = date("Y-m-d",strtotime('+1 Month',$a_time));
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<32;$i++){
					$k=$i;
					$cd='+'.$k.' day';
					$b_time = strtotime($cd,$a_time );
					$Qtime=date('Y-m-d',$b_time);
					if($flag<1){
						array_push($tcount_2,$Qtime);
						if($b_time==strtotime($endtime)){
							$flag=1;
						}
					}
				}
			}
			if($type==2){
				$cond="%Y-%m";
				$grup="months";
				$tcount=array();
				$a_time = strtotime($begintime);
				$begintime=date('Y-01-01', $a_time);
				$b_time1 = strtotime('-3 Month',strtotime($begintime));
				$a_time1 = strtotime($begintime);
				$a_time2 = strtotime('+3 Month',strtotime($begintime));
				$a_time3 = strtotime('+6 Month',strtotime($begintime));
				$a_time4 = strtotime('+9 Month',strtotime($begintime));
				$a_time5 = strtotime('+1 Year',strtotime($begintime));
				$endtime = date("Y-m-d",$a_time5);
				if($a_time>=$b_time1 &&$a_time< $a_time1 ){
					$begintime=date('Y-m-01', $b_time1);
					$endtime=date('Y-m-01', $a_time1);
				}
				if($a_time>=$a_time1 &&$a_time< $a_time2){
					$begintime=date('Y-m-01', $a_time1);
					$endtime=date('Y-m-01', $a_time2);
				}
				if($a_time>=$a_time2 &&$a_time< $a_time3){
					$begintime=date('Y-m-01', $a_time2);
					$endtime=date('Y-m-01', $a_time3);
				}
				if($a_time>=$a_time3 &&$a_time< $a_time4){
					$begintime=date('Y-m-01', $a_time3);
					$endtime=date('Y-m-01', $a_time4);
				}
				if($a_time>=$a_time4 &&$a_time< $a_time5){
					$begintime=date('Y-m-01', $a_time4);
					$endtime=date('Y-m-01', $a_time5);
				}
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<4;$i++){
				$k=$i;
				$cd='+'.$k.' Month';
				$b_time = strtotime($cd,strtotime($begintime));
				$Qtime=date('Y-m-d',$b_time);
				if($flag<1){
					array_push($tcount_2,$Qtime);
					if($b_time==strtotime($endtime)){
						$flag=1;
						}
					}
				}
			}
			if($type==3){
				$cond="%Y-%m";
				$grup="months";
				$tcount=array();
				$a_time = strtotime($begintime);
				$begintime=date('Y-01-01', $a_time);
				$a_time5 = strtotime('+1 Year',strtotime($begintime));
				$endtime = date("Y-m-d",$a_time5);
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<13;$i++){
				$k=$i;
				$cd='+'.$k.' Month';
				$b_time = strtotime($cd,strtotime($begintime) );
				$Qtime=date('Y-m-01',$b_time);
				if($flag<1){
					array_push($tcount_2,$Qtime);
					if($b_time==strtotime($endtime)){
						$flag=1;
						}
					}
				}
			}
			
			if($type==4){
				$cond="%Y-%m-%d";
				$grup="days";
				$tcount=array();
				//$begintime = date("Y-m-d");
				$a_time = strtotime($begintime);
				$Wday = date("w",$a_time);//总天数
				$a_time = strtotime('-'.$Wday.' Day',$a_time);
				$a_time = strtotime('+1 Day',$a_time);
				$begintime=date('Y-m-d', $a_time);
				$a_time5 = strtotime('+7 Day',strtotime($begintime));
				$endtime = date("Y-m-d",$a_time5);
				$tcount_2=array();
				$flag=0;
				for($i=0;$i<8;$i++){
				$k=$i;
				$cd='+'.$k.' Day';
				$b_time = strtotime($cd,strtotime($begintime) );
				$Qtime=date('Y-m-d',$b_time);
				if($flag<1){
					array_push($tcount_2,$Qtime);
					if($b_time==strtotime($endtime)){
						$flag=1;
						}
					}
				}
			}
			if($type==5){
				$cond="%Y-%m-%d";
				$grup="days";
				$a_time = strtotime($begintime);
				$endtime = date("Y-m-d",strtotime('+1 Day',$a_time));
				$tcount_2=array();
				array_push($tcount_2,$begintime,$endtime);
			}
		//=================================(SQL段)====================================
		$tcount=array();
		if($SOtype==1){
			$query="SELECT p_name,p_id,sum(p_mun) as rcount,sum(o_totale_price) as totalprice  FROM slb_order  where custid=".$customer_id." and p_type='".$Ptype."' and o_state>0 and c_isvalid=1 and UNIX_TIMESTAMP(c_createtime)>=".strtotime($begintime)." and UNIX_TIMESTAMP(c_createtime)<".strtotime($endtime)." group by p_id order by sum(o_totale_price) desc";
			$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$tcount=array();
				$rcountTOTAL=0;
				$priceTOTAL=0;
				while ($row = mysql_fetch_object($result)) {
					$p_id=$row->p_id;
					$name=$row->p_name;
					
					$rcount=$row->rcount;
					$totalprice=$row->totalprice;
					if($rcount==null  || $rcount=="" ){
						$rcount=0;
					}
					if($totalprice==null  || $totalprice=="" ){
						$totalprice=0;
					}
					$rcountTOTAL=$rcountTOTAL+$rcount;
					$priceTOTAL=$priceTOTAL+$totalprice;
					$map=array();
					array_push($map,$p_id,$name,$rcount,$totalprice);
					array_push($tcount,$map);
				}
				array_push($tcount,$rcountTOTAL,$priceTOTAL);
				//array_push($tcount,$query);
		}
		
		if($SOtype==5){
			$rcountTOTAL=0;
			$priceTOTAL=0;
			$tcount=array();
			for($i=0;$i<(count($tcount_2)-1);$i++){
				$query="SELECT sum(p_mun) as rcount,sum(o_totale_price) as totalprice  FROM slb_order  where custid=".$customer_id."  and p_type='".$Ptype."' and o_state>0  and c_isvalid=1 and UNIX_TIMESTAMP(c_createtime)>=".strtotime($tcount_2[$i])." and UNIX_TIMESTAMP(c_createtime)<".strtotime($tcount_2[$i+1]);	
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
					$p_id="0";
					$name=$tcount_2[$i];
					$rcount=$row->rcount;
					$totalprice=$row->totalprice;
					if($rcount==null  || $rcount=="" ){
						$rcount=0;
					}
					if($totalprice==null  || $totalprice=="" ){
						$totalprice=0;
					}
					$rcountTOTAL=$rcountTOTAL+$rcount;
					$priceTOTAL=$priceTOTAL+$totalprice;
					$map=array();
					array_push($map,$p_id,$name,$rcount,$totalprice);
				}
				array_push($tcount,$map);
			}
			array_push($tcount,$rcountTOTAL,$priceTOTAL);			
		}
		array_push($tcount,$begintime);
		array_push($tcount,$endtime);
		echo json_encode($tcount);
		return;
		break;

}
$S_SX_1_SQL="select id,sx_type,sx_name,sx_introduce from slb_sx where sx_type=-1 and c_isvalid=1 and custid='".$customer_id."'";
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>销售统计</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../Common/js/Data/js/ichartjs/ichart.1.2.min.js"></script>
<style>

.FZ1{
transition:transform 2s;
-moz-transition: -moz-transform 2s; /* Firefox 4 */
-webkit-transition: -webkit-transform 2s; /* Safari and Chrome */
-o-transition: -o-transform 2s; /* Opera */

transform:rotateX(360deg);
-moz-transform:rotateX(360deg); /* Firefox 4 */
-webkit-transform:rotateX(360deg); /* Safari and Chrome */
-o-transform:rotateX(360deg); /* Opera */
}
.FZ2{
transition:transform 2s;
-moz-transition: -moz-transform 2s; /* Firefox 4 */
-webkit-transition: -webkit-transform 2s; /* Safari and Chrome */
-o-transition: -o-transform 2s; /* Opera */

transform:rotateX(0deg);
-moz-transform:rotateX(0deg); /* Firefox 4 */
-webkit-transform:rotateX(0deg); /* Safari and Chrome */
-o-transform:rotateX(0deg); /* Opera */
}
.FZ3{
transition:transform 2s;
-moz-transition: -moz-transform 2s; /* Firefox 4 */
-webkit-transition: -webkit-transform 2s; /* Safari and Chrome */
-o-transition: -o-transform 2s; /* Opera */

transform:rotateY(360deg);
-moz-transform:rotateY(360deg); /* Firefox 4 */
-webkit-transform:rotateY(360deg); /* Safari and Chrome */
-o-transform:rotateY(360deg); /* Opera */
}
.FZ4{
transition:transform 2s;
-moz-transition: -moz-transform 2s; /* Firefox 4 */
-webkit-transition: -webkit-transform 2s; /* Safari and Chrome */
-o-transition: -o-transform 2s; /* Opera */

transform:rotateY(0deg);
-moz-transform:rotateY(0deg); /* Firefox 4 */
-webkit-transform:rotateY(0deg); /* Safari and Chrome */
-o-transform:rotateY(0deg); /* Opera */
}


</style>
</head>

<body> 
<!--内容框架-->



	<div class="WSY_content">

		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/slb_mall/basic_head.php");
			?>
			<!--列表头部切换结束-->

			<script>
			var loadTYPE=0;
			$(function(){
				var SOtype=$("#SOtype").val();
				if(SOtype==1){
					$(".SOtype").css({"display":"none"});
				}else{
					$(".SOtype").css({"display":"block"});
				}
				$("#Shead").animate({'opacity':0},1).animate({'opacity':1},1000);
				$("#Ssearch").animate({'width':"0%","margin-left":"100%"}, 1).animate({'width':"100%","margin-left":"0%"},1000);
				$("#Spainting").animate({'width':"0px"}, 1).animate({'width':"800px"},1000);
				$("#Saddsearch").animate({'opacity':0}, 1).delay(1000).animate({'opacity':1},1500);
				loadTYPE=0;
				TTsale();
			});
			</script>
			<div class="WSY_data" style="padding:10px;width: 98%;">		
				<div id="Shead" style="width:99%;background: #EFEFEF none repeat scroll 0% 0%;border-left: 10px solid #06A7E1;height: 38px;">
							<a style="padding:5px 0px 5px 34px;line-height: 38px;font-weight: bold;font-size: 15px;background: url(../../Common/images/Data/qushiicon/qushi_icon_21.png) no-repeat 6px center;">销售统计</a>
				</div>
				<div id="Ssearch" class="WSY_search_q" style="height: 38px;margin-top: 10px;display: block;">	
					 <li style="width:24%;float:left">
					时间：&nbsp;
					<a>
					<input type="text" class="Wdate" style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});"  id="TSbegintime" name="AccTime_S" value="<?php echo $begintime;	?>" >
					</a>-<a>
					<input type="text" class="Wdate" readonly="readonly"  style="width:30%;border: 1px solid #CFCBCB;height: 26px;"  onclick="WdatePicker({dateFmt:'yyyy-MM-dd'});" id="TSendtime" name="AccTime_E" value="<?php echo $endtime; ?>" >
					</a>
					</li>
					<li style="width:14%;">
					商品类型：&nbsp;
					<a>
					<select id="Ptype" style="width: 60%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
					<?php
							
							$S_SX_1_R = _mysql_query($S_SX_1_SQL) or die('Query failed1: ' . mysql_error());
							while ($S_SX_2_row = mysql_fetch_object($S_SX_1_R)) {
							$sx_2_id=$S_SX_2_row->id;
							$sx_2_name=$S_SX_2_row->sx_name;
							?>
							<option value="<?php echo $sx_2_id; ?>" <?php if($sx_2_id==$p_type){ echo "selected='selected'"; } ?>><?php echo $sx_2_name; ?></option>
					<?php }?>

					</select>
					</a>
					</li>
					<li style="width:14%;">
					搜索类型：&nbsp;
					<a>
					<select id="SOtype" style="width: 60%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
					<option value="1">--默认--</option>
					<option value="5">全部商品销售情况</option>
					<option value="1">各个商品销售情况</option>
					</select>
					</a>
					</li>
					<li style="width:14%;float:left">
					时间类型：&nbsp;
					<a>
					<select id="TTYPE" style="width: 50%;border: 1px solid #CFCBCB;height: 28px;margin-bottom: 5px;border-radius: 3px;">
					<option value="1">--默认--</option>
					<option value="4">按星期</option>
					<option value="1">按月份</option>
					<option value="2">按季度</option>
					<option value="3">按年份</option>
					
					
					</select>
					</a>
					</li>
					<li style="width:16%;float:left" class="WSY_bottonliss">
					<input id="Osearch_bar" type="button"  onclick="search_ST(this)" value="搜索" 
					
					style="width:40%;border-radius: 3px;height:25px;color:#fff;cursor: pointer;" >	
					<input  type="button"  onclick="search_ST(this)" value="刷新数据"  style="width:40%;margin-left: 9%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >
					</li>			
				</div>
				<script>
				
			
					var Ftype=0;
					function FZ(){
						Ftype=Ftype+1;
						if(Ftype%4==1){
						
							$("#TcanvasDiv").addClass('FZ1');
							$("#TcanvasDiv").removeClass('FZ4');	
						}else if(Ftype%4==2){
							$("#TcanvasDiv").addClass('FZ2');
							$("#TcanvasDiv").removeClass('FZ1');
						}else if(Ftype%4==3){
							$("#TcanvasDiv").addClass('FZ3');
							$("#TcanvasDiv").removeClass('FZ2');
						}else{
							$("#TcanvasDiv").addClass('FZ4');
							$("#TcanvasDiv").removeClass('FZ3');
						}

					}
			
				
				function TTsale(){	//分销商数
						var begintime=$("#TSbegintime").val();
						var endtime=$("#TSendtime").val();
						var customer_id=$("#customer_id").val();
						var QJTTIME=$("#TTYPE").val();
						var SOtype=$("#SOtype").val();
						if(SOtype==1){
							$(".SOtype").css({"display":"none"});
						}else{
							$(".SOtype").css({"display":"block"});
						}
						var Ptype=$("#Ptype").val();
					$.ajax({
						type: "post",
						url: "statistics.php",
						dataType: "json",
						//begintime:begintime,endtime:endtime,
						data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:QJTTIME,SOtype:SOtype,Ptype:Ptype},
						success: function (result) {
						var length=result.length;
						var Str="<input id='ksize' class='display'  value="+(length-4)+">";
						for(var i=0;i<(length-4);i++){
							var key0=result[i][0];
							var key1=result[i][1];
							var key2=result[i][2];
							var key3=result[i][3];
							Str=Str+"<input id='key"+i+"' class='display' value="+key0+">";
							Str=Str+"<input id='MAN"+i+"' class='display' value="+key1+">";
							Str=Str+"<input id='mun"+i+"' class='display' value="+key2+">";
							Str=Str+"<input id='pri"+i+"' class='display' value="+key3+">";
						}
						Str=Str+"<input id='RTOTAL' class='display' value="+result[length-4]+">";
						Str=Str+"<input id='PTOTAL' class='display' value="+result[length-3]+">";
						$("#Tdata").html(Str);
						$("#TSbegintime").val(result[length-2]);
						$("#TSendtime").val(result[length-1]);
						TIchartjs();
						
							 
						}
					});
				}
				
				function search_ST(obj){
					var	QJTTIME=$("#STYPE").val();
					var ktitle=$(obj).val();
	
					if(ktitle=="搜索"){
						TTsale();
						FZ();
					}
					if(ktitle=="刷新数据"){
						$("#TSbegintime").val('');
						loadTYPE=0;
						TTsale();
						FZ();
					}
					if(ktitle=="销售额统计"){
						loadTYPE=0;
						TIchartjs();
					}
					if(ktitle=="销售量统计"){
						loadTYPE=1;
						TIchartjs();
					}
					
					if(ktitle=="列表查看"){
						TIchartjs_lB();
						//TTsaleIchartjs();
					}
					if(ktitle=="详细查看"){
						search_LB(obj,3);
					}
					if(ktitle=="导出订单"){

						excel_OD(obj,3);
					}
					if(ktitle=="导出飞豆"){

						excel_FD(obj,3);
					}
				}
				</script>
				<div style="height: 450px;margin-top: 20px;">	
				<div id="Spainting" style="width:800px;height: 400px;float: left;border-radius: 2px;">
				<div id='TcanvasDiv'></div>
				</div>
				<div id="Tdata" style="display:none">
				</div>
				<div id="Saddsearch" style="width:100px;height: 400px;float: left;margin-left: 10px;">
				<div class="WSY_bottonliss uli" >
						<ul>
						<li style="margin-top:10px"><input  type="button" class="search_btn SOtype" onclick="search_ST(this)" value="销售额统计"  style="width:85%;margin-left:7%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn SOtype" onclick="search_ST(this)" value="销售量统计"  style="width:85%;margin-left:7%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_ST(this)" value="列表查看"  style="width:85%;margin-left:7%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<!--<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_ST(this)" value="详细查看"  style="width:85%;margin-left:7%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onclick="search_ST(this)" value="导出订单"  style="width:85%;margin-left:7%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>
						<li style="margin-top:10px"><input  type="button" class="search_btn" onClick="search_ST(this)" value="导出飞豆"  style="width:85%;margin-left:7%;border-radius: 3px;height:25px;color:#fff;cursor: pointer; " >	</li>-->
						</ul>
					</div>
				</div>
				</div>				
			</div>
		</div>
		<input type="hidden" id="customer_id" value="<?php echo customer_id;?>" />
	</div>
</body>
<script>
var title="前20名销售情况";
function  TIchartjs(){
	$("#TcanvasDiv").html("");
		//======================(title动态赋值)==========
			var SOtype=$("#SOtype").val();
			var size=$("#ksize").val();
			if(size==0){
				title="暂无相应数据";
			}else{
				var ADDtitle="各礼包前20名销售情况";
				if(SOtype==1){
					ADDtitle="各礼包前20名销售情况";
				}
				if(SOtype==5){
					ADDtitle="销售情况";
				}
			var QJFTIME=$("#TTYPE").val();
			if(QJFTIME==1){
				title=$("#TSbegintime").val()+'~'+$("#TSendtime").val()+ADDtitle;
			}else if(QJFTIME==2 ){
				var Month=$("#TSbegintime").val();
				var year=Month.substring(0,Month.indexOf("-"));
				Month=Month.substring(Month.indexOf("-")+1,Month.lastIndexOf("-"));
				var Q="";
				if(parseInt(Month)==1){
					Q="第一季度";
				}else if(parseInt(Month)==4){
					Q="第二季度";
				}else if(parseInt(Month)==7){
					Q="第三季度";
				}else if(parseInt(Month)==10){
					Q="第四季度";
				}
				title=year+'年'+Q+ADDtitle;
			}else if(QJFTIME==3 ){
				var year=$("#TSbegintime").val();
				year=year.substring(0,year.indexOf("-"));
				title=year+'年度'+ADDtitle;
			}else if(QJFTIME==4 ){
				var year=$("#TSbegintime").val();
				title=year+'本周'+ADDtitle;
			}
			}
			//==========================
			// ------------获取数据-----------
			
			var labels=["MAY\n2011","JUN\n2011","JUL\n2011","AUG\n2011","SEP\n2011","SEP\n2011","OCT\n2011","DEC\n2011","JAN\n2011"];
			var Tabels=["MAY\n2011","JUN\n2011","JUL\n2011","AUG\n2011","SEP\n2011","SEP\n2011","OCT\n2011","DEC\n2011","JAN\n2011"];
			var Sabels=["MAY\n2011","JUN\n2011","JUL\n2011","AUG\n2011","SEP\n2011","SEP\n2011","OCT\n2011","DEC\n2011","JAN\n2011"];
			var data1=["0","0","0","0","0","0","0","0","0"];
			var data2=["0","0","0","0","0","0","0","0","0"];
			var data3=["0","0","0","0","0","0","0","0","0"];
			var TTets=['一','二','三','四','五','六','天'];
			var idlabels=[];
			if(size>0){
				Sabels=[];
				Tabels=[];
				labels=[];
				data1=[];
				data2=[];
				data3=[];
			}
			var Lmax=0;
			var Rmax=0;
			
			var EX20VAL="其他";
			var EX20VAI=0;
			var EX20VAK=0;
			for(var i=0;i<size;i++){
				var key=$("#key"+i).val();
				var val=$("#MAN"+i).val();
				var vai=$("#mun"+i).val();
				var vak=$("#pri"+i).val();
				if(Lmax<parseInt(vak)){
					Lmax+=parseInt(vak*100)/100;
				}
				if(Rmax<parseInt(vai)){
					Rmax+=parseInt(vai*100)/100;
				}
				vak=parseInt(vak*100)/100;
				if(SOtype==1){
				if(i<20){
					idlabels.push(key);
					labels.push(val);
					data1.push(vai);
					data2.push(vak);
				}else{
					EX20VAI=EX20VAI+parseInt(vai);
					EX20VAK=EX20VAK+(parseInt(vak*100)/100);
				}
				}
				if(SOtype==5){
					idlabels.push(key);
					Tabels.push(val);
					if(QJFTIME==1){
						labels.push(val.substring(val.lastIndexOf("-")+1));	
						Sabels.push(val);	
					}
					if(QJFTIME==2 || QJFTIME==3){
						labels.push(val.substring(val.indexOf("-")+1,val.lastIndexOf("-")));	
						Sabels.push(val.substring(0,val.lastIndexOf("-"))+'月');	
					}
					if(QJFTIME==4){
						labels.push(val.substring(val.indexOf("-")+1)+',星期'+TTets[i]);	
						Sabels.push(val);	
					}
								
					data1.push(vai);
					data2.push(vak);
				}
				
			
			}
			if(EX20VAI>0 || EX20VAK>0){
				EX20VAI=parseInt(EX20VAI);
				EX20VAK=parseInt(EX20VAK*100)/100;
				labels.push(EX20VAL);
				data1.push(EX20VAI);
				data2.push(EX20VAK);
			}
			//=================(计算Y轴值)==================

			var TLmax=Lmax;
			var TL=0;
			var KLmax=1;
			for(var i=0;i<100;i++){
				TLmax=parseInt(TLmax/10);
				if(TLmax<10){
					TL=i;
					break;
				}
			}
			TLmax=(TLmax+1)*10;
			for(var i=0;i<TL;i++){
				KLmax=KLmax*10;
			}
			var Lstep=(parseInt(TLmax/5)*KLmax);
			if(Lstep==0){
				Lstep=1;
			}
			
			var TRmax=Rmax;
			var TR=0;
			var KRmax=1;
			for(var i=0;i<100;i++){
				TRmax=parseInt(TRmax/10);
				if(TRmax<10){
					TR=i;
					break;
				}
			}
			TRmax=(TRmax+1)*10;
			for(var i=0;i<TR;i++){
				KRmax=KRmax*10;
			}
			var Rstep=(parseInt(TRmax/5)*KRmax);
			if(Rstep==0){
				Rstep=1;
			}
	if(SOtype==1){
			var data = [
			         	{
			         		name : '销售额',
			         		value:data2,
			         		color:'#06A7E1'
			         	}
			         ];
					
			var data4 = [
				        	{
				        		name : '销售量',
				        		value:data1,
				        		color:'#68ba17',
				        		line_width:5
				        	}
				       ];
		       
			var chart = new iChart.ColumnStacked2D({
					render : 'TcanvasDiv',
					data: data,
					labels:labels,
					title : {
						text:title,
						color:'#333333',
						textAlign:'left',
						padding:'0 40',
						font:'微软雅黑',
						border:{
							enable:true,
							width:[0,0,4,0],
							color:'#698389'
						},
						height:40
					},
					
					padding:'8 0',
					width : 800,
					height : 400,
					//animation : true,//开启过渡动画
					column_width:70,
					//gradient : true,//应用背景渐变
					//gradient_mode:'LinearGradientDownUp',//渐变类型
					//color_factor : 0.1,//渐变因子
					background_color : '#fff',
					sub_option:{
						label:false,
						border : false
					},
					label : {
					font:'微软雅黑',
					fontweight:600,
					fontsize:11,
					textAlign:'right',
					textBaseline:'middle',
					rotate:-30,
					color : '#333333'
					},
					legend:{
						enable:true,
						background_color : null,
						line_height:25,
						color:'#333333',
						fontsize:12,
						font:'微软雅黑',
						fontweight:600,
						border : {
							enable : false
						},
						offsety:-160
					},
					column_width:80,
					coordinate:{
						background_color : 0,
						grid_color:'#dadada',
						axis : {
							color : '#c0d0e0',
							width : 0
						}, 
						scale:[{
							 position:'left',	
							 scale_enable : false,
							 start_scale:0,
							 scale_space:Lstep,
							 end_scale:Lstep*5,			
							 label:{color:'#06A7E1',fontsize:11,fontweight:600}
						},{
						 position:'right',	
						 scale_enable : false,
						 start_scale:0,
						 scale_space:Rstep,
						 end_scale:Rstep*5,
						 scaleAlign:'right',
						 label:{
							color:'#68ba17'
						 },
						 listeners:{
							parseText:function(t,x,y){
								//自定义右侧坐标系刻度文本的格式。
								return {text:''+t+''}
							}
						 }
					}
						],
						width:'80%',
						height:'70%'
					}
			});


			//构造折线图
			var line = new iChart.LineBasic2D({
				z_index:1000,
				data: data4,
				label:{
					color:'#4c4f48'
				},
				tip:{
					enable :true,
					listeners:{
						//tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
						parseText:function(tip,name,value,text,i){
							var kv=parseInt(data1[i])+parseInt(data2[i]);
							return labels[i]+"<br/><span style='color:#06A7E1'>销售额:"+data2[i]+"</span><br/><span style='color:#68ba17'>销售量:"+data1[i];
						}
					} 
				},
				listeners:{
				/**
				* d:相当于data[0],即是一个线段的对象
				* v:相当于data[0].value
				* x:计算出来的横坐标
				* x:计算出来的纵坐标
				* j:序号 从0开始
				*/
				
				parsePoint:function(d,v,x,y,j){
					//利用序号进行过滤春节休息期间 
					if(QJFTIME==1 || QJFTIME==4){
					var kft=false;
					var myDate = new Date();
					var Nyear=myDate.getFullYear(); 
					var NMonth=myDate.getMonth();  
					var NDate=myDate.getDate();
					var Eendtime=$("#TSendtime").val();
					var Eyear=Eendtime.substring(0,Eendtime.indexOf("-"));
					var EMonth=Eendtime.substring(Eendtime.indexOf("-")+1,Eendtime.lastIndexOf("-"));
					var EDate=Eendtime.substring(Eendtime.lastIndexOf("-")+1);
					if(parseInt(Eyear)>parseInt(Nyear)){
						kft=true;
					}else if(parseInt(Eyear)==parseInt(Nyear)){
						if(parseInt(EMonth)>(parseInt(NMonth)+1)){
							kft=true;
						}else if(parseInt(EMonth)==(parseInt(NMonth)+1)){
							if(parseInt(EDate)>parseInt(NDate)){
								kft=true;
							}
						}
					}
					if(kft &&(v==0))
					return {ignored:true}//ignored为true表示忽略该点
					}else{
						return {ignored:false}//ignored为true表示忽略该点
					}
				}
				},
				//animation : true,//开启过渡动画
				legend:{
						enable:true,
						background_color : null,
						line_height:25,
						color:'#333333',
						fontsize:12,
						font:'微软雅黑',
						fontweight:600,
						border : {
							enable : false
						},
						offsety:-145
						
				},
				point_space:chart.get('column_width')+chart.get('column_space'),
				scaleAlign : 'right',
				sub_option : {
					label:false,
					point_size:5
				},
				coordinate:chart.coo//共用坐标系
			});
			
			chart.plugin(line);
			
			
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
						.textFont('600 16px 微软雅黑')
						.fillText('总销售量:'+$("#RTOTAL").val(),x-20,y-20,false,'#68ba17')
						.textFont('600 12px 微软雅黑')
						.fillText('销售量(个)',x+coo.width-15,y-5,false,'#68ba17')


						//在右上侧的位置，渲染一个单位的文字
						chart.target.textAlign('end')
						.textBaseline('bottom')
						.textFont('600 16px 微软雅黑')
						.fillText('总销售额￥'+$("#PTOTAL").val(),x+coo.width-50,y-20,false,'#06A7E1')
						.textFont('600 12px 微软雅黑')
						.fillText('销售额(￥)',x+20,y-5,false,'#06A7E1')
						
					}
			}));
			
			chart.draw();
	}
	if(SOtype==5){
			var YZ="";
			var color="";
			var step=1;
			if(loadTYPE==0){
				data3=data2;
				color="#06A7E1";
				YZ="销售额(￥)";
				step=Lstep;
			}
			if(loadTYPE==1){
				data3=data1;
				color="#68ba17";
				YZ="销售量";
				step=step;
			}
			var data = [
			         
						{
			         		name : '销售额',
			         		value:data3,
			         		color:color,
			         		line_width:1.5
			         	}
			         ];
			

		       
			var chart = new iChart.LineBasic2D({
				render : 'TcanvasDiv',
				data: data,
				align:'center',
				title : {
					text:title,
					font : '微软雅黑',
					fontsize:24,
					color:'#333333'
				},
				subtitle : {
					text:'总销售量:'+$("#RTOTAL").val()+'              总销售额￥'+$("#PTOTAL").val(),
					font : '微软雅黑',
					color:'#333333'
				},
		
				width : 800,
				height : 400,
				shadow:false,
				//animation : true,//开启过渡动画
				//animation_duration:600,//600ms完成动画
				label : {
					fontsize:5,
					textAlign:'right',
					textBaseline:'middle',
					rotate:-60,
					color : '#666666'
				},
				//shadow_color : '#202020',
				//shadow_blur : 8,
				//shadow_offsetx : 0,
				//shadow_offsety : 0,
				background_color:'#fff',
				tip:{
					enable:true,
					shadow:true,
					listeners:{
						 //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
						parseText:function(tip,name,value,text,i){
							return "<span style='color:#005268;font-size:12px;'>"+Sabels[i]+""+
							" </span><br/><span style='color:#06A7E1;font-size:12px;'>销售额:￥"+data2[i]+"</span>"+
							" </span><br/><span style='color:#68ba17;font-size:12px;'>销售量:"+data1[i]+"</span>";
						}
					}
				},
				crosshair:{
					enable:true,
					line_color:'#dadada'
				},
				sub_option : {
					smooth : true,
					label:false,
					hollow:false,
					hollow_inside:false,
					point_size:5
				},
				coordinate:{
					background_color : 0,
						grid_color:'#dadada',
						axis : {
							color : '#c0d0e0',
							width : 0
						}, 
					scale:[{
						 position:'left',	
						 scale_enable : false,
						 start_scale:0,
						 scale_space:step,
						 end_scale:step*5,						 
						scale_enable : false,							 
						 label:{color:color,fontsize:11,fontweight:600}
						},
						{
							position:'bottom',	
							label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
							scale_enable : false,
							labels:labels
						}
						]
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
						.fillText(YZ,x-40,y-12,false,'#06A7E1')
						
						
					}
			}));
		//开始画图
		chart.draw();
	}
	
}
function TIchartjs_lB(){
			$("#TcanvasDiv").html("");
			var size=$("#ksize").val();
			var SOtype=$("#SOtype").val();
			var QJTTIME=$("#TTYPE").val();
			var RTOTAL=$("#RTOTAL").val();
			RTOTAL=parseInt(RTOTAL);
			var PTOTAL=$("#PTOTAL").val();
			PTOTAL=parseInt(PTOTAL);
			 /*for(var i=0;i<size;i++ ){
				 var VAL=$("#Oval"+i).val();
				total+=parseInt(VAL*100);
			 }
			 total=total/100;*/
			//Str+="</div>";
			var Str="<div class='WSY_weixinbox' id='TOSHOW' style='width:100%;height:100%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;margin-left: 0px;margin-top: 0px;'>";
			Str+="<div class='WSY_weixin'>";
            Str+="<a>"+title+"</a>";
            Str+="</div>";
			Str+="<div style='padding:20px;overflow:auto;height:85%;width:95%;margin-top: 5px;'>";
			Str+="<div style='background-color: #FFE7BA;border-radius: 10px;'>";
			Str+=" <table width='100%' border='0' cellspacing='0' cellpadding='0'>";
			Str+="<thead>";
			Str+="<tr style='line-height: 30px;background-color: rgb(6, 167, 225);'>";
			if(SOtype==5){
				if(QJTTIME<2){
				Str+="<th scope='col'>时间(日)</th>";
				}
				if(QJTTIME>1){
					Str+="<th scope='col'>时间(月)</th>";
				}
			}
			if(SOtype==1){
				Str+="<th scope='col'>产品名称</th>";
			}
			Str+="<th scope='col' style='display:none'>销售量</th>";
			Str+="<th scope='col'>销售量</th>";
			Str+="<th scope='col'>量百分比</th>";
			Str+="<th scope='col'>销售额</th>";
			Str+="<th scope='col'>额百分比</th>";
			Str+="</tr>";
			Str+="</thead>";
			Str+="<tbody>";
			
			for(var i=0;i<size;i++ ){
				Str+="<tr style='line-height: 30px;'>";
				var MAN=$("#MAN"+i).val();
				var VAL=$("#mun"+i).val();
				VAL=parseInt(VAL);
				var BVAL=parseInt((VAL*100)/RTOTAL);
				if(!RTOTAL){
					BVAL=0.0;
				}	
				var VAK=$("#pri"+i).val();
				VAK=parseInt(VAK*100);
				VAK=VAK/100;
				var BVAK=parseInt((VAK*100)/PTOTAL);
				if(!PTOTAL){
					BVAK=0.0;
				}	
				Str+="<td style='text-align:center' valign='middle' >"+MAN+"</td>";
				Str+="<td style='text-align:center' valign='middle' >"+VAL+"</td>";
				Str+="<td style='text-align:center' valign='middle' >"+BVAL+"%</td>";
				Str+="<td style='text-align:center' valign='middle' >"+VAK+"</td>";
				Str+="<td style='text-align:center' valign='middle' >"+BVAK+"%</td>";
			//	Str+="<td style='text-align:center' valign='middle' >" +
			//		"<a onclick='SEE_TO(this)' class='wsy_cost_style' title='列表查看' style='margin:2px;cursor:pointer;'><img src='../../Common/images/Data/qushiicon/btn_08.png' style='margin-top: 5px;' /></a>" +
			//		"</td>";
				Str+="</tr>";
			}
			Str+="<tr style='line-height: 30px;'>";
			Str+="<td style='text-align:center' valign='middle' > 销售量总计</td>";
			Str+="<td style='text-align:center' valign='middle' >"+RTOTAL+"</td>";
			Str+="<td style='text-align:center' valign='middle' > 销售额总计</td>";
			Str+="<td style='text-align:center' valign='middle' >"+PTOTAL+"</td>";
			Str+="</tr>";
			Str+="</tbody>";
			Str+="</table>";
			Str+="</div>";
			Str+="</div>";
			Str+="</div>";
			$("#TcanvasDiv").html(Str);
			$("#TOSHOW").css({width:0,height:0});
			$("#TOSHOW").animate({width:800,height:400},"slow");    
		
}

</script>
</html>
