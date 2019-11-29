<?php  
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");
$head = 1;
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
<title>分类管理</title>
<link rel="stylesheet" type="text/css"
	href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css"
	href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="product.css">

<script type="text/javascript"
	src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="category_shop.js"></script>

<style>
/*产品分类*/
.classificationbox {
	overflow: hidden;
	margin-bottom: 20px;
}

.content {
	width: 460px;
}

.classify {
	width: 600px;
}

#classify {
	width: 330px;
}

.conterbox000 {
	overflow: hidden;
}

.content,.classify,.show {
	background: #fff;
	border: 1px solid #c6c6c6;
	margin-top: 30px;
	margin-left: 25px;
	float: left;
	position: relative;
	padding-bottom: 20px;
}

.conterimgbox {
	height: 36px;
	background: #e7e7e7;
	margin-top: 20px;
	display: block;
	position: relative;
	margin-left: 10px;
	margin-right: 10px;
}

.icon-text {
	display: block;
	float: left;
	line-height: 36px;
	font-size: 16px;
	background: url(../images/menu_icon/icon1112/text-icon.png) no-repeat
		left center;
	margin-left: 20px;
	width:200px;
}

.caozuo_right {
	display: block;
	float: right;
	overflow: hidden;
}

.caozuo_right a,.WSY_botton_box {
	display: block;
	float: left;
	margin-right: 10px;
	margin-top: 10px;
}

.caozuo_right a img {
	display: block;
	width: 18px;
	height: 18px;
}

.caozuo_right .WSY_botton_box {
	margin-top: 10px;
}

.right_10px {
	margin-right: 35px;
}

.caozuo_right .conter_load img {
	width: 18px;
	height: 18px;
}

.conterimgbox000 {
	background: #fff;
	margin-top: 0;
	border: 1px solid #e7e7e7;
}

.conterimgbox000 .icon-text {
	margin-left: 30px;
}

.conterimgbox000 .check-on {
	left: 215px;
}

.conterimgbox000 .compile {
	left: 286px;
}

.conterimgbox000 .conter_delete {
	left: 320px;
}

.conterimgbox000 .conter_uploading {
	left: 350px;
}

.conterimgbox000 .conter_download {
	left: 380px;
}

.classify_text {
	font-size: 16px;
	display: block;
	margin-top: -10px;
	margin-left: 30px;
	background: #fbfbfb;
	width: 110px;
	text-align: center;
}

.classify_name {
	float: left;
	display: block;
	margin-top: 28px;
	margin-left: 20px;
	font-size: 14px;
}

.classify_name input {
	width: 212px;
	height: 24px;
	border: solid 1px #dadada;
	margin-left: 12px;
	border-radius: 2px;
}

.classify_span select {
	width: 214px;
    height: 26px;
    padding: 3px;
    border-radius: 3px;
    display: inline-block;
    border: solid 1px #dadada;
    margin-left: 14px;
}

.classify_span {
	float: left;
	margin-left: 18px;
	margin-top: 10px;
	font-size: 14px;
}

.classify_span input {
	margin-left: -10px;
}

.classify_content {
	width: 290px;
	height: 250px;
	border: solid 1px #d0d0d0;
	position: absolute;
	left: 14px;
	top: 117px;
}

.white {
	margin-left: 10px;
}

.classify_content p {
	width: 240px;
	font-size: 14px;
	margin-top: 16px;
	margin-left: 10px;
}

.classify_content_img {
	border: 0;
	margin-left: 5px;
	margin-top: 10px;
	display: block;
}

.classify_content-input {
	margin-top: 7px;
}

.show_text {
	font-size: 16px;
	background: #fff;
	margin-left: 30px;
	margin-top: -10px;
	display: block;
	width: 140px;
}

.show_img {
	overflow: hidden;
	margin-left: 26px;
	margin-top: 24px;
}

.show_img a {
	float: left;
	margin-right: 15px;
	margin-bottom: 30px;
}

.show_button {
	display: block;
	text-align: center;
}

