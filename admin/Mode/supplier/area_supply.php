<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');

// 数据库操作类
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();

// 连接数据库
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=8;//头部文件  0基本设置,1提现记录,2供应商管理
require('../../../../weixinpl/auth_user.php');

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

$search_brandstatus	= $database->init($_REQUEST['search_brandstatus']);
$search_user_id		= $database->init($_REQUEST['search_user_id']);
$search_name     	= $database->init($_REQUEST['search_name']);
$search_phone    	= $database->init($_REQUEST['search_phone']);

if($search_brandstatus!=='-1' && $search_brandstatus!=='' && $search_brandstatus!==NULL ){
	$where .= " and wcbs.wholesaler_status={$search_brandstatus} ";
}
if($search_user_id){
	$where .= " and wcbs.user_id like '%{$search_user_id}%' ";
}
if($search_name){
	$where .= " and wu.name like '%{$search_name}%' ";
}
if($search_phone){
	$where .= " and wcbs.wholesaler_tel='{$search_phone}' ";
}

$sql = "SELECT wcbs.user_id,wcbs.company_name,name,wholesaler_tel,wcbs.location_a,wcbs.location_c,wcbs.location_p,wcbs.id,wholesaler_status,wcbs.createtime,wq.id as wqid  
		from weixin_commonshop_wholesalers wcbs
		INNER JOIN weixin_users wu ON wcbs.user_id = wu.id
		INNER JOIN weixin_commonshop_applysupplys a ON a.user_id = wcbs.user_id and a.isvalid=true
		INNER JOIN weixin_qr_infos wqi on wqi.foreign_id = wu.id
		inner join weixin_qrs wq on wq.qr_info_id = wqi.id
		where wcbs.customer_id='{$customer_id}' and wcbs.isvalid=true {$where} 
		group by user_id  
		limit {$start},{$end}";
$data = $database->getData($sql);
// var_dump($data);
// echo $sql;
$op="";

$sql = "SELECT wcbs.user_id,wcbs.company_name,name,wholesaler_tel,wcbs.location_a,wcbs.location_c,wcbs.location_p,wcbs.id,wholesaler_status,wcbs.createtime,wq.id as wqid 
		from weixin_commonshop_wholesalers wcbs
		INNER JOIN weixin_users wu ON wcbs.user_id = wu.id
		INNER JOIN weixin_commonshop_applysupplys a ON a.user_id = wcbs.user_id and a.isvalid=true
		INNER JOIN weixin_qr_infos wqi on wqi.foreign_id = wu.id
		inner join weixin_qrs wq on wq.qr_info_id = wqi.id
		where wcbs.customer_id='{$customer_id}' and wcbs.isvalid=true {$where} 
		group by user_id ";
$rcount_q2 = count($database->getData($sql));
// var_dump($sql);

