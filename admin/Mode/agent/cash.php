<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=1;//头部文件  0基本设置,1提现记录,2代理商管理

$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$pagesize=20;
if(!empty($_GET["pagesize"])){
    $pagesize = $configutil->splash_new($_GET["pagesize"]);
}
if(!empty($_POST["pagesize"])){
    $pagesize = $configutil->splash_new($_POST["pagesize"]);
}
$start = ($pagenum-1) * $pagesize;
$end = $pagesize;

 $search_batchcode="";
if(!empty($_POST["search_batchcode"])){
   $search_batchcode = $configutil->splash_new($_POST["search_batchcode"]);
}

$pay_type = "IPSpay";
$sql = "SELECT gathering From pay_config WHERE customer_id='".$customer_id."' AND pay_type='".$pay_type."' AND isvalid=true limit 1";
$result = _mysql_query($sql) or die(mysql_error().$sql);
if($result){
    $arr = mysql_fetch_row($result);
    $gathering = $arr[0];
}
$gathering = unserialize($gathering);
foreach ($gathering as $key => $value) {
	$gathering_name[] 		= $value['pro_name'];
	$gathering_begintime[] 	= $value['begintime'];
	$gathering_endtime[] 	= $value['endtime'];
}
$gathering_name 		= implode(',', $gathering_name);
$gathering_begintime 	= implode(',', $gathering_begintime);
$gathering_endtime 		= implode(',', $gathering_endtime);
?>
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/agent/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js" charset="utf-8"></script>
<script type="text/javascript" src="../../../common/js/jquery.blockUI.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script src="../../../common/js/floatBox.js"></script>
<style>