.show_button2 {
	width: 110px;
	height: 30px;
	background: #07a7e1;
	border: 1px solid #056f9f;
	border-radius: 3px;
	cursor: pointer;
	font-size: 16px;
	font-family: "微软雅黑";
	color: #fff;
}

.list_right {
	float: right;
	margin-left: 20px;
	width: 270px;
	margin-top: -20px;
	height: 420px;
}

.list_right form {
	padding: 10px;
	background: f7f7f7;
	zoom: 1;
}

.list_right span {
	font-size: 16px;
	background: #fff;
	margin-top: -48px;
	display: block;
	margin-left: 10px;
	text-align: center;
	width: 90px;
}
/*.list_right .opt_item #pro-list-type2 {height:135px;}*/
.list_right .opt_item #pro-list-type2 li {
	float: left;
	width: 100px;
	height: 140px;
	overflow: hidden;
	padding: 25px 0 15px 15px;
}

.list_right .opt_item #pro-list-type2 li .item {
	position: relative;
	width: 100px;
	height: 135px;
}

.list_right .opt_item #pro-list-type2 li .item .img {
	position: absolute;
	width: 100px;
	height: 135px;
	z-index: 1;
}

.list_right .opt_item #pro-list-type2 li .item .filter {
	position: absolute;
	width: 100px;
	height: 135px;
	z-index: 2;
}

.list_right .opt_item #pro-list-type2 li .item .bg {
	position: absolute;
	width: 100px;
	height: 135px;
	z-index: 3;
}

.btn_green {
	background: #07a7e1;
	border: 1px solid #056f9f;
	width: 110px;
	height: 30px;
	font-size: 16px;
	color: #fff;
	font-family: "微软雅黑";
	border-radius: 3px;
	cursor: pointer;
	margin-top: 5px;
	margin-left: 16px;
	display: inline-block;
}

.list_right .opt_item #pro-list-type2 li .item_on .bg {
	background: url(../images/selected-icon.png) no-repeat center center;
}

.list_right .opt_item #pro-list-type2 li .item_on .filter {
	background: #000;
	opacity: 0.6;
}

.opactiy {
	width: 278px;
	height: 20px;
	background: #000;
	opacity: 0.5;
	display: block;
	margin-top: -22px;
	line-height: 20px;
	text-align: center;
	color: #fff;
}
/*产品属性*/
.first {
	left: 323px;
}

.two {
	left: 355px;
}

.conterbox {
	border: 1px solid #e7e7e7;
	background: #fff;
	margin-left: 10px;
	margin-right: 10px;
	overflow: hidden;
	padding-bottom: 10px;
	border-top:none;
}
.conterbox_text {
	/*width: 190px;*/
	width: 400px;
	height: 22px;
	border: 1px solid #e7e7e7;
	line-height: 22px;
	float: left;
	margin-left: 20px;
	margin-top: 10px;
	font-size: 12px;
	color: #7f7f7f;
	cursor: pointer;
}
/*商城添加导航*/
.conterbox_text_three{
	width: 170px;
	height: 22px;
	border: 1px solid #e7e7e7;
	line-height: 22px;
	float: left;
	margin-left: 20px;
	margin-top: 10px;
	margin-bottom: 10px;
	font-size: 12px;
	color: #7f7f7f;
	cursor: pointer;
}
.conterbox_text_three:hover {
	background: #e7e7e7;
}
.conterbox_text_three:hover .conterbox_text_img img {
	display: block;
}

/*商城添加导航*/
.conterbox_text:hover {
	background: #e7e7e7;
}

.conterbox_text:hover .conterbox_text_img img {
	display: block;
}
.conterbox_text:hover .writer_class_one{
	display: block;
}
.before {
	border-width: 12px;
	border-style: solid;
	border-color: transparent transparent transparent #e7e7e7;
	float: left;
}

#conterbox_text {
	background: #e7e7e7;
	z-index: 99;
}

.conterbox_text_img {
	z-index: 100;
	display: block;
	width: 14px;
	height: 14px;
	float: right;
	margin-top: 4px;
	margin-right: 8px;
}

.conterbox_text_img img {
	width: 14px;
	height: 14px;
	display: none;
}

#classify_name {
	margin-top: 10px;
}

