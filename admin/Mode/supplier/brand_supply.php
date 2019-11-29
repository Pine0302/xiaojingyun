<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=3;//头部文件  0基本设置,1提现记录,2供应商管理
require('../../../../weixinpl/auth_user.php');
$op="";
if(!empty($_GET["op"])){	
	$op = $configutil->splash_new($_GET["op"]);
	$id = $configutil->splash_new($_GET["id"]);
	$user_id = $configutil->splash_new($_GET["user_id"]);
	if($op=="del"){
		$brand_supplys="update weixin_commonshop_brand_supplys set isvalid=false where user_id=".$user_id." and isvalid=true";
		_mysql_query($brand_supplys);//删除品牌供应商表中信息
		$brand_supply="update weixin_commonshop_supply_kefu set isvalid=false where supply_id=".$user_id." and isvalid=true";
		_mysql_query($brand_supplys);//删除品牌供应商客服表中信息
		$applysupplys="update weixin_commonshop_applysupplys set isbrand_supply=false where user_id=".$user_id;
		_mysql_query($applysupplys);//取消品牌供应商标识

   }else if($op=="brand_status"){ //品牌供应商操作
		$status = $configutil->splash_new($_GET["status"]);
		$sql="update weixin_commonshop_brand_supplys set brand_status=".$status;
		if($status==1){
			$sql=$sql.",brand_opentime=now()";
		}
		if($status==-1){
			$reason = $configutil->splash_new($_GET["reason"]);
			$sql=$sql.",reason='".$reason."'";
		}
		$sql=$sql." where user_id=".$user_id."";
		_mysql_query($sql);
		if($status==1){
			$applysupplys="update weixin_commonshop_applysupplys set isbrand_supply=true where user_id=".$user_id;
			_mysql_query($applysupplys);//将供应商标识为品牌供应商
		}
	   
	   $brand_sql = "select user_name,user_phone,id_cards_num,location_p,location_c,location_a,brand_address,brand_name,brand_business_license,id_cards_pic from weixin_commonshop_brand_supplys where user_id=".$user_id." and isvalid = 1 ";
	   $result = _mysql_query($brand_sql) or die('Query failed: ' . mysql_error());
	   while ($row = mysql_fetch_object($result)) {
		    $user_name = $row->user_name;		
		    $user_phone = $row->user_phone;	
		    $id_cards_num = $row->id_cards_num;	
		    $location_p = $row->location_p;	
		    $location_c = $row->location_c;	
		    $location_a = $row->location_a;	
		    $brand_address = $row->brand_address;	
		    $brand_name = $row->brand_name;
		    $brand_business_license = $row->brand_business_license;
		    $id_cards_pic = $row->id_cards_pic;
		   break;
		}

		$applysupplys="update weixin_commonshop_applysupplys set user_name='".$user_name."',user_phone='".$user_phone."',id_cards_num='".$id_cards_num."',business_address='".$brand_address."',company_name='".$brand_name."',location_p='".$location_p."',location_c='".$location_c."',location_a='".$location_a."',business_licence_pic='".$brand_business_license."',id_cards_pic='".$id_cards_pic."' where user_id=".$user_id;
		_mysql_query($applysupplys);//将供应商标识为品牌供应商

   }

}
$exp_user_id=-1;

if(!empty($_GET["exp_user_id"])){
    $exp_user_id = $configutil->splash_new($_GET["exp_user_id"]);
}

$search_brandstatus=-1; //品牌供应商搜索
if(!empty($_GET["search_brandstatus"])){
    $search_brandstatus = $configutil->splash_new($_GET["search_brandstatus"]);
}
if(!empty($_POST["search_brandstatus"])){
    $search_brandstatus = $configutil->splash_new($_POST["search_brandstatus"]);
}

