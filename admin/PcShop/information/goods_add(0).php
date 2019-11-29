<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=0;//头部文件  0入驻文档

//分类链接
$typearr=[];
$query="select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $pt_id 		= $row->id;
   $pt_name 	= $row->name;
   $typearr[] 	= $pt_id."_".$pt_name;
}


$left_img = '';//左侧广告图片
$right_img = '';//右侧轮播广告图
$pro_id = '';//右侧轮播广告图关联产品ID
$query = "select left_img,right_img,pro_id from pcshop_merchants_settled_img where isvalid=true and customer_id=".$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $left_img = $row->left_img;
    $right_img = $row->right_img;	
    $pro_id = $row->pro_id;	
}
$right_img = explode('|',$right_img);
$pro_id = explode('|',$pro_id);
$pro_str = array();
foreach($pro_id as $k => $v){
	$pro_str[] = explode('_',$pro_id[$k]);
}

require_once('shoproom.php');
$pcshop = new Pcshop();
$customer_id_en = $pcshop->customer_id_en;

//上传图片
$save_goods_url = $_SERVER['DOCUMENT_ROOT'].'/weixinpl/back_newshops/PcShop/information/interfaceroom.php?action=save_goods';

?>  
<!doctype html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Mode/supplier/set.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<title>信息推广首页</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style>
.WSY_button1 {
	cursor: pointer;
    width: 50px;
    height: 25px;
    border: none;
    font-size: 13px;
    color: #f9fdff;
    border-radius: 2px;
    float: left;
    text-align: center;
}
#goods_descc{
	width: 420px;
    height: 125px;
}

.WSY_text_input #save_c{
	margin-right: 78% !important;
	margin-top: 30px !important;
}
</style>
</head>
<body>
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<?php
			// include("../../../../weixinpl/back_newshops/PcShop/merchants_settled/basic_head.php"); 
			include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/information/basic_head.php");
			?>
			<!--列表头部切换结束-->
			<form action="<?php echo $save_goods_url;?>&customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="upform" name="upform">
				<div class="WSY_remind_main">
					
					<dl class="WSY_member">			
						<div>
							<dt>产品图片：</dt>						
							<dd class="spa">
								<img src="<?php echo $left_img; ?>" id="img_v1" style="width:220px;height:200px;" /><br/>
								<input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18;margin-top: 5px;margin-bottom: 5px;" size="17" name="upfile_goods" id="upfile" class="upfile" type="file" value=""> (图片尺寸：宽220*高200)
								<input type=hidden value="<?php echo $left_img; ?>" name="imgurl1" id="imgurl1" /> 
							</dd>	
							
						</div>
					</dl>
					
					<dl class="WSY_member">			
						<div>
							<dt>产品标题：</dt>		
							<!-- <input type=botton class="WSY_button1 WSY-skin-bg" onclick="add_rotation()"  style="width:82px;height:30px;" value='增加轮播图'> -->
							<dd><input type="text" name="goods_title" value=""></dd>		
						</div>
					</dl>

					<dl class="WSY_member">			
						<div>
							<dt>产品描述：</dt>		
							<!-- <input type=botton class="WSY_button1 WSY-skin-bg" onclick="add_rotation()"  style="width:82px;height:30px;" value='增加轮播图'> -->
							<!-- <dd><input type="text" name="goods_desc" value=""></dd> -->
							<dd><textarea id="goods_descc"></textarea></dd>
						</div>
					</dl>
					
					
					<input type=hidden name="img_num" id="img_num" value="">
					<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id; ?>">
					<div class="WSY_text_input" ><button style="margin-top: 200px;" id="save_c" class="WSY_button" onclick=" return subBase();">提交保存</button><br class="WSY_clearfloat"></div>
				</div>
			</form>
		</div>
	</div>
<?php mysql_close($link);?>	
<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<script>
var deal = '';

 function subBase(){
	var imgurl_num = $('.spa').length;

	$('#img_num').val(imgurl_num);
	var title = $('.title').val();
	var cke_editor1 = $('.cke_editor1').val();
	
	if(title==""){
		alert("标题不能为空！");
		return false;
	}	
	if(cke_editor1==""){
		alert("内容不能为空！");
		return false;
	}
	
	var id = -1;
	var pass = 1;
	$('.img_div').each(function(){
		id = $(this).attr('id');
		if($('#upfile'+id).val()!='' || $('#imgurl'+id).val()!='' ){
			if($('#detail_id'+id).val()>0){}else{alert('轮播图片'+(--id)+'请选择关联产品');pass = 0;return false;}
		}
	});
	if(pass == 0){
		return false;
	}
	document.getElementById("upform").submit();
 }
