<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件  0基本设置,1提现记录,2供应商管理

//供应商模式,分销商城的功能项是 306
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = _mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
$supply_str = "未开启合作商模式";
if($dcount>0){
   $is_supply=1;
   $supply_str = "已开启合作商模式";
}


$query = "select id,deposit,commission,supply_detail,not_supply_tip,limit_money,limit_day,brandsupply_detail,brand_adimg,brand_ad_foreign_id,brand_ad_detail_id,brand_linktype,is_export_order,is_supplyData from weixin_commonshop_supplys where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$deposit = "";	//供应商押金
$commission = "";	//供应商提成
$supply_detail = "";	//供应商详情
$not_supply_tip = "";	//非供应商提醒
$limit_day = -1;  //限制时间
$limit_money =-1;  //限制金额
$type_ad_foreign_id=-1;
$type_ad_detail_id=-1;
$type_linktype=-1;
$is_export_order=-1;//订单导出开关
$is_supplyData=-1;//数据统计开关
while ( $row = mysql_fetch_object($result) ){
    $deposit=$row->deposit;
	$commission=$row->commission;
	$supply_detail=$row->supply_detail;
	$not_supply_tip=$row->not_supply_tip;
	$limit_money=$row->limit_money;
	$limit_day=$row->limit_day;
	$brandsupply_detail=$row->brandsupply_detail; //品牌供应商申请说明
	$brand_adimg=$row->brand_adimg; //分类页品牌广告图
	$type_ad_foreign_id = $row->brand_ad_foreign_id;
	$type_ad_detail_id = $row->brand_ad_detail_id;
	$type_linktype = $row->brand_linktype;
	$is_export_order = $row->is_export_order;
	$is_supplyData = $row->is_supplyData;
}

// $query = "select isOpenSupply,is_supplyset,isOpenBrandSupply,is_supply_product_off_shelves from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$query = "select isOpenSupply,is_supplyset,isOpenBrandSupply,is_supply_product_off_shelves,supply_must from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$isOpenSupply = 0; 
$is_supplyset = 0; //商家可设置开关 1:由供应商自己设置现价或者供货价 0:平台设置
$is_supply_product_off_shelves = 0;
while ($row = mysql_fetch_object($result)) {
	$isOpenSupply = $row->isOpenSupply;//是否在个人中心开启供应商申请
	$is_supplyset = $row->is_supplyset;//商家可设置开关 1:由供应商自己设置现价或者供货价 0:平台设置
	$isOpenBrandSupply = $row->isOpenBrandSupply;//是否在个人中心开启品牌供应商申请
    $is_supply_product_off_shelves = $row->is_supply_product_off_shelves;//是否合作商可以下架自己的产品
    $supply_must = explode('_',$row->supply_must);
}
//分类链接
$typearr=[];
$tuwenarr=[];
$query="select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $pt_id = $row->id;
   $pt_name = $row->name;
   $typearr[] = $pt_id."_".$pt_name;
}
//店铺链接
$brandarr=[];
$query="select user_id,brand_name from weixin_commonshop_brand_supplys where isvalid=true and brand_status=1 and customer_id=".$customer_id." order by brand_opentime desc";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $supplier_id = $row->user_id;
   $brand_name = $row->brand_name;
   $brandarr[] = $supplier_id."_".$brand_name;
}