$search_name="";
if(!empty($_GET["search_name"])){
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
if(!empty($_POST["search_name"])){
    $search_name = $configutil->splash_new($_POST["search_name"]);
}

$search_user_id="";
if(!empty($_GET["search_user_id"])){
    $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
if(!empty($_POST["search_user_id"])){
    $search_user_id = $configutil->splash_new($_POST["search_user_id"]);
}


$search_phone="";
if(!empty($_GET["search_phone"])){
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
if(!empty($_POST["search_phone"])){
    $search_phone = $configutil->splash_new($_POST["search_phone"]);
}

$op="";


$pagecount = 20;
if(!empty($_GET["pagecount"])){
	$pagecount = intval($_GET["pagecount"]);
}
$pagenum = 1;
if (!empty($_GET["pagenum"])) {
	$pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * $pagecount;
$end = $pagecount;

?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>合作商-品牌商户管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script charset="utf-8" src="inputexcel.js"></script>
<script src="../../../common/js/floatBox.js"></script>
<script>
function inputtext(table,filename){
	
	/*导出自行安装订单筛选框*/
	var excelArray = [
						["0","合作商编号"],
						["1","排序(降序)"],
						["2","姓名"],
						["3","公司名称"],
						["4","公司电话"],
						["5","所在地区"],
						["6","品牌合作商申请状态"],
						["7","申请时间"]
					 ];
	exportBox(excelArray);
	$(".floatbox").show();
	$(".floatinputs").click(function(){
		var str="";
		$("input[name='excel_field[]']:checkbox").each(function(){ 
            if($(this).is(':checked')){
                str += $(this).val()+","
            }
        })
        str = str.substring(0,str.length-1);	
		sstr = str.split(",");
		dataIntArr=sstr.map(function(data){  
			return +data;  
		}); 	
	
	//构建excel内容	
	var table = $('#WSY_t1');
	var excel="<table>";
	//表头开始
	table.children('thead').children('tr').each(function(){
		excel += '<tr>';
		$(this).children('th').each(function(i){
			//清除“操作”那一列
			if(i!=8 && (dataIntArr.indexOf(i)!=-1)){
				excel += '<th>';
				excel += $(this).text();
				excel += '</th>';
			}
		})
		excel += '</tr>';
	})
	//表头结束，内容开始
	table.children('tbody').children('tr').each(function(){
		excel += '<tr>';
		$(this).children('td').each(function(i){
			if(i!=8 && (dataIntArr.indexOf(i)!=-1)){
				
					excel += '<td>';
					excel += $(this).html();
					excel += '</td>';
				
			}
		})
		excel += '</tr>';
	})
	excel += '</table>';
	excel=excel.replace(/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/g ,"$2").replace(/[\s]+/g," ").replace(/<i[^>]*class="([^"]*)"[^>]*>(.*?)<\/i>/g,"");
	//构建excel内容结束
	form = $("<form></form>")
	form.attr('action','inputexl.php')
	form.attr('method','post')
	input1 = $("<input type='hidden' name='excel' />")
	input1.attr('value',excel)
	input2 = $("<input type='text' name='filename' />")
	input2.attr('value','品牌合作商') 
	form.append(input1)
	form.append(input2)
	form.appendTo("body")
	form.css('display','none')
	form.submit()
		$(".floatbox").hide();
		$(".floatbox").remove();
	});		
}
</script>
<style> 

tr {
    line-height: 22px;
}
.inventory{
	color:#06A7E1;
}
input.search_btn{
	margin-bottom:5px;
}
</style>
<title>品牌合作商管理</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>  
	<!--内容框架-->
	<div class="WSY_content"> 
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/supplier/basic_head.php"); 
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main"> 
				<form class="search" id="search_form" style="margin-left:18px; margin-top: 18px;">
						
						品牌合作商状态:<select name="search_brandstatus" id="search_brandstatus"  style="width:100px;" >
						<option value="-1">--请选择--</option>
						<option value="2" <?php if($search_brandstatus==2){ ?>selected <?php } ?>>待审核</option>
						<option value="1" <?php if($search_brandstatus==1){ ?>selected <?php } ?>>已确认</option>
						<option value="-2" <?php if($search_brandstatus==-2){ ?>selected <?php } ?>>已驳回/暂停</option>
						</select>
						&nbsp;合作商编号:<input type=text name="search_user_id" id="search_user_id" value="<?php echo $search_user_id; ?>" style="width:80px;" />
						&nbsp;姓名:<input type=text name="search_name" id="search_name" value="<?php echo $search_name; ?>" style="width:80px;" />
						&nbsp;电话:<input type=text name="search_phone" id="search_phone" value="<?php echo $search_phone; ?>"  style="width:80px;" />
					每页记录数：<input type=text name="pagecount" id="pagecount" value="<?php echo $pagecount; ?>"  style="width:80px;border: 1px solid #ccc; border-radius: 2px;height: 24px;margin-left: 10px;padding-left: 8px;" />
						<input type="button" class="search_btn" onclick="searchForm();" value="搜 索">
						<!--	<input class="search_btn" value="导出本页信息" onclick="javascript:inputtext('WSY_t1','合作商')" style="cursor:hand" type="button"> -->
						<input class="search_btn" value="导出全部信息" onclick="exportExcel()" style="cursor:hand" type="button">
						
				</form>	 
				<form action="/weixin/plat/app/index.php/Excel/js_excel" method="p"></form>
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="8%">合作商编号</th>
						<th width="4%">排序（降序）</th>
						<th width="8%">姓名</th>
						<th width="8%">公司名称</th>
						<th width="8%">公司电话</th>
						<th width="8%">所在地区</th>
						<th width="8%">品牌合作商申请状态</th> 
						<th width="8%">申请时间</th> 
						<th width="8%">操作</th> 
					</thead>
					<tbody>
					   <?php 
							$brand="SELECT 
										wcbs.user_id as user_id,
										wcbs.brand_logo as brand_logo,
										wcbs.brand_tel as brand_tel,
										wcbs.brand_intro as brand_intro,
										wcbs.brand_name as brand_name,
										wcbs.asort_value as asort_value,
										wcbs.brand_address as brand_address,
										wcbs.brand_business_license as brand_business_license,
										wcbs.addition as addition,
										wcbs.brand_status as brand_status,
										wcbs.reason as reason,
										wcbs.brand_opentime as brand_opentime,
										wcbs.creatime as creatime,
										wu.name as name,
										wu.weixin_name as weixin_name 
									from weixin_commonshop_brand_supplys wcbs 
									inner join weixin_users wu on wcbs.isvalid=true and wcbs.user_id=
										wu.id and wcbs.customer_id=".$customer_id;
							
							switch($search_brandstatus){
								case 2:
								   $brand = $brand." and wcbs.brand_status=0";
								   break;
								case 1:
								   $brand = $brand." and wcbs.brand_status=1";
								   break;
								case -2:
								   $brand = $brand." and wcbs.brand_status=-1";
								   break;
							}
							
							if(!empty($search_name)){
								$brand = $brand." and (wu.name like '%".$search_name."%' or wu.weixin_name like '%".$search_name."%')";
							}

							if(!empty($search_phone)){
								$brand = $brand." and wcbs.brand_tel like '%".$search_phone."%'";
							}
							if(!empty($search_user_id)){
								$brand = $brand." and wu.id like '%".$search_user_id."%'";
							}
							
							/* 输出数量开始 */
							$query2 = $brand.' order by wcbs.asort_value desc';
							$result2 = _mysql_query($query2) or die('Querynum failed: ' . mysql_error());
							$rcount_q2 = mysql_num_rows($result2);
							/* 输出数量结束 */
							
							$brand = $brand." order by wcbs.asort_value desc,wcbs.creatime desc"." limit ".$start.",".$end;
//							$brand = $brand." order by wcbs.creatime desc"." limit ".$start.",".$end;

							$result=_mysql_query($brand) or die ('brand faild' .mysql_error());
							while($row=mysql_fetch_object($result)){
								$user_id=$row->user_id;
								$brand_name=$row->brand_name;
								$brand_address=$row->brand_address;
								$brand_status=$row->brand_status;
								$reason=$row->reason;   
								$creatime=$row->creatime;
								$name=$row->name;
								$brand_tel=$row->brand_tel;
								$weixin_name=$row->weixin_name;//微信名称
								$username= $name."(".$weixin_name.")";
								
								$brandstatusstr    = "待审核";
								switch($brand_status){
								   case 1:
									 $brandstatusstr = "已确认";
									 break;
								   case -1:
									 $brandstatusstr = "已驳回/暂停";
									 break;
								}
								$asort_value = $row->asort_value;
								
							
					   ?>
						<tr>
							<td><?php echo $user_id; ?></td>
							<td><input type="text" value="<?php echo $asort_value;?>" style="border:1px solid #ccc;border-radius:5px;text-align:center;" class="ch_sort" sid="<?php echo $user_id;?>"></td>
							<td><?php echo $username;?></td>
							<td><?php echo $brand_name;?></td>
							<td><?php echo $brand_tel;?></td>
							<td><?php echo $brand_address;?></td>
							<td>
								<?php echo $brandstatusstr; ?><br/>
								<?php if($brand_status==-1){
									echo "原因：".$reason;
								}?>
							</td>
							<td><?php echo $creatime; ?></td>
							<td>
							<a class="btn1" href="brand_supply_detail.php?op=edit&user_id=<?php echo $user_id; ?>&customer_id=<?php echo $customer_id;?>" title="查看品牌合作商申请">
								<img src="../../../common/images_V6.0/operating_icon/icon05.png" align="absmiddle"/>
							</a>
							
							<?php if($brand_status!=1 && $brand_status!=-1){?>
							<a  class="btn1"  href="brand_supply.php?op=brand_status&status=1&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>"  title="通过品牌合作商申请">
							<img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle"/>
							</a>
							
							<a  class="btn1" href="javascript:showReason('brand_supply.php?op=brand_status&status=-1&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>');" title="驳回/暂停品牌合作商申请">
							  <img src="../../../common/images_V6.0/operating_icon/icon03.png" align="absmiddle"/>
							</a>
							<?php }?>
							
							<a class="btn1" href="brand_supply.php?customer_id=<?php echo $customer_id_en; ?>&id=<?php echo $id; ?>&op=del&user_id=<?php echo $user_id; ?>&qr_info_id=<?php echo $qr_info_id; ?>&pagenum=<?php echo $pagenum; ?>&parent_id=<?php echo $parent_id; ?>" onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};"><img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除"></a>
							
							</td>
						   
						</tr>				
						
					   <?php } ?> 
		
					</tbody>
				</table>
				<div class="blank20"></div>
				<div id="turn_page"></div>
				<!--翻页开始-->
				<div class="WSY_page">
        	
				</div>
				<!--翻页结束-->
			</div>
		</div>
	</div>
<script type="text/javascript" src="../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>

<script>

var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
  var count =Math.ceil(rcount_q2/end);//总页数
  
  //导出
	function exportExcel(){
		var search_user_id = document.getElementById("search_user_id").value; 
		var search_brandstatus = document.getElementById("search_brandstatus").value; 
		var search_name = document.getElementById("search_name").value; 
		var search_phone = document.getElementById("search_phone").value; 
		var url='/weixin/plat/app/index.php/Excel/brand_supply_excel/customer_id/<?php echo passport_decrypt($customer_id); ?>';
		
		if( search_user_id != '' && search_user_id > 0 ){
			url += '/search_user_id/'+search_user_id;
		}
		if( search_name != '' ){
			url += '/search_name/'+search_name;
		}
		if( search_phone != '' ){
			url += '/search_phone/'+search_phone;
		}
		if( search_brandstatus > 0 ){
			url += '/search_brandstatus/'+search_brandstatus;
		}
		
		document.location = url;
	}
  
	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			 var search_user_id = document.getElementById("search_user_id").value; 
			 var search_brandstatus = document.getElementById("search_brandstatus").value; 
			 var search_name = document.getElementById("search_name").value; 
			 var search_phone = document.getElementById("search_phone").value; 
			 document.location= "brand_supply.php?pagenum="+p+"&pagecount="+end+"&search_brandstatus="+search_brandstatus+"&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
	   }
    }); 
	var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
			 var search_user_id = document.getElementById("search_user_id").value; 
			 var search_brandstatus = document.getElementById("search_brandstatus").value; 
			 var search_name = document.getElementById("search_name").value; 
			 var search_phone = document.getElementById("search_phone").value; 
			 document.location= "brand_supply.php?pagenum="+a+"&pagecount="+end+"&search_brandstatus="+search_brandstatus+"&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
		 }
  }

	function searchForm(){
		console.log(111);
		var search_user_id = document.getElementById("search_user_id").value; 
		var search_brandstatus = document.getElementById("search_brandstatus").value; 
		 var search_name = document.getElementById("search_name").value; 
		 var search_phone = document.getElementById("search_phone").value;
		var pagecount = document.getElementById("pagecount").value;
		 document.location= "brand_supply.php?issearch=1&pagenum=1&search_brandstatus="+search_brandstatus+"&pagecount="+pagecount+"&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
	}
	
  function showReason(url){
  
    var str=prompt("请输入驳回/暂停理由","您不符合合作商条件，请联系客服");
    if(str)
    {
	   document.location = url+"&reason="+str;
    }
  }
  
$(".ch_sort").on("blur",function(i){
		var sid 	= $(this).attr('sid');
		var ch_sort = $(this).val();
		var op 		= "cha_sort_b";
		//console.log("user_id:"+sid+"值:"+ch_sort);
		$.ajax({
			url:"./save_set.php?customer_id=<?php echo $customer_id_en;?>",
			type:"post",
			data:{op:op,so_id:sid,ch_sort:ch_sort},
			success:function(result){
				if(result=="ok"){
					//alert("修改成功");
				}else{
					//alert("修改失败");
				}
				
			}

		});
});

  </script>
 <script type="text/javascript" language=JavaScript charset="UTF-8">

document.onkeydown=function(event)   //ENTER键盘按键触发事件
{
    var e = event || window.event || arguments.callee.caller.arguments[0];
    if (e && e.keyCode==13) 
    {
        searchForm();
    }
}
</script>

<?php mysql_close($link);?>	
 <script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>