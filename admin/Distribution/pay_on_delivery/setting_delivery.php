<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php');
require('../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/common/utility_common.php');
_mysql_query("SET NAMES UTF8");

$customer_id = passport_decrypt($customer_id);

//开启或关闭货到付款
if(!empty($_POST["action"]) && $_POST["action"] =='is_pay_on_delivery')
{
   $obj     = $configutil->splash_new($_POST["obj"]);
   $obj_sql = "update weixin_commonshops_extend set is_pay_on_delivery=$obj where isvalid=1 and customer_id=$customer_id";
   $del_res = _mysql_query($obj_sql) or die('Query_activity failed:'.mysql_error());
   $del_res ? $de = array("status"=>"ok") : array("status"=>"error");  
   exit(json_encode($de));
  
 }

//删除
if(!empty($_POST["action"]) && $_POST["action"] =='del')
{
   $del_id  = $configutil->splash_new($_POST["id"]);
   $del_sql = "update pay_on_delivery_products_t set isvalid=0 where id in($del_id)";
   $del_res = _mysql_query($del_sql) or die('Query_activity failed:'.mysql_error());
   $del_res ? $de = array("status"=>"ok") : array("status"=>"error");
   
   exit(json_encode($de));
  
 }

//添加
if(!empty($_POST["action"]) && $_POST["action"] =='add_pro')
{
	$products_t  = "select pid from pay_on_delivery_products_t where isvalid =1 and customer_id=".$customer_id;
	$products_res= _mysql_query($products_t) or die("sql error ".mysql_error());

	$pids        = $_POST['date'];
	$createtime  = date("Y-m-d H:i:s",time());
	$isvalid     = 1; 
	$values      = '';

	while ($row  = mysql_fetch_object($products_res)) 
	{
		$pro_pids = $row->pid;
		$pro[]    = $pro_pids;
	}

	foreach ($pids as $k => $v) 
	{
		if(in_array($v,$pro)) 
		{

			unset($pids[$k]);
			continue;
		}	
		
		$values[$k] = "($customer_id,'$createtime',$isvalid,$pids[$k])";
		
	}
	$value = implode($values,',');
	$query = "insert INTO pay_on_delivery_products_t(customer_id,createtime,isvalid,pid) VALUES $value";
	$res   = _mysql_query($query) or die('Query_activity failed:'.mysql_error());
	if($res)
	{	
		$con    = count($pids);
		$con_in = "select id from pay_on_delivery_products_t order by id desc limit $con";
		$get_id = _mysql_query($con_in) or die('Query_activity failed:'.mysql_error());
		while( $row_activity = mysql_fetch_object($get_id) )
		{
			$ids[] = $row_activity->id;
		}
		$insert_id = implode($ids,','); 
		$delivery  = "select de.id,de.customer_id,de.createtime,pro.name,pro.default_imgurl,pro.orgin_price,pro.now_price,pro.for_price,pro.storenum,pro.type_ids from pay_on_delivery_products_t as de left join weixin_commonshop_products as pro on de.pid= pro.id where de.id in($insert_id) ";
		$query     = _mysql_query($delivery) or die('Query_activity failed:'.mysql_error());
		while( $row_activity = mysql_fetch_assoc($query) )
		{
			//获取分类名称
			if($row_activity['type_ids'] != '')
			{
				$cate      = trim($row_activity['type_ids'],',');
				if ($cate != ""){
                    $cate_name = "select name from weixin_commonshop_types where id in($cate)";
                    $res       = _mysql_query($cate_name);
                    while($da  = mysql_fetch_object($res))
                    {
                        $cname[] = $da->name;
                    }
                    $row_activity['cname'] = implode(',',$cname);
                }
			}
			$data[]= $row_activity;
		}
		exit(json_encode(array('status'=>'ok','data'=>$data)));
	}
	else
	{
		exit(json_encode(array('status'=>"error")));
	}	
}

//获取配置开关
$is_delivery = "select is_pay_on_delivery from weixin_commonshops_extend where isvalid=1 and customer_id=$customer_id";
$is_dequery  = _mysql_query($is_delivery) or die('Query_activity failed:'.mysql_error());
while( $dequery_res = mysql_fetch_object($is_dequery) )
{
	$is_pay_on_delivery = $dequery_res ->is_pay_on_delivery;
}

$pagenum  = 1;//页码
$pagesize = 20;//每页数据数量

