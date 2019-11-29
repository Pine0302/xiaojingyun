<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
require('../../../../weixinpl/common/utility_4m.php'); 		//加载4M方法

$shop_4m = new Utiliy_4m_new();

/**********START************/
//是否开启4M
$rearr = $shop_4m->is_4M_new($customer_id);

//var_dump($rearr);

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

//是否本身就是厂家总店
//1：厂家总店； 2：代理商总店
$owner_general = $rearr[5] ;

$orgin_adminuser_id = $rearr[6] ;

/***********END***********/

if($is_shopgeneral == 1){
	
	//获取下级所有的商家编号（不包含总店）
	$getAllSubCustomers = $shop_4m->getAllSubCustomers_new($customer_id,2) ;

	$query = "select id from  customers where isvalid=true and id in (".$getAllSubCustomers.")";
	$result=_mysql_query($query);
	$customer_part_id = 0;
	while($row=mysql_fetch_object($result)){
		$customer_part_id = $row->id;	
		//加载初始化方法
		$shop_4m->is_exsit_4m_control($customer_part_id);
	}

}else{
		echo '数据缺失，请重新检查4M设置！';
		//echo "<script>history.go(-1);</script>";
		return;
}


/* echo '是4m分销='.$is_shopgeneral.'<br>';
echo '渠道-厂家编号='.$adminuser_id.'<br>';
echo '是否是厂家总店='.$is_samelevel.'<br>';
echo '总店模板编号='.$general_template_id.'<br>';
echo '总店商家编号='.$general_customer_id.'<br>';
echo '是否本身就是厂家总店='.$owner_general.'<br>';
echo '渠道-代理商 总店='.$orgin_adminuser_id.'<br>';

echo '渠道-获取下级所有的代理商编号='.$getAllSubs.'<br>';
echo '获取下级所有的商家编号='.$getAllSubCustomers.'<br>'; */
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>用户权限编辑</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>

<style>
label input[type="radio"]{
		width: auto;
  		height: auto;
}
#help{
	display: inline;
    float: left;
    line-height: 37px;
    padding-left: 15px;
    padding-right: 15px;
    font-size: 14px;
    color: #646464;
    cursor: pointer;
}
#temp{
	display:none;
    position: fixed;
    top: 10%;
    left: 60%;
    width: 420px;
    height: 380px;
    background-color: rgba(79, 230, 75, 0.96);
    
    padding: 20px;
   
    margin-left: -210px;
    border-radius: 6px;
	z-index:1000;
}
#temp>.cont{
	 color: white;
	 font-size: 18px;
     line-height: 30px;
}
#temp_zhe{
	display:none;
	position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.37);
    top: 0;
    z-index: 999;
}
</style>
</head>

