<?php 
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');
  require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
  require('../../../../weixinpl/back_init.php');
   $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
   mysql_select_db(DB_NAME) or die('Could not select database');
   _mysql_query("SET NAMES UTF8");
   require('../../../../weixinpl/proxy_info.php');
 // $theme='blue';
  
  $keyid = -1;
  $op = "";
  $asort = 0;

  if(!empty($_GET["keyid"])){
    $keyid = $configutil->splash_new($_GET["keyid"]);
  }

	$type = 0;
	
	$obj_id = -1;
	if(!empty($_GET["obj_id"])){
		$obj_id = $_GET["obj_id"];
	}
	
  if($keyid>0){
    
    $query = 'SELECT * FROM weixin_commonshop_guess_you_like where isvalid=true and id='.$keyid;
	$result = _mysql_query($query) or die('Query failed1: ' . mysql_error());  
	$asort  = 0;
	$pro_id = 0;
	while ($row = mysql_fetch_object($result)) {
		
			$pro_id = $row->pro_id;
			$asort  = $row->asort;
		
	}
	//查询产品信息
	if($pro_id>0){
		$query1 = "select name,default_imgurl from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and id=".$pro_id." ";
		$name 				= '';
		$default_imgurl 	= '';
		
		$result1=_mysql_query($query1)or die('Query failed2'.mysql_error());
		while($row1=mysql_fetch_object($result1)){
			$name 			= $row1->name;
			$default_imgurl = $row1->default_imgurl;
			break;
		}

		if(empty($default_imgurl) or $default_imgurl==""){
						 
		}else{
			  $pos = strpos($default_imgurl,"//");
			  if($pos===0){
			  }else{
				  $pos = strpos($default_imgurl,"../../../");
				  if($pos===0){
					 $default_imgurl = substr($default_imgurl,9);
				  }
				  $default_imgurl = '//'.CLIENT_HOST.$default_imgurl;
			  }
		}
				
	}	
	
			
				
		
  }
	

 if(!empty($_GET["op"])){
	  
  $op = $configutil->splash_new($_GET["op"]);
  
  
   if($op=="del"){
    
     
     $query = 'update weixin_commonshop_guess_you_like set isvalid=false where id='.(int)$keyid;
	 _mysql_query($query);
	 $error =mysql_error();
	 mysql_close($link);
	 //echo $error;
	 echo "<script>location.href='guess_you_like.php?customer_id=".$customer_id_en."';</script>";
	 return;
  }
}   
	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<script type="text/javascript" src="../../../js/WdatePicker.js"></script> 

</head>

<style type="text/css">
.WSY_member textarea {
width: 350px;
height: 150px;
}
dt{
	margin-top:6px;
}
.spa{
  position: relative;
  right: 32px;
  padding-left: 105px;
}
.WSY_member div {
    width: 50%;
}
.WSY_member dd {	
    float: none!important;
}
.WSY_member dt {
	width: 200px;
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
<body>
<div class="div_new_content">
<form action="save_guess_you_like.php?customer_id=<?php echo $customer_id_en ?>&keyid=<?php echo $keyid;?>" enctype="multipart/form-data" method="post" id="upform" name="upform">


    <div class="WSY_content">
		<div class="WSY_columnbox WSY_list">
	
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">关联产品</a> 
					
				</div>
			</div>

			<div class="WSY_data">
					
					<dl class="WSY_member" >					
						<div>
							<dt>排序编号</dt>
							<dd class="spa">
								<input type="text" value="<?php echo $asort;?>" name="asort" id="asort" style="width:100px; ">编号越大，排序越靠前
							</dd>
						
						</div>
					</dl>
					<dl class="WSY_member" id="product">					
						<div>
							<dt>产品</dt>
							<dd class="spa">
								<select name="pro_id" id="pro_id">
									<option value="-1">请选择产品</option> 
								<?php 
								
								$query_pro = "SELECT id,name,asort_value,type_id,type_ids,orgin_price,now_price,cost_price,need_score,default_imgurl,isnew,createtime,isout,ishot,isvp,good_level,meu_level,bad_level,is_supply_id,create_type,sell_count,is_QR,storenum FROM weixin_commonshop_products WHERE isvalid=true AND customer_id=".$customer_id." and isout = false  ";
								//echo $query_pro;
								$result=_mysql_query($query_pro)or die('Query failed'.mysql_error());
								while($row2=mysql_fetch_object($result)){
										$p_id            = $row2->id;
										$p_name 		 = $row2->name;
										$p_orgin_price   = $row2->orgin_price;
										$p_now_price     = $row2->now_price;
										$p_cost_price    = $row2->cost_price;
										$p_need_score    = $row2->need_score;
										$p_isnew         = $row2->isnew;
										$p_createtime    = $row2->createtime;
										$p_type_id       = $row2->type_id;
										$p_isout         = $row2->isout;
										$p_isnew         = $row2->isnew;
										$p_ishot         = $row2->ishot;
										$p_isvp          = $row2->isvp;
										$is_QR           = $row2->is_QR;
										$type_ids        = $row2->type_ids;
										$asort_value     = $row2->asort_value;
										$supply_id       = $row2->is_supply_id;
										$create_type     = $row2->create_type;
										$sell_count      = $row2->sell_count;
										$storenum        = $row2->storenum;
										$pro_img 		 = $row2->default_imgurl;
								
								?>
									
									<option <?php if($pro_id==$p_id)echo 'selected';?> value="<?php echo $p_id;?>" imgurl="<?php  echo "//".$http_host.$pro_img ;?>" ><?php echo $p_name ;?></option>
									
								<?php }?>	
								</select>
							
							</dd>
						
						</div>
					</dl>
					<dl class="WSY_member" id="product_img">			
						<div>
							<dt>产品图片</dt>
						
							<dd class="spa">
						
							  <img src="<?php echo $default_imgurl; ?>" id="img_v" style="width:150px;height:150px;" /><br/>
							   
							  
							   
							
							</dd>	
							
						</div>
					</dl>
				
					<input type=hidden value="<?php if($type ==1 ){echo "//".$http_host.$self_pro_imgurl;;}else{echo 'images/jp.png';} ?>" name="pro_imgurl" id="pro_imgurl" /> 
					
				
		
					<div class="WSY_text_input01">
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
						<div class="WSY_text_input"><input type="button" class="WSY_button" value="取消" onclick="javascript:history.go(-1);" style="cursor:pointer;"/></div>
					</div>
			
			</div>
	
		</div>
	</div>
 </form>

<div style="width:100%;height:20px;">
</div>
</div>	
<!--弹窗-->
<div id="temp">
	<div class="cont">

	</div>

</div>
<div id="temp_zhe"></div>

<script>
$(function(){
	
 $('select').change(function(){
		
		var p_id = $(this).children('option:selected').val();
		
		var imgurl = $(this).children('option:selected').attr('imgurl');
				
		$('.spa>img').attr('src',imgurl);				
		$('#pro_imgurl').val(imgurl);				
		
		
				
	}); 
	
 
	
});

function submitV(){
	
	
			var select = $('#product option:selected').val();
			//console.log('select'+select);
			if(select==-1)
			{
				alert('请选择产品！');
				return;
			}

		document.getElementById("upform").submit();	
} 




</script>
</body>

<?php mysql_close($link);?>	
</html>