</script>

<script>


$(function(){
	//setselect();
	function getObjectURL(file) {
		var url = null ; 
		if (window.createObjectURL!=undefined) {
		url = window.createObjectURL(file) ;
		} else if (window.URL!=undefined) {
		url = window.URL.createObjectURL(file) ;
		} else if (window.webkitURL!=undefined) {
		url = window.webkitURL.createObjectURL(file) ;
			}
		return url ;
		}
		$(".upfile").change(function(){
		var objUrl;
		if(navigator.userAgent.indexOf("MSIE")>0){
		objUrl = this.value;
		}else
		objUrl = getObjectURL(this.files[0]);
		var src_id = $(this).parent().find('img').attr('id');
		$("#"+src_id).attr("src",objUrl);
	}) ;
})

function add_rotation(){
	var rotation_num = $('.spa').length;
	if(rotation_num>=6){
		alert('轮播图最多可设置5张！');
		return;
	}
	img_id = rotation_num;
	rotation_num++;
	result = '';
	
	
	result += '<dl class="WSY_member img_div"  id="'+rotation_num+'"  style="float:left;width:500px;"><div><dt>轮播图片'+img_id+'</dt><div style="display: block;position: absolute;margin-left: 110px;"><select id="foreign_id'+rotation_num+'" name="foreign_id'+rotation_num+'" onchange="getproduct(this.options[this.options.selectedIndex].value,1,'+rotation_num+')"><optgroup label="---------------产品分类---------------"></optgroup>';
	<?php 
		for($n=0;$n<count($typearr);$n++){
			$typestr=explode("_",$typearr[$n]);
	?>	  
		result +='<option value="<?php echo $typestr[0];?>_1"><?php echo $typestr[1];?></option>';
	<?php 	
		}
	?>
	result += '</select></div><div style="display: block;position: absolute;margin-left: 110px;margin-top:20px;"><select id="detail_id'+rotation_num+'" name="detail_id'+rotation_num+'" style="width:160px;margin-top:5px;float:left;"></select></div><dd class="spa"  style="margin-top: 50px;"><img src=""  id="img_v'+rotation_num+'" style="width:208px;height:50px;" /><br/><input style="width:208;border:1 solid #9a9999; font-size:9pt; background-color:#ffffff; height:18;margin-top: 5px;margin-bottom: 5px;width: 160px;" size="17" name="upfile'+rotation_num+'" id="upfile'+rotation_num+'" type=file value=""><input type=botton style="float:none;margin-left:10px;" class="WSY_button1 WSY-skin-bg" onclick="delect_rotation('+rotation_num+')" value="删除"><br/>(图片尺寸：宽831*高200)	<input type=hidden value="" name="imgurl'+rotation_num+'" id="imgurl'+rotation_num+'" /> </dd>	</div></dl>';
	$('#add_rotation').append(result);
	
		function getObjectURL(file) {
		var url = null ; 
		if (window.createObjectURL!=undefined) {
		url = window.createObjectURL(file) ;
		} else if (window.URL!=undefined) {
		url = window.URL.createObjectURL(file) ;
		} else if (window.webkitURL!=undefined) {
		url = window.webkitURL.createObjectURL(file) ;
			}
		return url ;
		}
	
	$("#upfile"+rotation_num).change(function(){
		var objUrl;
		if(navigator.userAgent.indexOf("MSIE")>0){
		objUrl = this.value;
		}else
		objUrl = getObjectURL(this.files[0]);
		var src_id = $(this).parent().find('img').attr('id');
		$("#"+src_id).attr("src",objUrl);
	}) ;
		
}
function delect_rotation(obj){
	$('#'+obj).remove();
	$('.img_div').each(function(){		
		var id = $(this).attr('id');
		if(id>obj){
			var num1 = id-1;
			var num2 = id-2;
			$(this).children().find('dt').html('轮播图片'+num2);
			$(this).children().find('dd>img').attr('id','img_v'+num1);
			$(this).children().find('dd>.upfile').attr('id','upfile'+num1);
			$(this).children().find('dd>.upfile').attr('name','upfile'+num1);
			$(this).children().find('dd>.WSY_button1').attr('onclick','delect_rotation('+num1+')');
			$(this).attr('id',num1);
		}
	});
}