<body>   
	<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">厂家控制商家权限</a>
					<dd class="" id="help">帮助文档</dd> 
				</div>
			</div>
			<!--列表头部切换结束-->
					
		
			
        <!--权限管理代码开始-->
		<form action="" method="post" onsubmit="" class="form">
		
			<input name="C_id" type="hidden" value="" />
			<input name="user_id" type="hidden" value="" />
			<input name="types" type="hidden" value="1" />
			<div class="WSY_data">
				<div class="WSY_competence">
					
					<!--列表头部切换开始-->
					
					<div class="WSY_competence_header">

						<!-- <h3 id="h3">权限匹配<input id="s" onclick="$(this).attr(&#39;checked&#39;)?checkAll():uncheckAll()" type="checkbox"></h3> -->
						<div id="nav_list">
						

		                	<a class="white1" val = 1>控制商家添加产品权限</a>
		                	
							<a val = 2>控制商家修改产品权限</a>

							
							<div style="//display:none"><!-- 控制商家上传产品权限 -->
		                		<ul class="change_upload_pros whith1" style="border:none;float:left;">
									<?php 
								if(!empty($getAllSubCustomers)){
									//获取商家用户信息
									$query = "select id,name,adminuser_id from  customers where isvalid=true and id in (".$getAllSubCustomers.")";
									//echo $query;
									$SubCustomers_customer_name = '';	
									$SubCustomers_id = '';
									$SubCustomers_adminuser_id = '';
									$parent_adminuser_id = 0;		//商家渠道上级
									$result=_mysql_query($query);
									while($row=mysql_fetch_object($result)){
										$SubCustomers_id 				= $row->id;
										$SubCustomers_customer_name 	= $row->name;
										$SubCustomers_adminuser_id	    = $row->adminuser_id;
									
										//获取自己的渠道上级
										$parent_adminuser_id = $shop_4m->getMyparent_Adminuser_id($SubCustomers_id);
										
										//查询weixin_4m_control 商家上传产品资格
										$query2 = "select id,is_upload_pros from weixin_4m_control where isvalid=true and customer_id=$SubCustomers_id and adminuser_id=$SubCustomers_adminuser_id";
										//echo $query2;
										$is_upload_pros = 0;
										$control_id = 0;
										$result2=_mysql_query($query2);
										while($row2=mysql_fetch_object($result2)){
											$control_id 	= $row2->id;
											$is_upload_pros = $row2->is_upload_pros;
										}

										
									?>
										<dd>
											<input type="checkbox" name="links[]" data-id="<?php echo $control_id ;?>"  data-type="upload_pros" value="<?php echo $SubCustomers_id ;?>" <?php if(1 == $is_upload_pros) echo 'checked';?> class="link"  /><label><?php echo $SubCustomers_customer_name ;?></label>
										</dd>
									<?php 
									
									}
								}else{
									echo "<span style='color:red;'>暂无下级商家，请检查商家是否已成交</span>";
								}
									
									?>
								</ul>
							</div>

							<div style="display:none"><!-- 控制商家修改产品价格权限 --><!--此处更改为：控制商家修改产品所有权限-->
		                		<ul class="change_pros_price" style="border:none;float:left;">
									
								<?php 
								if(!empty($getAllSubCustomers)){
									//获取商家用户信息
									$query = "select id,name,adminuser_id from  customers where isvalid=true and id in (".$getAllSubCustomers.")";
									//echo $query;
									$SubCustomers_customer_name = '';	
									$SubCustomers_id = '';
									$SubCustomers_adminuser_id = '';
									$parent_adminuser_id = 0;		//商家渠道上级
									$result=_mysql_query($query);
									while($row=mysql_fetch_object($result)){
										$SubCustomers_id 				= $row->id;
										$SubCustomers_customer_name 	= $row->name;
										$SubCustomers_adminuser_id	    = $row->adminuser_id;
									
										//获取自己的渠道上级
										$parent_adminuser_id = $shop_4m->getMyparent_Adminuser_id($SubCustomers_id);
										
										//查询weixin_4m_control 商家修改产品价格资格
										$query2 = "select id,is_change_pros_price from weixin_4m_control where isvalid=true and customer_id=$SubCustomers_id and adminuser_id=$SubCustomers_adminuser_id";
										//echo $query2;
										$is_change_pros_price = 0;
										$control_id = 0;
										$result2=_mysql_query($query2);
										while($row2=mysql_fetch_object($result2)){
											$control_id 	= $row2->id;
											$is_change_pros_price = $row2->is_change_pros_price; //更改为控制商家修改产品所有权限
										}
										
									?>
										<dd>
											<input type="checkbox" name="links2[]" data-id="<?php echo $control_id ;?>" data-type="pros_price" value="<?php echo $SubCustomers_id ;?>" <?php if(1 == $is_change_pros_price) echo 'checked';?>  class="link"  /><label><?php echo $SubCustomers_customer_name ;?></label>
										</dd>
									<?php 
									
									}
								}else{
									echo "<span style='color:red;'>暂无下级商家，请检查商家是否已成交</span>";
								}
									?>
								
								</ul>
							</div>
							
						</div>

					</div>

				</div>
																	
				</div>
			<div class="WSY_text_input"><button type="button" class="WSY_button" onclick="comfirm()">提交</button><br class="WSY_clearfloat"></div>
			</div>
		<input type="hidden" name="choose_type" value="1"/>
		</form>
        <!--权限管理代码结束-->
	</div>
<!--弹窗-->
<div id="temp">
	<div class="cont">

	</div>

