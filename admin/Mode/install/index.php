<?php
	header("Content-type: text/html; charset=utf-8"); 
	require('../../../../weixinpl/config.php');
	$customer_id = passport_decrypt($customer_id);
	require('../../../../weixinpl/back_init.php');
	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	_mysql_query("SET NAMES UTF8");
	require('../../../../weixinpl/proxy_info.php');
//==================================开启安装平台
	$query = "select * from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
	$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
	$name="";
	$isOpenInstall=0;	//是否在个人中心开启安装预约 

	while ($row = mysql_fetch_object($result)) {
		$name=$row->name;
		$isOpenInstall=$row->isOpenInstall; //是否在个人中心开启安装预约

		
	}
	//安装平台,渠道开通与不开通
	$is_isinstall=0;//渠道取消安装平台功能
	$ins_count=0;
	$sp_query="select count(1) as ins_count from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='安装平台' and c.id=cf.column_id";
	$sp_result = _mysql_query($sp_query) or die('Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($sp_result)) {
	   $ins_count = $row->ins_count;
	   break;
	}
	if($ins_count>0){
	   $is_isinstall=1;
	}
//==================================开启安装平台
	
	$show_index = 0;
	
	if(!empty($_GET["show_index"])){
		$show_index = $_GET["show_index"];
	}
	$query = "select id , reward1,reward2,reward3,weight,punishment,rewardcomment,star1,star2,star3,star4,star5,quitscore,reward_account,registcomment from weixin_install_settings where isvalid = true and customer_id = ".$customer_id;
	$result = _mysql_query($query) or die("L104 query error : ".mysql_error());
	$setting_id = 0;
	$reward1 = 0 ;
	$reward2 = 0 ;
	$reward3 = 0 ;
	$weight = 0.4 ;
	$punishment = "";
	$rewardcomment = "";
	$star1 = 0 ;
	$star2 = 0 ;
	$star3 = 0 ;
	$star4 = 0 ;
	$star5 = 0 ;
	$quitscore = 0;
	$reward_account = 0;
	$registcomment = "";
	if($row = mysql_fetch_object($result)){
		$setting_id = $row->id;
		$reward1 = $row->reward1;
		$reward2 = $row->reward2;
		$reward3 = $row->reward3;
		$weight = $row->weight;
		$punishment = $row->punishment;
		$rewardcomment = $row->rewardcomment;
		$star1 = $row->star1;
		$star2 = $row->star2;
		$star3 = $row->star3;
		$star4 = $row->star4;
		$star5 = $row->star5;
		$quitscore = $row->quitscore;
		$reward_account = $row->reward_account;
		$registcomment = $row->registcomment;
	}
	$new_baseurl = "http://".$http_host;  
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>安装平台</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_liuliang/content.css"><!--调用内容CSS属性--> 
<link rel="stylesheet" type="text/css" href="../../../common/css_liuliang/content<?php echo $theme; ?>.css"><!--内容CSS配色·草绿-->
<link rel="stylesheet" type="text/css" href="../../../common/css_liuliang/flow<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../../common/js/jquery.min.js" ></script>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<!--日期插件JS-->
<!--
<link href="css/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" /> -->

