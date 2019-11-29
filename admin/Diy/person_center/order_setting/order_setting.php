<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../../weixinpl/config.php');
$customer_id = $_GET['customer_id'];  //解密
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../../weixinpl/proxy_info.php');
$pageindex= 0 ;//头部文件0礼包



$query = "select * from weixin_commonshop_order_setting_cus where isvalid=true and customer_id=".$customer_id." limit 1";
$keyid		 	= -1;
$choose 		= '';//商家的选择
$result = _mysql_query($query) or die('L15 Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$keyid 			= $row -> id;
	$choose 		= $row -> choose;
}
if($keyid<0){	//初始化
	
	$query = "select * from weixin_commonshop_order_setting where isvalid=true  order by sys_num asc  ";
	//echo $query;
	$result = _mysql_query($query) or die('L15 Query failed: ' . mysql_error());
	$sys_num=-1;
	$choose_str = '';
	while ($row = mysql_fetch_object($result)) {
		$keyid 			= $row -> id;
		$sys_num 		= $row -> sys_num;
		
		$sys_c = '_0';
		if($sys_num==1){			//商城默认选择
			$sys_c = '_1';
		}
		$choose_str.= $sys_num.$sys_c.'|*|';
	}
	//echo $choose_str;
	$query2 = "insert into weixin_commonshop_order_setting_cus(choose,customer_id,isvalid,createtime)values('".$choose_str."',".$customer_id.",1,now())";
	//echo $query2;
	_mysql_query($query2)or die('Query failed'.mysql_error());
	
}else{
	
	$choose_arr = explode('|*|',$choose);
	$choose_exp_arr = array();				//记录商家设置过的数据
	foreach($choose_arr as $value){
		//echo $value.'<br>';
		$temp = explode('_',$value);
		array_push($choose_exp_arr,$temp);
		
	}

	//print_r($choose_exp_arr);

}
// print_r($choose_arr);die();
$PC_SHOP = 0;	//pc商城权限，1：有权限，0：无权限

$query_pcshop = "SELECT COUNT(1) AS is_pcshop FROM customer_funs cf INNER JOIN columns c WHERE c.isvalid=true AND cf.isvalid=true AND cf.customer_id=".$customer_id." AND c.sys_name='PC商城' AND c.id=cf.column_id";

$result_pcshop = _mysql_query($query_pcshop) or die('Query_pcshop failed:'.mysql_error());
while( $row_pcshop = mysql_fetch_object($result_pcshop) ){
    $is_pcshop = $row_pcshop -> is_pcshop;
    break;
}
if( $is_pcshop > 0 ){
    $PC_SHOP = 1;
}


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>多种订单显示开关设置</title>
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script charset="utf-8" src="../../../../common/js/jquery.jsonp-2.2.0.js"></script>
</head>
<style>
.WSY_remind_labelbox {
	overflow: hidden;
	margin-bottom: 10px;
}
.clear{
	clear:both;
}
.WSY_remind_dl02{
	width:400px;margin-top:0px;
	float:left;
	/*height: 265px;*/
}
.phone_size{
	width:320px;
	height:480px;
}
.parent{
	overflow-y: scroll;
}