.classify_name img {
	width: 16px;
	height: 16px;
	margin-left: 5px;
	display: inline-block;
}
.classify_name select{
	width:214px;
	height:26px;
	border:solid 1px #dadada;
}

.classify_name_input {
	overflow: hidden;
}

.classify_name_input input {
	border: 1px solid #e7e7e7;
	height: 24px;
	display: block;
	width: 100px;
}

.classify_name_input img {
	width: 16px;
	height: 16px;
	float: left;
}

.classify .second {
	margin-top: 10px;
	margin-left: 82px;
}

.position {
	bottom: 186px;
	left: 50px;
}
/*银行子分类add*/
/*.conterbox_text:hover~.bank{display:block;}*/
.clear {
	clear: both;
}

.bank {
	width: 440px;
	height: 30px;
	padding-top: 10px;
}

.bank li {
	width: 100%;
	background-color: #e7e7e7;
	height: 20px;
	padding-top: 3px;
}

.bank li span {
	float: left;
	margin-left: 5px;
}

.bank li div {
	float: right;
	margin-right: 30px;
}
/*.bank li  image{margin-right:10px; display: block; width: 18px;height: 18px;}
.bank .imgList>image{margin-right:10px; display: block; width: 18px;height: 18px;}
#imgyy{width:15px;height:15px;}
#imgyyy{width:15px;height:15px;}
#imgyyl{width:15px;height:15px;}*/

/*add*/
.classify_name_text input {
	width: 150px;
}

.relation_type {
	margin-left: 9px;
}
.writer_class_one{
    z-index: 100;
    display:none;
    width: 14px;
    height: 14px;
    float: right;
    margin-top: 4px;
    margin-right: 8px;
}
.WSY_column_header{
	position: relative;
    top: -16px;
}
</style>
</head>