<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.core.js"></script>
<!--
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script> -->
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<style type="text/css">
	.detail_div{
		margin-left:0px!important;
		margin-right:0px!important;
	}
	.WSY_content{
		margin-left :0px!important;
		margin-top:0px!important;
	}
	table#WSY_t1 th{
		line-height:30px;
	}
	.WSY_table_header th{
		color:#fff;
	}
	.uploader input[type=file]{
		position:absolute;
		top:0;
		right:0;
		bottom:0;
		border:0;
		padding:0;
		margin:0;
		height:30px;
		cursor:pointer;
		opacity:0;
	}
	.WSY_text_input .WSY_button{
		margin-right:50%;
	}
	.textareaclass{
	  width: 500px;
	  height: 200px;
	  border: solid 1px rgb(219, 217, 217);
	}
	/*.WSY_generate_top{
	  padding: 20px;
	  border: solid 1px rgb(211, 206, 206);
	  margin-bottom: 30px;
	  border-radius: 5px;
	}*/
	.WSY_generate_top dl dd input[type="text"]{
		width:80px;
	}
	.WSY_generate_top{padding-top:20px}
	.sbtn{
	  width: 100px;
	  line-height: 23px;
	  border-radius: 5px;
	  border: solid 1px rgb(192, 187, 187);
	}
	tr td{
		text-align:center;
	}
	.div_idimg{
	  width: 48%;
	  line-height: 30px;
	  text-align: left;
	  
	  float: left;
	}
	.div_detail{
	  width: 50%;
	  height: 100%;
	  line-height: 50px;
	  text-align: left;
	  float:left;
	}
	.rev_eng_list{
		line-height:40px;
		padding:15px;
	}
	.detail_border{
	  border: solid 1px rgb(181, 178, 178);
	  border-radius: 10px;
	  padding: 10px;
	  margin-top: 30px;
	  overflow: hidden;
	}
	.input_border{
	  border: solid 1px #ccc;
	  line-height: 10px;
	  padding: 5px 0;
	  padding-left: 5px;
	  border-radius: 2px;
	}
	.WSY_allocation, .WSY_generate{padding:0 30px;}
	.WSY_generate_con{border:none;margin-top:0px;}
	.WSY_sales_dl02 dd select{padding:1px 1px;border:solid 1px #ccc;border-radius:2px;}
	.WSY_button{margin-top:0px;}
	.WSY_sales_dl01 dd input[type="text"]{width:100px;}
</style>
</head>

<body>
	<!--内容框架开始-->
	<div class="WSY_content">

		<!--列表内容大框开始-->
		<div class="WSY_columnbox"  style="min-height:500px">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">通用配置</a>
					<a>奖励设置</a>
					<a>技师列表</a>
					<a>安装工单</a>
					<a>安装规范</a>
					<a>导入工单</a>
				</div>
			</div>
			
			<script type="text/javascript">
			//控制当前所显示的选项卡页
			var index = "<?php echo $show_index;?>";
			/*
			$(".WSY_data").hide();
			$(".WSY_data:eq("+index+")").show();*/
			$(".WSY_columnnav a").removeClass("white1");
			$(".WSY_columnnav a:eq("+index+")").addClass("white1");
			
			</script>
			<!--列表头部切换结束-->
		<?php if($show_index == 0){
			?>
		<!-- 配置 begin -->
        <div class="WSY_data">
			<form method="post" id="frm_settings" action="save_index.php?customer_id=<?php echo passport_encrypt($customer_id);?>&show_index=<?php echo $show_index;?>">
				<div class="WSY_generate_top" style="  margin: auto;margin-left:10px">
						<div class="WSY_remind_main">
						<dl class="WSY_remind_dl02"> 
							<dt style="line-height:20px;font-weight:normal;" class="WSY_left">客户端个人中心开启安装平台 </dt>
							<dd>
								<?php if($isOpenInstall==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 27px;">开</p>
									<li onclick="change_sendstatus(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="change_sendstatus(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>								
								</ul>
								<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 13px;">关</p>
									<li onclick="change_sendstatus(0)" <?php if($isOpenInstall!=1){?>class="WSY_bot"<?php }?> style="display: none; left: 30px;"></li>
									<span onclick="change_sendstatus(1)" <?php if($isOpenInstall!=1){?>class="WSY_bot2"<?php }?> style="display: block; left: 30px;"></span>								
								</ul>					 			
								<?php } ?>
								<span style="float: left;margin: -16px 210px;color: #888;"></span>
							</dd>						
							<input type="hidden" name="isOpenInstall" id="isOpenInstall" value="<?php echo $isOpenInstall; ?>" />
						</dl>
						<input type=hidden name="agent_price" id="agent_price" value="<?php echo $agent_price ?>" />
					</div> 
				</div>
				<div class="WSY_generate">
					<div class="WSY_generate_top">
						<dl>
							<dt>推荐返佣：</dt>
							<dd><input type="text" name="reward_account" id="reward_account" value="<?php echo $reward_account;?>" autocomplete="off" /><?php echo OOF_T ?></dd>
						</dl>
						<dl>
							<dt>普通奖励：</dt>
							<dd><input type="text" name="reward1" id="reward1" value="<?php echo $reward1;?>" autocomplete="off" /></dd>
						</dl>
						<dl>
							<dt>黑铁奖励：</dt>
							<dd><input type="text" name="reward2" id="reward2" value="<?php echo $reward2;?>" autocomplete="off" /></dd>
						</dl>
						 <dl>
							<dt>青铜奖励：</dt>
							<dd><input type="text" name="reward3" id="reward3" value="<?php echo $reward3;?>" autocomplete="off" /></dd>
						</dl>
					</div>
					<div class="WSY_generate_top">
						<dl>
							<dt>技师退单扣分：</dt>
							<dd><input type="text"  name="quitscore" id="quitscore" value="<?php echo $quitscore;?>" autocomplete="off" />分</dd>
						</dl>
					</div>
					<div class="WSY_generate_top">
						<dl>
							<dt>权重：</dt>
							<dd><input type="text"  name="weight" id="weight" value="<?php echo $weight;?>" autocomplete="off" /></dd>
						</dl>
					</div>
					<div class="WSY_generate_top">
						<dl>
							<dt>一星积分：</dt>
							<dd><input type="text"  name="star1" id="star1" value="<?php echo $star1;?>" autocomplete="off" ></dd>
						</dl>
						<dl>
							<dt>二星积分：</dt>
							<dd><input type="text"  name="star2" id="star2" value="<?php echo $star2;?>" autocomplete="off" ></dd>
						</dl>
						<dl>
							<dt>三星积分：</dt>
							<dd><input type="text"  name="star3" id="star3" value="<?php echo $star3;?>" autocomplete="off" ></dd>
						</dl>
						<dl>
							<dt>四星积分：</dt>
							<dd><input type="text"  name="star4" id="star4" value="<?php echo $star4;?>" autocomplete="off" ></dd>
						</dl>
						<dl>
							<dt>五星积分：</dt>
							<dd><input type="text"  name="star5" id="star5" value="<?php echo $star5;?>" autocomplete="off" ></dd>
						</dl>
					</div>
					<!--
					<dl>
						<dt style="line-height:40px">惩罚协议：</dt>
						<dd><textarea name="punishment" class="textareaclass" id="punishment"><?php echo $punishment;?></textarea></dd>
					</dl>
					<dl>
						<dt style="line-height:40px">安装费标准：</dt>
						<dd><textarea name="rewardcomment" class="textareaclass" id="rewardcomment"><?php echo $rewardcomment;?></textarea></dd>
					</dl>
					-->
					<dl>
						<dt style="line-height:40px;font-size:14px">注册协议：</dt>
						<dd><textarea name="registcomment" class="textareaclass" id="registcomment"><?php echo $registcomment;?></textarea></dd>
					</dl>
					<div class="WSY_text_input01 WSY_text_input03" style="margin-top:20px;">
						<div class="WSY_text_input"><button class="WSY_button" type="button"  id="btn_savesettings">保存修改</button></div>
					</div>
					<input type="hidden" name="setting_id" value="<?php echo $setting_id;?>"/>
				</div>
			</form>
        </div>
		<?php } ?>
        <!-- 配置 end -->
		<?php if($show_index == 1){ ?>
		<!-- 奖励设置 begin -->
		<div class="WSY_data" style=" padding: 30px;">
		<form method="post" action="save_index.php?customer_id=<?php echo passport_encrypt($customer_id);?>&show_index=<?php echo $show_index;?>" id="frm_reward">
                <div class="WSY_generate_con" style="width:650px">
					<?php
						$query = "select id,score,reward from weixin_install_reward_settings where isvalid = true and customer_id=".$customer_id;
						$result = _mysql_query($query) or die("L240 query error : ".mysql_error());
						$rows = mysql_num_rows($result);
						if($rows > 0){
							$index = 1;
							while($row =  mysql_fetch_object($result)){
								
					?>
                	<div class="WSY_generate_div">
                        <dl class="WSY_generate01">
                            <dt>月积分：</dt>
                            <dd><input class="nums create_m" type="text" name="score[]"  value="<?php echo $row->score;?>" autocomplete="off" >分</dd>
                        </dl>
                        <dl class="WSY_generate02">
                            <dt>奖励：</dt>
                            <dd><input class="nums create_count" type="text" name="reward[]" value="<?php echo $row->reward;?>" autocomplete="off" ><span><?php echo OOF_T ?></span></dd>
                        </dl>
                        <div class="WSY_generate_icon">
                        	<a class="WSY_generate_icon001"></a>
							<?php if($index == $rows){ ?>
							<a class="WSY_generate_icon002"></a>
							<?php }?>
                        </div>
                    </div>
					<?php 
						$index ++;
					}
						}else{
							
						?>
                    <div class="WSY_generate_div">
                        <dl class="WSY_generate01">
                            <dt>月积分：</dt>
                            <dd><input class="nums create_m" type="text" name="score[]"  value="" autocomplete="off" >分</dd>
                        </dl>
                        <dl class="WSY_generate02">
                            <dt>奖励：</dt>
                            <dd><input class="nums create_count" type="text" name="reward[]" value="" autocomplete="off" ><span><?php echo OOF_T ?></span></dd>
                        </dl>
                        <div class="WSY_generate_icon">
                        	<a class="WSY_generate_icon001"></a>
                        </div>
                    </div>
                    <div class="WSY_generate_div">
                        <dl class="WSY_generate01">
                            <dt>月积分：</dt>
                            <dd><input class="nums create_m" type="text" name="score[]"  value="" autocomplete="off" >分</dd>
                        </dl>
                        <dl class="WSY_generate02">
                            <dt>奖励：</dt>
                            <dd><input class="nums create_count" type="text" name="reward[]" value="" autocomplete="off" ><span><?php echo OOF_T ?></span></dd>
                        </dl>
                        <div class="WSY_generate_icon">
                        	<a class="WSY_generate_icon001"></a>
                        	<a class="WSY_generate_icon002"></a>
                        </div>
                    </div>
						<?php }?>
                </div>
                <div class="WSY_text_input01 WSY_text_input05" style="margin-top:100px;">
                    <div class="WSY_text_input"><button class="WSY_button" type="button" onclick="checkReward();">保存配置</button></div>
                </div>
            </div>
		</form>
		</div> 
        <!-- 奖励设置 end -->
		<?php } ?>
		
		<?php if($show_index == 2){
					$pagenum = 1;

					if(!empty($_GET["pagenum"])){
					   $pagenum = $configutil->splash_new($_GET["pagenum"]);
					}
					
					$p_name = "";
					if(!empty($_GET["name"])){
					   $p_name = $configutil->splash_new($_GET["name"]);
					}
					
					$p_type = 0 ;
					if(!empty($_GET["type"])){
					   $p_type = $configutil->splash_new($_GET["type"]);
					}
					
					$p_status = 0;
					if(!empty($_GET["status"])){
					   $p_status = $configutil->splash_new($_GET["status"]);
					}
					
					$start = ($pagenum-1) * 20;
					$end = 20;
					
					$query = "select count(1) rowcount from weixin_install_engineer where isvalid = true and  customer_id=".$customer_id;
					if(!empty($p_name)){
						$query = $query . " and name like '".$p_name."' or phone like '".$p_name."' ";
					}
					if(!empty($p_type)){
						if($p_type == -1){
							$query = $query . " and status = ".$p_type;
						}else{
							$query = $query . " and status = ".($p_type-1);
						}
						
					}
					if(!empty($p_status)){
						$query = $query . " and isvailable = ".($p_status-1);
					}
					$result = _mysql_query($query) or die("L231 query error : ".mysql_error());
					$rcount = mysql_result($result,0,0);
					$page = ceil($rcount/$end);
					
					
				?>
		<!-- 推广员列表 begin -->
		<div class="WSY_data">
			<form method="get" action="index.php">
			<div class="WSY_sales">
				<dl class="WSY_sales_dl01" >
					<dt>姓名/手机号：</dt>
					<dd><input type="text" name="name" id="p_name" value="<?php echo $p_name; ?>" autocomplete="off"  class="test-style width150"></dd>
				</dl>
				<dl class="WSY_sales_dl02">
					<dt>状态：</dt>
					<dd>
						<select name="type" id="p_type">
							<option value="0" <?php echo $p_type == 0 ? "selected" : "" ;?>>所有</option>
							<option value="2" <?php echo $p_type == 2 ? "selected" : "" ;?>>已审核</option>
							<option value="1" <?php echo $p_type == 1 ? "selected" : "" ;?>>未审核</option>
							<option value="-1" <?php echo $p_type == -1 ? "selected" : "" ;?>>已禁用</option>
						</select>
					</dd>
				</dl>
				<dl class="WSY_sales_dl02">
					<dt>接单状态：</dt>
					<dd>
						<select name="status" id="p_status">
							<option value="0" <?php echo $p_status == 0 ? "selected" : "" ;?>>所有</option>
							<option value="2" <?php echo $p_status == 2 ? "selected" : "" ;?>>可接单</option>
							<option value="1" <?php echo $p_status == 1 ? "selected" : "" ;?>>不接单</option>
						</select>
					</dd>
				</dl>
				
				<dl class="WSY_sales_dl02">
					<input type="hidden" name="show_index" value="2"/>
					<input type="hidden" name="customer_id" value="<?php echo passport_encrypt($customer_id);?>"/>
					<input type="hidden" name="pagenum" value="<?php echo $pagenum;?>"/>
					<!--<button type="submit" class="sbtn">查询</button>-->
					<li class="WSY_bottonliss"><input type="submit" value="查询"></li>
				</dl>
				</form>
			</div>
				  
			<!--表格开始-->
			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			  <thead class="WSY_table_header">
				<th width="5%">编号</th>
				<th width="5%">姓名</th>
				<th width="8%">手机号</th>
				<th width="5%">积分/星级</th>
				<!--
				<th width="10%">身份证号</th>
				<th width="15%">地址</th> -->
				<th width="5%">是否接单</th>
				<th width="10%">完成数/取消数/未完成</th>
				<th width="10%">邀请人</th>
				<th width="10%">累积奖励</th>
				<th width="10%">申请时间</th>
				<th width="5%">状态</th>
				<th width="10%">操作</th>
			  </thead>
			  <?php  
				
					
					$query = "select e.id,e.name,e.isvailable,e.totalscore,e.phone,e.status,e.status_remark,e.createtime,u.parent_id ,e.user_id ,
					e.idimg,e.idnum,e.location_p,e.location_c,e.location_a,e.address,e.distance 
						from weixin_install_engineer e join weixin_users u on e.user_id = u.id where e.isvalid = true and  e.customer_id=".$customer_id;
					if(!empty($p_name)){
						$query = $query . " and e.name like '%".$p_name."%' or e.phone like '%".$p_name."%' ";
					}
					if(!empty($p_type)){
						if($p_type == -1){
							$query = $query . " and status = ".$p_type;
						}else{
							$query = $query . " and status = ".($p_type-1);
						}
					}
					if(!empty($p_status)){
						$query = $query . " and e.isvailable = ".($p_status-1);
					}
					$query = $query . " order by e.id desc ";
					$query = $query . " limit ".$start." , ".$end;
					//echo $query;
					$result = _mysql_query($query) or die("L310 query error : ".mysql_error());
					$pagerow = mysql_num_rows($result);
					while($row = mysql_fetch_object($result)){
						$id = $row->id;
						$status = $row->status;
						$user_id = $row->user_id;
						$statusStr = "";
						if($status == 0){
							$statusStr = "未审核";
						}else if($status == 1){
							$statusStr = "已审核";
						}else if($status == -1){
							$statusStr = "已禁用";
						}
						$name = $row->name;
						$phone = $row->phone;
						$idimg = $row->idimg;
						$isvailable = $row->isvailable;
						$totalscore = $row->totalscore;
						$createtime = $row->createtime;
						$idnum = $row->idnum;
						$location_p = $row->location_p;
						$location_c = $row->location_c;
						$location_a = $row->location_a;
						$address = $row->address;
						$distance = $row->distance;
						
			  ?>
			  <tr>
				<td><a href="javascript:showDetail(<?php echo $id;?>)" style="color:blue"><?php echo $id;?></a></td>
				<td><?php echo $name;?>
				
				</td>
				<td><?php echo $phone;?></td>
				<td>
					<?php 
					$totalscore = $row->totalscore;
					$starStr = "";
					if($totalscore >= $star5){
						$starStr = "五星";
					}else if($totalscore >= $star4){
						$starStr = "四星";
					}else if($totalscore >= $star3){
						$starStr = "三星";
					}else if($totalscore >= $star2){
						$starStr = "二星";
					}else{
						$starStr = "一星";
					}
					echo $totalscore."/".$starStr;
				?>
				</td>
				
				<td><?php echo ($isvailable == 1 ? "可接单": "不接单");?></td>
				<td>
				<?php 
					$query_total = "select count(1) from weixin_install_reservation_engineer where isvalid = true and engineer_id = ".$id." and status = 3";
					$result_total = _mysql_query($query_total) or die("L352 query error : ".mysql_error());
					$total = mysql_result($result_total,0,0);
					
					$query_cancel = "select count(1) from weixin_install_reservation_engineer where isvalid = true and engineer_id = ".$id." and status = 2 ";
					$result_cancel = _mysql_query($query_cancel) or die("L356 query error : ".mysql_error());
					$cancel = mysql_result($result_cancel,0,0);
					
					$query_doing = "select count(1) from weixin_install_reservation_engineer where isvalid = true and engineer_id = ".$id." and status = 1 ";
					$result_doing = _mysql_query($query_doing) or die("L360 query error : ".mysql_error());
					$doing = mysql_result($result_doing,0,0);
					echo $total."/".$cancel."/".$doing;
				?>
				</td>
				<td><?php 
					$parent_id = $row->parent_id;
					$query_par = "select weixin_name , name from weixin_users where isvalid = true and id = ".$parent_id." and customer_id=".$customer_id;
					$result_par = _mysql_query($query_par) or die("L368 query error : ".mysql_error());
					$parent_weixin_name = mysql_result($result_par,0,0);
					$parent_name = mysql_result($result_par,0,1);
					echo $parent_weixin_name ."(".$parent_name.")";
				?></td>
				<td><?php 
					//找到商家默认的返佣会员卡
					$query_card = "select shop_card_id from weixin_commonshops where isvalid = true and customer_id = ".$customer_id;
					$result_card = _mysql_query($query_card) or die("L383 query error : ".mysql_error());
					$shop_card_id = mysql_result($result_card,0,0);
					
					if($shop_card_id > 0){
						//找到技师所对应的会员卡号
						$query_member = "select id from weixin_card_members where card_id = ".$shop_card_id." and user_id = ".$user_id." and isvalid = true ";
						$result_member = _mysql_query($query_member) or die("L389 query error : ".mysql_error());
						$card_member_id = mysql_result($result_member,0,0);
						
						$sum_cost = 0;
						if($card_member_id > 0){
							//查找总安装佣金记录
							$query_recharge = "select sum(cost) from weixin_card_recharge_records where isvalid = true and recharge_type = 1 and card_member_id = ".$card_member_id;
							$result_recharge = _mysql_query($query_recharge) or die("L389 query error : ".mysql_error());
							$sum_cost = mysql_result($result_recharge,0,0);
						}
						echo $sum_cost;
					}
					
				?></td>
				<td><?php echo $createtime;?></td>
				<td><?php echo $statusStr; 
					if($status == -1){
						echo "<br/>(".$row->status_remark.")";
					}
				?></td>
				<td class="WSY_t4" id="WSY_t4">
					<a href="javascript:showDetail(<?php echo $id;?>)" title="详细信息"><img src="../../../common/images_V6.0/operating_icon/icon31.png"></a>
					<?php if($status == 0){ ?>
						<a href="javascript:engineer_state('<?php echo $row->id;?>',2)" title="通过"><img src="../../../common/images_V6.0/operating_icon/icon07.png"></a>
						<a href="javascript:engineer_state('<?php echo $row->id;?>',0)" title="拒绝"><img src="../../../common/images_V6.0/operating_icon/icon08.png"></a>
						<a href="javascript:engineer_state('<?php echo $row->id;?>',3)" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>
					<?php }?>
					<?php if($status == 1){ ?>
						<a href="javascript:engineer_state('<?php echo $row->id;?>',1)" title="暂停"><img src="../../../common/images_V6.0/operating_icon/icon08.png"></a>
						<a href="javascript:minusScore(<?php echo $id;?>)" title="扣分"><img src="../../../common/images_V6.0/operating_icon/icon25.png"></a>
					<?php }?>
					<?php if($status == -1){ ?>
						<a href="javascript:engineer_state('<?php echo $row->id;?>',2)" title="通过"><img src="../../../common/images_V6.0/operating_icon/icon07.png"></a>
					<?php }?>
					
				</td>
			  </tr>
			  <tr id="row_<?php echo $id;?>" style="display:none">
				<td colspan="11" style="padding:10px">
					<div class="detail_border" style="width:100%;height:200px;">
						<div class="div_detail">
							姓名 : <?php echo $name;?>&nbsp;&nbsp;&nbsp;&nbsp; 联系电话：<?php echo $phone;?>&nbsp;&nbsp;&nbsp;&nbsp;身份证号：<?php echo $idnum;?><br/>
							地址：<?php echo $location_p."".$location_c."".$location_a."".$address?>&nbsp;&nbsp;&nbsp;&nbsp;接单距离：<?php echo $distance;?>KM<br/>
						</div>
						<div class="div_idimg">
							<img src="/weixinpl<?php echo $idimg;?>" style="width:300px;height:200px"/>
						</div>
					</div>
				</td>
			  </tr>
			  <tr id="row_score_<?php echo $id;?>" style="display:none">
				<td colspan="11" style="padding:10px">
					<div class="detail_border" style="width:100%;height:200px;">
						<div class="div_detail" style="width:auto;margin-right:50px;line-height:150px">
							扣分：<input type="text" style="border:solid 1px gray; display:inline;margin:none" name="minusscore" id="minusscore_<?php echo $id;?>" style="width:100px"/><br/>
						</div>
						<div class="div_idimg">
							扣分原因：<textarea style="border:solid 1px gray; display:inline;margin:none;width:300px;height:150px; vertical-align: middle;" name="minuscomment" id="minuscomment_<?php echo $id;?>" ></textarea><br/>
							<button type="button" onclick="minus('<?php echo $id;?>')">确定扣分</button>
						</div>
					</div>
				</td>
			  </tr>
			  
					<?php } ?>	
			</table>
			<!--表格结束-->
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
		<!-- 销售 end -->
		<?php } ?>
		
		<?php if($show_index == 3){
					
			$pagenum = 1;

			if(!empty($_GET["pagenum"])){
			   $pagenum = $configutil->splash_new($_GET["pagenum"]);
			}
			$p_type = 0;
			if(!empty($_GET["type"])){
			   $p_type = $configutil->splash_new($_GET["type"]);
			}
			$p_reservation_num = "";
			if(!empty($_GET["reservation_num"])){
			   $p_reservation_num = $configutil->splash_new($_GET["reservation_num"]);
			}
			$p_order_num = "";
			if(!empty($_GET["order_num"])){
			   $p_order_num = $configutil->splash_new($_GET["order_num"]);
			}
			$p_ordertype = 0;
			if(!empty($_GET["ordertype"])){
			   $p_ordertype = $configutil->splash_new($_GET["ordertype"]);
			}
			$p_begin_time = "";
			if(!empty($_GET["begin_time"])){
			   $p_begin_time = $configutil->splash_new($_GET["begin_time"]);
			}
			$p_end_time = "";
			if(!empty($_GET["end_time"])){
			   $p_end_time = $configutil->splash_new($_GET["end_time"]);
			}

			$start = ($pagenum-1) * 20;
			$end = 20;
			
			$query = "select count(1) rowcount from weixin_install_reservation where isvalid = true and customer_id = ".$customer_id."";
			$query = $query." and ordertype = '".$p_ordertype."'";
			if(!empty($p_type)){
				$query  = $query." and status = ".($p_type-1);
			}
			if(!empty($p_reservation_num)){
				$query  = $query." and reservation_num like '".$p_reservation_num."' ";
			}
			if(!empty($p_order_num)){
				$query  = $query." and order_num like '".$p_order_num."' ";
			}
			if(!empty($p_begin_time)){
				$query = $query." and UNIX_TIMESTAMP(createtime)>=".strtotime($p_begin_time);
			}
			if(!empty($p_end_time)){
				//$p_end_time = $p_end_time;
				$query = $query." and UNIX_TIMESTAMP(createtime)<=".strtotime($p_end_time." 23:59");
			}
			$result = _mysql_query($query) or die("L383 query error : ".mysql_error());
			$rcount = mysql_result($result,0,0);
			$page = ceil($rcount/$end);
		?>
		<!-- 订单列表 begin -->
		<div class="WSY_data">
			<div class="WSY_sales" style="overflow:hidden;width:100%;">
				<form action="index.php">
				<dl class="WSY_sales_dl02">
					<dt>工单来源：</dt>
					<dd>
						<select name="ordertype" id="p_ordertype">
							<option value="0" <?php echo $p_ordertype == 0 ? "selected" : "";?>>商城订单</option>
							<option value="1" <?php echo $p_ordertype == 1 ? "selected" : "";?>>导入工单</option>
						</select>
					</dd>
				</dl>
				<dl class="WSY_sales_dl02">
					<dt>工单状态：</dt>
					<dd>
						<select name="type" id="p_type">
							<option value="0" <?php echo $p_type == 0 ? "selected" : "";?>>所有</option>
							<option value="1" <?php echo $p_type == 1 ? "selected" : "";?>>待放单</option>
							<option value="2" <?php echo $p_type == 2 ? "selected" : "";?>>指定未接受</option>
							<option value="3" <?php echo $p_type == 3 ? "selected" : "";?>>进行中</option>
							<option value="4" <?php echo $p_type == 4 ? "selected" : "";?>>安装完成</option>
							<option value="5" <?php echo $p_type == 5 ? "selected" : "";?>>回访完成</option>
							<option value="6" <?php echo $p_type == 6 ? "selected" : "";?>>结算完成</option>
						</select>
					</dd>
				</dl>
				<dl class="WSY_sales_dl01">
					<dt>安装工单号：</dt>
					<dd><input type="text" name="reservation_num" id="p_reservation_num" value="<?php echo $p_reservation_num; ?>"  autocomplete="off"  class="test-style width150"></dd>
				</dl>
				<dl class="WSY_sales_dl01">
					<dt>商城订单号：</dt>
					<dd><input type="text" name="order_num" id="p_order_num" value="<?php echo $p_order_num; ?>" autocomplete="off"  class="test-style width150"></dd>
				</dl>
				<dl class="WSY_sales_dl01">
					<dt>下单时间：</dt>
					<dd><input type="text" name="begin_time" id="p_begin_time" value="<?php echo $p_begin_time;?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'});" autocomplete="off"  class="test-style width150">
					-- <input type="text" name="end_time" id="p_end_time" value="<?php echo $p_end_time;?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'});" autocomplete="off"  class="test-style width150">
					</dd>
				</dl>
				
				<dl class="WSY_sales_dl02" style="display:block;float:left;">
					<dd>
					<input type="hidden" name="customer_id" value="<?php echo passport_encrypt($customer_id);?>"/>
					<input type="hidden" name="show_index" value="3"/>
					<input type="hidden" name="pagenum" value="<?php echo $pagenum;?>"/>
					<!--<button type="submit" class="sbtn">查询</button>-->
					<li class="WSY_bottonliss"><input type="submit" value="查询"></li>
					</dd>
					
				</dl>
				<dl style="float:right">
					<dd>
					<button class="WSY_button" type="button" onclick="checkExportSelf()" style="margin-right: 40px;width:150px">导出自行安装单</button>
					<button class="WSY_button" type="button" onclick="checkExport()" style="margin-right: 40px;">导出工单</button>
					</dd>
				</dl>
				<form>
			</div>
				  
			<!--表格开始-->
			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
			  <thead class="WSY_table_header">
				<th width="10%">预约工单号</th>
				<th width="10%"><?php echo "商城订单号" ;?></th>
				<th width="10%">创建时间</th>
				<th width="10%">联系人/电话</th>
				<th width="15%">地址</th>
				<th width="10%">预约时间</th>
				<th width="5%">安装费用</th>
				<th width="10%">接单技师</th>
				<th width="8%">状态</th>
				<th width="10%">操作</th>
			  </thead>
			  <?php 
				
			  
				$query = "select id,reservation_num,order_num,createtime,status,
				contact,phone,location_p,location_c,location_a,address,reservation_date,install_cost,product_name,product_count from weixin_install_reservation 
					where isvalid = true and customer_id = ".$customer_id;
				$query = $query." and ordertype = ".$p_ordertype;
				if(!empty($p_type)){
					$query  = $query." and status = ".($p_type-1);
				}
				if(!empty($p_reservation_num)){
					$query  = $query." and reservation_num like '".$p_reservation_num."' ";
				}
				if(!empty($p_order_num)){
					$query  = $query." and order_num like '".$p_order_num."' ";
				}
				if(!empty($p_begin_time)){
					$query = $query." and UNIX_TIMESTAMP(createtime)>=".strtotime($p_begin_time);
				}
				if(!empty($p_end_time)){
					$query = $query." and UNIX_TIMESTAMP(createtime)<=".strtotime($p_end_time);
				}
				$query = $query . " order by id desc ";
				$query = $query." limit ".$start." , ".$end;
				
				//echo $query."<br/>" ; 
				$result = _mysql_query($query) or die("L581 query error : ".mysql_error());
				while($row = mysql_fetch_object($result)){
					$status = $row->status;
					$statusStr = "";
					//0:待放单；1：指定未接受；2：进行中；3：技师完成；4：回访完成；5：结算完成
					if($status == 0){
						$statusStr = "待放单";
					}else if($status == 1){
						$statusStr = "指定未接受";
					}else if($status == 2){
						$statusStr = "进行中";
					}else if($status == 3){
						$statusStr = "技师完成";
					}else if($status == 4){
						$statusStr = "回访完成";
					}else if($status == 5){
						$statusStr = "结算完成";
					}else if($status == 6){
						$statusStr = "客户已评分";
					}
					$eng_name = "";
					$eng_phone = "";
					$reservation_num = $row->reservation_num;
					if($status > 0){
						$query2 = "select e.name , e.phone from weixin_install_engineer e inner join weixin_install_reservation_engineer r on e.id = r.engineer_id
						where r.isvalid = true and r.status != 2 and reservation_num = '".$reservation_num."' order by r.createtime desc limit 0,1";
						$result2 = _mysql_query($query2) or die("L472 query error : ".mysql_error());
						$eng_name = mysql_result($result2,0,0);
						$eng_phone = mysql_result($result2,0,1);
					}
					$install_engineer_id = 0;
					if($status == 4){
						$query3 = "select engineer_id from weixin_install_reservation_engineer r where r.isvalid = true and r.status =3 and reservation_num = '".$reservation_num."' limit 0,1";
						$result3 = _mysql_query($query3) or die("L737 query error : ".mysql_error());
						$install_engineer_id = mysql_result($result3,0,0);
					}
					
					$id = $row->id;
					$order_num = $row->order_num;
					$createtime = $row->createtime;
					$contact = $row->contact;
					$phone = $row->phone;
					$location_p = $row->location_p;
					$location_c = $row->location_c;
					$location_a = $row->location_a;
					$address = $row->address;
					$reservation_date = $row->reservation_date;
					$install_cost = $row->install_cost;
					$product_name = $row->product_name;
					$product_count = $row->product_count;
			  ?>
			  <tr>
				<td><a href="javascript:showDetail(<?php echo $id;?>)" style="color:blue"><?php echo $reservation_num;?></a></td>
				<td><?php echo $order_num;?></td>
				<td><?php echo $createtime;?></td>
				<td><?php echo $contact ."/". $phone;?></td>
				<td><?php echo $location_p ."". $location_c ."". $location_a ."". $address; ?></td>
				<td><?php echo $reservation_date; ?></td>
				<td><?php echo $install_cost; ?></td>
				<td><?php echo $eng_name ; ?>/<?php echo $eng_phone ; ?></td>
				<td><?php echo $statusStr ; ?></td>
				<td class="WSY_t4" id="WSY_t4">
					<a href="javascript:showDetail(<?php echo $id;?>)" title="详细信息"><img src="../../../common/images_V6.0/operating_icon/icon31.png"></a>
					<?php 
					if($status == 0){
					?>
						<a href="javascript:showDetail(<?php echo $id;?>)" title="指派技师"><img src="../../../common/images_V6.0/operating_icon/icon06.png"></a>
					<?php
					}
					if($status == 3 || $status == 6){
					?>
					<a href="javascript:showDetail(<?php echo $id;?>)" title="回访完成"><img src="../../../common/images_V6.0/operating_icon/icon24.png"></a>
					<?php
					}
					if($status == 4){
					?>
					<a href="javascript:minusScore(<?php echo $id;?>,'<?php echo $p_ordertype;?>')" title="扣分"><img src="../../../common/images_V6.0/operating_icon/icon25.png"></a>
					<a href="javascript:confirm_order(<?php echo $id;?>,<?php echo $reservation_num;?>,'<?php echo $p_ordertype;?>')" title="确定完成"><img src="../../../common/images_V6.0/operating_icon/icon23.png"></a>
					<?php } ?>
					<a href="javascript:order_state(<?php echo $id;?>,3,'<?php echo $p_ordertype;?>')" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>
				</td>
			  </tr>
			  <tr id="row_score_<?php echo $id;?>" style="display:none">
				<td colspan="11" style="padding:10px">
					<div class="detail_border" style="width:100%;height:200px;">
						<div class="div_detail" style="width:auto;margin-right:50px;line-height:150px">
							扣分：<input type="text" style="border:solid 1px gray; display:inline;margin:none" name="minusscore" id="minusscore_<?php echo $id;?>" style="width:100px"/><br/>
						</div>
						<div class="div_idimg">
							扣分原因：<textarea style="border:solid 1px gray; display:inline;margin:none;width:300px;height:150px; vertical-align: middle;" name="minuscomment" id="minuscomment_<?php echo $id;?>" ></textarea><br/>
							<button type="button" onclick="minusOrd(<?php echo $id;?>,'<?php echo $reservation_num;?>','<?php echo $install_engineer_id;?>','<?php echo $p_ordertype;?>')">确定扣分</button>
						</div>
					</div>
				</td>
			  </tr>
			  <tr id="row_<?php echo $id;?>" style="display:none">
				<td colspan="10" style="padding:10px">
					<div class="detail_border" style="min-height:100px;" >
					<div class="div_detail" style="width:60%" style="float:left">
					<?php 
						if($p_ordertype == 0){
							
						$query_pro = "select o.id , o.prvalues,p.name,p.install_price from weixin_commonshop_orders o inner join weixin_commonshop_products p
							on o.pid = p.id  where o.isvalid = true and o.batchcode = '".$order_num."' and o.customer_id = ".$customer_id;
						$result_pro = _mysql_query($query_pro) or die("L687 query error : ".mysql_error());
					?>
						
						<?php while($row_pro = mysql_fetch_object($result_pro)){ 
							$pid = $row_pro->id;
							$prvalues = $row_pro->prvalues;
							$pname = $row_pro->name;
							$install_price = $row_pro->install_price;
							$prvArr = str_replace("_",",",$prvalues);
							//echo $prvalues;
							if(!empty($prvArr)){
								$query_prv = "select name from weixin_commonshop_pros where isvalid = true and customer_id = ".$customer_id." and id in (".$prvArr.")";
							
								$result_prv = _mysql_query($query_prv) or die("L667 query error : ".mysql_error());
								$prvStr = "";
								while($row_prv = mysql_fetch_object($result_prv)){
									$prvStr = $prvStr ."  ".$row_prv->name;
								}
							}
							
							
						?>
							产品 : <?php echo $pname;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							<?php if(!empty($prvArr)){ ?>
							属性：<?php echo $prvStr;?>&nbsp;&nbsp;&nbsp;&nbsp;
							<?php } ?>							安装费:<?php echo $install_price;?><br/>
						<?php }
						}else if($p_ordertype ==1 ){
							?>
							产品：<?php echo $product_name;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							数量：<?php echo $product_count;?>&nbsp;&nbsp;&nbsp;&nbsp;
							安装费:<?php echo $install_cost;?><br/>
							
							<?php
						}	?>
						</div>
						<div class="div_idimg" style="width:38%;line-height:50px;text-align:right">
						<?php if($status == 0){
								$query_eng = "select id,name,phone from weixin_install_engineer where isvalid = true and isvailable = true and customer_id = ".$customer_id." 
								and location_p = '".$location_p."' and location_c = '".$location_c."' order by name asc";
								// and location_a = '".$location_a."'
								$result_eng = _mysql_query($query_eng) or die("L565 query error : ".mysql_error());
								$eng_arr = array();
								$index = 0;
								while($row_eng = mysql_fetch_object($result_eng)){
									$eng_arr[$index] = $row_eng->id ."_". $row_eng->name ."_" .$row_eng->phone;
									$index ++;
								}
							?>
							
							选择技师：
							<select id="sel_eng_<?php echo $id;?>">
							<?php for($i = 0 ; $i< count($eng_arr) ; $i++) {
									$eng_data = explode("_",$eng_arr[$i]);
								?>
								<option value="<?php echo $eng_data[0];?>"><?php echo $eng_data[1] ."/" .$eng_data[2];?></option>
							<?php }?>
							</select>
							<button type="button" onclick="assignEng(<?php echo $id;?>,<?php echo $reservation_num;?>,this)"> 确 定 </button>
						<?php } ?>
						</div>
					</div>
					<?php
						//接单详情
						$query_r_list = "select e.name ,e.phone ,r.reservation_num ,r.createtime,r.status,r.reservation_date from weixin_install_reservation_engineer r 
							inner join weixin_install_engineer e on r.engineer_id = e.id where r.isvalid = true and r.reservation_num = '".$reservation_num."' ";
						$result_r_list = _mysql_query($query_r_list) or die("L714 query error : ".mysql_error());
						$rows = mysql_num_rows($result_r_list);
						//echo "rows : ".$rows;
					?>
					<div class="rev_eng_list detail_border" style="text-align:left; <?php echo $rows <= 0 ? "display:none;" : "";?>">
						<div style="text-align: left;width: 100%;float: left;font-size: 16px;font-weight: bold;">接单详情</div>
						<div style="text-align:left; ">
						<?php 
							while($row_list = mysql_fetch_object($result_r_list)){
								$e_name = $row_list->name;
								$e_phone = $row_list->phone;
								$r_reservation_num = $row_list->reservation_num;
								$r_createtime = $row_list->createtime;
								$r_status = $row_list->status;
								$r_statusSr = "进行中";
								if($r_status == 0){
									$r_statusSr = "待接受";
								}else if ($r_status == 3){
									$r_statusSr = "已安装完成";
								}else if($r_status == 2){
									$r_statusSr = "已取消";
								}
								
								$r_reservation_date = $row_list->reservation_date;
							?>
							技师姓名：<?php echo $e_name;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							电话:<?php echo $e_phone;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							时间：<?php echo $r_createtime;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							状态：<?php echo $r_statusSr; ?>&nbsp;&nbsp;&nbsp;&nbsp; 
							预约时间：<?php echo $r_reservation_date; ?>&nbsp;&nbsp;&nbsp;&nbsp; <br/>
						<?php
							}
						?>
						</div>
					</div>
					<?php
						//评分详情
						$query_s_list = "select score1,score2,status,score1time,score2time,totalscore,score1remark,score2remark,createtime,scoretype from weixin_install_score 
							where isvalid = true and reservation_num = '".$reservation_num."'";
						$result_s_list = _mysql_query($query_s_list) or die("L754 query error : ".mysql_error());
						$rows = mysql_num_rows($result_s_list);
						//echo "rows : ".$rows;
					?>
					<div class="rev_eng_list detail_border" style="text-align:left; <?php echo $rows <= 0 ? "display:none;" : "";?>">
						<div style="text-align: left;width: 100%;float: left;font-size: 16px;font-weight: bold;">评分详情</div>
						<div style="text-align:left; ">
						<?php 
							while($row_list = mysql_fetch_object($result_s_list)){
								$score1 = $row_list->score1;
								$score2 = $row_list->score2;
								$r_status = $row_list->status;
								$score1time = $row_list->score1time;
								$score2time = $row_list->score2time;
								$totalscore = $row_list->totalscore;
								$score1remark = $row_list->score1remark;
								$score2remark = $row_list->score2remark;
								$createtime = $row_list->createtime;
								$r_statusSr = "默认评分";
								if($r_status == 1){
									$r_statusSr = "客户评分";
								}
								$scoretype = $row_list->scoretype;
								if($scoretype == 0){
									
							?>
							客户评分：<?php echo $score1."[".$r_statusSr."]";?>&nbsp;&nbsp;&nbsp;&nbsp; 
							评分时间：<?php echo $score1time;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							回访评分:<?php echo empty($score2) ? "暂未评分" : $score2;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							回访时间：<?php echo $score2time;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							回访评价：<?php echo $score2remark;?>&nbsp;&nbsp;&nbsp;&nbsp; 
							总分：<?php echo empty($totalscore) ? "未统计" : $totalscore;?>&nbsp;&nbsp;&nbsp;&nbsp; <br/>
						<?php
								}
							}
						?>
						</div>
					</div>
					
					
					<?php
						//回访评分
						if($status == 3 ||$status == 6){
					?>
					<div class="rev_eng_list detail_border" style="text-align:left;">
						<div style="text-align: left;width: 100%;float: left;font-size: 16px;font-weight: bold;">回访评分</div>
						<div style="text-align:left; ">
							评分：<br/>
							<input type="radio" value="1" name="txt_score2_<?php echo $id;?>" id="txt_score2_<?php echo $id;?>_1" style="display:inline"/> 
							<label for="txt_score2_<?php echo $id;?>_1">1分[很不满意]<label><br/>
							<input type="radio" value="2" name="txt_score2_<?php echo $id;?>" id="txt_score2_<?php echo $id;?>_2" style="display:inline"/> 
							<label for="txt_score2_<?php echo $id;?>_2">2分[不满意]</label><br/>
							<input type="radio" checked="checked" value="3" name="txt_score2_<?php echo $id;?>" id="txt_score2_<?php echo $id;?>_3" style="display:inline"/>
							<label for="txt_score2_<?php echo $id;?>_3">3分[一般]</label><br/>
							<input type="radio" value="4" name="txt_score2_<?php echo $id;?>" id="txt_score2_<?php echo $id;?>_4" style="display:inline"/> 
							<label for="txt_score2_<?php echo $id;?>_4">4分[满意]</label><br/>
							<input type="radio" value="5" name="txt_score2_<?php echo $id;?>" id="txt_score2_<?php echo $id;?>_5" style="display:inline"/> 
							<label for="txt_score2_<?php echo $id;?>_5">5分[非常满意]</label><br/>
							备注：<textarea class="input_border" style="width:300px;height:80px;vertical-align: middle" id="txt_remark2_<?php echo $id;?>"></textarea>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<button type="button" onclick="callback_order(<?php echo $id;?>,'<?php echo $reservation_num;?>')" class="sbtn"> 确 定 </button>
						</div>
					</div>
					<?php } ?>
				</td>
			  </tr>
			  
				<?php } ?>
			</table>
			<!--表格结束-->
			
			<!--翻页开始-->
			<div class="WSY_page">
				
			</div>
			<!--翻页结束-->
		</div>
		<!-- 订单列表 end -->
		<?php } ?>
		
		<?php if($show_index == 4){
			$tab = 0; 
			$tab = $configutil->splash_new($_GET["tab"]);
			
		?>
		<!-- 安装规范 begin -->
		<div class="WSY_data" style=" <?php echo $tab == 1 ? 'padding-left:30px':'';?> ">
		<?php
			if($tab == 1){
				$article_id = $configutil->splash_new($_GET["article_id"]);
				$title = "";
				$content = "";
				$icon = "";
				$ordernum = 0;
				if(!empty($article_id) && $article_id > 0){
					$query = "select title,content,icon,ordernum from weixin_install_article where isvalid = true and id = ".$article_id;
					$result = _mysql_query($query) or die("L1010 : query error : ".mysql_error());
					$title = mysql_result($result,0,0);
					$content = mysql_result($result,0,1);
					$icon = mysql_result($result,0,2);
					$ordernum = mysql_result($result,0,3);
				}
		?>
			<form method="post" action="save_index.php?customer_id=<?php echo passport_encrypt($customer_id);?>&show_index=<?php echo $show_index;?>" id="frm_article">
				<input type="hidden" name="article_id" value="<?php echo $article_id;?>"/>
                <div class="WSY_generate_con" style="width:90%">
                	<div class="WSY_generate_div">
                        <dl class="WSY_generate01">
                            <dt style="line-height:30px">标题：</dt>
                            <dd><input type="text" id="ar_title" name="title" style="height:20px;width:300px" value="<?php echo $title;?>" autocomplete="off" ></dd>
                        </dl>
                    </div>
					
                    <div class="WSY_generate_div" style="width:90%">
                        <dl class="WSY_generate01" style="width:90%">
                            <dt>内容：</dt>
                            <dd style="width:90%">
								<textarea name="content" id="ar_content" style="width:90%;height:300px"><?php echo $content;?></textarea>
							</dd>
                        </dl>
                    </div>
					
                    <div class="WSY_generate_div">
                        <dl class="WSY_generate01">
                            <dt>图标：</dt>
                            <dd>
							<iframe src="iframe_images_articleicon.php?customer_id=<?php echo $customer_id; ?>&article_id=<?php echo $article_id; ?>&icon=<?php echo $icon; ?>" height=200 width=1024 FRAMEBORDER=0 SCROLLING=no></iframe>
							</dd>
<!-- 				 		<div class="WSY_memberimg">
							<?php if($welfare_images!=""){?>
							<img src="<?php echo $welfare_images; ?>" style="width:64px;height:100px;">
							<?php }else{ ?>
							<img src="../../../pic/uniqlo.png" style="width:150px;height:100px;">
							<?php } ?>
							<span>(上传1张图片，作为首页的图片。<br>图片大小建议：450*300像素,70k以下）</span>
							<div class="uploader white">
								<input type="text" class="filename" readonly/>
								<input type="button" name="file" class="button" value="上传..."/>
								<input size="17" name="new_welfare_images" id="new_welfare_images" type=file value="<?php echo $welfare_images ?>">
								<input type=hidden value="<?php echo $welfare_images ?>" name="welfare_images" id="welfare_images" /> 
							</div>
						</div>  -->
                        </dl>
						<input type=hidden name="icon" id="ar_icon" value="<?php echo $icon ; ?>" />
                    </div>
					
					<div class="WSY_generate_div">
                        <dl class="WSY_generate01">
                            <dt style="line-height:30px">排序：</dt>
                            <dd>
								<input class="nums create_m" style="height:20px" type="text" name="ordernum" id="ar_ordernum" value="<?php echo $ordernum;?>" autocomplete="off" >
							</dd>
                        </dl>
                    </div>
					
                </div>
                <div class="WSY_text_input01 WSY_text_input05">
                    <div class="WSY_text_input"><button class="WSY_button" type="button" onclick="checkSaveArticle()">保存</button></div>
					<div class="WSY_text_input"><button class="WSY_button" type="button" onclick="javascript:history.go(-1);">取消</button></div>
                </div>
            </div>
		</form>
		<?php
			}else{
						
				$pagenum = 1;

				if(!empty($_GET["pagenum"])){
				   $pagenum = $configutil->splash_new($_GET["pagenum"]);
				}
				
				$start = ($pagenum-1) * 20;
				$end = 20;
				
				$query = "select count(1) rowcount from weixin_install_article where isvalid = true and customer_id = ".$customer_id."";
				
				$result = _mysql_query($query) or die("L1075 query error : ".mysql_error());
				$rcount = mysql_result($result,0,0);
				$page = ceil($rcount/$end);
				
				$query = "select id , title,content,icon,ordernum,createtime from weixin_install_article where isvalid = true and customer_id = ".$customer_id;
				$query = $query." order by createtime desc  limit ".$start.",".$end;
				$result = _mysql_query($query) or die("L1081 query error : ".mysql_error());
		?>
				<div class="WSY_sales">
					<li class="WSY_bottonliss">
					<button type="button" style="width:80px;height:30px;float:right;margin-right:50px;"
					onclick="javascript:location.href='index.php?customer_id=<?php echo  passport_encrypt($customer_id); ?>&show_index=4&tab=1'">添加</button></li>
				</div>
				<!--表格开始-->
				<table width="97%" class="WSY_table WSY_t2" id="WSY_t1">
				  <thead class="WSY_table_header">
					<th width="10%">排序</th>
					<th width="30%">标题</th>
					<th width="10%">创建时间</th>
					<th width="15%">图标</th>
					<th width="15%">操作</th>
				  </thead>
				  <?php
					while($row = mysql_fetch_object($result)){
				  ?>
				  <tr>
					<td><?php echo $row->ordernum;?></td>
					<td><?php echo $row->title;?></td>
					<td><?php echo $row->createtime;?></td>
					<td>
						<img src="<?php echo $new_baseurl.$row->icon;?>" style="width:50px;height:50px"/>
					</td>
					<td class="WSY_t4" id="WSY_t4">
					
						<a href="index.php?customer_id=<?php echo passport_encrypt($customer_id); ?>&show_index=4&tab=1&article_id=<?php echo $row->id;?>" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
						<a href="javascript:article_delete('<?php echo $row->id;?>')" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>
					</td>
				  </tr>
				<?php } ?>
				  
				</table>
				<!--表格结束-->
				
				<!--翻页开始-->
				<div class="WSY_page">
					
				</div>
				<!--翻页结束-->
				<?php 
				}	?>
			</div>
			<!-- 安装规范 end -->
		<?php 
		} ?>
		
		
		<!-- 导入工单 -->
		<?php if($show_index == 5){
		?>
		<!-- 安装规范 begin -->
		<div class="WSY_data" style=" padding-left:30px">
			<div class="WSY_sales" style="display:block;float:right;">
				<li class="WSY_bottonliss">
					<button type="button" style="width:80px;height:30px;float:right;margin-right:50px;"
					onclick="javascript:location.href='import_template.xls'">下载模板</button>
				</li>
			</div>
			<div style="display:block;float:left">
			<form id="importform" method="post" enctype="multipart/form-data" action="save_import.php?customer_id=<?php echo passport_encrypt($customer_id);?>">
				<div class="uploader white WSY_import">
					<input type="input" class="filename" id="showfile" />
					<input type="button" name="file" class="button" value="上传..."/>
					<input type="file" size="30" id="excelfile" name="excelfile"/>
				</div>
				<div class="WSY_import_button"><input type="button" onclick="checkImport()" value="导入记录"></div>
            </form>
			</div>
		</div>
			<!-- 导入工单 end -->
		<?php 
		} ?>
		
		
	</div>
</div>

<script type="text/javascript" src="../../../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<script>
<?php
	if($show_index == 4){
?>
	CKEDITOR.replace( 'content',
	{
	extraAllowedContent: 'img iframe[*]',
	filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
	filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
	filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
	filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
	filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	});
<?php
	}
?>
<?php
	if($show_index == 0){
?>
/*
CKEDITOR.replace( 'punishment',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
CKEDITOR.replace( 'rewardcomment',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
*/
CKEDITOR.replace( 'registcomment',
{
toolbar :
 [
	//加粗     斜体，     下划线 
	['Bold','Italic','Underline'],
	// 数字列表          实体列表            减小缩进    增大缩进
	['NumberedList','BulletedList','-','Outdent','Indent'],
	//左对 齐             居中对齐          右对齐          两端对齐
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	'/',
	// 样式       格式      字体    字体大小
	['Styles','Format','Font','FontSize'],
	//文本颜色     背景颜色
	['TextColor','BGColor'],
	//全屏           显示区块
	['Maximize', 'ShowBlocks','-']
 ],
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Images',
filebrowserFlashBrowseUrl : '../../../../weixin/plat/Public/ckfinder/ckfinder.html?type=Flash',
filebrowserUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
<?php
	}
?>
</script>
<script src="../../../common/js/percent/jquery.percentageloader.0.2.js"></script>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script src="../../../common/js/floatBox.js"></script>
<script type="text/javascript">
$(function(){
	$("#excelfile").change(function(){
		$("#showfile").val(this.value);
	});
});

//导出 － 安装工单
function checkExport(){
	var customer_id = '<?php echo $customer_id;?>';
	var source = $("#p_ordertype").val(); //工单来源
	var status = $("#p_type").val(); //工单状态
	var reservation_num = $("#p_reservation_num").val();//安装工单号
	var order_num = $("#p_order_num").val(); //商城订单号
	var begin_time = $("#p_begin_time").val();
	var end_time = $("#p_end_time").val();
	/*导出自行安装订单筛选框*/
	var excelArray = [
						["reservation_num","预约订单号"],
						["order_num","商城订单号"],
						["createtime","创建时间"],
						["contact","联系人"],
						["phone","电话"],
						["address","地址"],
						["reservation_date","预约时间"],
						["product_name","产品名"],
						["product_type","型号"],
						["install_cost","安装费用"],
						["engineer_name","技师姓名"],
						["engineer_phone","技师手机号"],
						["status","状态"],
						["first_level","一级安装员推荐师手机号"],
						["second_level","二级安装员推荐师手机号"],
						["third_level","三级安装员推荐师手机号"]
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
		
		var url="/weixin/plat/app/index.php/Excel/installplatform_order_export/customer_id/"+customer_id+"/source/"+source+"/status/"+status;
		if(reservation_num != ""){
			url = url +"/reservation_num/"+reservation_num;
		}
		if(order_num != ""){
			url = url +"/order_num/"+order_num;
		}
		if(begin_time != ""){
			url = url + "/begin_time/" + begin_time;
		}
		if(end_time != ""){
			url = url + "/end_time/" + end_time;
		}
		if(str != ""){
			url = url + "/excel_fields/" + str;
		}
		url = url + "/";
		console.log(url);
		location.href = url;
		$(".floatbox").hide();
		$(".floatbox").remove();
	});
	
}
//导出 － 自行安装订单
function checkExportSelf(){
	var customer_id = '<?php echo $customer_id;?>'
	var begin_time = $("#p_begin_time").val();
	var end_time = $("#p_end_time").val();
	if(begin_time == "" || end_time == ""){
		alert("导出前请先选择时间！");
		return;
	}
	/*导出自行安装订单筛选框*/
	var excelArray = [
						["batchcode","订单编号"],
						["user_id","用户编号"],
						["username","用户名"],
						["userphone","手机号"],
						["product_name","产品名"],
						["product_type","型号"],
						["install_price","安装费"],
						["createtime","下单时间"],
						["receivetime","收货时间"]
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
		
		var url="/weixin/plat/app/index.php/Excel/ipself_order_export/customer_id/"+customer_id;
		if(begin_time != ""){
			url = url + "/begin_time/" + begin_time;
		}
		if(end_time != ""){
			url = url + "/end_time/" + end_time;
		}
		if(str != ""){
			url = url + "/excel_fields/" + str;
		}
		url = url + "/";
		console.log(url);
		location.href = url;
		$(".floatbox").hide();
		$(".floatbox").remove(); 
	});
	
}


//导入 － 上传提交
function checkImport(){
	var f_content = $("#excelfile").val();
	var fileext=f_content.substring(f_content.lastIndexOf("."),f_content.length)
	fileext=fileext.toLowerCase()
	if (fileext!='.xls')
	{
		alert("对不起，导入数据格式必须是xls格式文件哦，请您调整格式后重新上传，谢谢 ！");
		return ;
	}
	$("#importform").submit();
}
function checkSaveArticle(){
	var  title = $("#ar_title").val();
	
	var  ordernum = $("#ar_ordernum").val();
	
	if(title == ""){
		alert("请输入标题！");
		return;
	}
	
	if(ordernum == "" || parseInt(ordernum) < 0){
		alert("请输入排序");
		return;
	}
	
	$("#frm_article").submit();
}

function article_delete(id){
	if(confirm("是否确定删除？删除后不可恢复！")){
		$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:"del",
				article_id:id,
				type:"article"
			},
			dataType: "json",
			success:function(data){
				if(data.result == 1){
					url="index.php?show_index=4&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum;
					location.replace(url);
				}
			} 

		}); 
	}
}
function setParentDefaultimgurl(default_imgurl){
    document.getElementById("ar_icon").value=default_imgurl;
}
  var pagenum = '<?php echo $pagenum; ?>';
  var count ='<?php echo $page; ?>';//总页数
   var page = '<?php echo $page; ?>';
	<?php if($show_index == 2){?>
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		var p_name = $("#p_name").val();
		var p_type = $("#p_type").val();
		var p_status = $("#p_status").val();
		document.location= "index.php?customer_id=<?php echo  passport_encrypt($customer_id); ?>&pagenum="+p+"&name="+p_name+"&type="+p_type+"&status="+p_status+"&show_index=2";
	   }
    });
	function jumppage(){
		var a=parseInt($("#WSY_jump_page").val());
		if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
			return false;
		}else{
			var p_name = $("#p_name").val();
			var p_type = $("#p_type").val();
			var p_status = $("#p_status").val();
			document.location= "index.php?customer_id=<?php echo  passport_encrypt($customer_id); ?>&pagenum="+a+"&name="+p_name+"&type="+p_type+"&status="+p_status+"&show_index=2";
		}
	}
	<?php }
		if($show_index == 3){
	?>
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		var p_type = $("#p_type").val();
		var p_reservation_num = $("#p_reservation_num").val();
		var p_order_num = $("#p_order_num").val();
		var p_begin_time = $("#p_begin_time").val();
		var p_end_time = $("#p_end_time").val();
		var p_ordertype = $("#p_ordertype").val()
		document.location= "index.php?customer_id=<?php echo  passport_encrypt($customer_id); ?>&pagenum="+p+"&type="+p_type+"&reservation_num="
		+p_reservation_num+"&order_num="+p_order_num+"&begin_time="+p_begin_time+"&end_time="+p_end_time+"&show_index=3&ordertype="+p_ordertype+"";
	   }
    });
	function jumppage(){
		var a=parseInt($("#WSY_jump_page").val());
		if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
			return false;
		}else{
			var p_type = $("#p_type").val();
			var p_reservation_num = $("#p_reservation_num").val();
			var p_order_num = $("#p_order_num").val();
			var p_begin_time = $("#p_begin_time").val();
			var p_end_time = $("#p_end_time").val();
			var p_ordertype = $("#p_ordertype").val()
			document.location= "index.php?customer_id=<?php echo passport_encrypt($customer_id); ?>&pagenum="+a+"&type="+p_type+"&reservation_num="
			+p_reservation_num+"&order_num="+p_order_num+"&begin_time="+p_begin_time+"&end_time="+p_end_time+"&show_index=3&ordertype="+p_ordertype+"";
	   }
	}
		<?php } ?>