if(!empty($_GET["pagenum"])) 
{
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$condition  = '';
if(!empty($_GET["search_type"])) 
{
   $search_type = $configutil->splash_new($_GET["search_type"]);
   $condition  .= " and find_in_set($search_type, pro.type_ids)";
}

if(!empty($_GET["search_name"])) 
{
   $search_name = $configutil->splash_new($_GET["search_name"]);
   $condition  .= " and pro.name like '%$search_name%'";
}

if(!empty($_GET["begintime"])) 
{
   $begintime   = $configutil->splash_new($_GET["begintime"]);
   $begin       = strtotime($begintime);
   $condition  .= " and UNIX_TIMESTAMP(de.createtime) > $begin";
}

if(!empty($_GET["endtime"])) 
{
   $endtime     = $configutil->splash_new($_GET["endtime"]);
   $end         = strtotime($endtime);
   $condition  .= " and UNIX_TIMESTAMP(de.createtime) <$end";
}

$start = ($pagenum-1) * $pagesize;

$delivery = "select de.id,de.customer_id,de.createtime,pro.name,pro.default_imgurl,pro.orgin_price,pro.now_price,pro.for_price,pro.storenum,pro.type_ids from pay_on_delivery_products_t as de left join weixin_commonshop_products as pro on de.pid= pro.id where de.customer_id=$customer_id and de.isvalid = 1 $condition order by de.createtime desc limit $start,$pagesize";
$query   = _mysql_query($delivery) or die('Query_activity failed:'.mysql_error());
$k=0;
while( $row_activity = mysql_fetch_assoc($query) )
{
	//获取分类名称
	if($row_activity['type_ids'] != '')
	{
		$cate      = trim($row_activity['type_ids'],',');
		if ($cate != ""){
            $cate_name = "select name from weixin_commonshop_types where id in($cate)";
            $res       = _mysql_query($cate_name);
            while($da  = mysql_fetch_object($res))
            {
                $cname[] = $da->name;
            }
            $ccname = implode(',',$cname);
            unset($cname);
        }
	}
	$date[$k] = $row_activity;

	$date[$k]['cname'] = $ccname ;
	$k++;
}

$count    = "select count(de.id) as ccount from pay_on_delivery_products_t as de left join weixin_commonshop_products as pro on de.pid= pro.id where de.isvalid = 1";
$result   = _mysql_query($count);
while( $row_success = mysql_fetch_object($result) )
{
	$count = $row_success ->ccount;
}
$page = ceil($count/$pagesize);

//获取产品分类
$link = new shopLink_Utlity($customer_id);
$link_arr = $link->getSelectLink(array(3),1);
$type_arr = $link_arr['type_arr'];	
mysql_close($link);
 
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<script type="text/javascript" src="../../../js/tis.js"></script>

<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../Common/css/MarkPro/packages/packages.css">
<script charset="utf-8" src="../../../common/js/jquery.jsonp-2.2.0.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script><!--添加时间插件-->
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css"> 
body{background: #fff}
table#WSY_t1 td{text-align: center;}
td img {  padding:5px 5%; width: 100%}

.WSY_columnbox{overflow:auto}
.navi_head{height:38px;background:#F4F4F4;border-bottom:1px solid #D8D8D8}
.navi_body{height:50px;cursor:pointer;transition:height ease .5s}
.headbox li{float:left;width:150px;text-align:center;font-weight:700;color:#FFF;font-size:14px;vertical-align:top}
.headbox li p{height:38px;line-height:38px}
.headbox li .navi_title{font-size:15px;line-height:38px;margin-top:0;color:#646464;font-weight:400}

.navbox{background:#06a7e1;display: none;position: absolute;border-bottom:2px solid #06A7E1;}
.navbox li{float: none;height: 38px;line-height: 38px;background:#FFFFFF;color:#646464;font-weight:normal;}
.navbox li:hover{background:#EBEBEB;}
.navbox li:hover a{border:none;}
.clear{clear:both;}
.navi_title:hover{background:#FFFFFF;}
.header-left{float:left;margin-left:5px;}
#u187_input{width:100px;height:30px;font-family:'Arial Normal',Arial;font-weight:400;font-style:normal;font-size:14px;text-decoration:none;color:#fff;text-align:center; border-radius: 3px; line-height: 30px; cursor: pointer;}
span.delall{width:100px;height:30px;font-family:'Arial Normal',Arial;font-weight:400;font-style:normal;font-size:14px;text-decoration:none;color:#fff;text-align:center;border-radius: 3px; line-height: 30px; cursor: pointer;display: inline-block;}
span.add_product{width:100px;height:30px;font-family:'Arial Normal',Arial;font-weight:400;font-style:normal;font-size:14px;text-decoration:none;color:#fff;text-align:center;border-radius: 3px; line-height: 30px; cursor: pointer;display: inline-block;}

.button_blue{cursor: pointer;margin-left: 10px;font-size: 14px;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:20px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.name{  margin-top: 10px;height: 30px;line-height: 30px;font-size: 13px;text-align: left;font-weight: bolder;margin-left: 19px;}
.button_box{width: 296px;display: block;text-align: right;}
.button_box .WSY_button{border-radius:2px;border:none;}    
span.delivery-button{padding: 3px 15px;border-radius: 2px;cursor:pointer; border-radius: 3px; background: #06a7e1; color: #fff}
.product-box{width:95%;margin-top: 15px;padding: 15px;border: 1px #ccc solid;display:none;}
.header-left{float:left;}
.header-right{float:right;}
.search-box{height: 22px;line-height: 22px;} 
.delivery_time{margin: 5px 35px;}
.delivery_time input{margin: 0 20px;}
.delivery_limit_box{margin: 15px 35px;position: relative;} 
.delivery_font{font-size: 15px;font-weight: bold;width:140px;text-align:right;display:inline-block;}
.delivery_limit_box input[type=text]{margin: 0 20px;}
.selected-date-content{display: block;margin-left: 30px;margin-top: 10px;}
.page-box1{margin-left:30px;}
.page-box1,.page-box2{margin-top:15px;margin-left: 70px;}
.show-data-num{margin:0 20px;}
.page-box1 input,.page-box2 input{width:30px;text-align:center;}
.show-data-num-btn{margin-right:30px;}
.current-page{margin: 0 20px;}
.go-page-btn{margin-left:15px;}
#to-page-num1,#to-page-num2{margin-left:20px;}
.relation_table th,.list_table th{text-align:center;}
.activity_title{width:100%;border-bottom:1px #DEDBDB solid;padding: 3px 0 3px 20px;margin-top: 15px;}
.activity_title_span{padding: 5px 10px;background-color: #DEDBDB;}
.operation-button{height: 50px;line-height: 50px;text-align: center;}
.back-button,.close-float-table{padding:5px 40px;;background-color:#06a7e1;font-size:15px;color:#fff;margin: 0 10px;cursor:pointer;}
.save-button,.add-selected-product{padding:5px 40px;;background-color:#06a7e1;font-size:15px;color:#fff;margin: 0 10px;cursor:pointer;}
.float-table{position: fixed;top: 0;height: 100%;width: 100%;background-color: #fff;overflow: scroll;z-index: 999;}
.float-table-title-box{text-align: center;background-color: #E0E0E0;height: 40px;line-height: 40px;}
.float-table-title{font-size: 18px;font-weight: bold;}
.selected-table td,.selected-table input{font-size: 12px !important;}
table#WSY_t1 td{word-wrap: break-word;text-align:center;}
.product-name{width:100%;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;}

.WSY_list dl{margin-left:5px;margin-top:20px;margin-bottom:10px}
.WSY_list dd,.WSY_list dt{display:block;float:left;margin-right:10px;line-height:20px}
.WSY_list dl ul{overflow:hidden;width:50px;height:20px;border-radius:300px;position:relative}
.WSY_list dl ul p{position:absolute;font-size:12px;font-family:Arial;line-height:20px}
.WSY_list dl ul li{width:16px;height:16px;border-radius:300px;background:#fff;position:absolute;z-index:999;margin-left:2px;margin-top:2px;cursor:pointer}
.WSY_list dl ul span{width:16px;height:16px;border-radius:300px;background:#fff;position:absolute;margin-left:2px;margin-top:2px;cursor:pointer}
.WSY_columnbox {
    overflow: hidden;
    margin-left: 2%;
    width: 95%;
    background-color: rgb( 251, 251, 251 );
    box-shadow: 0px 3px 6px 0px rgb( 193, 193, 193 );
    margin-top: 15px;
}
body{background: #e4e4e4}
</style>
</head>
<body>
<div class="WSY_content" id="WSY_content_height" style="z-index:1">
<div class="WSY_columnbox">
<div class="WSY_list" style="padding-top:15px;">

	<dl class="WSY_remind_dl02">
	<dt class="">货到付款开关：</dt>
		<dd>
			<?php if($is_pay_on_delivery==1){ ?>
			<ul style="background-color: rgb(255, 113, 112);">
				<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
				<li onclick="change_express(0)" class="WSY_bot" style="left: 0px;"></li>
				<span onclick="change_express(1)" class="WSY_bot2" style="display: none; left: 30px;"></span>
			</ul>
			<?php }else{ ?>
			<ul style="background-color: rgb(203, 210, 216);">
				<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 10px;">关</p>
				<li onclick="change_express(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
				<span onclick="change_express(1)" class="WSY_bot2" style="display: block; right: 0px;"></span>
			</ul>						
			<?php } ?>
		</dd>
		<input type="hidden" name="sendstyle_express" id="sendstyle_express" value="1">
	</dl>

	<div class="header-left">
		<span>分类：</span>
		<select id="search-type">
			<option value="-1">全部</option>
			<?php
				foreach( $type_arr['-1'] as $key => $value ){
					$option_arr = explode('_',$value);
					$option_val = $option_arr[0];
					$option_name = $option_arr[1];
			?>
			<option value="<?php echo $option_val;?>" <?php if($search_type == $option_val){ ?> selected="selected" <?php } ?>><?php echo $option_name;?></option>
				<?php
					if( !empty($type_arr[$option_val]) ){
						foreach( $type_arr[$option_val] as $key2 => $value2 ){
							$option_arr = explode('_',$value2);
							$option_val = $option_arr[0];
							$option_name = $option_arr[1];
				?>
			<option value="<?php echo $option_val;?>" <?php if($search_type == $option_val){ ?> selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
				<?php
					if( !empty($type_arr[$option_val]) ){
						foreach( $type_arr[$option_val] as $key3 => $value3 ){
							$option_arr = explode('_',$value3);
							$option_val = $option_arr[0];
							$option_name = $option_arr[1];
				?>
			<option value="<?php echo $option_val;?>" <?php if($search_type == $option_val){ ?> selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
				<?php
					if( !empty($type_arr[$option_val]) ){
						foreach( $type_arr[$option_val] as $key4 => $value4 ){
							$option_arr = explode('_',$value4);
							$option_val = $option_arr[0];
							$option_name = $option_arr[1];
				?>
			<option value="<?php echo $option_val;?>" <?php if($search_type == $option_val){ ?> selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
				<?php
						}
					}
						}
					}
						}
					}
				}
			?>
		</select>
		<span>产品名：</span><input type="text" class="search-box" id="search-name" value="<?php echo $search_name ?>">
		<span>时间：</span><input class="login-input-username" type=text id=begintime name=begintime value="<?php echo $begintime ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'endtime\')}'});"  />
		<span>至：</span><input  class="login-input-username" type=text id=endtime name=endtime value="<?php echo $endtime ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'begintime\')}'});"  />
		<input id="u187_input" class="WSY-skin-bg" type="submit" onclick="search()" value="搜索">
		<span>&nbsp;</span>
		<span class="delall WSY-skin-bg" onclick="delall()">批量删除</span>
		<span>&nbsp;</span>
		<span class="add_product WSY-skin-bg" onclick="add_product()">添加产品</span>
	</div>
</div>
<div class="main">
<table width="97%" class="WSY_table" id="WSY_t1">
		<thead class="WSY_table_header WSY_table_header2">
			<th width="7%">
				<input type="checkbox" id="selectall" onclick="selectall()">
			</th>
			<th width="10%">产品图片</th>
			<th width="20%">产品名称</th>
			<th width="15%">产品分类</th>
			<th width="8%">产品原价</th>
			<th width="8%">产品现价</th>
			<th width="8%">产品成本</th>
			<th width="8%">产品库存</th>
			<th width="10%">添加时间</th>
			<th width="8%">操作管理</th>
		</thead>
		<tbody class="dfds"></tbody>
		<?php
			$pid_arr = [];
			$pid_str = '';
			foreach($date as $k=>$row_activity){

		?>
		
		<tr >
			<td><input type="checkbox" class="checkbox" name="ids" value="<?php echo $row_activity['id']; ?>"></td>
			<td><img src="<?php echo $row_activity['default_imgurl']; ?>"  /></td>
			<td><?php echo $row_activity['name']; ?></td>
			<td><?php echo $row_activity['cname']; ?></td>
			<td><?php echo $row_activity['orgin_price']; ?></td>
			<td><?php echo $row_activity['now_price']; ?></td>
			<td><?php echo $row_activity['for_price']; ?></td>
			<td><?php echo $row_activity['storenum']; ?></td>
			<td><?php echo $row_activity['createtime']; ?></td>
			<td><a href="javascript:void(0)"  class="del WSY-skin-bg" style="padding:3px 10px; color: #fff;border-radius: 3px;" onclick="del(this)" ids="<?php echo $row_activity['id']; ?>">删除</a></td>
		</tr>
		<?php }?>
	</table>
</div>	

	<!--翻页开始-->
    <div class="WSY_page">
    	
    </div>
</div>
</div>    

    <div style="clear: both"></div>
  
<style type="text/css">
	
</style>
    <div class="float-table" style="display:none;">
    	<div class="WSY_content" id="WSY_content_height" style="z-index:1">
<div class="WSY_columnbox" style="padding-bottom: 20px;">  
	<div class="WSY_column_header">
		<div class="WSY_columnnav">
			<a class="white1">选择产品</a>   
		</div>
	</div>
	<div class="float-table-search" style="margin-top: 10px;padding-left: 10px;">
		<span>产品编号：</span><input type="text" id="search-pid" onkeyup="clearInt(this)" />
		<span>产品名称：</span><input type="text" id="search-pname" />
		<span>合作商ID：</span><input type="text" id="search-supply-id" onkeyup="clearInt(this)" />
		<span>产品分类：</span>
		<select id="search-ptype">
			<option value="-1">全部</option>
			<?php
				foreach( $type_arr['-1'] as $key => $value ){
					$option_arr = explode('_',$value);
					$option_val = $option_arr[0];
					$option_name = $option_arr[1];
			?>
			<option value="<?php echo $option_val;?>"><?php echo $option_name;?></option>
				<?php
					if( !empty($type_arr[$option_val]) ){
						foreach( $type_arr[$option_val] as $key2 => $value2 ){
							$option_arr = explode('_',$value2);
							$option_val = $option_arr[0];
							$option_name = $option_arr[1];
				?>
			<option value="<?php echo $option_val;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
				<?php
					if( !empty($type_arr[$option_val]) ){
						foreach( $type_arr[$option_val] as $key3 => $value3 ){
							$option_arr = explode('_',$value3);
							$option_val = $option_arr[0];
							$option_name = $option_arr[1];
				?>
			<option value="<?php echo $option_val;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
				<?php
					if( !empty($type_arr[$option_val]) ){
						foreach( $type_arr[$option_val] as $key4 => $value4 ){
							$option_arr = explode('_',$value4);
							$option_val = $option_arr[0];
							$option_name = $option_arr[1];
				?>
			<option value="<?php echo $option_val;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $option_name;?></option>
				<?php
						}
					}
						}
					}
						}
					}
				}
			?>
		</select>
		<span class="add-selected-product" onclick="addProduct(2)" style="border-radius: 3px;">批量添加</span>
		<span class="close-float-table" style="border-radius: 3px;">返回</span>
	</div>
	<div style="margin-top: 10px;padding-left: 10px;">
		<span>产品来源：</span>
		<select id="search-pfrom">
			<option value="-1">全部</option>
			<option value="1">平台</option>
			<option value="2">供应商</option>
		</select>
		<span style="margin-left: 15px;">产品标签：</span>
		<select id="search-ptag">
			<option value="-1">全部</option>
			<option value="1">热卖</option>
			<option value="2">新品</option>
			<option value="3">包邮</option>
			<option value="4">虚拟产品</option>
			<option value="5"><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?></option>
		</select>
		<span class="delivery-button search-button" style="margin-left: 35px;" onclick="searchProduct()">搜索</span>
		
		<!-- <span class="delivery-button search-button" style="float:right;margin-right: 10%;" onclick="searchProduct()">搜索</span> -->
	</div>
	<table width="93%" class="WSY_table list_table" id="WSY_t1" >
		<thead class="WSY_table_header">
			<th width="7%"><input type="checkbox" id="select-all" style="vertical-align: middle;" /><label for="select-all">全选</label></th>
			<th width="8%">产品ID</th>
			<th width="15%">名称</th>
			<th width="10%">分类</th>
			<th width="15%">价格</th>
			<th width="7%">销量</th>
			<th width="7%">库存</th>
			<th width="10%">图片</th>
			<th width="10%">标签</th>
			<th width="12%">创建时间</th>
			<!-- <th width="10%">操作</th> -->
		</thead>
	</table>
	<!-- <div class="operation-button">
		<span class="close-float-table">返回</span>
		<span class="add-selected-product" onclick="addProduct(2)">批量添加</span>
	</div> -->
</div>
</div>
</div>


</body>

<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script>
$(".close-float-table").click(function(){
	$(".float-table").css("display","none");
});
var customer_id    = '<?php echo $customer_id;?>';
var customer_id_en = '<?php echo $customer_id_en;?>';
var search_name    = '<?php echo $search_name;?>';
var search_type    = '<?php echo $search_type;?>';
var begintime      = '<?php echo $begintime;?>';
var endtime        = '<?php echo $endtime;?>';
var pagenum 	   = <?php echo $pagenum ?>;
var count          = <?php echo $page ?>;//总页数
var keyid 		   = '<?php echo $keyid;?>';
var comeFrom       = '<?php echo $comeFrom;?>';
var pidArr         = eval('<?php echo json_encode($pid_arr);?>');
var pidStr         = '<?php echo $pid_str; ?>';
var delPidArr      = new Array();	//移除产品id数组
var delPidStr      = '';				//移除产品id字符串
var addPidArr      = new Array();	//添加产品id数组
var addPidStr      = '';				//添加产品id字符串
var showProductLimitStart  = 0,
	showProductLimitEnd    = 9,
	showProductCurrentPage = 1,
	showProductEachPageNum = 10,
	showProductTotalPage   = 1,
	search_pid   = '',
	search_pname = '',
	search_supply_id = '',
	search_ptype = -1,
	search_pfrom = -1,
	search_ptag  = -1;
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
			var url = "setting_delivery.php?pagenum="+p+"&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>";
			
			if( search_name != '' ){
				url += '&search_name='+search_name;
			}
			if( search_type > 0 ){
				url += '&search_type='+search_type;
			}

			document.location= url;
	   }
    });
</script>

<script>
var pagenum = <?php echo $pagenum ?>;
var page    = <?php echo $page ?>;
function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || isNaN(a)){
		return false;
	}else{
		var url = "setting_delivery.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;

		if( search_name != '' ){
			url += '&search_name='+search_name;
		}
		if( search_type > 0 ){
			url += '&search_type='+search_type;
		}
		if(begintime != '')
		{
			url += "&begintime="+begintime;
		}	
		if(endtime != '')
		{
			url += "&endtime="+endtime;
		}
		document.location= url;
	}
}
</script>

<script type="text/javascript">

//货到付款快关
function change_express(obj)
{
	$.post("setting_delivery.php",{"action":"is_pay_on_delivery","obj":obj},function(da){
		if(da.status== "error")
		{
			alert("设置失败");
		}	
	},'json');
}

//搜索
function search()
{
	var url 		 = "setting_delivery.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>";
	var search_type  = $('#search-type option:selected').val();
	var search_name  = $('#search-name').val();
	var begintime    = $("#begintime").val();
	var endtime      = $("#endtime").val();

	if(search_type != -1)
	{
		url += "&search_type="+search_type;
	}	
	if(search_name != '')
	{
		url += "&search_name="+search_name;
	}
	if(begintime != '')
	{
		url += "&begintime="+begintime;
	}	
	if(endtime != '')
	{
		url += "&endtime="+endtime;
	}	
	document.location= url;
}

/*全选*/
function selectall(){
	var all_box = $("#selectall").is(':checked')
	if( all_box ){
		$(".checkbox").prop("checked",true);
	}else{
		$(".checkbox").prop("checked",false);
	}
}

//单选删除
function del(d)
{
	var answer = confirm("删除后不能恢复，是否删除？");
	if(answer)
	{	
		var id = $(d).attr("ids");
		$.post('setting_delivery.php',{id:id,customer_id:customer_id_en,action:'del'},function(res){
			if(res.status == 'ok')
			{ 
				$(d).parent().parent().remove();
			}
			else
			{
				alert("删除失败！");
			}	
		},'json')
	}	
}

//批量删除
function delall(){
	var id=$('input:checkbox[name^=ids]:checked').map(function(){
		return $(this).val();
	}).get().join(",");
	if(!id)
	{
		alert('请选择要删除的项!');
	}
	else
	{
		var answer = confirm("删除后不能恢复，是否删除？");
		if(answer)
		{
			$.post('setting_delivery.php',{id:id,customer_id:customer_id_en,action:'del'},function(res){
				if(res.status == 'ok')
				{ 
					$('input:checkbox[name^=ids]:checked').map(function(){
						$(this).parent().parent().remove();
					})
				}
				else
				{
					alert("删除失败！");
				}	
			},'json')
		}	
	}
}
//输入框按回车键触发搜索
$('body').find('.float-table-search>input').on('keydown',function(){
	if( event.keyCode == 13 ){
		searchProduct();
	}
});


$("#select-all").click(function() { // 全选/取消全部 
	if (this.checked == true) { 
		$(".product-info-checkbox").each(function() { 
			this.checked = true; 
		}); 
	} else { 
		$(".product-info-checkbox").each(function() { 
			this.checked = false;
		}); 
	} 
});

//搜索
function searchProduct(){
	search_pid = $('#search-pid').val();
	search_pname = $('#search-pname').val();
	search_supply_id = $('#search-supply-id').val();
	search_ptype = $('#search-ptype').val();
	search_pfrom = $('#search-pfrom').val();
	search_ptag = $('#search-ptag').val();
	
	showProductCurrentPage = 1;
	showProductLimitStart = 0;
	showProductLimitEnd = showProductEachPageNum - 1;
	
	get_all_product();
}
//上一页
function goToLeftPage(){
	showProductCurrentPage --;
	showProductLimitStart -= showProductEachPageNum;
	showProductLimitEnd -= showProductEachPageNum;
	get_all_product();
}
//下一页
function goToRightPage(){
	showProductCurrentPage ++;
	showProductLimitStart += showProductEachPageNum;
	showProductLimitEnd += showProductEachPageNum;
	get_all_product();
}
//跳转
function goToPage(){
	var pageNum = $('#to-page-num2').val();
	
	if( pageNum < 1 || pageNum > showProductTotalPage || pageNum == showProductCurrentPage ){
		return;
	}
	showProductCurrentPage = pageNum;
	
	showProductLimitStart = (showProductCurrentPage - 1) * showProductEachPageNum;
	
	showProductLimitEnd = showProductLimitStart + showProductEachPageNum - 1;
	
	get_all_product();
}

//添加产品
function addProduct(type){
	var selectedProductId = new Array();
	var willAddproductId = '';
	if( type == 1 ){
		if( arguments[1] != undefined ){
			selectedProductId[0] = arguments[1];
			if( pidArr.indexOf(selectedProductId[0]) == -1 ){
				pidArr.push(selectedProductId[0]);
				willAddproductId = selectedProductId[0];
				addPidArr.push(selectedProductId[0]);
				
				// if( delPidArr.indexOf(selectedProductId[0]) >= 0 ){
					// delPidArr.splice(delPidArr.indexOf(selectedProductId[0]),1);
				// }
			}
		}
	} else {
		var selectedProduct = $('.product-info-checkbox:checked');
		if( selectedProduct.length == 0 ){
			alert('请选择产品！');
			return false;
		}
		selectedProduct.each(function(i) { 
			selectedProductId[i] = $(this).val();
			if( pidArr.indexOf(selectedProductId[i]) == -1 ){
				pidArr.push(selectedProductId[i]);
				willAddproductId += selectedProductId[i]+',';
				addPidArr.push(selectedProductId[i]);
				
				// if( delPidArr.indexOf(selectedProductId[i]) >= 0 ){
					// delPidArr.splice(delPidArr.indexOf(selectedProductId[i]),1);
				// }
			}
		});
		if( willAddproductId.length > 0 ){
			willAddproductId = willAddproductId.slice(0,-1);
		}
	}
	pidStr = pidArr.join(',');
	addPidStr = addPidArr.join(',');
	// delPidStr = delPidArr.join(',');
	$.ajax({
		url: 'ajax_handle.php?customer_id='+customer_id_en,
		dataType: 'json',
		type: 'post',
		data: {
			op : 'select_add_activitie_product',
			willAddproductId : willAddproductId
		},
		success: function(res){
			if( res ){
				var date = Array();
				var html = '';
				for(i in res)
				{
				    date[i] = res[i]['id'];
				}	
				
				$.post('setting_delivery.php?customer_id='+customer_id_en,{"action":'add_pro','date':date},function(da){
					
					if(da.status == 'ok')
					{
						var result = da.data;
						for( i in result )
						{
							html += "<tr><td><input type='checkbox'class='checkbox' name='ids' value='"+result[i]['id']+"'></td>";
							html += "<td><img src='"+result[i]['default_imgurl']+"'  /></td>";
							html += "<td>"+result[i]['name']+"</td>";
							html += "<td>"+result[i]['cname']+"</td>";
							html += "<td>"+result[i]['orgin_price']+"</td>";
							html += "<td>"+result[i]['now_price']+"</td>";
							html += "<td>"+result[i]['for_price']+"</td>";
							html += "<td>"+result[i]['storenum']+"</td>";
							html += "<td>"+result[i]['createtime']+"</td>";
							html += "<td><a href='javascript:void(0)' style='padding:3px 10px; color: #fff;background: #06a7e1;border-radius: 3px;' class='del' onclick='del(this)' ids='"+result[i]['id']+"'>删除</a></td></tr>";
						}
						$('.dfds').append(html);
						
					}
					else
					{
						alert('添加失败')
					}	
				},'json');
				
				
			}
			$('.float-table').fadeOut();
		},
		error: function(err){
			alert(err);
		}
	});
}

//产品获取
function add_product()
{
	get_all_product();
	$('.float-table').fadeIn();
}
//获取产品
function get_all_product()
{
	$.ajax({
		url: 'ajax_handle.php?customer_id='+customer_id_en,
		dataType: 'json',
		type: 'post',
		data: {
			op : 'get_all_product',
			search_pid : search_pid,
			search_pname : search_pname,
			search_supply_id : search_supply_id,
			search_ptype : search_ptype,
			search_pfrom : search_pfrom,
			search_ptag : search_ptag,
			pid_str : pidStr,
			del_pid_str : delPidStr,
			limitstart : showProductLimitStart,
			limitend : showProductEachPageNum,
			is_count : 1
		},
		success: function(data){
			var productLen = data['product'].length,
				html = '',
				html_p = '';
			
			for( i in data['product'] ){
				var tag = '';
				
				html +='<tr class="product-list">';
				html +='	<td><input type="checkbox" class="product-info-checkbox" value="'+data['product'][i]['id']+'" /></td>';
				html +='	<td>'+data['product'][i]['id']+'</td>';
				html +='	<td>'+data['product'][i]['name']+'</td>';
				html +='	<td>'+data['product'][i]['type_name']+'</td>';
				html +='	<td><span style="display:block;">原价：'+data['product'][i]['orgin_price']+'</span><span style="display:block;">现价：'+data['product'][i]['now_price']+'</span></td>';
				html +='	<td>'+data['product'][i]['sell_count']+'</td>';
				html +='	<td>'+data['product'][i]['storenum']+'</td>';
				html +='	<td><img src="'+data['product'][i]['default_imgurl']+'" style="width: 100%;"></td>';
				if( data['product'][i]['ishot'] == 1 ){
					tag += '热卖/';
				}
				if( data['product'][i]['isnew'] == 1 ){
					tag += '新品/';
				}
				if( data['product'][i]['is_free_shipping'] == 1 ){
					tag += '包邮/';
				}
				if( data['product'][i]['is_virtual'] == 1 ){
					tag += '虚拟产品/';
				}
				if( data['product'][i]['is_currency'] == 1 ){
					tag += '购物币/';
				}
				if( tag != '' ){
					tag = tag.slice(0,-1);
				}
				html +='	<td>'+tag+'</td>'
				html +='	<td>'+data['product'][i]['createtime']+'</td>';
				//html +='	<td><span class="delivery-button" style="padding:3px 10px;" onclick="addProduct(1,'+data['product'][i]['id']+')">选择</span></td>';
				html +='</tr>';
			}
			if( productLen > 0){
				//翻页
				showProductTotalPage = Math.ceil(data['count'] / showProductEachPageNum);
				html_p +='<div class="page-box2">';
				html_p +='	<span class="data-num">共计'+data['count']+'条记录</span>';
				// html_p +='	<span class="show-data-num">每页<input type="text" id="show-data-num" width="25" value="'+showProductEachPageNum+'" />条</span>';
				// html_p +='	<span class="delivery-button show-data-num-btn">确定</span> ';
				if( showProductCurrentPage > 1 ){	//当前是第一页不显示上一页
					html_p +='	<span class="delivery-button page-left" onclick="goToLeftPage()">上一页</span> ';
				}
				html_p +='	<span class="current-page">当前第'+showProductCurrentPage+'页，共'+showProductTotalPage+'页</span> ';
				if( showProductCurrentPage < showProductTotalPage ){	//当前是最后一页不显示下一页
					html_p +='	<span class="delivery-button page-right" onclick="goToRightPage()">下一页</span> ';
				}
				html_p +='	<input type="text" id="to-page-num2" width="25" value="'+showProductCurrentPage+'" >页 ';
				html_p +='	<span class="delivery-button go-page-btn" onclick="goToPage()">跳转</span> ';
				html_p +='</div>';
			}
			
			$('.product-list').remove();
			$('.page-box2').remove();
			$('.list_table').append(html);
			$(".list_table").after(html_p); 
		},
		error: function(err){
			alert('获取产品出错！');
		}
	});
}

//正整数
function clearInt(obj){
	if(obj.value.length==1){obj.value=obj.value.replace(/[^1-9]/g,'')}else{obj.value=obj.value.replace(/\D/g,'')}
}
</script>
</html>

