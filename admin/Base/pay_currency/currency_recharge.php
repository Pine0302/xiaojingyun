<?php
header("Content-type: text/html; charset=utf-8"); 
require_once('../../../../weixinpl/config.php');
require_once('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require_once('../../../../weixinpl/proxy_info.php');
require_once('../../../../weixinpl/function_model/currency.php');

$currency_head = 3;

$pagenum = 1;

if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}

$start = ($pagenum-1) * 20;
$end = 20;

$type = 0;
$endtime 	= $_GET['endtime'];
$begintime 	= $_GET['begintime'];
$type 	    = intval($_GET['type']);

$keyword 	= trim($_GET["keyword"]);
if(strpos($keyword,"%") !== false){		
	$keyword = str_replace("%","\%",$keyword);								
}  
if($begintime){
	$begintime_where = " and starttime > '{$begintime}'";
}
if($endtime){
	$endtime_where = " and endtime < '{$endtime}'";
}
if($keyword){
	$keyword_where = " and (title like '%".$keyword."%' or num like '%".$keyword."%' or money like '%".$keyword."%')";
}
if($type){
	$type_where = " and status=".$type;
}

_mysql_query("SET NAMES UTF8");

$query = 'SELECT id,createtime,title,num,money,starttime,endtime,status,used,not_used FROM currency_recharge_card_list_t where isvalid=true and customer_id='.$customer_id;
$query1 = 'SELECT count(1) as wcount FROM currency_recharge_card_list_t where isvalid=true and customer_id='.$customer_id;
if(!empty($begintime)){
		$query.= $begintime_where;
		$query1.= $begintime_where;
	}
	if(!empty($endtime)){
		$query.= $endtime_where;
		$query1.= $endtime_where;
	}
	if(!empty($keyword)){
		$query.= $keyword_where;
		$query1.= $keyword_where;
	}
	if(!empty($type)){
		$query.= $type_where;
		$query1.= $type_where;
	}

$query .= ' order by id desc'.' limit '.$start.','.$end;

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$result1 = _mysql_query($query1) or die('Query1 failed: ' . mysql_error());

$wcount =0;
$page=0;
while ($row1 = mysql_fetch_object($result1)) {
	$wcount =  $row1->wcount;
}	
$page=ceil($wcount/$end);
$rcount_q = mysql_num_rows($result);