</script>	
<script type="text/javascript">
var pagenum = '<?php echo $pagenum ?>';
function engineer_state(dataId,op){
	var result = true;
	var reason = "";
	if(op == 0){ //拒绝
		reason = prompt("请输入拒绝/暂停理由","您不符合申请条件，请联系客服");
		if(reason == null || reason == ""){
			result = false;
		}
	}else if(op == 1){ //暂停
		reason = prompt("请输入拒绝/暂停理由","您长时间未有接单，技师身份已被收回，如有疑问请联系商城在线客服");
		if(reason == null || reason == ""){
			result = false;
		}
	}else if(op == 2){
		result = confirm("是否确定通过审核？");
	}
	else if(op == 3){
		result = confirm("是否确定删除？");
	}
	if(result == true){
		$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:op,
				dataId:dataId,
				type:"engineer",
				reason:reason
			},
			dataType: "json",
			success:function(data){
				if(data.result == 1){
					url="index.php?show_index=2&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum;
					location.replace(url);
				}
			} 

		}); 
	}
}
function showDetail(id){
	$("#row_"+id).toggle();
}

function minusScore(id){
	$("#row_score_"+id).toggle();
}

function minus(id){
	var score = $("#minusscore_"+id).val();
	var comment = $("#minuscomment_"+id).val();
	if(isNaN(score)){
		alert("扣分请输入数字!");
		return
	}
	if(comment == ""){
		alert("请输入扣分理由!");
		return;
	}
	if(confirm("是否确定扣除技师积分？")){
		$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:4,
				dataId:id,
				type:"engineer",
				score:score,
				comment:comment
			},
			dataType: "json",
			success:function(data){
				if(data.result == 1){
					url="index.php?show_index=2&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum;
					location.replace(url);
				}
			} 

		}); 
	}
}