.parent >iframe{
	height:1000px;
}
</style>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<?php
				include("../head.php"); 
			?>
			<form id="upform" class="upform" method="post" action="save_base.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data">
			<input type=hidden name="keyid" id="keyid" value="<?php echo $keyid; ?>" />
			<input type=hidden name="is_ncomission" id="is_ncomission" value="<?php echo $is_ncomission; ?>" />
			<input type=hidden name="is_shareholder" id="is_shareholder" value="<?php echo $is_shareholder; ?>" />
			<input type=hidden name="is_team" id="is_team" value="<?php echo $is_team; ?>" />
			<input type=hidden name="isOpenAgent" id="isOpenAgent" value="<?php echo $isOpenAgent; ?>" />
			<input type=hidden name="isOpenSupply" id="isOpenSupply" value="<?php echo $isOpenSupply; ?>" />
			
				<div class="WSY_data WSY_remind_main">
					<dl class="WSY_remind_dl02" > 
						<dt>多种订单显示开关选择：</dt>
						<dd style="overflow:hidden;margin-top:5px;">
							<?php
							//查询独立开关
							$i = 0;							
							$query = "SELECT * from weixin_commonshop_order_setting where isvalid=true  and type=1  order by sys_num asc  ";

							$result = _mysql_query($query) or die('L15 Query failed: ' . mysql_error());
							
							$keyid=-1;		
							$sys_num=-1;	//默认序号
							$remark = '';	//每种选择名称
							while ($row = mysql_fetch_object($result)) {
								$keyid 			= $row->id;
								$sys_num 		= $row->sys_num;
								$remark 		= $row->remark;								
							    if ($keyid == 23 && !$PC_SHOP){//PC商城-大礼包订单独立开关
                                }else{
							?>
							<div class="WSY_remind_labelbox">
								<label>
									
									<input type="checkbox" sys_num="<?php echo $sys_num ;?>" value="<?php if(in_array(array($sys_num,1),$choose_exp_arr)) {echo 1;} else{ echo 0 ;} ?>" name="sys_num_<?php echo $sys_num?>" <?php if(in_array(array($sys_num,1),$choose_exp_arr) )echo 'checked'; ?> onclick="click_check_box(this);" >
									<?php echo $remark ?>
								</label>
							</div>
							<?php $i++; }} ?>
						</dd>
						<input type=hidden name="P_num" id="P_num" value="<?php echo $i; ?>" />
					</dl>
					<dl class="WSY_remind_dl02" style=""> 
						<dt>子按钮显示开关选择：</dt>
						<dd style="overflow:hidden;margin-top:5px;">
							<?php
							//查询子开关
							$i = 0;							
							$query = "SELECT * from weixin_commonshop_order_setting where isvalid=true and type=2 order by sys_num asc  ";
							$result = _mysql_query($query) or die('L15 Query failed: ' . mysql_error());
							$keyid=-1;		
							$sys_num=-1;	//默认序号
							$remark = '';	//每种选择名称
							while ($row = mysql_fetch_object($result)) {
								$keyid 			= $row->id;
								$sys_num 		= $row->sys_num;
								$remark 		= $row->remark;								
							
							?>
							<div class="WSY_remind_labelbox">
								<label>
								
									<input type="checkbox" sys_num="<?php echo $sys_num ;?>" value="<?php if(in_array(array($sys_num,1),$choose_exp_arr)) {echo 1;} else{ echo 0 ;} ?>" name="sys_num_<?php echo $sys_num?>" <?php if(in_array(array($sys_num,1),$choose_exp_arr) )echo 'checked'; ?> onclick="click_check_box(this);" >
									<?php echo $remark ?>
								</label>
							</div>
							<?php $i++; } ?>
						</dd>
						<input type=hidden name="P_num" id="P_num" value="<?php echo $i; ?>" />
					</dl>	

					<dl class="WSY_remind_dl02 phone_size " style="display:none;"> 
						<dt>显示效果</dt>
						<div>
						<dd class="phone_size parent">
						
						<iframe id="mainFrame"  name="mainFrame" src="../../../../mshop/person_center_back_show.php" scrolling="no" frameborder="0" height="100%" width="100%">
						</iframe>
						
						</dd>
						</div>
						<input type=hidden name="P_num" id="P_num" value="<?php echo $i; ?>" />
					</dl>	
					<div class="clear">	</div>
				</div>	
				<div class="WSY_text_input01">
					<div class="WSY_text_input">
						<button class="WSY_button" style="margin-left:400px;" id="btnSave" type="button" onclick="saveBase()">提交保存</button>
					</div>
				</div>
			</form>
		</div>		
	</div>
<script>
var customer_id = '<?php echo $customer_id ;?>';
function click_check_box(obj){
	self = $(obj);
	var val = self.val();
	var sys_num = self.attr('sys_num');
	if(val == 0 ){
		self.val(1);
	}else{
		self.val(0);
	}
	var _temp = sys_num+'_'+self.val()
	$('#mainFrame').attr('src','../../../../mshop/person_center_back_show.php?type='+_temp);
}
function saveBase(){
	var check_box_sel = new Array();
	$('.WSY_remind_labelbox input').each(function(){
			var checkbox_val =  $(this).val();
			var sys_num = $(this).attr('sys_num');
			//console.log(sys_num);	
			//console.log(checkbox_val);	
			var arr = new Array(sys_num,checkbox_val);
			check_box_sel.push(arr);
			
		});
		console.log(check_box_sel);
		check_box_sel = JSON.stringify(check_box_sel);
		
		$.ajax({
		   url: "save_order_setting.php?op=save&customer_id=<?php echo $customer_id_en;?>",
		   data:{check_box_sel:check_box_sel},
		   type: "POST",
		   dataType:'json',
		   async: true,      
		   success:function(res){
			   alert(res.msg);
		   },
		   
	   });
}
</script>
<script type="text/javascript" src="../../../../common/js_V6.0/content.js"></script>
</body>
</html>