if(strpos($keyword,"\%") !== false){		
	$keyword = str_replace("\%","%",$keyword);								
}  
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>充值记录</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/inside.css" media="all">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" href="../../Order/order/percent/jquery.percentageloader.0.2.css">
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/js/inside.js"></script>
<script type="text/javascript" src="../../Order/order/percent/jquery.percentageloader.0.2.js"></script>
<script type="text/javascript" src="/weixinpl/back_newshops/Common/js/layer/layer.js"></script>
</head>
<style>
/*.white1{background-color: #fff;
border-bottom: solid 2px #06a7e1;}*/
table th{color: #FFF;line-height: 30px;text-align: center;font-size: 12px; }
table td{height: 40px;line-height: 20px;font-size: 12px;color: #323232;padding: 0px 1em;text-align: center;border: 1px solid #D8D8D8; }
.display{display:none}
.count{
	_width: 200px;
	height:30px;
	margin-left: 40px;
	margin-top: 40px;
	float: left;
}

.count span{
	font-size: 18px;
	color: #68af27;
	font-weight: bold;
}
</style>

<style>
    /*<!-- 导出字段 -->*/
    .floatbox{position: fixed;top: 270px;left: 40%;padding: 15px;background-color: #dddddd;display: none;}
    .floatbox .tishitext{margin-bottom: 4px;}
    .floatbox .checkboxsdiv{border: 1px solid #888888;padding: 8px;width: 200px;background-color: #ffffff;}
    .checkboxsdiv input,.quanbuxuan input{display: inline-block;}
    .checkboxsdiv p,.quanbuxuan p{display: inline-block;white-space: nowrap;overflow: hidden;max-width: 181px;margin-left: 5px;}
    .floatbox .floatinputs{width: 60px;height: 27px;border-radius: 6px;background-color: #2eade8;cursor: pointer;color: #ffffff;display: inline-block;margin-top: 15px;margin-left: 16px;margin-right: 10px;}
    .floatbox .floatinputc{width: 60px;height: 27px;color: #ffffff;background-color: #aaaaaa;cursor: pointer;border-radius: 6px;display: inline-block;margin-top: 15px;}
    .quanbuxuan{display: inline-block;padding: 5px 0 0 10px;vertical-align: middle;margin-top: 15px;}
    .subdivb{display: inline-block;vertical-align: middle;}
    /*<!-- 导出字段 End -->*/
	
	/*<!--充值卡发布信息动画-->*/
	#topLoader {width: 256px;height: 256px;margin-bottom: 32px;position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100; padding:1px;}
	#per_container {width: 500px;margin-left: auto;margin-right: auto;}
	#BgDiv{background-color:#e3e3e3; position:absolute; z-index:99; left:0; top:0; display:none; width:100%;height:1000px;opacity:0.5;filter: alpha(opacity=50);-moz-opacity: 0.5;}
	#DialogDiv{position:absolute;width:400px; left:50%; top:50%; margin-left:-200px; height:auto; z-index:100;background-color:#fff; border:1px #8FA4F5 solid; padding:1px;}
	/*<!--充值卡发布信息动画End-->*/
</style>
<body>
     <!--充值卡发布信息动画-->
    <div id="BgDiv"></div>
    <div id="per_container">
    <div style="display:none" id="topLoader"></div>
    </div>
    <!--充值卡发布信息动画 End-->
	
	<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<div class="WSY_column_header">
				<div class="WSY_columnnav_currency WSY_columnnav">
					<a href="">卡列表</a>	
                    <a href="currency_recharge_detail.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">充值明细</a>						
				</div>
			</div>

  
  
		<!--群发信息开始-->
		<div class="WSY_data">
			<!--列表按钮开始-->
			<div class="WSY_list">
			<form action="currency_recharge?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>" method="get" id="upform" name="upform" >
				<div style="margin-left:40px;margin-top:0px;">
					<div class="WSY_position1" style="float:left">
						<ul>		
							<li class="WSY_position_date tate001"  style='margin-right: 20px'>
								<p>时间：<input class="date_picker" type="text" name="begintime" id="begintime" value="<?php echo $begintime; ?>" readonly="readonly" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',maxDate:'#F{$dp.$D(\'endtime\')}'});"></p>
								<p style="margin-left:0px;">&nbsp;&nbsp;-&nbsp;&nbsp;<input class="date_picker" type="text" name="endtime" readonly="readonly" id="endtime" value="<?php echo $endtime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm',minDate:'#F{$dp.$D(\'begintime\')}'});"></p>
							</li>				
						</ul>
					</div>
					<span>关键词：</span>
					<input type="text" name="keyword" id="keyword" value="<?php echo $keyword;?>" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;"  > 
					<span>有效方式：</span>
					<select name="type" id="send_type" style="width:100px;height:25px;border:1px solid #ccc;border-radius:3px;">
						<option value="0" id="type_0">--全部--</option>
						<option value="1" id="type_1">--未发布--</option>
						<option value="2" id="type_2">--发布中--</option>
						<option value="3" id="type_3">--冻结--</option>	
						<option value="4" id="type_4">--已结束--</option>					
					</select>
					<input type="submit" class="my_search" id="my_search" value="搜索">
					<input type="button" class="my_search" id="my_excel"  value="导出">
					<input type="button" class="my_search" id="my_search"  value="添加" onclick="go_link('add_recharge')">
					<ul class="WSY_righticon">
						<li style="margin-top: 20px;margin-right: 60px;"><a href="pay_currency.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>">返回</a></li>
					</ul>
				</div>

			</form>
			</div>
			<!--列表按钮开始-->
			<!--表格开始-->
			<table width="97%" class="WSY_table WSY_t2" id="WSY_t1" style="border: 1px solid #D8D8D8;border-collapse: collapse;">
			 <thead class="WSY_table_header">
				<tr style="border:none">
					<th width="2%" >卡编号</th>
					<th width="5%" >卡名称</th>
					<th width="2%">发卡数量</th>
					<th width="4%">有效方式</th>
					<th width="8%">有效期</th>
					<th width="4%"><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>额</th>			
					<th width="6%">使用情况</th>
					<th width="4%">操作</th>
				</tr>
			</thead>
				<?php 
			while ($row = mysql_fetch_object($result)) {
					$keyid     = $row->id;
					$createtime= $row->createtime;
					$title      = $row->title;
					$num        = $row->num;
					$money      = $row->money;
					$starttime  = $row->starttime;
					$endtime1    = $row->endtime;
					$status    = (string)$row->status;
					$used       = $row->used;
					$not_used  = $row->not_used;			
					
					switch($status){
						case "1":
						   $style = "未发布";
						break;
						case "2":
						   $style = "发布中";
						break;
						case "3":
						   $style = "已冻结";
						break;
						case "4":
						   $style = "已结束";
						break;
						default:
						   $style = "未知";
						break;
					}
			?>
			 	<tr style="border:1px solid #D8D8D8">
					<td style="text-align: center;"><?php echo $keyid;?></td>
					<td style="text-align: center;"><?php echo $title;?></td>
					<td style="text-align: center;">
					<?php if($status !="1"){?>
					   <a style="cursor:pointer;color:#06a7e1;" onclick="currency_recharge_key_detail(<?php echo $keyid;?>)"><?php echo $num;?></a>
				  <?php }else{?> 
                        <?php echo $num;?>
				  <?php }?>
					</td>
					<td style="text-align: center;"><?php echo $style;?></td>
					<td style="text-align: center;"><?php echo $starttime."至".$endtime1;?></td>
					<td style="text-align: center;"><?php echo $money;?></td>
					<td style="text-align: center;">已充值：<?php echo $used;?>;待充值：<?php echo $not_used;?></td>
					<td class="WSY_t4" id="WSY_t4" style="text-align: center;">
					   <?php if($status == "1"){?>
							<a style="cursor:pointer;" class="WSY_operation" href="./add_currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&keyid=<?php echo $keyid;?>&pagenum=<?php echo $pagenum; ?>" title="编辑"><img src="../../../common/images_V6.0/operating_icon/icon05.png"></a>
					   <?php }?>
					   <?php if($status !="1"){?>
					        <a href="./currency_recharge_detail.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&keyid=<?php echo $keyid;?>&pagenum=<?php echo $pagenum; ?>" style="cursor:pointer;" class="WSY_operation" title="明细"><img src="../../../common/images_V6.0/operating_icon/icon30.png"></a>
					    <?php }?>
					   <?php if($status == "2"){?>							
							 <a href="javascript: G.ui.tips.confirm('您确定冻结此批次吗？','./add_currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&keyid=<?php echo $keyid;?>&op=stop&pagenum=<?php echo $pagenum; ?>')" title="冻结"><img src="../../../common/images_V6.0/operating_icon/icon50.png"></a>
					   <?php }?>
					    <?php if($status == "3"){?>							
							 <a href="javascript: G.ui.tips.confirm('您确定解冻此批次吗？','./add_currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&keyid=<?php echo $keyid;?>&op=thaw_stop&pagenum=<?php echo $pagenum; ?>')" title="解冻"><img src="../../../common/images_V6.0/operating_icon/icon49.png"></a>
					   <?php }?>
                       <?php if($status != "2"){?>					   
					   <a href="javascript: G.ui.tips.confirm('您确定删除吗？','./add_currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&keyid=<?php echo $keyid;?>&op=del&pagenum=<?php echo $pagenum; ?>')" title="删除"><img src="../../../common/images_V6.0/operating_icon/icon04.png"></a>
					   <?php }?>
					   <?php if($status == "1"){?>
					        <!--<a href="javascript: G.ui.tips.confirm('您确定发布吗？','./add_currency_recharge.php?customer_id=<?php //echo passport_encrypt((string)$customer_id);?>&keyid=<?php //echo $keyid;?>&op=send')" title="发布"><img src="../../../common/images_V6.0/operating_icon/icon11.png"></a>-->
							<a style="cursor:pointer;" title="发布" onclick="send_currency(<?php echo $keyid;?>)"><img src="../../../common/images_V6.0/operating_icon/icon82.png"></a>
					   <?php }?>
					   					 
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
			<!--群发信息结束-->
		</div>
				<div class="floatbox">
		    <p class="tishitext">导出字段选择</p>
		    <div class="checkboxsdiv">
		        <div><input type="checkbox" checked name="excel_field" value="id"><p>卡编号</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="title"><p>卡名称</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="num"><p>发卡数量</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="status"><p>有效方式</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="time"><p>有效期</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="money"><p><?php echo defined('PAY_CURRENCY_NAME')? PAY_CURRENCY_NAME: '购物币'; ?>额</p></div>
		        <div><input type="checkbox" checked name="excel_field" value="user_detail"><p>使用情况</p></div>
		    </div>
		    <div class="quanbuxuan">
		    	<input type="checkbox" id="allselects" checked="checked" value="全选"><p>全选</p>
		    </div>
		    <div class="subdivb">
		    	<input type="submit" class="floatinputs" value="确定">
		    	<input type="submit" class="floatinputc" value="取消">
		    </div>
		</div>
	</div>
<?php mysql_close($link); ?>
<script src="../../../js/fenye/jquery.page1.js"></script>
<script type="text/javascript">
		  var pagenum = <?php echo $pagenum ?>;
		  var count =<?php echo $page ?>;//总页数
			//pageCount：总页数
			//current：当前页
			var keyword   = "<?php echo $keyword;?>";
			var type      =  "<?php echo $type;?>";
            var begintime = "<?php echo $begintime;?>";
            var endtime   = "<?php echo $endtime;?>"; 

			$(".WSY_page").createPage({
				pageCount:count,
				current:pagenum,
				backFn:function(p){
				 document.location= "currency_recharge.php?pagenum="+p+"&begintime="+begintime+"&endtime="+endtime+"&keyword="+keyword+"&type="+type;
			   }
			});

		  var page = <?php echo $page ?>;
		  
		  function jumppage(){
			var a=parseInt($("#WSY_jump_page").val());
			if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
				return false;
			}else{
			document.location= "currency_recharge.php?pagenum="+a+"&begintime="+begintime+"&endtime="+endtime+"&keyword="+keyword+"&type="+type;
			}
		  }	
		</script>

	</div>
</div>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/fenye/fenye.css" media="all">
<script type="text/javascript" src="../../../js/tis.js"></script>
<script src="/wsy_pub/admin/static/js/sms_verification.js"></script>
<script>
   $(".WSY_columnnav_currency").find("a").eq(0).addClass('white1');
   $("#send_type option:eq(<?php echo $type;?>)").attr("selected",true);
	// 显示导出界面
	$("#my_excel").click(function(){
	    $(".floatbox").toggle();
	});

	// 导出取消
	$(".floatinputc").click(function(){
	    $(".floatbox").hide();
	}); 

	 // 全选
	$("#allselects").click(function(){    
	    if(this.checked){    
	        $(".checkboxsdiv :checkbox").attr("checked", true);   
	    }else{    
	        $(".checkboxsdiv :checkbox").attr("checked", false); 
	    }    
	});	
	//导出
	$(".floatinputs").click(function(){
		var text = [];
		$('input[name="excel_field"]:checked').each(function(){ 
			text.push($(this).val()); 
		}); 
		if(text.length == 0){
			alert("请至少选择一个字段！");
			return;
		}
		
		var send_type = $('#send_type').val();
		var customer_id = <?php echo $customer_id ?>;
		var begintime  = $('#begintime').val();
		var endtime = $('#endtime').val();
		var keyword = $('#keyword').val();
		
		if(!begintime)	begintime = 0;
		if(!endtime)	endtime = 0;		
		
		if(send_type==""){
			send_type = 0;
		}
		if(keyword==""){
			keyword = 0;
		}		
			
		var url_base_recharge='/weixin/plat/app/index.php/Excel/currency_recharge_excel/excel_fields/'+text+'/customer_id/'+customer_id+'/begintime/'+begintime+'/endtime/'+endtime+'/status/'+send_type+"/keyword/"+encodeURI(keyword);
		inti_per();
		ShowDIV('topLoader');	

		if (topLoaderRunning) {
			return;
		}
		topLoaderRunning = true;
		var oFunc = function () {
			url = url_base_recharge + '/limit_count/20/limit_p/'+obj_json.page+'/page_count/'+obj_json.page_count+'/count/'+obj_json.count+'/';

			$.ajax({type:'GET', async:false, url:url,
				success:function(data){
					obj_json = eval('('+data+')');
					if(obj_json.page_count<obj_json.page){
						closeDiv('topLoader');
						window.location.href=url+'output/go/';	
						
					}else{ }
				}		
			});
			
			glo_add = glo_add + glo_per;
			$topLoader.percentageLoader({progress: glo_add});
			$topLoader.percentageLoader({value: ('导出中，请勿刷新和关闭页面！')});
			if(glo_add<1){
				setTimeout(oFunc, 200);
			}else{
				topLoaderRunning = false;
			}
		}

		if(obj_json.length==0){
			$topLoader.percentageLoader({progress: glo_add});
			$topLoader.percentageLoader({value: ('导出中，请勿刷新和关闭页面！')});
			url = url_base_recharge + '/limit_count/20/limit_p/0/';
			$.ajax({type:'GET', async:false, url:url,
				success:function(data){
					obj_json = eval('('+data+')');
					glo_per = 1 / obj_json.page_count;
					setTimeout(oFunc, 1000);

				}
			});	
		}else{ }			
		 $(".floatbox").hide();		
	});	
		
	function go_link(type){
		switch(type){
			case "add_recharge":
			  location.href="./add_currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>";
			  break;
			default:
			  break;
		}
	}
	
	function send_currency(keyid){
		    var url_base = 'send_currency_recharge.php?customer_id=<?php echo $customer_id_en; ?>';
            
            inti_per();
            ShowDIV('topLoader');   

            if (topLoaderRunning) {
                return;
            }
            topLoaderRunning = true;
            var oFunc = function () {
                url = url_base + '&limit_count=1000&limit_p='+obj_json.page+'&page_count='+obj_json.page_count+'&count='+obj_json.count;
                $.ajax({type:'POST', async:false, url:url,
                        data:{keyid: keyid}, 
                        dataType: "json",
                    success:function(data){
                        obj_json = eval(data);
                        if((obj_json.page_count <= obj_json.page-1) || ((obj_json.page-1)*1000==obj_json.count)){
                            closeDiv('topLoader');							
							get_jump(url,keyid);	
                        }else{ }
                    }				
                });
                
                glo_add = glo_add + glo_per;
                // console.log(glo_add)
                $topLoader.percentageLoader({progress: glo_add});
                $topLoader.percentageLoader({value: ('正在发布中，请勿刷新！')});
                if(glo_add<1){
                    setTimeout(oFunc, 200);
                }else{
                    topLoaderRunning = false;
                }
            }

            if(obj_json.length==0){
                $topLoader.percentageLoader({progress: glo_add});
                $topLoader.percentageLoader({value: ('正在发布中，请勿刷新！')});
                url = url_base + '&limit_count=1000&limit_p=0';
                $.ajax({type:'POST', async:false, url:url, 
                        data:{keyid: keyid}, 
                        dataType: "json",
                    success:function(data){
                        obj_json = eval(data);
                        glo_per = 1 / obj_json.page_count;
                        setTimeout(oFunc, 1000);

                    }
                }); 
            }else{ } 
	}
	
	//模板信息发送动画
    var glo_add;
    var glo_per;//完成百份比
    var obj_json;
    var topLoaderRunning;
    var $topLoader;
    $(function() {
        inti_per();
    });

    function inti_per(){
        glo_add = 0.0;
        glo_per = 0.0;
        obj_json = new Array(); 
        $topLoader = $("#topLoader").percentageLoader({
            width: 256, height: 256, controllable: true, progress: glo_add, onProgressUpdate: function (val) {
              this.setValue(Math.round(val * 100.0) + '%初始化中，请勿刷新！');
              console.log(val)
            }
        });
        topLoaderRunning = false;   
    }
    
    function ShowDIV(thisObjID) {
        $("#BgDiv").css({ display: "block", height: $(document).height() });
        var yscroll = document.documentElement.scrollTop;
        $("#" + thisObjID).css("top", "100px");
        $("#" + thisObjID).css("display", "block");
        document.documentElement.scrollTop = 0;
    }

    function closeDiv(thisObjID) {
        $("#BgDiv").css("display", "none");
        $("#" + thisObjID).css("display", "none");
    }
	function get_jump(url,keyid){
		var jump_url = url+"&op=output";
		 $.ajax({type:'POST', async:false, url:jump_url,
				data:{keyid: keyid}, 
				dataType: "json",
				success:function(data){
					var code = data.code;
					var msg  = data.msg;
					if(code == 1){
						location.href="./currency_recharge.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&pagenum=<?php echo $pagenum; ?>";
					}
				},
				error:function(data){
					console.log(data);
				}					
		});
	}

   //短信验证+查看按钮
   function currency_recharge_key_detail(keyid) {
       //判断是否开启短信验证
       var param = [keyid]
       sms_check("currency_camilo", "go_currency_recharge_key_detail", param);
   }
   function go_currency_recharge_key_detail(keyid){
       location.href = "/weixinpl/back_newshops/Base/pay_currency/currency_recharge_key_detail.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&keyid=" + keyid + "&status=0";
   }
</script>
</body>
</html>