function minusOrd(id,reservation_num,engineer_id,ordertype){
	var score = $("#minusscore_"+id).val();
	var comment = $("#minuscomment_"+id).val();
	if(isNaN(score)){
		alert("扣分请输入数字!");
		return
	}
	if(comment == ""){
		alert("请输入扣分理由!");
		return;
	}
	if(confirm("是否确定扣除技师积分？")){
		$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:5,
				dataId:id,
				type:"order",
				score:score,
				comment:comment,
				engineer_id:engineer_id,
				reservation_num:reservation_num
			},
			dataType: "json",
			success:function(data){
				if(data.result == 1){
					url="index.php?show_index=3&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum+"&ordertype="+ordertype;
					location.replace(url);
				}
			} 

		}); 
	}
}


function order_state(dataId,op,ordertype){
	var result = true;
	var reason = "";
	if(op == 3){
		result = confirm("是否确定删除？");
	}
	if(result == true){
		$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:op,
				dataId:dataId,
				type:"order"
			},
			dataType: "json",
			success:function(data){
				if(data.result == 1){
					url="index.php?show_index=3&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum+"&ordertype="+ordertype;
					location.replace(url);
				}
			} 

		}); 
	}
}

function callback_order(dataId,reservation_num){
	var score = $("input[name='txt_score2_"+dataId+"']:checked").val();
	var remark = $("#txt_remark2_"+dataId).val();
	if(isNaN(score) || parseInt(score) <=0 || parseInt(score) > 5){
		alert("评分必需在1-5之间，请确认！");
		return;
	}
		$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:2, //回访
				dataId:dataId,
				type:"order",
				reservation_num:reservation_num,
				score:score,
				remark:remark
			},
			dataType: "json",
			success:function(data){
				if(data.result == 1){
					url="index.php?show_index=3&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum;
					location.replace(url);
				}
			} 

		}); 
}