tr {
    line-height: 22px;
}
.promptBox{position:fixed;top:50%;left:50%;width:480px;height:220px;margin:-110px 0 0 -240px;background:#fff;border:solid 1px #797979;box-sizing:border-box;padding:12px 40px;}
.promptBox h3{text-align:center;font-size:18px;color:#333;}
.promptBox ul{}
.promptBox ul li{line-height:30px;}
.promptBox p{text-align:center;}
.promptBox p .WSY_button,.promptBox1 p .WSY_button{margin-top:0;float:none;}
.promptBox p .WSY_button:first-child{margin-right:60px;}
.promptBox1{width:350px;height:170px;position:fixed;top:50%;left:50%;margin:-85px 0 0 -175px;background:#fff;border:solid 1px #797979;box-sizing:border-box;padding:12px 40px;}
.promptBox1 p{text-align:center;}
.promptBox1 div{height:80px;display:table-cell;vertical-align:middle;}
.promptBox1 p .WSY_button:first-child{margin-right:25px;}
</style>
<title>提现记录</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			// include("../../../../weixinpl/back_newshops/Mode/agent/basic_head.php");
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/Mode/agent/basic_head.php");
			?>
			<!--列表头部切换结束-->
			<div class="WSY_remind_main">
				<form class="search" id="search_form" method="post" action="cash.php?customer_id=<?php echo $customer_id_en; ?>">
					<div class="WSY_list" style="margin-top: 18px;">
						<li class="WSY_left"><a>订单号：</a></li>
						<input style="margin-top:5px" type=text name="search_batchcode" id="search_batchcode" value="<?php echo $search_batchcode; ?>"/>
						<input style="margin-top:5px" type="submit" class="search_btn" value="搜 索">
						<input style="margin-top:5px" type="button" class="search_btn" value="导出记录" onclick="export_excel()">
					</div>
				</form>
				<table width="97%" class="WSY_table" id="WSY_t1">
					<thead class="WSY_table_header">
						<th width="5%">用户ID</th>
						<th width="11%">订单号</th>
						<th width="14%">交易流水号</th>
						<th width="8%">账户类型</th>
						<th width="17%">账户</th>
						<th width="8%">代理商库存</th>
						<th width="8%">申请金额</th>
						<th width="8%">申请时间</th>
						<th width="5%">申请状态</th>
						<th width="8%">确认时间</th>
						<th width="8%">备注</th>
						<th width="8%">操作</th>
					</thead>
					<tbody>
					   <?php


						 $query="select distinct wd.id,wd.user_id,wd.serial_number,wd.money,wd.remark,wd.status,wd.createtime,wd.confirmtime,wd.person_information,agc.batchcode from weixin_commonshop_withdrawals wd inner join weixin_commonshop_agentfee_records agc where agc.withdrawal_id = wd.id and wd.isvalid=1 and wd.type=1 and wd.user_type=1 and wd.customer_id=".$customer_id;

						  if(!empty($search_batchcode)){
							$query = $query." and agc.batchcode like '%".$search_batchcode."%'";
						 }
						  /* 输出数量开始 */
						 $query2 = $query.' group by id order by id';
						 $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
						 $rcount_q2 = mysql_num_rows($result2);
						 /* 输出数量结束 */
						 $query = $query." order by id desc"." limit ".$start.",".$end;
						 // echo $query;
						 $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
						 while ($row = mysql_fetch_object($result)) {

							$keyid = $row->id;
							$user_id =$row->user_id;
							$money = $row->money;
							$money = round($money, 2);
							$status = $row->status;
							$createtime = $row->createtime;
							$confirmtime = $row->confirmtime;

							$serial_number=$row->serial_number;
							$remark = $row->remark;
							$batchcode = $row->batchcode;
							$person_information = $row->person_information;
							if( !empty( $person_information ) ){
								$person_information = json_decode($person_information,true);
								$username = $person_information['name'].'('.$person_information['weixin_name'].')';
								$account = $person_information['account'];
								$account_type = $person_information['account_type'];
								$email = $person_information['email'];
								$bank_open = $person_information['bank_open'];
								$name = $person_information['name'];
								$bank_name = $person_information['bank_name'];
								$bank_type = $person_information['bank_type'];
								$bank_address = $person_information['bank_address'];
								$userphone = $person_information['phone'];
							}else{
								//查询用户名
								$query2= "select name,phone,weixin_name from weixin_users where isvalid=true and id=".$user_id." and customer_id=".$customer_id." limit 0,1";
								$username="";
								$userphone ="";
								$weixin_name = "";
								$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
								while ($row2 = mysql_fetch_object($result2)) {
									$username=$row2->name;
									//$userphone = $row2->phone;
									$weixin_name = $row2->weixin_name;
									$username = $username."(".$weixin_name.")";
									break;
								}
								//查找账户和支付宝
								$query2="select account_type,email,account,bank_open,bank_name,name,bank_address,phone,bank_type from weixin_card_members where isvalid=true and user_id=".$user_id;
								$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
								$account = "";
								$account_type="";
								$email="";
								$bank_open="";
								$name="";
								$bank_name="";
								$userphone ="";
								$bank_address="";
								$bank_type=1;	//1:对公账号 2:对私账号
								while ($row2 = mysql_fetch_object($result2)) {
									$account= $row2->account;
									$account_type =$row2->account_type;
									$email =$row2->email;
									$bank_open = $row2->bank_open;
									$name = $row2->name;
									$bank_name = $row2->bank_name;
									$bank_type = $row2->bank_type;
									$bank_address = $row2->bank_address;
									$userphone = $row2->phone;
								}
							}

							$statusstr="待审核";
							switch($status){
							   case 1:
								 $statusstr="待审核";
								 break;
							   case 2:
								 $statusstr="通过";
								 break;
							   case 3:
								 $statusstr="已驳回";
								 break;
							}


							//查询代理商库存
							$query2= "select agent_inventory from promoters where isvalid=true and user_id=".$user_id." and customer_id=".$customer_id." limit 0,1";
							$agent_inventory=0;
							$result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
							while ($row2 = mysql_fetch_object($result2)) {
								$agent_inventory=$row2->agent_inventory;
								break;
							}

							$bank_type_str="";
							switch($bank_type){
								case 1:
								   $bank_type_str="对公账号";
								   break;
								case 2:
								   $bank_type_str="对私账号";
								   break;
							}
							if( $account_type == 4 ){
								switch($bank_type){
									case 3:
									   $ips_type_str="个人";
									   break;
									case 4:
									   $ips_str = $ips_type_str = "企业";
									   break;
								}
							}
							$account_type_str="";
							switch($account_type){
								case 1:
								   $account_type_str="支付宝";
								   break;
								case 2:
								   $account_type_str="财付通";
								   break;
								case 3:
								   $account_type_str="银行账户";
								   break;
								case 4:
								   $account_type_str="环迅账户";
								   break;
							}

					   ?>
						<tr>
							<td><?php echo $user_id; ?></td>
							<td style="word-break:break-all"><?php echo $batchcode; ?></td>
							<td style="word-break:break-all"><span id="span_serial_number_<?php echo $keyid;?>"><?php echo $serial_number; ?></span></td>
							<td><?php echo $account_type_str; ?></td>
							<td>
								<?php if($account_type!=4){ ?>
								<?php echo $username; ?></a><br/>
							   	<?php echo $userphone; ?><br/>
							   	<?php } ?>

							   <?php if($account_type==3){ ?>
							   银行账户:<?php echo $account; ?><br/>
							   开户银行：<?php echo '('.$bank_type_str.')'.$bank_open; ?><br/>
							   开户姓名：<?php echo $bank_name; ?>
							   <?php }elseif($account_type==4){ ?>
							   <?php echo $ips_type_str; ?>账户:<?php echo $account; ?><br/>
							   <?php echo $ips_str ?>名称：<?php echo $name; ?>
							   <?php }else{ ?>
							   账户:<?php echo $email; ?><br/>
							   名称：<?php echo $name; ?>
							   <?php } ?>
						   </td>
						   <td><?php echo $agent_inventory; ?>元</td>
						   <td><a href="agentcost_detail.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>&istype=2&search_batchcode=<?php echo $batchcode; ?>"><?php echo $money; ?>元</a></td>
						   <td><?php echo $createtime; ?></td>
						   <td><span id="span_status_<?php echo $keyid;?>"><?php echo $statusstr; ?></span></td>
						   <td><span id="span_confirmtime_<?php echo $keyid;?>"><?php echo $confirmtime; ?></span></td>
						  <td><span id="span_remark_<?php echo $keyid;?>"><?php echo $remark; ?></span></td>

						   <td>
						   <span id="span_op_<?php echo $keyid;?>">
						   <?php if($status==1){?>
						   		<?php if( $account_type == 4 ){ ?>
									<a class="btn1" onclick="promptBox('<?php echo $money; ?>','<?php echo $account; ?>','<?php echo $name; ?>','<?php echo $gathering_name ?>','<?php echo $gathering_begintime ?>','<?php echo $gathering_endtime ?>',<?php echo $keyid; ?>)" title="确定打款">
										<img src="../../../common/images_V6.0/operating_icon/icon23.png" class="<?php echo $id;?>" align="absmiddle">
									</a>
						   		<?php }else{ ?>
								<a  class="btn1" href="javascript:sub_status(2,<?php echo $keyid; ?>,<?php echo $user_id; ?>);"  title="通过">
								<img src="../../../common/images_V6.0/operating_icon/icon23.png" align="absmiddle"/>
								</a>
								<?php } ?>
								<a  class="btn1" href="javascript:showReason(3,<?php echo $keyid; ?>,<?php echo $user_id; ?>,<?php echo $money; ?>,<?php echo $batchcode; ?>);"  title="驳回">
								<img src="../../../common/images_V6.0/operating_icon/icon03.png" align="absmiddle"/>
								</a>
						   <?php }?>
						   </span>

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


<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="../../Common/js/layer/layer.js"></script>
<script>
var pagenum = <?php echo $pagenum ?>;
 var rcount_q2 = <?php echo $rcount_q2 ?>;
 var end = <?php echo $end ?>;
 var customer_id = <?php echo $customer_id ?>;

 /* var user_id = <?php echo $user_id ?>; */
 var count =Math.ceil(rcount_q2/end);//总页数

  	//pageCount：总页数
	//current：当前页

	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 var search_batchcode = document.getElementById("search_batchcode").value;
		 document.location= "cash.php?pagenum="+p+"&search_batchcode="+search_batchcode;
	   }
    });

  var pagenum = <?php echo $pagenum ?>;
   var page = count;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	var search_batchcode = document.getElementById("search_batchcode").value;
		 document.location= "cash.php?pagenum="+a+"&search_batchcode="+search_batchcode;
	}
  }

  function promptBox(money,account,name,select,begin,end,id){
  	var select 	= select.split(',');
  	var begin 	= begin.split(',');
  	var end 	= end.split(',');

  	var str = '<ul>';
  		str += '<li><span>提现金额：</span> ￥'+money+'</li>';
  		str += '<li><span>提现账户号：</span> '+account+'</li>';
  		str += '<li><span>提现名称：</span> '+name+'</li>';
  		str += '<li><span>收款项目：</span> <select style="width:100px;" id="project">';
  		for (var i = 0; i < select.length; i++) {
  			str += '<option data-begin="'+begin[i]+'" data-end="'+end[i]+'">'+select[i]+'</option>';
  		}
  		str += '';
  		str += '</select></li>';
  		str += '</ul>';

	layer.open({
		title:'确认提示',
		content:str,
		btn:['确定','取消'],
		yes:function(index){
			submitTrue(id,account,money,name);
			layer.close(index);
		},
		btn2:function(index){
			layer.close(index);
		},
		btnAlign: 'c'
	});
  }

  function tab(date1,date2){
      var oDate1 = new Date(date1);
      var oDate2 = new Date(date2);
      if(oDate1.getTime() > oDate2.getTime()){
          return 1;
      } else {
          return 2;
      }
  }

  function submitTrue(id,account,money,name){
  	var batchcode = '<?php echo time(); ?>';
  	var begin = $('#project option:selected').data('begin');
  	var end = $('#project option:selected').data('end');
    var name = $('#project option:selected').text();
  	var now = '<?php echo date("Y-m-d") ?>';
  	if( tab(now,begin)==2 || tab(end,now)==2 ){
  		txt = '您的收款项目已过期,请重新添加!'
  		var str = '<div class="promptBox1"><p>提示</p><div>';
  		str += ''+txt+'';
  		str += '</div><p><a href="//<?php echo HostUrl ?>/wsy_pay/admin/pay_set/hxpay_set.php"><input type="button" class="WSY_button" value="去添加" style="cursor:pointer;" ></a>';
  		str += '<input type="button" class="WSY_button" value="取消" style="cursor:pointer;" onclick="offpromptBox();"></p></div>';
  		$('body').append(str);
  	}else{
  		$('.promptBox').remove();
  		$.ajax({
  			url:'../../../mshop/ips_operation.php',
  			dataType:'json',
  			type:'post',
  			data:{
  				batchcode:batchcode,
  				price:money,
  				account:account,
                name:name,
  				id:id,
  				op:2
  			},
  			success:function(data){
  				if(data.code==0){
                    var serial_number = data.serial_number
  					url='save_cash.php?callback=jsonpCallback_save_shop_applycash&status=2&keyid='+id+'&serial_number='+serial_number;
  					$.jsonp({
  						url:url,
  						callbackParameter: 'jsonpCallback_save_shop_applycash'
  					});
                    location.href=location;
  				}else{
                    alert(data.msg)
                    location.href=location;
  				}
  			}
  		});
  	}
  }
  function offpromptBox(){
  	$('.promptBox,.promptBox1').remove();
  }

  function sub_status(status,id,user_id){	//确认提交申请提现
    if(status==2){
		var serial_number=prompt("请输入交易流水账号","");
	}
    if(serial_number==""){
	  alert("不能为空");
	  return;
	}
	if(serial_number){
	 url='save_cash.php?callback=jsonpCallback_save_shop_applycash&user_id='+user_id+'&status='+status+"&keyid="+id+"&pagenum="+pagenum+"&serial_number="+serial_number;
		 console.log(url);
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_save_shop_applycash'
		});
	}
  }
   function jsonpCallback_save_shop_applycash(results){
	 var keyid = results[0].keyid;
	 var remark = results[0].remark;
	 var serial_number = results[0].serial_number;
	 var confirmtime = results[0].confirmtime;
      document.getElementById("span_status_"+keyid).innerHTML="<span id='span_status_"+keyid+"'>确认</span>";
	   document.getElementById("span_remark_"+keyid).innerHTML="<span id='span_remark_"+keyid+"'>"+remark+"</span>";
      document.getElementById("span_confirmtime_"+keyid).innerHTML="<span id='span_confirmtime_"+keyid+"'>"+confirmtime+"</span>";
      document.getElementById("span_serial_number_"+keyid).innerHTML="<span id='span_serial_number_"+keyid+"'>"+serial_number+"</span>";
      document.getElementById("span_op_"+keyid).innerHTML="<span id='span_op_"+keyid+"'></span>";
  }

  function showReason(status,id,user_id,money,batchcode){	//驳回提现申请
   if(status==3){
	var str=prompt("请输入驳回/暂停理由","您的余额不足以提现，请联系客服");
	}
    if(str)
    {
	   url='save_cash.php?callback=jsonpCallback_showReason&user_id='+user_id+'&status='+status+"&keyid="+id+"&money="+money+"&batchcode="+batchcode+"&pagenum="+pagenum+"&reason="+str;
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_showReason'
		});
    }
  }
  function jsonpCallback_showReason(results){
	 var keyid = results[0].keyid;
	 var remark = results[0].remark;
	 var confirmtime = results[0].confirmtime;
	 document.getElementById("span_status_"+keyid).innerHTML="<span id='span_status_"+keyid+"'>已驳回</span>";
      document.getElementById("span_remark_"+keyid).innerHTML="<span id='span_remark_"+keyid+"'>"+remark+"</span>";
	  document.getElementById("span_confirmtime_"+keyid).innerHTML="<span id='span_confirmtime_"+keyid+"'>"+confirmtime+"</span>";
	  document.getElementById("span_op_"+keyid).innerHTML="<span id='span_op_"+keyid+"'></span>";
  }
</script>
<script>
function export_excel(){
	var search_batchcode = $('#search_batchcode').val();//搜索订单
	if(search_batchcode==""){
		search_batchcode = 0;
	}

	/*导出自行安装订单筛选框*/
	var excelArray = [
						["user_id","用户ID"],
						["batchcode","订单号"],
						["serial_number","交易流水号"],
						["account_type_str","账户类型"],
						["username","姓名"],
						["userphone","账户电话"],
						["email","账户"],
						["name","账户名称"],
						["agent_inventory","代理商库存"],
						["money","申请金额"],
						["createtime","申请时间"],
						["statusstr","申请状态"],
						["confirmtime","确认时间"],
						["remark","备注"]
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

	if(str==""){
		str = 0;
	}

	var url='/weixin/plat/app/index.php/Excel/commonshop_excel_cash/customer_id/'+customer_id+"/excel_fields/"+str+"/search_batchcode/"+search_batchcode;
	document.location = url;

		$(".floatbox").hide();
		$(".floatbox").remove();
	});
}
</script>

<?php mysql_close($link);?>

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>