<body>
	<!--内容框架开始-->
	<div class="WSY_content">
		<!--列表内容大框开始-->
		<div class="WSY_columnbox">
			<?php
			include ($_SERVER ['DOCUMENT_ROOT'] . "/mshop/admin/PcShop/information/basic_head.php");
			?> 
			<?php
			require_once ('shoproom.php');
			$pc = new Pcshop ();
			$customer_id_en = $pc->customer_id_en;
			$sortinfo = $pc->sortinfo();
			//保存分类
			$settltype_url = $_SERVER['DOCUMENT_ROOT'].'/weixinpl/back_newshops/PcShop/information/interfaceroom.php?action=settltype&customer_id='.$pc->customer_id_en;  
			$edtcate_url = $_SERVER['DOCUMENT_ROOT'].'/weixinpl/back_newshops/PcShop/information/interfaceroom.php?action=edtcate&customer_id='.$pc->customer_id_en;
			?>
		<div class="WSY_data">
				<div class="classificationbox">

				<div class="content">
				
				
				    <?php 
				    
				    foreach($sortinfo as $va){
                        $pid = $va['id'];
                        $second_id_str = '';
                        if(isset($va['next'])){
                            foreach($va['next'] as $val){
                            	$second_id_str .= $val['id'].',';
                            }
                            $second_id_str = substr($second_id_str,0,-1);
                        }
				    	
				    
				    ?>
					
					<!-- 查询父级属性 -->
                    <div class="conterimgbox parent_props" data-id="<?php echo $va['parent_id'];?>">
						<a href="#" contenteditable='false' id='parent_title_<?php echo $pid;?>' onblur="ablur(<?php echo $va[id];?>,'first')" title="<?php echo $va['catename'];?>" class="icon-text"><?php echo $va['catename'];?></a>
						<div class="caozuo_right">
							<a href="javascript:canwirter('#parent_title_<?php echo $pid;?>')" 
							   class="compile first" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"/>
							</a> 
							<a href="interfaceroom.php?action=delcate&customer_id=<?php echo $customer_id_en; ?>&op=del&id=<?php echo $va[id];?>"
							   onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};"
							   class="conter_delete two" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"/>
							</a> 
							<a onclick="btn()" href="javascript:expandHide('<?php echo $pid;?>','<?php echo $second_id_str;?>')" class="conter_load" title="展开"> 
								<img id="img_expand_<?php echo $pid;?>" 
								data-status="hide"
								src="../../Common/images/Product/icon1112/dot-jiantou_<?php echo $productpro_id == $parent_id ? "x" : "s";?>.png">
							</a>
						</div>
				    </div>

				  <!-- 查询子属性 -->
				  
				  <?php  
				    
				      if(isset($va['next'])){
				        foreach($va['next'] as $vb){
                        $bid = $vb['id'];
						$three_id_str = '';
						if(isset($vb['next'])){
							foreach($vb['next'] as $vcc){
								$three_id_str .= $vcc['id'].',';
							}
							$three_id_str = substr($three_id_str,0,-1);
						}
				  ?>
                  <div class="conterbox child_props" style='display:none;' id="childprops_<?php echo $vb['id'];?>">
                  		
                  		<div class="conterbox_text" data-child-id="<?php echo $vb['parent_id'];?>">
							<div class="before"></div>
							<div contenteditable='false' onblur="ablur(<?php echo $vb['id'];?>,'second')" id='child_title_<?php echo $vb['id'];?>' style="width: 140px; height: 22px; overflow: hidden; float: left;"><?php echo $vb['catename'];?></div>
							<a class="conterbox_text_img" href="javascript:expandHide_next('<?php echo $bid;?>','<?php echo $three_id_str;?>')"
                                >
							   <img id="img_expand_<?php echo $vb['id'];?>" data-status="hide" src="../../Common/images/Product/icon1112/dot-jiantou_<?php echo $productpro_id == $parent_id ? "x" : "s";?>.png"/>
							</a>
							<a class="conterbox_text_img" href="interfaceroom.php?action=delcate&customer_id=<?php echo $customer_id_en; ?>&op=del&id=<?php echo $vb['id'];?>"
							   onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};">
							   <img src="../../../common/images_V6.0/operating_icon/icon04.png" />
							</a>
							<a href="javascript:canwirter('#child_title_<?php echo $vb['id'];?>')" 
							   class="compile first" title="编辑"><img class='writer_class_one' src="../../../common/images_V6.0/operating_icon/icon05.png"/>
							</a> 
					    </div>
				        <div class="clear"></div>
				        
				        <!-- 三级 -->
				        <?php 
				        
				        if(isset($vb['next'])){
                            foreach($vb['next'] as $vc){
				        
				        ?>
				        <div class="shang_cheng" id='nextchildprops_<?php echo $vc['id'];?>' style="display:none;margin-left: 20px;width: 400px;border: 1px solid #e7e7e7;border-top: none;overflow: hidden;">
							<div class="conterbox_text_three" data-child-id="<?php echo $vc['id'];?>">
								<div class="before"></div>
								<div contenteditable='false' id='next_child_<?php echo $vc['id'];?>' onblur="ablur(<?php echo $vc['id'];?>,'third')" style="width: 100px; height: 22px; overflow: hidden; float: left;"><?php echo $vc['catename'];?></div>
								<a class="conterbox_text_img" href="interfaceroom.php?action=delcate&customer_id=<?php echo $customer_id_en; ?>&op=del&id=<?php echo $vc['id'];?>"
								   onclick="if(!confirm(&#39;删除后不可恢复，继续吗？&#39;)){return false};">
								   <img src="../../../common/images_V6.0/operating_icon/icon04.png"/>
								</a>
								<a  href="javascript:canwirter('#next_child_<?php echo $vc['id'];?>')" 
							    class="conterbox_text_img" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"/>
							    </a> 
					    	</div>		        	
				        </div>
					  <?php }} ?>
				          
                  </div>
                  
                <?php }}} ?>
                  
              </div>
                

					<!-- 添加编辑 -->
					<div class="content">
						<form id="frm_producttype" class="" action="<?php echo $settltype_url; ?>" method="post">
							<div class="list_left">
								<div class="classify" id="classify">
									<p class="classify_text">添加导航分类</p>

									<p class="classify_name">
										分类名称：<input type="text" name="name"
											value="<?php echo $deal_name; ?>" id="name">
									</p>
									<span class="classify_span">隶属关系：<?php echo $pc->typeinfo(); ?><input type="hidden" name="settltype_name" value="顶级分类"/></span>
									<p class="classify_name settltype">
										分类排序： <select name="listorder" style='margin-left: 9px;' class="sort_ys">
											<option value="first">设定为第一个</option>
								    <?php 
								        $comtype = $pc->comtype();
								        if(count($comtype)>0){
								        for($te=[];list(,$dr)=each($comtype);){
								        if($dr['level']==1)$te[]=$dr;}}
								        if(count($te)>0){
								        	for($k=0;$k<count($te);$k++){?>
                                                <option
												value="<?php echo $te[$k]['listorder'];?>">于 [<?php echo $te[$k]['catename'];?>] 之后</option>
                                                <?php
								            }
								        }
								    ?> 
							        </select>
									</p>
							   
									<div class="classify_content-input">
										<button type="button" class="classify_input" id="saveProtype" style="margin-top: 26px; margin-left: 50px;">保存分类</button>
										<button type="button" class="classify_input2" id="returnBack">取消</button>
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
      
    //可编辑标题
	function canwirter(id){
		$(id).attr('contenteditable','true');
		var texthtml=$(id).html();
		$(id).html('').focus().html(texthtml);	
	}
	
	//编辑标题失去焦点保存编辑
	function ablur(id,cate_class){
		var content = '';
		if(!cate_class)return;
		if(cate_class=='first'){
			content = $("#parent_title_"+id).text();
		}else if(cate_class=='second'){
			content = $("#child_title_"+id).text();
		}else if(cate_class=='third'){
			content = $("#next_child_"+id).text();
		}else{
			return;
		}$.ajax({
		   url:"<?php echo $edtcate_url;?>",
		   data:{id:id,content:content},
		   type:'post',            
		   dataType:'json',      
		   async:false,                  
		   success:function(res){
		   	   console.log(res);
		   	   if(res.errcode){
		   	   	   alert(res.data);
		   	   	   return;
		   	   }
		   },
		   error:function(x){
		   	   console.log(x);
		   	   alert('请求失败！！！');
		   } 
		});
	}
	
	//一级子类展开/隐藏的方法
	function expandHide(parentId,propId){
		console.log(parentId);
		console.log(propId);
		propId=propId.split(',');
		console.log(propId);

		var img = $("#img_expand_"+parentId);
		console.log(img)
		for(var i=0;i<propId.length;i++){
			if(img.data("status") == "hide"){
				console.log(1)
			  $("#childprops_"+propId[i]).show();
			}else{
				console.log(2)
			  $("#childprops_"+propId[i]).hide(); 
			}
		}
		
		if(img.data("status") == "hide"){
			console.log(3)
			img.attr("src","../../Common/images/Product/icon1112/dot-jiantou_s.png");
			img.data("status","show");
		}else{
			console.log(4)
			img.data("status","hide");
			img.attr("src","../../Common/images/Product/icon1112/dot-jiantou_x.png");
		}
	};
	//二级子类展开隐藏
	function expandHide_next(parentId,propId){
        propId=propId.split(',');
		var img = $("#img_expand_"+parentId);
		console.log(parentId);
		console.log(propId);
		for(var i=0;i<propId.length;i++){
			if(img.data("status") == "hide"){
			  $("#nextchildprops_"+propId[i]).show();
			}else{
			  $("#nextchildprops_"+propId[i]).hide(); 
			}
		}
		
		if(img.data("status") == "hide"){
			img.attr("src","../../Common/images/Product/icon1112/dot-jiantou_s.png");
			img.data("status","show");
		}else{
			img.data("status","hide");
			img.attr("src","../../Common/images/Product/icon1112/dot-jiantou_x.png");
		}
	};
	
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
			window.location.reload();
			//location.href='documentation.php?customer_id=<?php echo $customer_id_en;?>';
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


    //检查深度
    $("select#settltype").on('change',function(){
    	var depth = $(this).val();
    	if(depth==0){
    		$(".settltype").show();
    	}else{
            $(".settltype").hide();
    	}
    });





</script>

</body>
</html>

<?php ?>