function assignEng(dataId,res_num,btn){
	var sel_eng = $(btn).parent().find("#sel_eng_"+dataId);
	var sel_id = sel_eng.val();
	var result = true;
	if(sel_id != ""){
		result = confirm("是否确定给订单指派技师？");
	}
	if(result == true){
		$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:1,
				dataId:dataId,
				eng_id:sel_id,
				res_num:res_num,
				type:"order"
			},
			dataType: "json",
			success:function(data){
				if(data.result == 1){
					url="index.php?show_index=3&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum;
					location.replace(url);
				}else{
					alert(data.msg);
				}
			} 

		}); 
	}
}

function confirm_order(dataId,reservation_num,ordertype){
	$.ajax({
			type: 'POST',
			url: "change_state.php?customer_id=<?php echo passport_encrypt($customer_id);?>",
			data: {
				op:4,
				dataId:dataId,
				type:"order",
				reservation_num:reservation_num
			},
			dataType: "json",
			success:function(data){
				alert(data.msg);
				if(data.result == 1){
					url="index.php?show_index=3&customer_id=<?php echo passport_encrypt($customer_id);?>&pagenum="+pagenum+"&ordertype="+ordertype;
					location.replace(url);
				}
			} 

		});
}

function checkReward(){
	$("#frm_reward").submit();
}
$(function(){
	
	//选项卡点击事件 
	$(".WSY_columnnav a").click(function(){
		$(".WSY_columnnav a").removeClass("white1");
		$(this).addClass("white1");
		var n=$(".WSY_columnnav a").index(this)
		location.href="index.php?customer_id=<?php echo passport_encrypt($customer_id); ?>&show_index="+n;
	});
	
	//生成 - 配置项后的 “ － ” 按钮
	$(".WSY_generate_icon001").click(function(){
		//如果当前删除的为最后一个 - ，将上一个 - 的尾部加一个 +
		var cindex = $(this).index(".WSY_generate_icon001");
		if(cindex == $(".WSY_generate_icon001").length-1){
			var cplus = $(this).siblings("a").clone(true);
			$(".WSY_generate_icon001:eq("+(cindex-1)+")").parent().append(cplus);
		}
		$(this).parents(".WSY_generate_div").remove();
	});
	
	//生成 - 配置项后的 “ + ” 按钮
	$(".WSY_generate_icon002").click(function(){
		var newDIV= $(this).parents(".WSY_generate_div").clone(true);
		newDIV.find(".nums").val("");
		$(this).parents(".WSY_generate_div").parent().append(newDIV);
		$(this).remove();
	});
	
	$("#btn_savesettings").click(function(){
		var reward1 = $("#reward1").val();
		var reward2 = $("#reward2").val();
		var reward3 = $("#reward3").val();
		if((parseFloat(reward1) + parseFloat(reward2) + parseFloat(reward3)) > 1.0){
			alert("三级佣金比总数不能大于 1 ");
			return;
		}
		var weight = $("#weight").val();
		if(parseFloat(weight) > 1 ){
			alert("权重值不能大于 1 ");
			return;
		}
		$("#frm_settings").submit();
	});
});
//
</script>
<script>
function change_sendstatus(obj){ 
	$("#isOpenInstall").val(obj);
}


</script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
</body>
</html>
		
		
	
		
		
<?php

mysql_close($link);
?>