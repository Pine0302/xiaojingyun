<?php  
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");
$head = 2;
$op = '';
if(!empty($_GET['op'])){
	$op = $_GET['op'];
	switch($op){
		case 'del':
			$tid = -1;
			if(!empty($_GET['tid'])){
				$tid = $_GET['tid'];
			}
			$sql = "update pcshop_merchants_settled_type set isvalid=false where id=".$tid;
			_mysql_query($sql) or die(' DEL SQL failed: ' . mysql_error());
		break;
	}
}
$deal_id = -1;
$deal_name = '';
if(!empty($_GET['deal_id'])){
	$deal_id = $_GET['deal_id'];
	$query = "select name from pcshop_merchants_settled_type where isvalid=true and id=".$deal_id;
	$result = _mysql_query($query) or die(' query failed: ' . mysql_error());
	while($row = mysql_fetch_object($result)){
		$deal_name = $row->name;
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>入驻分类</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Product/product.css">

<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>

<style>
/*产品分类*/
.classificationbox{overflow:hidden;margin-bottom:20px;}
.content{width:460px;}
.classify{width:600px;}
#classify{width:330px;}
.conterbox000{overflow:hidden;}
.content,.classify,.show{background:#fff;border:1px solid #c6c6c6;margin-top:30px;margin-left:25px;float:left;position:relative;padding-bottom: 20px;}
.conterimgbox{height:36px;background:#e7e7e7;margin-top:20px;display:block;position:relative;margin-left:10px;margin-right:10px;}
.icon-text{display:block;float:left;line-height:36px;font-size:16px;margin-left:6px;background:url(../images/menu_icon/icon1112/text-icon.png) no-repeat left center;
	padding-left:20px;}
.caozuo_right{display:block;float:right;overflow:hidden;}
.caozuo_right a,.WSY_botton_box{display:block;float:left;margin-right:10px;margin-top:10px;}
.caozuo_right a img{display:block;width:18px;height:18px;}
.caozuo_right .WSY_botton_box{margin-top:10px;}
.right_10px{margin-right:35px;}
.caozuo_right .conter_load img{width:18px;height:18px;}
.conterimgbox000{background:#fff;margin-top:0;border:1px solid #e7e7e7;}
.conterimgbox000 .icon-text{margin-left:30px;}
.conterimgbox000 .check-on{left:215px;} 
.conterimgbox000 .compile{left:286px;}
.conterimgbox000 .conter_delete{left:320px;}
.conterimgbox000 .conter_uploading{left:350px;}
.conterimgbox000 .conter_download{left:380px;}
.classify_text{font-size:16px;display:block;margin-top:-10px;margin-left:30px;background:#fbfbfb;width:110px;text-align:center;}
.classify_name{float:left;display:block;margin-top:28px;margin-left:20px;font-size:14px;}
.classify_name input{width:212px;height:24px;border:solid 1px #dadada;margin-left:12px;border-radius:2px;}
.classify_span select{width:212px;height:24px;padding:3px;border-radius:3px;display:inline-block;border:solid 1px #dadada;}
.classify_span{float:left;margin-left:18px;margin-top:10px;font-size:14px;}  
.classify_span input{margin-left:-10px;}
.classify_content{width:290px;height:250px;border:solid 1px #d0d0d0;position:absolute;left:14px;top:117px;}
.white{margin-left:10px;}
.classify_content p{width:240px;font-size:14px;margin-top:16px;margin-left:10px;}
.classify_content_img{border:0;margin-left:5px;margin-top:10px;display:block;}
.classify_content-input{margin-top:7px;}

.show_text{font-size:16px;background:#fff;margin-left:30px;margin-top:-10px;display:block;width:140px;}
.show_img{overflow:hidden;margin-left:26px;margin-top:24px;}
.show_img a{float:left;margin-right:15px;margin-bottom:30px;}
.show_button{display:block;text-align:center;}
.show_button2{width:110px;height:30px;background:#07a7e1;border:1px solid #056f9f;border-radius:3px;cursor:pointer;font-size:16px;font-family:"微软雅黑";color:#fff;}
.list_right{float:right;margin-left:20px;width:270px;margin-top:-20px;height:420px;}
.list_right form{padding:10px;background:f7f7f7;zoom:1;}
.list_right span{font-size:16px;background:#fff;margin-top:-48px;display:block;margin-left:10px;text-align:center;width:90px;}
/*.list_right .opt_item #pro-list-type2 {height:135px;}*/
.list_right .opt_item #pro-list-type2 li{float:left;width:100px;height:140px;overflow:hidden;padding:25px 0 15px 15px;}
.list_right .opt_item #pro-list-type2 li .item{position:relative;width:100px;height:135px;}
.list_right .opt_item #pro-list-type2 li .item .img{position:absolute;width:100px;height:135px;z-index:1;}
.list_right .opt_item #pro-list-type2 li .item .filter{position:absolute;width:100px;height:135px;z-index:2;}
.list_right .opt_item #pro-list-type2 li .item .bg{position:absolute;width:100px;height:135px;z-index:3;}
.btn_green{  background: #07a7e1;border: 1px solid #056f9f;width: 110px;height: 30px;font-size: 16px;color: #fff;font-family: "微软雅黑";border-radius: 3px;cursor: pointer;margin-top:5px;margin-left:16px;display:inline-block;}
.list_right .opt_item #pro-list-type2 li .item_on .bg{background:url(../images/selected-icon.png) no-repeat center center;}
.list_right .opt_item #pro-list-type2 li .item_on .filter{background:#000; opacity:0.6;}
.opactiy{width:278px;height:20px;background:#000;opacity:0.5;display:block;margin-top:-22px;line-height:20px;text-align:center;color:#fff;}
/*产品属性*/
.first{left:323px;}
.two{left:355px;}
.conterbox{border:1px solid #e7e7e7;background:#fff;margin-left:10px;margin-right:10px;overflow:hidden;padding-bottom:10px;}
.conterbox_text{width:190px;border:1px solid #e7e7e7;height:22px;line-height:22px;float:left;margin-left:20px;margin-top:10px;font-size:12px;color:#7f7f7f;cursor:pointer;}
.conterbox_text:hover{background:#e7e7e7;}
.conterbox_text:hover .conterbox_text_img img{display:block;}
.before{border-width:12px;border-style:solid;border-color:transparent transparent transparent #e7e7e7;float:left;}
#conterbox_text{background:#e7e7e7;z-index:99; }
.conterbox_text_img{z-index:100;display:block;width:14px;height:14px;float:right;margin-top:4px;margin-right:8px;}
.conterbox_text_img img{width:14px;height:14px;display:none;}
#classify_name{margin-top:10px;}
.classify_name img{width:16px;height:16px;margin-left:5px;display:inline-block;}
.classify_name_input{overflow:hidden;}
.classify_name_input input{border:1px solid #e7e7e7;height:24px;display:block;width:100px;}
.classify_name_input img{width:16px;height:16px;float:left;}
.classify .second{margin-top:10px;margin-left:82px;}
.position{bottom:186px;left:50px;}

.classify_name_text input{width:150px;}
.relation_type{margin-left: 9px;}
</style>
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content">
       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
			<?php
				include($_SERVER['DOCUMENT_ROOT']."/mshop/admin/PcShop/merchants_settled/basic_head.php"); 
				?>
		<div class="WSY_data">
		 
			<div class="classificationbox">
			<div class="content" >			
		<?php 
					//查询父级属性
					$query="select id,name,parent_id from pcshop_merchants_settled_type where isvalid=true and parent_id=-1 and customer_id=".$customer_id." and level=1";
					//echo $query;
					$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
					   $parent_id = $row->id;
					   $pname = $row->name;
				?>
        	
                   <div class="conterimgbox parent_props" data-id="<?php echo $parent_id;?>">
                         <a href="#" title="<?php echo $p_name;?>" class="icon-text"><?php echo $pname;?></a>
                         <div class="caozuo_right">
							 <a href="settled_type.php?customer_id=<?php echo $customer_id_en; ?>&deal_id=<?php echo $parent_id; ?>" class="compile first" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"/> </a>
							 
							 <a href="settled_type.php?customer_id=<?php echo $customer_id_en; ?>&op=del&tid=<?php echo $parent_id; ?>"
								onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};" class="conter_delete two" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"/> </a>

                         <a href="javascript:expandHide('<?php echo $parent_id;?>')" class="conter_load" title="展开">
							<img id="img_expand_<?php echo $parent_id;?>" data-status="<?php echo $productpro_id != $parent_id ? "hide" : "show";?>" src="../../Common/images/Product/icon1112/dot-jiantou_<?php echo $productpro_id != $parent_id ? "x" : "s";?>.png"> </a>
                         </div>
               	  </div>
			   <?php 
						//查询子属性
						 $query2 = "select id,name from pcshop_merchants_settled_type where isvalid=true and parent_id=".$parent_id;
						 $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
						 $crows = mysql_num_rows($result2);
						 if($crows >0){
					   ?>
                  <div class="conterbox child_props" id="childprops_<?php echo $parent_id;?>" style="<?php echo $productpro_id != $parent_id ? "display:none" : "";?>">
					<?php  while ($row2 = mysql_fetch_object($result2)) {
							 $p_id=$row2->id;
							 $p_name =$row2->name;
					?>
                  		<div class="conterbox_text" data-child-id="<?php echo $p_id;?>">
                        		<div class="before"></div><div style="width:140px;height:22px;overflow:hidden;float:left;"><?php echo $p_name;?></div>
								<a class="conterbox_text_img" href="settled_type.php?customer_id=<?php echo $customer_id_en; ?>&op=del&tid=<?php echo $p_id; ?>" onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};">
								<img src="../../../common/images_V6.0/operating_icon/icon04.png"/>
								</a>
                        </div>
                        <?php } ?>      
                  </div>
						
						 <?php } 
			  } ?>
            </div>
				<div class="content" >
					<form id="frm_producttype" class="" action="save_settled_type.php?customer_id=<?php echo $customer_id_en; ?>" method="post" >
						<div class="list_left">
						 <div class="classify" id="classify">
							<p class="classify_text">添加商家分类</p>
							<p class="classify_name">分类名称:<input type="text" name="name" value="<?php echo $deal_name; ?>" id="name"></p>
								<?php if($deal_id<0){ ?>
									<span class="classify_span">隶属关系：
										<select name="parent_id" id="parent_id">
											<option value="-1">顶级</option>
											<?php
											  $query = "select id,name from pcshop_merchants_settled_type where isvalid=true and parent_id = -1 and customer_id=".$customer_id." and level=1";
											  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
											   while ($row = mysql_fetch_object($result)) {
												   $pt_id = $row->id;
												   $pt_name = $row->name;
												   
											 ?>
											<option value="<?php echo $pt_id; ?>" <?php if($producttype_parent_id==$pt_id){?>selected <?php } ?>><?php echo $pt_name; ?></option>
											   <?php } ?>
										</select>
								   </span>
								<?php } ?>
							   <?php 
								if($deal_id>0){
								//修改
									 $query2= "select id,name from pcshop_merchants_settled_type where isvalid=true and parent_id=".$deal_id;
									 $result2 = _mysql_query($query2) or die('Query failed: ' . mysql_error());
									  $index = 0;
									 while ($row2 = mysql_fetch_object($result2)) {
										 $p_id=$row2->id;
										 $p_name =$row2->name;
										 if($index == 0){
								?>
								<p class="classify_name classify_name_text" id="classify_name">二级列表:<input type="text" name="PropertyList[]" value="<?php echo $p_name; ?>">
								<input type="hidden" name="LId[]" value="<?php echo $p_id; ?>">
									<img src="../../../common/images_V6.0/operating_icon/icon04.png" class="delProps"/>
								</p>
										 <?php }else{ ?>
								<p class="classify_name second classify_name_text">
									<input type="hidden" name="LId[]" value="<?php echo $p_id; ?>">
									<input type="text" name="PropertyList[]" value="<?php echo $p_name; ?>">
									<img src="../../../common/images_V6.0/operating_icon/icon04.png" class="delProps"/>
								</p>
										 <?php }
										 $index++;
									 }?>
								
								<p class="classify_name second classify_name_text">
									<input type="hidden" name="LId[]" value="">
									<input type="text" name="PropertyList[]" value="">
									<img src="../../../common/images_V6.0/operating_icon/icon04.png" class="delProps"/>
									
									<img src="../../../common/images_V6.0/operating_icon/icon45.png" class="addProps"/>
								</p>
								<?php }else{
							  ?>
								<p class="classify_name classify_name_text" id="classify_name">二级列表:<input type="text" name="PropertyList[]" value="">
									<input type="hidden" name="LId[]" value="">
									<img src="../../../common/images_V6.0/operating_icon/icon04.png" class="delProps"/>
								</p>
							
								<p class="classify_name second classify_name_text">
									<input type="hidden" name="LId[]" value="">
									<input type="text" name="PropertyList[]" value="">
									<img src="../../../common/images_V6.0/operating_icon/icon04.png" class="delProps"/>
									
									<img src="../../../common/images_V6.0/operating_icon/icon45.png" class="addProps"/>
								</p>
								<?php }?>
							<input type=hidden name="deal_id" value="<?php echo $deal_id; ?>">
							<div class="classify_content-input">
									<button type="button" class="classify_input" id="saveProtype" style="margin-top: 10px;margin-left: 50px;">保存分类</button>
									<button type="button" class="classify_input2" id="returnBack">返回</button>
							</div>
						</div>							
						</div>
					</form>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
customer_id_en = '<?php echo $customer_id_en;?>';
page_index = 1;
var search_type_id = null;
</script>
<!--内容框架结束-->
<script type="text/javascript">

	//子类展开/隐藏的方法
	function expandHide(propId){
		var ctype = $("#childprops_"+propId);
		var img = $("#img_expand_"+propId);
		if(ctype.length > 0){
			//ctype.each(function(i,n){
				if(img.data("status") == "hide"){
					ctype.show();
				}else{
					ctype.hide();
				}
			//});
		}
		
		if(img.data("status") == "hide"){
			img.attr("src","../../Common/images/Product/icon1112/dot-jiantou_s.png");
			img.data("status","show");
		}else{
			img.data("status","hide");
			img.attr("src","../../Common/images/Product/icon1112/dot-jiantou_x.png");
		}
	}
	
	function isRepeat(arr){		//验证是否有重复值
		var hash = {};
		for(var i in arr) {
			if(arr[i]=='')	continue;
			if(hash[arr[i]])	return true;
			hash[arr[i]] = true;
		}
		return false;
	}
	
	function isValue(arr){		//验证是否有不为空的值
		for(var i in arr) {
			if(arr[i]==''){
				continue;
			}else{
				return true;
			}
		}
		return false;
	}
	
	function subPro(){
	   var name = $("#name").val();
	   var pattern=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;
	   var class_pattern = /^\s+$/g;
	   if($.trim(name)==""){
		  alert('请输入属性名称');
		  return;
	   }else if(pattern.test(name)){
			alert('提示信息：您输入的数据含有非法字符！');
			name="";
			return;
		}
	   var pros = $("input[name='PropertyList[]']");
	   var ids = $("input[name='LId[]']");
	   var check_repeat = false;
	   var check_value = false;
	   var len = pros.length;
	   var pros_val = "";
	   var ids_val = "";

	   var prop_arr = new Array();
	   pros.each(function(i,n){
		   var prop = n.value;
		   prop_arr[i] = prop;
		   if(prop!=""){
			    pros_val += prop+",";
				var id = ids.eq(i).val();
				id = id == "" ? "-1" : id;
				ids_val += id+",";
		   }
	   });
	   // console.log(prop_arr);
	   check_repeat = isRepeat(prop_arr);
	   if(check_repeat){
		   alert('提示信息：属性值不能重复！');
		   return;
	   }
	   check_value = isValue(prop_arr);
	   if(!check_value){
		   alert('提示信息：至少有一个属性值！');
		   return;
	   }
	   //alert("pros_val : "+pros_val);
	   //alert("ids_val : "+ids_val);
	   $("#propStr").val(pros_val);
	   $("#idStr").val(ids_val);
	   
	   //关联分类
	   var relation_type_id = $('.relation_type').val();
	   var keyid = $('#keyid').val();
	   $.ajax({
		   url: 'props_ajax.php?customer_id=<?php echo $customer_id_en;?>',
		   data:{name:name,relation_type_id:relation_type_id,keyid:keyid},
		   dataType: 'json',
		   type: 'post',
		   success:function(res){
			   if(res.status == 1){
				   $("#frm_pro").submit();
			   }else{
				   alert('提示信息：该分类下有相同的属性名！');
			   }
		   }
	   })
	}
	$(function(){
		//删除属性
		$(".delProps").click(function(){
			var props = $(".delProps");
			var props_len = props.length;
			if(props_len == 1){
				alert("提示信息：只剩一个了，再删就没了！");
				return;
			}
			$(this).parent("p").remove();
		});
		$(".addProps").click(function(){
			var newprop = $(this).parent("p").clone(true);
			//newprop.find(".addProps").remove();
			newprop.find("input[type='text']").val("");
			newprop.insertAfter($(this).parent("p"));
			$(this).remove();
			
		});
		//修改按钮
		$("#saveProtype").click(function(){
			var pattern=/[`~!@#$%^&*()+<>?:"{},.\/;'[\]]/im;
			var name = $("#name").val();
			if(name == ""){
				alert("类型名称必填！");
				return;
			}else if(pattern.test(name)){
                alert('提示信息：您输入的数据含有非法字符！');
                name="";
				return;
            }
			$("#parent_id").removeAttr("disabled");
			$("#frm_producttype").submit();
		});
		//返回按钮
		$("#returnBack").click(function(){
			location.href='documentation.php?customer_id=<?php echo $customer_id_en;?>';
		});
		$('#parent_id').change(function(){
			var val = $(this).val();
			if(val>0){
				$('#classify_name').hide();
				$('.second>input').hide();
				$('.second').hide();
			}else{
				$('#classify_name').show();
				$('.second>input').show();
				$('.second').show();
			}
		});
	});
</script>
</body>
</html>

<?php ?>