// function setselect(){
	
	// var str_array = new Array();
	// var i = 0;
	// var n = 2;
	// for(i=0;i<=4;i++){
		// str_array['foreign_id'+n] = '<?php echo $pro_str[0][0]?>'
	// }
	// var foreign_id2='<?php echo $pro_str[0][0]?>';
	// var foreign_id3='<?php echo $pro_str[1][0]?>';
	// var foreign_id4='<?php echo $pro_str[2][0]?>';
	// var foreign_id5='<?php echo $pro_str[3][0]?>';
	// var foreign_id6='<?php echo $pro_str[4][0]?>';

	// var detail_id2='<?php echo $pro_str[0][1]?>';
	// var detail_id3='<?php echo $pro_str[1][1]?>';
	// var detail_id4='<?php echo $pro_str[2][1]?>';
	// var detail_id5='<?php echo $pro_str[3][1]?>';
	// var detail_id6='<?php echo $pro_str[4][1]?>';

	// var sobj2= document.getElementById("foreign_id2");
	// var options2 = sobj2.options;
	// var sobj3= document.getElementById("foreign_id3");
	// var options3 = sobj3.options;
	// var sobj4= document.getElementById("foreign_id4");
	// var options4 = sobj4.options;
	// var sobj5= document.getElementById("foreign_id5");
	// var options5 = sobj5.options;
	// var sobj6= document.getElementById("foreign_id6");
	// var options6 = sobj6.options;
	
	// for(var j=0;j<options2.length;j++){
		// document.getElementById("detail_id2").style.display="block";
		// var ov = options2[j].value;
		// var ovlen = ov.length;
		// var sel_type = 1;
		// var ov_id= -1;
		// var ovtype = 1;
		// if(ov.indexOf('_')!=-1){
		   // var ovarr = ov.split('_');
		   // ov = ovarr[0];
		   // ovtype = ovarr[1];
	
		// }
		// if(ov==foreign_id2){
			// if(type_linktype1==1){
				// var dd =options1[j].selected;
				// options1[j].selected ="selected";
				// if(foreign_id1>0){
					// //产品分类才显示出 选择产品，图文不需要
					// document.getElementById("pro_select1").style.display="block";
				// }
				// if(detail_id1>0){
					// console.log(foreign_id1);
					// changeProductType21(foreign_id1,detail_id1); 
				// }else{
					// changeProductType21(foreign_id1,-1); 
				// }
			// }else{
			  // options1[j].selected ="selected";
			 
			// }
			// break;
		// }	
	// }
	// for(var j=0;j<options2.length;j++){
		// document.getElementById("pro_select2").style.display="block";
		// var ov = options2[j].value;
		// var ovlen = ov.length;
		// var sel_type = 1;
		// var ov_id= -1;
		// var ovtype = 1;
		// if(ov.indexOf('_')!=-1){
		   // var ovarr = ov.split('_');
		   // ov = ovarr[0];
		   // ovtype = ovarr[1];
	
		// }
		// if(ov==foreign_id2 && ovtype==type_linktype2){
			// if(type_linktype2==1){
				// var dd =options2[j].selected;
				// options2[j].selected ="selected";
				// if(foreign_id2>0){
					// //产品分类才显示出 选择产品，图文不需要
					// document.getElementById("pro_select2").style.display="block";
				// }
				// if(detail_id2>0){
					// console.log(foreign_id2);
					// changeProductType22(foreign_id2,detail_id2); 
				// }else{
					// changeProductType22(foreign_id,-1); 
				// }
			// }else{
			  // options2[j].selected ="selected";
			 
			// }
			// break;
		// }	
	// }
	// for(var j=0;j<options3.length;j++){
		// document.getElementById("pro_select3").style.display="block";
		// var ov = options3[j].value;
		// var ovlen = ov.length;
		// var sel_type = 1;
		// var ov_id= -1;
		// var ovtype = 1;
		// if(ov.indexOf('_')!=-1){
		   // var ovarr = ov.split('_');
		   // ov = ovarr[0];
		   // ovtype = ovarr[1];
	
		// }
		// if(ov==foreign_id3 && ovtype==type_linktype3){
			// if(type_linktype3==1){
				// var dd =options3[j].selected;
				// options3[j].selected ="selected";
				// if(foreign_id3>0){
					// //产品分类才显示出 选择产品，图文不需要
					// document.getElementById("pro_select1").style.display="block";
				// }
				// if(detail_id3>0){
					// console.log(foreign_id3);
					// changeProductType23(foreign_id3,detail_id3); 
				// }else{
					// changeProductType23(foreign_id,-1); 
				// }
			// }else{
			  // options3[j].selected ="selected";
			 
			// }
			// break;
		// }	
	// }for(var j=0;j<options4.length;j++){
		// document.getElementById("pro_select1").style.display="block";
		// var ov = options4[j].value;
		// var ovlen = ov.length;
		// var sel_type = 1;
		// var ov_id= -1;
		// var ovtype = 1;
		// if(ov.indexOf('_')!=-1){
		   // var ovarr = ov.split('_');
		   // ov = ovarr[0];
		   // ovtype = ovarr[1];
	
		// }
		// if(ov==foreign_id4 && ovtype==type_linktype4){
			// if(type_linktype4==1){
				// var dd =options4[j].selected;
				// options4[j].selected ="selected";
				// if(foreign_id4>0){
					// //产品分类才显示出 选择产品，图文不需要
					// document.getElementById("pro_select4").style.display="block";
				// }
				// if(detail_id4>0){
					// console.log(foreign_id4);
					// changeProductType24(foreign_id4,detail_id4); 
				// }else{
					// changeProductType24(foreign_id4,-1); 
				// }
			// }else{
			  // options4[j].selected ="selected";
			 
			// }
			// break;
		// }	
	// }for(var j=0;j<options5.length;j++){
		// document.getElementById("pro_select5").style.display="block";
		// var ov = options5[j].value;
		// var ovlen = ov.length;
		// var sel_type = 1;
		// var ov_id= -1;
		// var ovtype = 1;
		// if(ov.indexOf('_')!=-1){
		   // var ovarr = ov.split('_');
		   // ov = ovarr[0];
		   // ovtype = ovarr[1];
	
		// }
		// if(ov==foreign_id6){
			// if(type_linktype5==1){
				// var dd =options5[j].selected;
				// options5[j].selected ="selected";
				// if(foreign_id5>0){
					// //产品分类才显示出 选择产品，图文不需要
					// document.getElementById("pro_select5").style.display="block";
				// }
				// if(detail_id1>0){
					// console.log(foreign_id5);
					// changeProductType25(foreign_id5,detail_id5); 
				// }else{
					// changeProductType25(foreign_id5,-1); 
				// }
			// }else{
			  // options5[j].selected ="selected";
			 
			// }
			// break;
		// }	
	// }
// }
function getproduct(typeid,num,obj){
	deal = obj;
	var typearr= new Array(); 
	typearr=typeid.split("_");	
	if(typearr[1]==1){			
		url='get_product_list.php?callback=jsonpCallback_get_product_list&type_id='+typearr[0]+'&num='+num;
		 $.jsonp({
			url:url,
			callbackParameter: 'jsonpCallback_get_product_list'
		});		
		$("#pro_select1").css("display","block");
	}
	
}
function jsonpCallback_get_product_list(results){
	var len = results.length;
	var sel_pro = document.getElementById("detail_id"+deal);
	if(results[2].num==1){
		var new_option = new Option("---请选择一个产品---",-1);
		sel_pro.options.length=0;
		sel_pro.options.add(new_option);
	}
	for(i=2;i<len;i++){
		var pid = results[i].pid;
		var pname = results[i].pname;
		if(results[2].num==1){
			var new_option = new Option(pname,pid);
			sel_pro.options.add(new_option);
		}
		if(pid=="detail_id"+deal){
			new_option.selected=true;
		}
	}
}


</script>

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>