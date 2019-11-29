<?php
header("Content-type: text/html; charset=utf-8"); //ini_set('display_errors','on');
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link =    mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');

$title = "";  //快递模板名称  
if(!empty($_GET["title"])){
	$title = $configutil->splash_new($_GET["title"]);
}
$tem_id = -1;  //快递模板ID
if(!empty($_GET["tem_id"])){
	$tem_id = $configutil->splash_new($_GET["tem_id"]);
}

$action = "";  //操作 add:新增 edit:修改
if(!empty($_GET["action"])){
	$action = $configutil->splash_new($_GET["action"]);
}
$op = 'add';
if(!empty($_GET["op"])){
	$op	=	$configutil->splash_new($_GET["op"]);	
}
$print_temp_id = 0;  //快递模板ID
if(!empty($_GET["print_temp_id"])){
	$print_temp_id = $configutil->splash_new($_GET["print_temp_id"]);
}

//$ert_arr = json_encode($ert_arr);
//var_dump($ert_arr);
//var_dump($express_arr);



    //$array_print_temp = array();
    $sql_print_temp = "SELECT id,print_name from weixin_print_temp WHERE isvalid=1 AND is_supply=0 AND customer_id=".$customer_id;
	$obj_print_temp = _mysql_query($sql_print_temp); $array_print_temp = array();
	while ($row_print_temp = mysql_fetch_object($obj_print_temp)){
		$array_print_temp[] = $row_print_temp;
	}
    
//list in select 检测是否等于$val值是否相等，如果等于就返回$selected值，默认为selected
function l_s($val_0, $val_1, $selected='selected'){
	//if(in_array($array_val,$array)){return $selected;}else{return '';}
	if($val_0==$val_1){return $selected;}else{return '';}
}    
    
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>物流公司设置</title>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script> 
<style>
	label input[type="radio"]{
		width: auto;
  		height: auto;
	}
</style>
</head>

<body>   
	<!--内容框架-->
	<div class="WSY_content">
		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<?php 
				//头部列表
				$header = 3;
				include("../../../../weixinpl/back_newshops/Distribution/express/head.php");
				
			?>			
        <!--权限管理代码开始-->
		<form action="express_company.class.php?customer_id=<?php echo $customer_id_en;?>&op=<?php echo $op;?>&tem_id=<?php echo $tem_id; ?>" method="post" id="myform" onsubmit = "return check();">
			<div class="WSY_data">
				<div class="WSY_competence">
					<p>物流公司名称：<input type="text" name="title"  id="title" value="<?php echo $title;?>"><i>长度为1~16位字符</i></p>					
				</div>
                <div class="WSY_competence">
                    <p>
                        <label style="font-size: 14px;">关联打印模板：</label>
                        <span class="input">
                        <select name="print_temp_id" id="print_temp_id">
                            <option value="0">选择模板绑定</option>
                            <?php foreach($array_print_temp as $val){ ?>
                            <option value="<?php echo $val->id ?>" <?php echo l_s($val->id,$print_temp_id) ?>><?php echo $val->print_name ?></option>
                            <?php } ?>
                        </select>
                        <a href="add_delivery_temp.php?customer_id=<?php echo $customer_id ?>">添加运单模板</a> 
                        <a id="edit_print_temp_url" style="display:none;" href="">编辑运单模板</a>
                        <a id="del_print_temp_url" style="display:none;" href="" onClick="return confirm('确认要删除吗？');">删除运单模板</a>
                        </span>
                    </p>
                    <div class="clear"></div>
                </div>
				<div class="WSY_text_input"><input class="WSY_button" type="button" id="formid" value="提交" onclick="check()"><br class="WSY_clearfloat"></div>
			</div>
		</form>
        
        <!--权限管理代码结束-->
	</div>
<script>
    
	// ---------提交------start
	var title_v = '<?php echo $title;?>';
	function check(){
		var title = document.getElementById('title').value;
		if( title == "" ){
			win_alert('请输入名称');
			return false;
		}
		document.getElementById("myform").submit();		
	}
 
$(function() {
    $("#print_temp_id").change( function() {
		if($("#print_temp_id").val() == '0'){
			$('#edit_print_temp_url').hide();
			$('#del_print_temp_url').hide();
		}else{
			show_url($("#print_temp_id").val());
		}
	});
	
	<?php if($print_temp_id>0){?>
	show_url($("#print_temp_id").val());
	<?php } ?>
    
});

function show_url(print_temp_id){
	$('#edit_print_temp_url').show();
	$('#edit_print_temp_url').attr('href','add_delivery_temp.php?id='+print_temp_id);
	$('#del_print_temp_url').show();
	$('#del_print_temp_url').attr('href','save_delivery.php?do=del&id='+print_temp_id);
}

</script>
</body>
</html>