?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>合作商-区域批发商户管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<link rel="stylesheet" type="text/css" href="../../../css/inside.css">
<style>
	.heidi{position: fixed;top:0;left: 0; width: 100%;height: 100%;background: rgba(0,0,0,.3);}
	.content{position: fixed;top: 260px;left: 580px; width: 480px;height: 340px;}
	.head{width: 100%;height: 30px;line-height: 30px; background:#e4e4e4;color: #8d8d8d;margin:0;box-sizing:border-box; text-align: center;}
	.bodybox{background: #fff;}
	.zhuti{padding-left: 50px;padding-top: 20px;}
	.zhuti input{width:300px;height: 30px;}
	.tips{color: #e74c3c;text-align: center;}
	.baocun{ background: #06a7e1;color: #fff;padding: 8px 30px;margin: 20px 195px;}
	.selectzt{overflow: hidden;padding: 15px 0;}
	.selectzt li{float: left;width: 20%;text-align: center;}
	.selectzt li input{vertical-align: middle;margin-right: 3px;}
	.beizhu{padding-left: 35px;}
	.beizhu input{border:1px solid #d6d6d6;height: 25px;width: 250px}
	.update{ background: #06a7e1;color: #fff;padding: 8px 16px;margin: 20px 195px;border: none;}
	.WSY_table{margin-top: 20px}
</style>
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
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
						["6","区域批发商申请状态"],
						["7","申请时间"]
					 ];
	exportBox(excelArray);
	$(".floatbox").show();
	/*$(".floatinputs").click(function(){
		var str="";
		$("input[name='excel_field[]']:checkbox").each(function(){
			if($(this).is(':checked')){
				str += $(this).val()+","
			}
		})
		excel_fields = str.substring(0,str.length-1);

		if(excel_fields==""){
			excel_fields = 0;
		}
		console.log(excel_fields)
		var customer_id = '<?php echo $customer_id_en ?>';
		var search_brandstatus = $('#search_brandstatus').val();
		var search_user_id = $('#search_user_id').val();
		var search_name = $('#search_name').val();
		var search_phone = $('#search_phone').val();
		if(!search_brandstatus){
			search_brandstatus = -1
		}
		if(!search_user_id){
			search_user_id = -1
		}
		if(!search_name){
			search_name = -1
		}
		if(!search_phone){
			search_phone = -1
		}
		window.location.href = '/weixin/plat/app/index.php/Excel/area_supply/customer_id'+customer_id+'/search_brandstatus/'+search_brandstatus+'/search_user_id/'+search_user_id+'/search_name/'+search_name+'/search_phone/'+search_phone+'/excel_fields/'+excel_fields;
	});	*/
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
	input2.attr('value','区域批发商') 
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
</style>
<title>区域批发商管理</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>  
	<!--内容框架-->
	<div class="WSY_content"> 
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			// include("../../../../weixinpl/back_newshops/Mode/supplier/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/supplier/basic_head.php");
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main"> 
				<form class="search" id="search_form" style="margin-left:18px; margin-top: 18px;">
						
						区域批发商状态:<select name="search_brandstatus" id="search_brandstatus"  style="width:100px;" >
						<option value="-1">--请选择--</option>
						<option value="0" <?php if($search_brandstatus==='0'){ ?>selected <?php } ?>>待审核</option>
						<option value="1" <?php if($search_brandstatus==1){ ?>selected <?php } ?>>已确认</option>
						<option value="2" <?php if($search_brandstatus==2){ ?>selected <?php } ?>>已驳回</option>
						</select>
						&nbsp;合作商编号：<input type=text name="search_user_id" id="search_user_id" value="<?php echo $search_user_id; ?>" style="width:80px;" />
						&nbsp;姓名：<input type=text name="search_name" id="search_name" value="<?php echo $search_name; ?>" style="width:80px;" />
						&nbsp;电话：<input type=text name="search_phone" id="search_phone" value="<?php echo $search_phone; ?>"  style="width:80px;" />
					&nbsp;每页记录数：<input type=text name="pagecount" id="pagecount" value="<?php echo $pagecount; ?>"  style="width:80px;border: 1px solid #ccc; border-radius: 2px;height: 24px;margin-left: 10px;padding-left: 8px;" />
						<input type="submit" class="search_btn" onclick="" value="搜 索">
						<input class="search_btn" value="导出本页信息" onclick="javascript:inputtext('WSY_t1','区域批发商')" style="cursor:hand" type="button">
				</form>	 
				<form action="/weixin/plat/app/index.php/Excel/js_excel" method="p"></form>
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="8%">合作商编号</th>
						<!-- <th width="4%">排序（降序）</th> -->
						<th width="8%">姓名</th>
						<th width="8%">公司名称</th>
						<th width="8%">公司电话</th>
						<th width="8%">所在地区</th>
						<th width="8%">区域批发商申请状态</th> 
						<th width="8%">申请时间</th> 
						<th width="8%">操作</th> 
					</thead>
					<tbody>
					   <?php foreach ($data as $key => $value) {  
					   	$switch = array('审核中','通过','驳回');
					   	$value['wholesaler_status_str'] = $database->switchReplace($value['wholesaler_status'],$switch);
					   	?>

						<tr>
							<td><?php echo $value['user_id']; ?></td>
							<!-- <td><input type="text" value="<?php echo $asort_value;?>" style="border:1px solid #ccc;border-radius:5px;text-align:center;" class="ch_sort" sid="<?php echo $user_id;?>"></td> -->
							<td><?php echo $value['name'];?></td>
							<td><?php echo $value['company_name'];?></td>
							<td><?php echo $value['wholesaler_tel'];?></td>
							<td><?php echo $value['location_p'].$value['location_a'].$value['location_c'];?></td>
							<td><?php echo $value['wholesaler_status_str'];?></td>
							<td><?php echo $value['createtime']; ?></td>
							<td>
							<a class="btn1" href="regionalWholesale_detail.php?op=edit&user_id=<?php echo $value['user_id']; ?>&customer_id=<?php echo $customer_id;?>" title="查看区域批发商申请">
								<img src="../../../common/images_V6.0/operating_icon/icon05.png" align="absmiddle"/>
							</a>
							<a class="btn1" href="area_supply_log.php?user_id=<?php echo $value['user_id']; ?>&customer_id=<?php echo $customer_id;?>" title="查看区域批发商日志">
								<img src="../../../common/images_V6.0/operating_icon/icon30.png" align="absmiddle"/>
							</a>

							<!-- <a class="btn1" onclick="show_area()" title="查看区域批发商日志">
								<img src="../../../common/images_V6.0/operating_icon/icon30.png" align="absmiddle"/>
							</a> -->
							
							<?php if( $value['wholesaler_status']==0 ){?>
							<a  class="btn1"  onclick="G.ui.tips.confirm('您确定通过吗？','area_operation.php?user_id=<?php echo $value['user_id']; ?>&op=7&type=1')"  title="通过区域批发商申请">
							<img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle"/>
							</a>
							
							<a  class="btn1" onclick='showReason("<?php echo $value['user_id']; ?>","2","<?php echo $value['wqid']; ?>")' title="驳回/暂停区域批发商申请">
							  <img src="../../../common/images_V6.0/operating_icon/icon03.png" align="absmiddle"/>
							</a>
							<?php }?>
							
							<a class="btn1" onclick="G.ui.tips.confirm('您确定删除吗？','area_operation.php?id=<?php echo $value['id']; ?>&user_id=<?php echo $value['user_id']; ?>&op=8')"><img src="../../../common/images_V6.0/operating_icon/icon04.png" align="absmiddle" alt="删除"></a>
							
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
	<div class="tanbox" style="display:none">
	    <div class="heidi"></div>
	    <div class="content">
	        <p class="head">状态更新</p>
	        <div class="bodybox">
	            
	        </div>
	    </div>
	</div>
<script type="text/javascript" src="../../Common/js/Base/mall_setting/ToolTip.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>

<script>

var pagenum = '<?php echo $pagenum ?>';
 var rcount_q2 = '<?php echo $rcount_q2 ?>';
 var end = '<?php echo $end ?>';
  var count =Math.ceil(rcount_q2/end);//总页数
	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			var search_brandstatus = '<?php echo $search_brandstatus ?>';
			var search_user_id = '<?php echo $search_user_id ?>';
			var search_name = '<?php echo $search_name ?>';
			var search_phone = '<?php echo $search_phone ?>';
			 document.location= "area_supply.php?pagenum="+p+"&pagecount="+end+"&search_brandstatus="+search_brandstatus+"&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
	   }
    }); 
	var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
			 var search_brandstatus = '<?php echo $search_brandstatus ?>';
			var search_user_id = '<?php echo $search_user_id ?>';
			var search_name = '<?php echo $search_name ?>';
			var search_phone = '<?php echo $search_phone ?>';
			 document.location= "area_supply.php?pagenum="+a+"&pagecount="+end+"&search_brandstatus="+search_brandstatus+"&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>";
		 }
  }

  function show_area(){
  	$('#area').show();
  }
	function change(id,type){
		showReason('brand_supply.php?op=brand_status&status=-1&user_id=202709&pagenum=1');
		
	}

	/*function del(id){
		if(confirm("确定要删除此区域批发商吗？")){
			$.post('area_operation.php',{'op':8,'id':id},function(data){
				console.log(data)
				window.location.href = location;
			})
		}
	}*/
	
  function showReason(user_id,type,wqid){
  
    var str=prompt("请输入驳回/暂停理由","您不符合合作商条件，请联系客服");
    if(str){
    	$.post('area_operation.php',{'op':7,'user_id':user_id,'type':2,'str':str,'wqid':wqid},function(data){
    		console.log(data)
    		window.location.href = location;
    	})
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

<?php mysql_close($link);?>	
 <script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>