</div>
<div id="temp_zhe"></div>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript">

function comfirm(){
	
	var data_array = new Array();
	var choose_type = choose_type = $("input[name='choose_type']").val();	
	if(1 == choose_type){
		var _b = 'links[]';
	}else{
		var _b = 'links2[]';
	}		
	$("input[name='"+_b+"']").each(function(){
			var arr = new Array();
			var _this = $(this);
			var id = _this.data('id');
			var type = _this.data('type');
			var c_box_stu = _this.prop('checked');
			arr[0] =  id;				//id
			arr[1] =  type;				//类型
			arr[2] =  c_box_stu;		//用户修改的状态
			//console.log(arr);
			data_array.push(arr);		//数组拼接		
	});			
	console.log(data_array);
	data_array = JSON.stringify(data_array);  //数组转json
	
	$.ajax({
			url: "save.php?customer_id=<?php echo $customer_id_en ;?>&stu=save",
			data:{
				data_array:data_array,
				choose_type:choose_type
			},
			type: "POST",
			dataType:'json',
			async: true,     
			success:function(res){
				if(res.code == 10002){
					alert(res.msg);
				}else{
					alert(res.msg);
				}
				setTimeout(function(){
				
				history.go(0);
				
				},500);
				
				
			},
			error:function(er){
			
			}
	});

}


</script>
<script>

// ---------全选效果
	function checkAll() {
		var code_Values = document.all['links[]'];
		if (code_Values.length) {
			for (var i = 0; i < code_Values.length; i++) {
				code_Values[i].checked = true;
				console.log(code_Values[i].value);
			}
		} else {
			code_Values.checked = true;
		}
		
		var code_Values2 = document.all['links2[]'];
		if (code_Values2.length) {
			for (var i = 0; i < code_Values2.length; i++) {
				code_Values2[i].checked = true;
			}
		} else {
			code_Values2.checked = true;
		}
		
	}
	function uncheckAll() {
		var code_Values = document.all['links[]'];
		if (code_Values.length) {
			for (var i = 0; i < code_Values.length; i++) {
				code_Values[i].checked = false;
			}
		} else {
			code_Values.checked = false;
		}
		var code_Values2 = document.all['links2[]'];
		if (code_Values2.length) {
			for (var i = 0; i < code_Values2.length; i++) {
				code_Values2[i].checked = false;
			}
		} else {
			code_Values2.checked = false;
		}
	}
	
	//点击分类
	$(function(){
	
	$('#nav_list a').click(function(){
			console.log('a');
			$('#nav_list a').removeClass('white1');
			$(this).addClass('white1');
			var type = $(this).attr('val');
			if(1 == type){
				$('.change_pros_price').parent().hide();
				$('.change_upload_pros').parent().show();
				$("input[name='choose_type']").val(1);
			}else{
			
				$('.change_upload_pros').parent().hide();
				$('.change_pros_price').parent().show();
				$("input[name='choose_type']").val(2);
			}
		});
	});

$('#help').on('click', function(){
	
	float_text(new_config,content);
 
});

var new_config = {
	bg 			:'#81BA25',
	delay_time 	: 0
};
var content =  '1.关闭4M设置前，先把总店的产品分类属性删除，再在渠道关闭总店身份，最后关掉4M设置。否则会造成产品无法编辑和删除的问题。<br>';
	content += '';

	
	

function float_text(config,content){
	
	$('#temp_zhe').show();
	if(config.bg!=''){
		$('#temp').css('background-color',config.bg);
	}
	$('.cont').html(content);
	$('#temp').show();
	
	if(config.delay_time>0){
		
		setTimeout(function(){
			
				$('#temp').hide();
				$('#temp_zhe').hide();
				
		},config.delay_time); 
	}
	$(function(){
		var temp_zhe_stu = $('#temp_zhe').css('display');
		console.log(temp_zhe_stu);
		if(temp_zhe_stu != 'none'){
			$('#temp,#temp_zhe').click(function(){
				$('#temp').hide();
				$('#temp_zhe').hide();
			});
		}
	});
	
}


</script>
</body>
</html>