$query="select is_open_suning from ".WSY_SHOP.".suning_setting where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $is_open_suning = $row->is_open_suning;
}
?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>供应商-基本设置</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
.chooseItem{padding: 10px 0 0 70px}
.chooseItem-left,.chooseItem-right{float:left;}
.chooseItem-left{font-size: 14px;margin-right: 10px;}
.chooseItem-right{width:25%}
.margin-right-20{margin-right:20px;width: 14px !important;height: 14px !important;vertical-align: -3px;}
.chooseItem-right .checkboxItem{width:50%;float:left;padding-bottom: 10px;}
/* .chooseItem-right label{margin-right:50px} */
</style>
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
			<form action="save_set.php?customer_id=<?php echo $customer_id_en;?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
				<div class="WSY_remind_main">
					<!-- <dl class="WSY_remind_dl02" style="margin-top:40px;"> 
						<dt style="line-height:20px;" class="WSY_left">苏宁对接开关：</dt>
						<dd>
							<?php if($is_open_suning==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_is_open_suning(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_open_suning(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_open_suning(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_open_suning(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd> -->
						<!-- <input type="hidden" name="is_open_suning" id="is_open_suning" value="<?php echo $isOpenSupply; ?>" /> -->
					<!-- </dl> -->
					<dl class="WSY_remind_dl02" > 
						<dt style="line-height:20px;" class="WSY_left">客户端个人中心开启合作商申请入口：</dt>
						<dd>
							<?php if($isOpenSupply==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_isOpenSupply(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isOpenSupply(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isOpenSupply(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isOpenSupply(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>
						<input type="hidden" name="isOpenSupply" id="isOpenSupply" value="<?php echo $isOpenSupply; ?>" />
							<div class="chooseItem" id="isOpenSupplyEx">
									<div class="chooseItem-left">客户端申请合作商必填项：</div>
									<div class="chooseItem-right">
										<div class="checkboxItem" id='checkboxItem1'>
											<?php if($supply_must[0]==1){ ?>
												<input class="margin-right-20" type="checkbox" value="1" checked='checked' onclick="changeCheckBox(this)"><label>申请人姓名与电话</label>
											<?php }else{ ?>
												<input class="margin-right-20" type="checkbox" name='' value="0"  onclick="changeCheckBox(this)"><label>申请人姓名与电话</label>
											<?php } ?>
											<input type="hidden" name='checkboxItem[]' value="<?php echo $supply_must[0]; ?>">
										</div>

										<div class="checkboxItem">
											<?php if($supply_must[1]==1){ ?>
												<input class="margin-right-20" type="checkbox"  value="1" checked='checked' onclick="changeCheckBox(this)"><label>合作商地址</label>
											<?php }else{ ?>
												<input class="margin-right-20" type="checkbox"  value="0"  onclick="changeCheckBox(this)"><label>合作商地址</label>
											<?php } ?>
											<input type="hidden" name='checkboxItem[]' value="<?php echo $supply_must[1]; ?>">
										</div>

										<div class="checkboxItem">
											<?php if($supply_must[2]==1){ ?>
												<input class="margin-right-20" type="checkbox"  value="1" checked='checked' onclick="changeCheckBox(this)"><label>身份证号码</label>
											<?php }else{ ?>
												<input class="margin-right-20" type="checkbox"  value="0"  onclick="changeCheckBox(this)"><label>身份证号码</label>
											<?php } ?>
											<input type="hidden" name='checkboxItem[]' value="<?php echo $supply_must[2]; ?>">
										</div>


										<div class="checkboxItem">
											<?php if($supply_must[3]==1){ ?>
												<input class="margin-right-20" type="checkbox"  value="1" checked='checked' onclick="changeCheckBox(this)"><label>公司名称</label>
											<?php }else{ ?>
												<input class="margin-right-20" type="checkbox"  value="0"  onclick="changeCheckBox(this)"><label>公司名称</label>
											<?php } ?>
											<input type="hidden" name='checkboxItem[]' value="<?php echo $supply_must[3]; ?>">
										</div>


										<div class="checkboxItem">
											<?php if($supply_must[4]==1){ ?>
												<input class="margin-right-20" type="checkbox"  value="1" checked='checked' onclick="changeCheckBox(this)"><label>身份证正反两面图片</label>
											<?php }else{ ?>
												<input class="margin-right-20" type="checkbox"  value="0"  onclick="changeCheckBox(this)"><label>身份证正反两面图片</label>
											<?php } ?>
											<input type="hidden" name='checkboxItem[]' value="<?php echo $supply_must[4]; ?>">
										</div>


										<div class="checkboxItem">
											<?php if($supply_must[5]==1){ ?>
												<input class="margin-right-20" type="checkbox"  value="1" checked='checked' onclick="changeCheckBox(this)"><label>营业执照图片</label>
											<?php }else{ ?>
												<input class="margin-right-20" type="checkbox"  value="0"  onclick="changeCheckBox(this)"><label>营业执照图片</label>
											<?php } ?>
											<input type="hidden" name='checkboxItem[]' value="<?php echo $supply_must[5]; ?>">
										</div>
									</div>
							</div>
					</dl>
					<dl class="WSY_remind_dl02" > 
						<dt style="line-height:20px;" class="WSY_left">开启品牌合作商：</dt>
						<dd>
							<?php if($isOpenBrandSupply==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_isOpenBrandSupply(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_isOpenBrandSupply(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_isOpenBrandSupply(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_isOpenBrandSupply(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="isOpenBrandSupply" id="isOpenBrandSupply" value="<?php echo $isOpenBrandSupply; ?>" />
					</dl>
					<dl class="WSY_remind_dl02"> 
						<dt style="line-height:20px;" class="WSY_left">合作商订单导出开关：</dt>
						<dd>
							<?php if($is_export_order==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_is_export_order(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_export_order(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_export_order(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_export_order(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="is_export_order" id="is_export_order" value="<?php echo $is_export_order; ?>" />
					</dl>		
					<dl class="WSY_remind_dl02"> 
						<dt style="line-height:20px;" class="WSY_left">店铺数据统计开关：</dt>
						<dd>
							<?php if($is_supplyData==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_is_supplyData(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_is_supplyData(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_is_supplyData(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_is_supplyData(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="is_supplyData" id="is_supplyData" value="<?php echo $is_supplyData; ?>" />
					</dl>
					<dl class="WSY_remind_dl02"> 
						<dt style="line-height:20px;" class="WSY_left">由合作商上传现价和供货价：</dt>
						<dd>
							<?php if($is_supplyset==1){ ?>
							<ul style="background-color: rgb(255, 113, 112);">
								<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
								<li onclick="change_supplyset(0)" class="WSY_bot" style="left: 0px;"></li>
								<span onclick="change_supplyset(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
							</ul>
							<?php }else{ ?>
							<ul style="background-color: rgb(203, 210, 216);">
								<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
								<li onclick="change_supplyset(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
								<span onclick="change_supplyset(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>								
							</ul>					 			
							<?php } ?>
						</dd>						
						<input type="hidden" name="is_supplyset" id="is_supplyset" value="<?php echo $is_supplyset; ?>" />
					</dl>
                    <dl class="WSY_remind_dl02">
                        <dt style="line-height:20px;" class="WSY_left">合作商可以下架自己的产品：</dt>
                        <dd>
                            <?php if($is_supply_product_off_shelves==1){ ?>
                                <ul style="background-color: rgb(255, 113, 112);">
                                    <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
                                    <li onclick="change_downset(0)" class="WSY_bot" style="left: 0px;"></li>
                                    <span onclick="change_downset(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                                </ul>
                            <?php }else{ ?>
                                <ul style="background-color: rgb(203, 210, 216);">
                                    <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
                                    <li onclick="change_downset(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                                    <span onclick="change_downset(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                                </ul>
                            <?php } ?>
                        </dd>
                        <input type="hidden" name="is_supply_product_off_shelves" id="is_supply_product_off_shelves" value="<?php echo $is_supply_product_off_shelves; ?>" />
                    </dl>
					<!--<dl class="WSY_remind_dl02" id="brandiframe" style="height:300px;<?php //if($isOpenBrandSupply>0){echo "display:block;";}else{echo "display:none;";}?>" > -->
					<!--<dt style="line-height:20px;" class="WSY_left">广告图图片：</dt>
						
						<div style="height:78px;display:block;float:left;">
							<span class="classify_span">
							<select style="width:160px;height:24px" id="type_foreign_id" name="type_foreign_id" onchange="getproduct(this.options[this.options.selectedIndex].value)">

							<optgroup label="---------------产品分类---------------"></optgroup>
							<?php 
								//for($i=0;$i<count($typearr);$i++){
								//	$typestr=explode("_",$typearr[$i]);
							?>	  
								<option value="<?php// echo $typestr[0];?>_1"><?php //echo $typestr[1];?></option>
							<?php 	
								//}
							?>
							<optgroup label="---------------品牌供应商店铺---------------"></optgroup>
							<?php 
								//for($i=0;$i<count($brandarr);$i++){
									//$brandstr=explode("_",$brandarr[$i]);
							?>	  
								<option value="<?php //echo $brandstr[0];?>_5" ><?php //echo $brandstr[1];?></option>
							<?php 	
								//}
							?>
							</select>
							<div class="pro_select"  id="pro_select" style="display:none;">
								<select id="type_detail_id" name="type_detail_id" style="width:160px;margin-top:7px;float:left;height:24px;margin-left:0">
									
								</select>
							</div>
							</span>
							<!--<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							<select style="width:200px;margin-left:20px;float:left;"><option>111111111</option></select>
							-->
						<!--</div>
						
						
						<iframe id="frm_typeimg" src="brand_adimg.php?customer_id=<?php //echo $customer_id_en; ?>&brand_adimg=<?php// echo $brand_adimg; ?>" 
							height=200 width=100% FRAMEBORDER=0 SCROLLING=no style="width:290px;height:250px;border: solid 1px #d0d0d0;position: absolute;top:358px;left:60px;"></iframe>
					
					</dl>
					<input type="hidden" name="brand_adimg" id="brand_adimg" value="<?php// echo $brand_adimg ; ?>" />
					-->
					<table width="97%" class="WSY_table" id="WSY_t1" style="margin-top:30px;">
						<thead class="WSY_table_header">
							<th width="25%">合作商押金</th>
							<th width="25%">非合作商提示</th>
							<th width="25%">每次提现金额不低于</th>
							<th width="25%">每次提现间隔天数</th> 
						</thead>
						<tr>
							<td class="WSY_t5"><input type=text name="deposit" id="deposit" value="<?php echo $deposit; ?>" placeholder="合作商押金" onkeyup="value=value.replace(/[^\d.]/g,'')"/><span><?php echo OOF_T ?></span></td>
							<td class="WSY_t5"><input type=text name="not_supply_tip" id="not_supply_tip" value="<?php echo $not_supply_tip; ?>" placeholder="您好，您现在还不是合作商，请申请成为合作商"/></td>
							<td class="WSY_t5"><input type=text name="limit_money" id="limit_money" value="<?php echo $limit_money; ?>" placeholder="金额不低于" onkeyup="value=value.replace(/[^\d.]/g,'')"/><span><?php echo OOF_T ?></span></td>
							<td class="WSY_t5"><input type=text name="limit_day" id="limit_day" value="<?php echo $limit_day; ?>" placeholder="间隔天数"/></td>
						</tr>
					</table>
					<div class="clear"></div>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:28px;" class="WSY_left">合作商协议：</dt>							
					</dl>
					<textarea id="editor1"   name="supply_detail"><?php echo $supply_detail;?></textarea>
					
					<div id="editor2_supply" style="<?php if($isOpenBrandSupply>0){echo "display:block;";}else{echo "display:none;";}?>">
						<dl class="WSY_remind_dl02">
							<dt style="line-height:28px;" class="WSY_left">品牌合作商协议：</dt>							
						</dl>
						<textarea id="editor2"   name="brandsupply_detail"><?php echo $brandsupply_detail;?></textarea>
					</div>
					<div class="WSY_text_input"><button type="button" class="WSY_button" onclick="subBase();">提交保存</button><br class="WSY_clearfloat"></div>
				</div>
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
//该边打钩的选项的值
function changeCheckBox(obj){
	var check = $(obj).val();
	if(check=='1'){
        $(obj).val('0');
         $(obj).siblings().val('0');
	}
	if(check=='0'){
        $(obj).val('1');
        $(obj).siblings().val('1');
	}
}
</script>
<script>

$(document).ready(function(){ 
	//setselect();
}); 

function setTypeImg(imgurl){
		$("#brand_adimg").val(imgurl);
	}
 function subBase(){
	var deposit = document.getElementById("deposit").value;
	//var commission = document.getElementById("commission").value;
	var not_supply_tip = document.getElementById("not_supply_tip").value;
	if(deposit==""){
		alert('请输入合作商押金');
	   return;
	}
	/* if(commission==""){
		alert('请输入供应商提成');
	   return;
	} 
	if(commission>1){
		alert('请输入供应商提成0~1');
	   return;
	}*/
	if(not_supply_tip==""){
		alert('请输入非合作商提醒');
	   return;
	}

	var is_open=$('#isOpenSupply').val();

	// console.log(is_open);

	if(is_open == 1){

		var nochoice=new Array();
		$('.chooseItem-right').find('.margin-right-20').each(function(){
			nochoice.push($(this).val());
		});

		var item_num=0;

		for (var i = 0; i < nochoice.length; i++) {
			if( nochoice[i] == 0 ){
				item_num++;
			}
		}

		if(item_num == nochoice.length ){
			alert('请至少勾选一个必填项');
			return false;
		}
	}
	
	document.getElementById("upform").submit();
 }
</script>
<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="../../../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束--->
<script>
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});

CKEDITOR.replace( 'editor2',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
function change_is_open_suning(obj) {
	$("#is_open_suning").val(obj);
}
function change_isOpenSupply(obj){
	$("#isOpenSupply").val(obj);
	if(obj) {
		$('#isOpenSupplyEx').css("display","block");
		$('#isOpenSupply').val("1");
		$('#checkboxItem1').css("display","block");

	} else {
		$('#isOpenSupplyEx').css("display","none");
		$('#isOpenSupply').val("0");
	}
}

var isOpenSupplyEx = $('#isOpenSupply').val();
if (isOpenSupplyEx == 1) {
	$('#isOpenSupplyEx').css("display","block");
} else {
	$('#isOpenSupplyEx').css("display","none");
}


function change_isOpenBrandSupply(obj){
	$("#isOpenBrandSupply").val(obj);
	console.log(obj);
	if(obj){
		$("#editor2_supply").css("display","block");
		$("#brandiframe").css("display","block");
	}else{
		$("#editor2_supply").css("display","none");	
		$("#brandiframe").css("display","none");	
	}
}
function change_supplyset(obj){
	$("#is_supplyset").val(obj);
}
function change_downset(obj){
    $("#is_supply_product_off_shelves").val(obj);
}
function change_is_export_order(obj){
	$("#is_export_order").val(obj);
}
function change_is_supplyData(obj){
	$("#is_supplyData").val(obj);
}

/*function getproduct(typeid){
	console.log(typeid);
	var typearr= new Array(); 
	typearr=typeid.split("_");	
	if(typearr[1]==1){			
		url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+typearr[0];
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list'
		});		
		$("#pro_select").css("display","block");
	}else if(typearr[1]==5){
		$("#pro_select").css("display","none");
	}
	
}
var detail_id=-1;	
function jsonpCallback_get_product_list(results){
	var len = results.length;
	console.log(results);
	var sel_pro = document.getElementById("type_detail_id");
	sel_pro.options.length=0;
	var new_option = new Option("---请选择一个产品---",-1);
	sel_pro.options.add(new_option);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;

		var new_option = new Option(pname,pid);
		sel_pro.options.add(new_option);
		if(pid==detail_id){
			new_option.selected=true;
		}
	}
}
var type_ad_foreign_id=-1;
var type_ad_detail_id=-1;
var type_linktype=-1;
function setselect(){
	
	var type_ad_foreign_id='<?php echo $type_ad_foreign_id?>';
	var type_ad_detail_id='<?php echo $type_ad_detail_id?>';
	var type_linktype='<?php echo $type_linktype?>';
	var sobj= document.getElementById("type_foreign_id");
	var options = sobj.options;
	
	for(var j=0;j<options.length;j++){
		document.getElementById("pro_select").style.display="none";
		var ov = options[j].value;
		var ovlen = ov.length;
		var sel_type = 1;
		var ov_id= -1;
		var ovtype = 1;
		if(ov.indexOf('_')!=-1){
		   var ovarr = ov.split('_');
		   ov = ovarr[0];
		   ovtype = ovarr[1];
	
		}
		if(ov==type_ad_foreign_id && ovtype==type_linktype){
			if(type_linktype==1){
				var dd =options[j].selected;
				options[j].selected ="selected";
				if(type_ad_foreign_id>0){
					//产品分类才显示出 选择产品，图文不需要
					document.getElementById("pro_select").style.display="block";
				}
				if(type_ad_detail_id>0){
					console.log(type_ad_foreign_id);
					changeProductType2(type_ad_foreign_id,type_ad_detail_id); 
				}else{
					changeProductType2(type_ad_foreign_id,-1); 
				}
			}else{
			  options[j].selected ="selected";
			 
			}
			break;
		}	
	}
}

function changeProductType2(pro_typeid,d_id){   //执行edit时候

	 p_detail_id = d_id;
	 //是产品分类
	 url='get_product_list.php?callback=jsonpCallback_get_product_list2&type_id='+pro_typeid;
	 $.jsonp({
		url:url,
		callbackParameter: 'jsonpCallback_get_product_list2'
	});
	
 // }
}
function jsonpCallback_get_product_list2(results){
	var len = results.length;
	var sel_pro = document.getElementById("type_detail_id");
	sel_pro.options.length=0;
	var new_option = new Option("---请选择一个产品---",-1);
	sel_pro.options.add(new_option);
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		var new_option = new Option(pname,pid);
		sel_pro.options.add(new_option);
		if(pid==p_detail_id){
			new_option.selected=true;
		}
	}   
}*/
</script>

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>