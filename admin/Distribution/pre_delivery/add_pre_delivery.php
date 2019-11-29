<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

$keyid = -1;
$op = '';
$checkedId = '';
if( !empty($_GET['keyid']) ){
	$keyid = $configutil->splash_new($_GET["keyid"]);
}
if( !empty($_GET['op']) ){
	$op = $configutil->splash_new($_GET["op"]);
}
if( !empty($_GET['checkedId']) ){
	$checkedId = $configutil->splash_new($_GET["checkedId"]);
}

$supply_id = -1;	//供应商id
$supply_id_en = '';	//供应商id
if( !empty($_GET['supply_id']) && !empty($_SESSION['supplier_Acount']) && empty($_GET['customer_id']) ){
	$supply_id = $_SESSION['supplier_Acount'];
	$supply_id_en = $_GET['supply_id'];
} else if( !empty($_SESSION['supplier_Acount']) && empty($_GET['customer_id']) ) {
	die('操作异常！');
}

if( $op == 'del' ){
	$query_del = "UPDATE weixin_commonshop_pre_delivery SET isvalid=false WHERE id in (".$checkedId.")";
	_mysql_query($query_del) or die('Query_del failed:'.mysql_error());
	$query_del_p = "UPDATE weixin_commonshop_pre_delivery_product_relation SET isvalid=false WHERE delivery_id in (".$checkedId.") AND isvalid=true";
	_mysql_query($query_del_p) or die('Query_del_p failed:'.mysql_error());
	mysql_close($link);
	if( $supply_id > 0 ){
		echo "<script>location.href='pre_delivery_list.php?supply_id=".$supply_id_en."';</script>";
	} else {
		echo "<script>location.href='pre_delivery_list.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
	}
	
}

$delivery_name 	= '';	//名称
$delivery_time 	= '';	//配送时间段
$earliest_hour 	= '';	//最早配送时间所加的小时
$latest_hour 	= '';	//最晚配送时间所加的小时
$custom_date 	= '';	//自定义日期
$delivery_limit = 0;	//自选配送限制，0：早晚时间设置，1：自定义日期
$pid_arr[]		= -1;	//关联产品id数组
if( $keyid ){
	$query = "SELECT delivery_name,delivery_time,earliest_hour,latest_hour,custom_date,delivery_limit FROM weixin_commonshop_pre_delivery WHERE customer_id=".$customer_id." AND id=".$keyid." AND isvalid=true"." AND supply_id=".$supply_id;
	$result = _mysql_query($query) or die('Query failed:'.mysql_error());
	while( $row = mysql_fetch_object($result) ){
		$delivery_name 	= $row -> delivery_name;
		$delivery_time 	= $row -> delivery_time;
		$earliest_hour 	= $row -> earliest_hour;
		$latest_hour 	= $row -> latest_hour;
		$custom_date 	= $row -> custom_date;
		$delivery_limit = $row -> delivery_limit;
	}
	//查找关联产品
	$query_product_relation = "SELECT wcpdrr.pid 
								FROM weixin_commonshop_pre_delivery_product_relation AS wcpdrr
								INNER JOIN weixin_commonshop_products AS wcp ON wcpdrr.pid=wcp.id
								WHERE wcpdrr.customer_id=".$customer_id." AND wcpdrr.delivery_id=".$keyid." AND wcpdrr.isvalid=true AND wcp.isvalid=true AND wcp.isout=false AND wcp.isout_status=true AND wcp.is_QR=false AND wcp.is_virtual=false";
	$result_product_relation = _mysql_query($query_product_relation) or die('Query_product_relation failed:'.mysql_error());
	while( $row_product_relation = mysql_fetch_object($result_product_relation) ){
		$pid_arr[] = $row_product_relation -> pid;
	}
}

$pid_str = implode(',',$pid_arr);

?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link href="css/wnl.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../../js/tis.js"></script>
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="./js/select_date.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
a:hover{text-decoration: none;}   
.button_blue{cursor: pointer;margin-left: 10px;font-size: 14px;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:20px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.name{  margin-top: 10px;height: 30px;line-height: 30px;font-size: 13px;text-align: left;font-weight: bolder;margin-left: 19px;}
.button_box{width: 296px;display: block;text-align: right;}
.button_box .WSY_button{border-radius:2px;border:none;}    
.delivery-button{padding: 3px 15px;color: #fff;border-radius: 2px;cursor:pointer;}
.product-box{width:80%;margin-top: 15px;padding: 15px;border: 1px #ccc solid;display:none;}
.header-left{float:left;}
.header-right{float:right;}
.search-box{height: 22px;line-height: 22px;}
.delivery_time{margin: 5px 35px;}
.delivery_time input{margin: 0 20px;}
.delivery_limit_box{margin: 15px 35px;position: relative;} 
.delivery_font{font-size: 15px;font-weight: bold;}
.delivery_limit_box input[type=text]{margin: 0 20px;}
.selected-date-content{display: block;margin-left: 30px;margin-top: 10px;}
.page-box1{margin-left:30px;}
.page-box1,.page-box2{margin-top:15px;}
.show-data-num{margin:0 20px;}
.page-box1 input,.page-box2 input{width:30px;text-align:center;}
.show-data-num-btn{margin-right:30px;}
.current-page{margin: 0 20px;}
.go-page-btn{margin-left:15px;}
#to-page-num1,#to-page-num2{margin-left:20px;}
.relation_table th,.list_table th{text-align:center;}
#main{min-width:540px;position:absolute;padding-top: 10px;top: -397px;left: 172px;}
#main .mh-field,.mh-time-panel,.mh-dates-bar,.mh-desc,.mh-almanac-extra{font-size:12px;}
</style>
</head>
<body>
<div>  
    <div class="WSY_content">
		<div class="WSY_columnbox">
		<div class="WSY_column_header">
			<div class="WSY_columnnav">
				<a class="white1">时间设置</a>
			</div>
		</div>  
<form action="save_pre_delivery.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>&supply_id=<?php echo $supply_id;?>&supply_id_en=<?php echo $supply_id_en;?>" enctype="multipart/form-data" id="delivery-form" method="post">
    <!--<div class="name">
	    添加送货时间
	</div>-->
	<div id="products" class="r_con_wrap">
		<div style="margin-top:20px">
			<label class="delivery_font">名称：</label>
			<span class="input">
				<input type=text value="<?php echo $delivery_name ?>" style="width:250px;height:24px;" name="delivery_name" id="delivery_name" maxlength="20" />
			</span>
		</div>
		<div style="margin-top:20px">
			<label class="delivery_font">可选择产品：</label>
			<span class="delivery-button WSY-skin-bg" id="select-product">选择产品</span>
		</div>
		<div class="product-table" style="margin-top:20px">
			<table class="WSY_table relation_table" width="80%" id="WSY_t1">
				<colgroup>
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="12%">
					<col width="8%">
					<col width="8%">
					<col width="8%">
				</colgroup>
				<thead class="WSY_table_header">
					<th>ID</th>
					<th>产品图</th>
					<th>产品名称</th>
					<th>产品分类</th>
					<th>价格</th>
					<th>库存</th>
					<th>防伪二维码</th>
				</thead>
				
			</table>
		</div>
		<!-- 选择产品 -->
		<div class="product-box">
			<div class="header">
				<div class="header-left">
					<input type="text" class="search-box" id="search-box" placeholder="请输入关键字"  />
					<span class="delivery-button WSY-skin-bg" id="search-button">搜索</span>
				</div>
				<div class="header-right">
					<input type="checkbox" id="select-all" />
					<label for="select-all">全选</label>
					<span class="delivery-button WSY-skin-bg" id="confirm-select" style="margin: 0 35px;">确定</span>
				</div>
				<div class="clear"></div>
			</div>
			<table class="WSY_table list_table" width="100%" id="WSY_t1" style="margin-left:0;">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="12%">
					<col width="8%">
					<col width="8%">
					<col width="8%">
				</colgroup>
				<thead class="WSY_table_header">
					<th>勾选</th>
					<th>ID</th>
					<th>产品图</th>
					<th>产品名称</th>
					<th>产品分类</th>
					<th>价格</th>
					<th>库存</th>
					<th>防伪二维码</th>
				</thead>
			</table>
		</div>
		<div class="delivery-time-box" style="margin-top:20px">
			<label class="delivery_font">用户选择时间：</label>
			<?php
				$delivery_time_arr_len = 1;
				if( !empty($delivery_time) ){
					$delivery_time_arr = explode(',',$delivery_time);
					$delivery_time_arr_len = count($delivery_time_arr);
					
					for( $i = 0; $i < $delivery_time_arr_len; $i++ ){
						$time_arr = explode('_',$delivery_time_arr[$i]);
						$start_time = $time_arr[0];
						$end_time = $time_arr[1];
			?>
			<div class="delivery_time delivery_time<?php echo $i;?>">
				<input type="text" class="start-time" id="delivery-start-time<?php echo $i;?>" onfocus="WdatePicker({dateFmt:'H:mm',maxDate:'#F{$dp.$D(\'delivery-end-time<?php echo $i;?>\')}',autoPickDate:false});" onchange="changeTimeVal(this)" value="<?php echo $start_time;?>" readonly />
				至
				<input type="text" class="end-time" id="delivery-end-time<?php echo $i;?>" onfocus="WdatePicker({dateFmt:'H:mm',minDate:'#F{$dp.$D(\'delivery-start-time<?php echo $i;?>\')}',autoPickDate:false});" onchange="changeTimeVal(this)" value="<?php echo $end_time;?>" readonly />
				<span class="delivery-button WSY-skin-bg add-time add-time<?php echo $i;?>">新增时段</span>
				<span class="delivery-button WSY-skin-bg del-time del-time<?php echo $i;?>">删除时段</span>
			</div>
			<?php
					}
				} else {
			?>
			<div class="delivery_time delivery_time0">
				<input type="text" class="start-time" id="delivery-start-time0" onfocus="WdatePicker({dateFmt:'H:mm',maxDate:'#F{$dp.$D(\'delivery-end-time0\')}',autoPickDate:false});" onchange="changeTimeVal(this)" value="<?php echo $start_time;?>" readonly />
				至
				<input type="text" class="end-time" id="delivery-end-time0" onfocus="WdatePicker({dateFmt:'H:mm',minDate:'#F{$dp.$D(\'delivery-start-time0\')}',autoPickDate:false});" onchange="changeTimeVal(this)" value="<?php echo $end_time;?>" readonly />
				<span class="delivery-button WSY-skin-bg add-time add-time0">新增时段</span>
				<span class="delivery-button WSY-skin-bg del-time del-time0">删除时段</span>
			</div>
			<?php }?>
		</div>
		<div style="margin-top:20px">
			<label class="delivery_font">自选限制：</label>
			<div class="delivery_limit_box">
				<input type="radio" id="delivery_limit0" name="delivery_limit" value="0" <?php if($delivery_limit==0){ echo 'checked';}?> />
				<span class="delivery_font">最早时间<span style="color:red;margin:0 25px;">下单当天</span>加<input type="text" name="earliest_hour" id="earliest_hour" value="<?php echo $earliest_hour;?>" style="width:50px;text-align:center;" onkeyup="clearInt(this)" />小时<span style="margin-left:10px;display: inline-block;"><img src="../../Common/images/Base/help.png" onMouseOver="toolTip('例如：下单当天时间是2017-01-01 10:00:00，后台设置加8小时，那么用户可以选择的就是2017-01-01  18:00:00之后的时间段')" onMouseOut="toolTip()"></span></span>
				<span class="delivery_font" style="display: block;margin-left: 17px;margin-top: 20px;">最晚时间<span style="color:red;margin:0 25px;">下单当天</span>加<input type="text" name="latest_hour" id="latest_hour" value="<?php echo $latest_hour;?>" style="width:50px;text-align:center;" onkeyup="clearInt(this)" />小时<span style="margin-left:10px;display: inline-block;"><img src="../../Common/images/Base/help.png" onMouseOver="toolTip('例如：下单当天时间是2017-01-01 10:00:00，后台设置加72小时，那么用户可以选择的就是2017-01-04  10:00:00之前的时间段')" onMouseOut="toolTip()"></span></span>
			</div>
			<div class="delivery_limit_box">
				<input type="radio" id="delivery_limit1" name="delivery_limit" value="1" <?php if($delivery_limit==1){ echo 'checked';}?> />
				<span class="delivery-button WSY-skin-bg custom_date" onclick="customDate()">自定义日期</span>
				
<div id="main" style="display:none;">
<div id="so_top">

</div>
<ul id="m-result" class="result"><li id="first" class="res-list">
<div id="mohe-rili" class="g-mohe" data-mohe-type="rili">





<div class="mh-rili-wap mh-rili-only " data-mgd="{&quot;b&quot;:&quot;rili-body&quot;}">
	<div class="mh-tips" style="display: none;">
		<div class="mh-loading">
			<i class="mh-ico-loading"></i>正在为您努力加载中...
		</div>
		<div class="mh-err-tips">亲，出了点问题~ 您可<a href="#reload" class="mh-js-reload">重试</a></div>
	</div>
	<div class="mh-rili-widget">
								
<div class="mh-doc-bd mh-calendar">
	<div class="mh-hint-bar gclearfix">
		<div class="mh-control-bar">
			<div class="mh-control-module mh-year-control mh-year-bar">
				<a href="#prev-year" action="prev" class="mh-prev" data-md="{&quot;p&quot;:&quot;prev-year&quot;}"></a>
				<div class="mh-control">
					<i class="mh-trigger"></i>
					<div class="mh-field mh-year" val="2017">2017年</div>
				</div>
				<a href="#next-year" action="next" class="mh-next" data-md="{&quot;p&quot;:&quot;next-year&quot;}"></a>
				<ul class="mh-list year-list" style="display:none;" data-md="{&quot;p&quot;:&quot;select-year&quot;}"><li val="1901">1901年</li><li val="1902">1902年</li><li val="1903">1903年</li><li val="1904">1904年</li><li val="1905">1905年</li><li val="1906">1906年</li><li val="1907">1907年</li><li val="1908">1908年</li><li val="1909">1909年</li><li val="1910">1910年</li><li val="1911">1911年</li><li val="1912">1912年</li><li val="1913">1913年</li><li val="1914">1914年</li><li val="1915">1915年</li><li val="1916">1916年</li><li val="1917">1917年</li><li val="1918">1918年</li><li val="1919">1919年</li><li val="1920">1920年</li><li val="1921">1921年</li><li val="1922">1922年</li><li val="1923">1923年</li><li val="1924">1924年</li><li val="1925">1925年</li><li val="1926">1926年</li><li val="1927">1927年</li><li val="1928">1928年</li><li val="1929">1929年</li><li val="1930">1930年</li><li val="1931">1931年</li><li val="1932">1932年</li><li val="1933">1933年</li><li val="1934">1934年</li><li val="1935">1935年</li><li val="1936">1936年</li><li val="1937">1937年</li><li val="1938">1938年</li><li val="1939">1939年</li><li val="1940">1940年</li><li val="1941">1941年</li><li val="1942">1942年</li><li val="1943">1943年</li><li val="1944">1944年</li><li val="1945">1945年</li><li val="1946">1946年</li><li val="1947">1947年</li><li val="1948">1948年</li><li val="1949">1949年</li><li val="1950">1950年</li><li val="1951">1951年</li><li val="1952">1952年</li><li val="1953">1953年</li><li val="1954">1954年</li><li val="1955">1955年</li><li val="1956">1956年</li><li val="1957">1957年</li><li val="1958">1958年</li><li val="1959">1959年</li><li val="1960">1960年</li><li val="1961">1961年</li><li val="1962">1962年</li><li val="1963">1963年</li><li val="1964">1964年</li><li val="1965">1965年</li><li val="1966">1966年</li><li val="1967">1967年</li><li val="1968">1968年</li><li val="1969">1969年</li><li val="1970">1970年</li><li val="1971">1971年</li><li val="1972">1972年</li><li val="1973">1973年</li><li val="1974">1974年</li><li val="1975">1975年</li><li val="1976">1976年</li><li val="1977">1977年</li><li val="1978">1978年</li><li val="1979">1979年</li><li val="1980">1980年</li><li val="1981">1981年</li><li val="1982">1982年</li><li val="1983">1983年</li><li val="1984">1984年</li><li val="1985">1985年</li><li val="1986">1986年</li><li val="1987">1987年</li><li val="1988">1988年</li><li val="1989">1989年</li><li val="1990">1990年</li><li val="1991">1991年</li><li val="1992">1992年</li><li val="1993">1993年</li><li val="1994">1994年</li><li val="1995">1995年</li><li val="1996">1996年</li><li val="1997">1997年</li><li val="1998">1998年</li><li val="1999">1999年</li><li val="2000">2000年</li><li val="2001">2001年</li><li val="2002">2002年</li><li val="2003">2003年</li><li val="2004">2004年</li><li val="2005">2005年</li><li val="2006">2006年</li><li val="2007">2007年</li><li val="2008">2008年</li><li val="2009">2009年</li><li val="2010">2010年</li><li val="2011">2011年</li><li val="2012">2012年</li><li val="2013">2013年</li><li val="2014">2014年</li><li val="2015">2015年</li><li val="2016">2016年</li><li val="2017">2017年</li><li val="2018">2018年</li><li val="2019">2019年</li><li val="2020">2020年</li><li val="2021">2021年</li><li val="2022">2022年</li><li val="2023">2023年</li><li val="2024">2024年</li><li val="2025">2025年</li><li val="2026">2026年</li><li val="2027">2027年</li><li val="2028">2028年</li><li val="2029">2029年</li><li val="2030">2030年</li><li val="2031">2031年</li><li val="2032">2032年</li><li val="2033">2033年</li><li val="2034">2034年</li><li val="2035">2035年</li><li val="2036">2036年</li><li val="2037">2037年</li><li val="2038">2038年</li><li val="2039">2039年</li><li val="2040">2040年</li><li val="2041">2041年</li><li val="2042">2042年</li><li val="2043">2043年</li><li val="2044">2044年</li><li val="2045">2045年</li><li val="2046">2046年</li><li val="2047">2047年</li><li val="2048">2048年</li><li val="2049">2049年</li><li val="2050">2050年</li><li val="2051">2051年</li><li val="2052">2052年</li><li val="2053">2053年</li><li val="2054">2054年</li><li val="2055">2055年</li><li val="2056">2056年</li><li val="2057">2057年</li><li val="2058">2058年</li><li val="2059">2059年</li><li val="2060">2060年</li><li val="2061">2061年</li><li val="2062">2062年</li><li val="2063">2063年</li><li val="2064">2064年</li><li val="2065">2065年</li><li val="2066">2066年</li><li val="2067">2067年</li><li val="2068">2068年</li><li val="2069">2069年</li><li val="2070">2070年</li><li val="2071">2071年</li><li val="2072">2072年</li><li val="2073">2073年</li><li val="2074">2074年</li><li val="2075">2075年</li><li val="2076">2076年</li><li val="2077">2077年</li><li val="2078">2078年</li><li val="2079">2079年</li><li val="2080">2080年</li><li val="2081">2081年</li><li val="2082">2082年</li><li val="2083">2083年</li><li val="2084">2084年</li><li val="2085">2085年</li><li val="2086">2086年</li><li val="2087">2087年</li><li val="2088">2088年</li><li val="2089">2089年</li><li val="2090">2090年</li><li val="2091">2091年</li><li val="2092">2092年</li><li val="2093">2093年</li><li val="2094">2094年</li><li val="2095">2095年</li><li val="2096">2096年</li><li val="2097">2097年</li><li val="2098">2098年</li><li val="2099">2099年</li><li val="2100">2100年</li></ul>
			</div>
			<div class="mh-control-module mh-month-control mh-mouth-bar">
				<a href="#prev-month" action="prev" class="mh-prev" data-md="{&quot;p&quot;:&quot;prev-month&quot;}"></a>
				<div class="mh-control">
					<i class="mh-trigger"></i>
					<div class="mh-field mh-month" val="2">2月</div>
				</div>
				<a href="#next-month" action="next" class="mh-next" data-md="{&quot;p&quot;:&quot;next-month&quot;}"></a>
				<ul class="mh-list month-list" style="display:none;" data-md="{&quot;p&quot;:&quot;select-month&quot;}"><li val="1">1月</li><li val="2">2月</li><li val="3">3月</li><li val="4">4月</li><li val="5">5月</li><li val="6">6月</li><li val="7">7月</li><li val="8">8月</li><li val="9">9月</li><li val="10">10月</li><li val="11">11月</li><li val="12">12月</li></ul>
			</div>
			<div class="mh-control-module mh-holiday-control mh-holiday-bar">
				<div class="mh-control">
					<i class="mh-trigger"></i>
					<div class="mh-field mh-holiday" val="">2017年假日安排</div>
				</div>
				<ul class="mh-list" style="display:none;" data-md="{&quot;p&quot;:&quot;select-holiday&quot;}"><li val="" is_sel="{is_sel}">2017年假日安排</li><li val="20170101" is_sel="false">元旦</li><li val="20170128" is_sel="false">春节</li><li val="20170404" is_sel="false">清明节</li><li val="20170501" is_sel="false">劳动节</li><li val="20170530" is_sel="false">端午节</li><li val="20170903" is_sel="false">抗战纪念日</li><li val="20171004" is_sel="false">中秋节</li><li val="20171001" is_sel="false">国庆节</li></ul>
			</div>
			<div class="mh-btn-today" data-md="{&quot;p&quot;:&quot;btn-today&quot;}">返回今天</div>
		</div>
		<div class="mh-time-panel">
			<dl class="gclearfix">
				<dt class="mh-time-monitor-title">北京时间:</dt>
				<dd class="mh-time-monitor">10:02:38</dd>
			</dl>
		</div>
	</div>
	<div class="mh-cal-main">
		<div class="mh-col-1 mh-dates">
			<ul class="mh-dates-hd gclearfix">
				<li class="mh-days-title">一</li>
				<li class="mh-days-title">二</li>
				<li class="mh-days-title">三</li>
				<li class="mh-days-title">四</li>
				<li class="mh-days-title">五</li>
				<li class="mh-days-title mh-weekend">六</li>
				<li class="mh-days-title mh-last mh-weekend">日</li>
			</ul>
			<ol class="mh-dates-bd"><li class="mh-cross-month" date="2017/1/30" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">30</div><span class="mh-lunar">初三</span></li><li class="mh-cross-month" date="2017/1/31" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">31</div><span class="mh-lunar">初四</span></li><li class="mh-on" date="2017/2/1" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">1</div><span class="mh-lunar">初五</span></li><li class="mh-on mh-isolar-style" date="2017/2/2" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">2</div><span class="mh-lunar" title="世界湿地日">世界湿地日</span></li><li class="mh-on mh-solar-style" date="2017/2/3" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">3</div><span class="mh-lunar">立春</span></li><li class="mh-weekend" date="2017/2/4" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">4</div><span class="mh-lunar">初八</span></li><li class="mh-last mh-weekend" date="2017/2/5" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">5</div><span class="mh-lunar">初九</span></li><li class="" date="2017/2/6" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">6</div><span class="mh-lunar">初十</span></li><li class="" date="2017/2/7" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">7</div><span class="mh-lunar">十一</span></li><li class="" date="2017/2/8" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">8</div><span class="mh-lunar">十二</span></li><li class="" date="2017/2/9" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">9</div><span class="mh-lunar">十三</span></li><li class="" date="2017/2/10" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">10</div><span class="mh-lunar">十四</span></li><li class="mh-on mh-weekend mh-vsolar-style" date="2017/2/11" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">11</div><span class="mh-lunar">元宵节</span></li><li class="mh-last mh-weekend" date="2017/2/12" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">12</div><span class="mh-lunar">十六</span></li><li class="" date="2017/2/13" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">13</div><span class="mh-lunar">十七</span></li><li class="mh-vsolar-style" date="2017/2/14" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">14</div><span class="mh-lunar" title="西洋情人节">西洋情人节</span></li><li class="mh-work" date="2017/2/15" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">15</div><span class="mh-lunar">十九</span></li><li class="mh-today mh-on" date="2017/2/16" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">16</div><span class="mh-lunar">二十</span></li><li class="" date="2017/2/17" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">17</div><span class="mh-lunar">廿一</span></li><li class="mh-weekend mh-solar-style mh-rest mh-vacation" date="2017/2/18" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">18</div><span class="mh-lunar">雨水</span></li><li class="mh-last mh-weekend mh-rest mh-vacation" date="2017/2/19" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">19</div><span class="mh-lunar">廿三</span></li><li class="mh-rest mh-vacation" date="2017/2/20" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">20</div><span class="mh-lunar">廿四</span></li><li class="mh-rest mh-vacation" date="2017/2/21" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">21</div><span class="mh-lunar">廿五</span></li><li class="mh-rest mh-vacation" date="2017/2/22" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">22</div><span class="mh-lunar">廿六</span></li><li class="mh-rest mh-vacation" date="2017/2/23" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">23</div><span class="mh-lunar">廿七</span></li><li class="mh-rest mh-vacation" date="2017/2/24" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">24</div><span class="mh-lunar">廿八</span></li><li class="mh-weekend" date="2017/2/25" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">25</div><span class="mh-lunar">廿九</span></li><li class="mh-last mh-weekend" date="2017/2/26" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">26</div><span class="mh-lunar">二月大</span></li><li class="mh-vsolar-style" date="2017/2/27" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">27</div><span class="mh-lunar">龙头节</span></li><li class="mh-work" date="2017/2/28" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">28</div><span class="mh-lunar">初三</span></li><li class="mh-cross-month" date="2017/3/1" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">1</div><span class="mh-lunar">初四</span></li><li class="mh-cross-month" date="2017/3/2" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">2</div><span class="mh-lunar">初五</span></li><li class="mh-cross-month" date="2017/3/3" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">3</div><span class="mh-lunar">初六</span></li><li class="mh-cross-month mh-weekend" date="2017/3/4" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">4</div><span class="mh-lunar">初七</span></li><li class="mh-cross-month mh-last mh-weekend mh-solar-style" date="2017/3/5" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">5</div><span class="mh-lunar">惊蛰</span></li><li class="mh-cross-month" date="2017/3/6" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">6</div><span class="mh-lunar">初九</span></li><li class="mh-cross-month" date="2017/3/7" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">7</div><span class="mh-lunar">初十</span></li><li class="mh-cross-month mh-isolar-style" date="2017/3/8" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">8</div><span class="mh-lunar" title="国际妇女节">国际妇女节</span></li><li class="mh-cross-month" date="2017/3/9" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">9</div><span class="mh-lunar">十二</span></li><li class="mh-cross-month" date="2017/3/10" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">10</div><span class="mh-lunar">十三</span></li><li class="mh-cross-month mh-weekend" date="2017/3/11" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">11</div><span class="mh-lunar">十四</span></li><li class="mh-cross-month mh-last mh-weekend mh-isolar-style" date="2017/3/12" data-md="{&quot;p&quot;:&quot;day-box&quot;}"><span class="mh-border"></span><div class="mh-solar">12</div><span class="mh-lunar">植树节</span></li></ol>
		</div>
		<div class="mh-col-2 mh-almanac">
			<div class="mh-almanac-base mh-almanac-main"><div class="mh-dates-bar"><span class="mh-date">2017-02-16</span><span class="mh-weekday">星期四</span></div><div class="mh-date-show-panel">16</div><div class="mh-desc"><div class="mh-lunar">农历正月二十</div><div class="mh-lunar-ganzhi">丁酉年壬寅月甲戌日</div><div class="mh-other-info"><span class="mh-animal-year">[<em class="mh-animal">鸡年</em>]</span><span class="mh-astro">水瓶座</span></div><div class="mh-lunar-term"></div></div></div>
			<div class="mh-almanac-extra gclearfix" style="">
				<div class="mh-suited">
					<h3 class="mh-st-label">宜</h3>
					<ul class="mh-st-items gclearfix" title="塞穴 结网 取渔 畋猎"><li>塞穴</li><li>结网</li><li>取渔</li><li>畋猎</li></ul>
				</div>
				<div class="mh-tapu">
					<h3 class="mh-st-label">忌</h3>
					<ul class="mh-st-items gclearfix" title="嫁娶 安门 移徙 入宅 安葬"><li>嫁娶</li><li>安门</li><li>移徙</li><li>入宅</li><li class="cl">安葬</li></ul>
				</div>
			</div>
			
	
		</div>
        
	</div>
    
</div>

<span id="mh-date-y" style="display:block;">2017</span>

<script>
/*
2017节假日清单，一年一改
*/

(function(){window.OB=window.OB||{},window.OB.RiLi=window.OB.RiLi||{},window.OB.RiLi.dateRest=["0101","0102","0103","0218","0219","0220","0221","0222","0223","0224","0404","0405","0406","0501","0502","0503","0620","0621","0622","0903","0904","0905","0926","0927","1001","1002","1003","1004","1005","1006","1007"],window.OB.RiLi.dateWork=["0104","0215","0228","0906","1010"],window.OB.RiLi.dateFestival=["20170101||元旦","20170128||春节","20170404||清明节","20170501||劳动节","20170530||端午节","20170903||抗战纪念日","20171004||中秋节","20171001||国庆节"],window.OB.RiLi.dateAllFestival=["正月初一|v,春节","正月十五|v,元宵节","二月初二|v,龙头节","五月初五|v,端午节","七月初七|v,七夕节","七月十五|v,中元节","八月十五|v,中秋节","九月初九|v,重阳节","十月初一|i,寒衣节","十月十五|i,下元节","腊月初八|i,腊八节","腊月廿三|i,祭灶节","0202|i,世界湿地日,1996","0214|v,西洋情人节","0308|i,国际妇女节,1975","0315|i,国际消费者权益日,1983","0422|i,世界地球日,1990","0501|v,国际劳动节,1889","0512|i,国际护士节,1912","0518|i,国际博物馆日,1977","0605|i,世界环境日,1972","0623|i,国际奥林匹克日,1948","0624|i,世界骨质疏松日,1997","1117|i,世界学生日,1942","1201|i,世界艾滋病日,1988","0101|v,元旦","0312|i,植树节,1979","0504|i,五四青年节,1939","0601|v,儿童节,1950","0701|v,建党节,1941","0801|v,建军节,1933","0903|v,抗战胜利纪念日","0910|v,教师节,1985","1001|v,国庆节,1949","1224|v,平安夜","1225|v,圣诞节","w:0520|v,母亲节,1913","w:0630|v,父亲节","w:1144|v,感恩节(美国)","w:1021|v,感恩节(加拿大)"];var e="https://s.ssl.qhimg.com/!97be6a4c/data/"/*本地老黄历库在lhl文件夹，此处是官网调用的*/;location.protocol=="https:"&&(e="https://s.ssl.qhimg.com/!97be6a4c/data/")/*本地老黄历库在lhl文件夹，此处是官网调用的*/,window.OB.RiLi.hlUrl={2008:e+"hl2008.js",2009:e+"hl2009.js",2010:e+"hl2010.js",2011:e+"hl2011.js",2012:e+"hl2012.js",2013:e+"hl2013.js",2014:e+"hl2014.js",2015:e+"hl2015.js",2016:e+"hl2016.js",2017:e+"hl2017.js",2018:e+"hl2018.js",2019:e+"hl2019.js",2020:e+"hl2020.js"},window.OB.RiLi.dateHuochepiao=["-20141201||20","20141201||30","20141202||36","20141203||42","20141204||48","20141205||54","+20141205||60","c20141221-20141228||red"],window.OB.RiLi.useLunarTicketDay={2015:{"0218":"除夕","0219":"初一","0220":"初二","0221":"初三","0222":"初四","0223":"初五","0224":"初六","0225":"初七"}}})()</script>

			</div>
</div>

<div class="mh-rili-foot"></div>
    <div class="user-choice">
      您的选择是:
      <button class="dateDelete" style="margin-left:336px;">清空</button>
      <button class="dateConfirm" >确定</button>
	  <?php
		if( $custom_date != '' ){
			$custom_date_arr = explode(',',$custom_date);
			$custom_date_num = count($custom_date_arr);
			for( $i = 0; $i < $custom_date_num; $i++ ){
				$custom_date_arr_new = explode('-',$custom_date_arr[$i]);
				$custom_date_arr_new[1] = substr($custom_date_arr_new[1],1);
				$custom_date_new = $custom_date_arr_new[0].'/'.$custom_date_arr_new[1].'/'.$custom_date_arr_new[2];
	  ?>
	  <span date="<?php echo $custom_date_new;?>"><?php echo $custom_date_arr[$i];?></span>
	  <?php
			}
		}
	  ?>
    </div>
<select class="mh-holiday-data" style="display:none;">
	<option value="0" data-desc="" data-gl="">放假安排</option>
			<option value="抗战胜利纪念日" data-desc="9月3日至5日放假调休，共3天。9月6日（星期日）上班。" data-gl="">抗战胜利纪念日</option>
			<option value="国庆节" data-desc="10月1日至7日放假调休，共7天。10月10日（星期六）上班。" data-gl="">国庆节</option>
			<option value="中秋节" data-desc="9月27日放假。" data-gl="">中秋节</option>
			<option value="端午节" data-desc="6月20日放假，6月22日（星期一）补休。" data-gl="">端午节</option>
			<option value="劳动节" data-desc="5月1日放假，与周末连休。" data-gl="">劳动节</option>
			<option value="清明节" data-desc="4月5日放假，4月6日（星期一）补休。" data-gl="">清明节</option>
			<option value="春节" data-desc="2月18日至24日放假调休，共7天。2月15日（星期日）、2月28日（星期六）上班。" data-gl="">春节</option>
			<option value="元旦" data-desc="1月1日至3日放假调休，共3天。1月4日（星期日）上班。" data-gl="">元旦</option>
	</select>
      <!--value获取当前PHP服务器时间-->
<input type="hidden" id="mh-rili-params" value="">

</div><script>_loader.use("jquery",function(){function l(){t.slideDown(),r.slideDown(),i=="1"&&$.ajax({url:v("https://open.onebox.haosou.com/dataApi"),dataType:"jsonp",data:{query:"日历",url:"日历",type:"rili",user_tpl:"ajax/rili/html",selectorPrefix:s,asynLoading:i,src:"onebox",tpl:"1"},timeout:5e3,success:function(t){t&&t.html?(e.find(".mh-rili-widget").html(t.html),n.hide().addClass("mh-err"),i="0"):d()},error:function(){d()}})}function c(t,n){t=t.replace("\u6e05\u660e","\u6e05\u660e\u8282").replace("\u56fd\u9645\u52b3\u52a8\u8282","\u52b3\u52a8\u8282");var r=new RegExp(u);f=f||e.find("#mh-date-y").html(),u&&n==f&&r.test(t)?a=!0:a=!1,o.val(t).trigger("change")}function h(){$.each(o.find("option"),function(e,t){var n=$(this);n.data("desc")&&n.val()&&(u+=n.val()+"|")}),u=u.substring(0,u.length-2)}function p(){n.hide()}function d(){n.addClass("mh-err")}function v(e){return location.protocol=="https:"?"https://open.onebox.haosou.com/api/proxy?__url__="+encodeURIComponent(e):e}jQuery.curCSS=jQuery.css;var e=$("#mohe-rili"),t=$(".mh-rili-wap",e),n=$(".mh-tips",e),r=$(".mh-rili-foot",e),i="0",s="#mohe-rili .mh-rili-widget",o=e.find(".mh-holiday-data"),u="",a=!1,f=e.find("#mh-date-y").html();h(),e.on("click",".mh-op a",function(e){e.preventDefault();var n=$(this).closest(".mh-op");n.hasClass("mh-op-less")?(t.slideUp(),r.slideUp()):l(),n.toggleClass("mh-op-less")}).on("click",".mh-js-reload",function(e){e.preventDefault(),l()}).on("change",".mh-holiday-data",function(){var e=$(this),t=e.val(),n=e.find("option:selected"),i=n.attr("data-desc")||"",s=n.attr("data-gl")||"";if(!a||t=="0"||i===""&&s==="")r.html("");else{var o='';i&&(i="<p>"+i+"</p>"),s&&(s="<p><span>\u4f11\u5047\u653b\u7565\uff1a</span>"+s+"</p>"),o=o.replace("[holidayDetail]",i).replace("[holidaySug]",s),r.html(o)}}),window.OB=window.OB||{},window.OB.RiLi=window.OB.RiLi||{},window.OB.RiLi.rootSelector="#mohe-rili ",window.OB.RiLi.CallBack={afterInit:p,holiday:c}})</script>

<!--<div class="mh-rili-holiday">[holidayDetail][holidaySug]</div>节假日说明-->





	<script>

/**
 * 描述：本脚本是从360好搜扒下来的，别说我如何如何无耻，360扒的百度，百度扒的谷歌，就是这么屌！
    rili-widget 所包含的JS文件们
 * 共包含15个JS文件，由于彼此间存在依赖关系，它们的顺序必须依次是：
 *		1.jquery-ui-1.10.3.custom
 *		2.msg_config	// 配置事件消息
 *
 *		3.mediator	  //库，基于事件的异步编程
 *		4.calendar    //日历类
 *		5.lunar       //农历
 *
 *		6.cachesvc    //window. appdata依赖它
 *		7.appdata     //window. 时间矫正
 *		8.timesvc     //window.TimeSVC  时间同步服务
 *
 *		9.huochepiao    //购票（无用）
 *
 *		10.fakeSelect    //$-ui  年份月份下拉选择器
 *		11.speCalendar   //$-ui 日历单元格的特殊内容
 *		12.webCalendar   //$-ui 日历单元格
 *		13.dayDetail     //$-ui 日历右侧的详情（黄历 忌宜）
 *
 *		14.xianhao      //注册事件：日历上方的操作工具条：年月日节假日 返回今天
 *		15.dispatcher   //提取参数，初始化日历
 *
 * 最后拼接的顺序是 jquery-ui-1.10.3.custom,msg_config,mediator,calendar,lunar,cachesvc,appdata,timesvc,huochepiao,fakeSelect,speCalendar,webCalendar,dayDetail,xianhao,dispatcher
 *
 * edit by @gaosong 2015-08-31
 *
 * 代码从导航日历迁移过来，
 */
 
_loader.remove && _loader.remove("rili-widget");
_loader.add("rili-widget", "./js/wnl22.js");//上述JS文件们已让我压缩成wnl.js
_loader.use("jquery, rili-widget", function(){

	var RiLi = window.OB.RiLi;

	var gMsg = RiLi.msg_config,
		dispatcher = RiLi.Dispatcher,
		mediator = RiLi.mediator;

	var root = window.OB.RiLi.rootSelector || '';

	// RiLi.AppData(namespace, signature, storeObj) 为了解决"In IE7, keys may not contain special chars"
	//'api.hao.360.cn:rili' 仅仅是个 namespace
	var timeData = new RiLi.AppData('api.hao.360.cn:rili'),
		gap = timeData.get('timeOffset'),
		dt = new Date(new Date() - (gap || 0));

	RiLi.action = "default";

	var $detail = $(root+'.mh-almanac .mh-almanac-main');
	$detail.dayDetail(dt);

	RiLi.today = dt;

	var $wbc = $(root+'.mh-calendar'); 

	mediator.subscribe(gMsg.type.actionfestival , function (d){
		var holi = RiLi.dateFestival,
			val = d.val ? decodeURIComponent(d.val) : "",
			holiHash = {},
			el,
			node = {};

		for (var i = 0 ; i < holi.length ; ++i){
			el = holi[i];
			el = $.trim(el).split("||");
			if (el.length == 2){
				node = {};
				node.year = el[0].substr(0 , 4);
				node.month = el[0].substr(4 , 2);
				node.day = el[0].substr(6 , 2);
				holiHash[el[1]] = node;
			}
		};

		RiLi.action = "festival";
		
		if (holiHash[val]){
			node.year = holiHash[val].year;
			node.month = holiHash[val].month;
			node.day = holiHash[val].day;

			RiLi.needDay = new Date(parseInt(node.year , 10) , parseInt(node.month ,10) - 1 , node.day);
			$wbc.webCalendar({
				time : new Date(parseInt(node.year , 10) , parseInt(node.month ,10) - 1 , node.day),
				onselect: function(d, l){
					$detail.dayDetail('init', d , l);
				}
			}); 
		}
		else{
			RiLi.action = "default";
		}
	});	

	mediator.subscribe(gMsg.type.actionquery , function (d){
		var strDate;

		if (!d.year || d.year > 2100 || d.year < 1901){
			RiLi.action = "default";
			return 0;
		}
		
		d.month = parseInt(d.month , 10);

		if (d.month &&  (d.month > 12 || d.month < 1)){
			RiLi.action = "default";
			return 0;
		}

		if (!d.month){
			d.month = 1 ;
		}
		
		d.day = parseInt(d.day , 10);

		if (!d.day){
			d.day = 1;
		} 

		RiLi.action = "query";    	
		RiLi.needDay = new Date(parseInt(d.year , 10) , parseInt(d.month ,10) - 1 , d.day);

		$wbc.webCalendar({
			time : new Date(parseInt(d.year , 10) , parseInt(d.month ,10) - 1 , d.day),
			onselect: function(d, l){
				$detail.dayDetail('init', d , l);
			}
		}); 
	});

	mediator.subscribe(gMsg.type.actiongoupiao, function (d){
		RiLi.action = "goupiao";
		$wbc.webCalendar({
			time : dt,
			onselect: function(d, l){
				$detail.dayDetail('init', d , l);
			}
		}); 
	   
	});

	mediator.subscribe(gMsg.type.actiondefault , function (d){
		RiLi.needDay = dt;
		$wbc.webCalendar({
			time : dt,
			onselect: function(d, l){
				$detail.dayDetail('init', d , l);
			}
		}); 
	});    

	dispatcher.dispatch();

	mediator.subscribe(gMsg.type.dch , function (d){
		// if (RiLi.needDay){
		// 	$wbc.webCalendar("initTime" , RiLi.needDay);
		// }
		// else{
		// 	$wbc.webCalendar("initTime" , RiLi.today);
		// }
		$wbc.webCalendar("initTime" , RiLi.needDay||RiLi.today);
	});   
	
	mediator.publish(gMsg.type.dch ,  dt);

	var nowDate = (new Date()).getTime() ;

	/* 时间矫正 */
	RiLi.TimeSVC.getTime(function(d){
		var trueTime = d.getTime();
		var timeData = new RiLi.AppData('api.hao.360.cn:rili') , isFirst = true;

		if(Math.abs(nowDate - trueTime) > 300000){
			timeData.set('timeOffset', nowDate - trueTime);
		}
		else {
			timeData.remove('timeOffset');
		}

		if (typeof gap == undefined || !isFirst){
			RiLi.today = d;
			mediator.publish(gMsg.type.dch , d);
		}

		isFirst = false;
	});

	//日历初始完后的回调
	if(typeof RiLi.CallBack.afterInit === "function"){
		RiLi.CallBack.afterInit();
	}

});
</script>
</li></ul></div>
				<span class="selected-date-content">已选日期：
				<?php
					if( $custom_date != '' ){
						for( $i = 0; $i < $custom_date_num; $i++ ){
				?>
					<span class="selected-date-content-span" style="display: inline-block;margin-right: 10px;padding: 0 5px;background: #FFFFFF;border: 1px solid #d2d2d2;border-radius: 3px;margin-bottom: 5px;margin-top: 5px;"><?php echo $custom_date_arr[$i];?></span>
				<?php
						}
					}
				?>
				</span>
			</div>
			
		</div>
		
			<span class="button_box">
				<input type=button class="WSY_button"  value="提交" onclick="submitV();"  style="float:none"/>
				&nbsp;	
				<input type=button class="WSY_button"  value="取消" onclick="document.location='pre_delivery_list.php?<?php if($supply_id>0){echo 'supply_id='.$supply_id_en;}else{echo 'customer_id='.passport_encrypt((string)$customer_id);}?>';" style="float:none" />
			</span>
		</div> 
		<input type=hidden name="keyid" value="<?php echo $keyid;?>" />
		<input type=hidden name="product_relation" id="product_relation" value="<?php echo $pid_str;?>" />
		<input type=hidden name="delivery_time" id="delivery_time" value="<?php echo $delivery_time;?>" />
		<input type=hidden name="custom_date" id="custom_date" value="<?php echo $custom_date;?>" />
		</div>
	</div>
</form>
<div style="width:100%;height:20px;">
</div>
</div>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/ToolTip.js"></script>
<script>
var customer_id = '<?php echo $customer_id;?>';
var supply_id = '<?php echo $supply_id;?>';
var supply_id_en = '<?php echo $supply_id_en;?>';
var delivery_id = <?php echo $keyid;?>;
var timeNum = <?php echo $delivery_time_arr_len - 1 ;?>;
var pidArr = eval('<?php echo json_encode($pid_arr);?>');
var pidStr = '<?php echo $pid_str;?>';
// var pidStrNew = '<?php echo ','.$pid_str.',';?>';

var selectedLimitStart = 0,
	selectedLimitEnd = 4,
	selectedCurrentPage = 1,
	selectedEachPageNum = 5,
	selectedTotalPage = 1;
	
var showProductLimitStart = 0,
	showProductLimitEnd = 4,
	showProductCurrentPage = 1,
	showProductEachPageNum = 5,
	showProductTotalPage = 1,
	search_name = '';

$(document).ready(function(){
	get_product_relation(pidStr);
	get_all_product();
	
	$("#select-all").click(function() { // 全选/取消全部 
		if (this.checked == true) { 
			$(".product-list-checkbox").each(function() { 
				this.checked = true; 
				checkProductSelected(this);
			}); 
		} else { 
			$(".product-list-checkbox").each(function() { 
				this.checked = false;
				checkProductSelected(this);
			}); 
		} 
	});
});
//勾选产品触发事件
function checkProductSelected(obj){
	var pid = $(obj).val(),
		isChecked = $(obj).is(':checked'),
		checkedNum = $(".product-list-checkbox:checked").length;
	
	if( isChecked ){
		if( pidArr.indexOf(pid) == -1 ){
			pidArr.push(pid);
		}
	} else {
		if( pidArr.indexOf(pid) >= 0 ){
			pidArr.splice(pidArr.indexOf(pid),1);
		}
	}
	
	if( checkedNum == showProductEachPageNum ){
		$('#select-all').prop('checked',true);
	} else {
		$('#select-all').prop('checked',false);
	}
	pidStr = pidArr.join(',');
	$('#product_relation').val(pidStr);
}
//选择产品
$('body').on('click','#select-product',function(){
	$('.product-box').show();
	$('.product-table').hide();
	$(this).hide();
});
//确定关联产品
$('body').on('click','#confirm-select',function(){
	selectedLimitStart = 0,
	selectedLimitEnd = 4,
	selectedCurrentPage = 1,
	selectedEachPageNum = 5,
	selectedTotalPage = 1;
	// $('#product_relation').val(pidStr);
	get_product_relation(pidStr);
	$('.product-box').hide();
	$('.product-table').show();
	$('#select-product').show();
});
//增加时间段
$('body').on('click','.add-time',function(){
	var html = '';
	timeNum ++;
	
	html += '<div class="delivery_time delivery_time'+timeNum+'">';
	html += '	<input type="text" class="start-time" id="delivery-start-time'+timeNum+'" onfocus="WdatePicker({dateFmt:\'H:mm\',maxDate:\'#F{$dp.$D(\\\'delivery-end-time'+timeNum+'\\\')}\',autoPickDate:false});" onchange="changeTimeVal(this)" readonly />';
	html += '	至';
	html += '	<input type="text" class="end-time" id="delivery-end-time'+timeNum+'" onfocus="WdatePicker({dateFmt:\'H:mm\',minDate:\'#F{$dp.$D(\\\'delivery-start-time'+timeNum+'\\\')}\',autoPickDate:false});" onchange="changeTimeVal(this)" readonly />';
	html += '	<span class="delivery-button WSY-skin-bg add-time add-time'+timeNum+'">新增时段</span>';
	html += '	<span class="delivery-button WSY-skin-bg del-time del-time'+timeNum+'">删除时段</span>';
	html += '</div>';
	
	$('.delivery-time-box').append(html);
});
//删除时间段
$('body').on('click','.del-time',function(){
	var timeLen = $('.delivery_time').length;
	if( timeLen == 1 ){
		alert('亲，不能再删了！');
		return;
	}
	$(this).parent().remove();
});

function changeTimeVal(obj){
	var val = $(obj).val();
	
	if( val != '' && val != undefined ){
		var timeArr = val.split(':');
		$(obj).val(timeArr[0]+':'+timeArr[1]+':00');
	}
}
function changeTimeVal2(time){
	if( time != '' && time != undefined ){
		var timeArr = time.split(':');
		return timeArr[0]+':'+timeArr[1]+':00';
	}
}
function complateTime(time){		//加上当前日期，转换成时间戳格式进行比较
	if( time != '' && time != undefined ){
		var date = new Date();
		var m = date.getMonth() + 1;
		var fun_time = date.getFullYear()+"-"+m+"-"+date.getDate()+" "+time;

		return Date.parse(fun_time);
		// return fun_time;
	}
}
//获取关联产品
function get_product_relation(pid_str){
	$.ajax({
		url: 'get_product_relation.php?customer_id='+customer_id,
		dataType: 'json',
		data: {
			pid_str:pid_str,
			limitstart:selectedLimitStart,
			limitend:selectedEachPageNum,
			delivery_id:delivery_id,
			supply_id:supply_id
		},
		type: 'post',
		success: function(data){
			var dataLen = data.length,
				html = '',
				html_p = '';
			
			for( i in data ){
				html +='<tr class="product-info">';
				html +='	<td>'+data[i]['pid']+'</td>';
				html +='	<td><img src="'+data[i]['default_imgurl']+'" style="max-width:100%;height:80px;" ></td>';
				html +='	<td>'+data[i]['pname']+'</td>';
				html +='	<td>'+data[i]['type_name']+'</td>';
				html +='	<td>'+data[i]['now_price']+'</td>';
				html +='	<td>'+data[i]['storenum']+'</td>';
				html +='	<td>'+data[i]['qrcount']+'</td>';
				html +='</tr>';
			}
			if( dataLen > 0){
				//翻页
				selectedTotalPage = Math.ceil(data[i]['pcount'] / selectedEachPageNum);
				html_p +='<div class="page-box1">';
				html_p +='	<span class="data-num">共计'+data[i]['pcount']+'条记录</span>';
				// html_p +='	<span class="show-data-num">每页<input type="text" id="show-data-num" width="25" value="'+selectedEachPageNum+'" />条</span>';
				// html_p +='	<span class="delivery-button show-data-num-btn">确定</span> ';
				if( selectedCurrentPage > 1 ){	//当前是第一页不显示上一页
					html_p +='	<span class="delivery-button WSY-skin-bg page-left" onclick="goToLeftPage(1)">上一页</span> ';
				}
				html_p +='	<span class="current-page">当前第'+selectedCurrentPage+'页，共'+selectedTotalPage+'页</span> ';
				if( selectedCurrentPage < selectedTotalPage ){	//当前是最后一页不显示下一页
					html_p +='	<span class="delivery-button WSY-skin-bg page-right" onclick="goToRightPage(1)">下一页</span> ';
				}
				html_p +='	<input type="text" id="to-page-num1" width="25" value="'+selectedCurrentPage+'" >页 ';
				html_p +='	<span class="delivery-button WSY-skin-bg go-page-btn" onclick="goToPage(1)">跳转</span> ';
				html_p +='</div>';
			}
			
			$('.product-info').remove();
			$('.page-box1').remove();
			$('.relation_table').append(html);
			$('.product-table').append(html_p);
		},
		error: function(err){
			alert('获取关联产品出错！');
		}
	});
}
//获取所有产品
function get_all_product(){
	// if( arguments[0] != undefined ){
		// search_name = arguments[0];
	// }
	$.ajax({
		url: 'get_product_relation.php?customer_id='+customer_id,
		dataType: 'json',
		type: 'post',
		data: {
			search_name:search_name,
			limitstart:showProductLimitStart,
			limitend:showProductEachPageNum,
			delivery_id:delivery_id,
			supply_id:supply_id
		},
		success: function(data){
			var dataLen = data.length,
				html = '',
				html_p = '',
				checkedNum = 0;
			
			for( i in data ){
				html +='<tr class="product-list">';
				html +='	<td><input type="checkbox" class="product-list-checkbox" value="'+data[i]['pid']+'"  onclick="checkProductSelected(this)"';
				if( pidArr.indexOf(data[i]['pid']) >= 0 ){
					html += 'checked';
					checkedNum ++;
				}
				html += ' /></td>';
				html +='	<td>'+data[i]['pid']+'</td>';
				html +='	<td><img src="'+data[i]['default_imgurl']+'" style="max-width:100%;height:80px;" ></td>';
				html +='	<td>'+data[i]['pname']+'</td>';
				html +='	<td>'+data[i]['type_name']+'</td>';
				html +='	<td>'+data[i]['now_price']+'</td>';
				html +='	<td>'+data[i]['storenum']+'</td>';
				html +='	<td>'+data[i]['qrcount']+'</td>';
				html +='</tr>';
			}
			if( dataLen > 0){
				//翻页
				showProductTotalPage = Math.ceil(data[i]['pcount'] / showProductEachPageNum);
				html_p +='<div class="page-box2">';
				html_p +='	<span class="data-num">共计'+data[i]['pcount']+'条记录</span>';
				// html_p +='	<span class="show-data-num">每页<input type="text" id="show-data-num" width="25" value="'+showProductEachPageNum+'" />条</span>';
				// html_p +='	<span class="delivery-button show-data-num-btn">确定</span> ';
				if( showProductCurrentPage > 1 ){	//当前是第一页不显示上一页
					html_p +='	<span class="delivery-button WSY-skin-bg page-left" onclick="goToLeftPage(2)">上一页</span> ';
				}
				html_p +='	<span class="current-page">当前第'+showProductCurrentPage+'页，共'+showProductTotalPage+'页</span> ';
				if( showProductCurrentPage < showProductTotalPage ){	//当前是最后一页不显示下一页
					html_p +='	<span class="delivery-button WSY-skin-bg page-right" onclick="goToRightPage(2)">下一页</span> ';
				}
				html_p +='	<input type="text" id="to-page-num2" width="25" value="'+showProductCurrentPage+'" >页 ';
				html_p +='	<span class="delivery-button WSY-skin-bg go-page-btn" onclick="goToPage(2)">跳转</span> ';
				html_p +='</div>';
			}
			if( checkedNum == dataLen ){
				$('#select-all').prop('checked',true);
			} else {
				$('#select-all').prop('checked',false);
			}
			$('.product-list').remove();
			$('.page-box2').remove();
			$('.list_table').append(html);
			$('.product-box').append(html_p);
		},
		error: function(err){
			alert('获取产品列表出错！');
		}
	});
}
//搜索
$('#search-button').click(function(){
	search_name = $('#search-box').val();
	
	showProductCurrentPage = 1;
	showProductLimitStart = 0;
	showProductLimitEnd = showProductEachPageNum - 1;
	
	get_all_product();
});
//上一页
function goToLeftPage(type){
	if( type == 1 ){
		selectedCurrentPage --;
		selectedLimitStart -= selectedEachPageNum;
		selectedLimitEnd -= selectedEachPageNum;
		get_product_relation(pidStr);
	} else if( type == 2 ){
		showProductCurrentPage --;
		showProductLimitStart -= showProductEachPageNum;
		showProductLimitEnd -= showProductEachPageNum;
		get_all_product();
	}
}
//下一页
function goToRightPage(type){
	if( type == 1 ){
		selectedCurrentPage ++;
		selectedLimitStart += selectedEachPageNum;
		selectedLimitEnd += selectedEachPageNum;
		get_product_relation(pidStr);
	} else if( type == 2 ){
		showProductCurrentPage ++;
		showProductLimitStart += showProductEachPageNum;
		showProductLimitEnd += showProductEachPageNum;
		get_all_product();
	}
}
//自定义日期
function customDate(){
	$('#main').fadeToggle();
}
//跳转
function goToPage(type){
	if( type == 1 ){
		var pageNum = $('#to-page-num1').val();
		
		if( pageNum < 1 || pageNum > selectedTotalPage || pageNum == selectedCurrentPage ){
			return;
		}
		selectedCurrentPage = pageNum;
		
		selectedLimitStart = (selectedCurrentPage - 1) * selectedEachPageNum;
		
		selectedLimitEnd = selectedLimitStart + selectedEachPageNum - 1;
		
		get_product_relation(pidStr);
	} else if( type == 2 ){
		var pageNum = $('#to-page-num2').val();
		
		if( pageNum < 1 || pageNum > showProductTotalPage || pageNum == showProductCurrentPage ){
			return;
		}
		showProductCurrentPage = pageNum;
		
		showProductLimitStart = (showProductCurrentPage - 1) * showProductEachPageNum;
		
		showProductLimitEnd = showProductLimitStart + showProductEachPageNum - 1;
		
		get_all_product();
	}
}
//提交
function submitV(){
	var $deliveryTime = $('.delivery_time'),
		timeLen = $deliveryTime.length,
		time = '',
		delivery_name = $('#delivery_name').val();
	
	if( delivery_name == '' || (/^\s+$/g).test(delivery_name) || delivery_name == undefined ){
		alert('请输入名称！');
		return false;
	}
	
	for( var i = 0; i < timeLen ; i++ ){
		var startTime = $deliveryTime.eq(i).find('.start-time').val(),
			endTime = $deliveryTime.eq(i).find('.end-time').val();
		
		if( startTime == '' && endTime == '' ){
			alert('请填写完整时间段！');
			return false;
		}
		
		if( (startTime != '' && endTime == '') || (startTime == '' && endTime != '') ){
			alert('请填写完整时间段！');
			return false;
		}
		startTime = changeTimeVal2(startTime);
		endTime = changeTimeVal2(endTime);
		time += startTime+'_'+endTime+',';
	}
	// console.log(startTime+"---"+time);

	for( var i = 0; i < timeLen ; i++ ){
		var startTime = $deliveryTime.eq(i).find('.start-time').val(),
			endTime = $deliveryTime.eq(i).find('.end-time').val();

			console.log(startTime+"|"+endTime);

			startTime = complateTime(startTime);
			endTime = complateTime(endTime);

		for( var j = i+1; j < timeLen ; j++ ){
			var startTime_other = $deliveryTime.eq(j).find('.start-time').val(),
			endTime_other = $deliveryTime.eq(j).find('.end-time').val();

			console.log(startTime_other+"|"+endTime_other);

			startTime_other = complateTime(startTime_other);
			endTime_other = complateTime(endTime_other);

			// console.log(startTime+"|"+startTime_other+"|"+endTime+"|"+endTime_other);

			if(startTime > startTime_other && startTime > endTime_other && endTime > endTime_other && endTime > startTime_other){
				
			}else if(startTime < startTime_other && startTime < endTime_other && endTime < endTime_other && endTime < startTime_other){
				
			}else{
				alert('设置的时间段不能有交集！');
				return false;
			}
		}
	}
	
	time = time.substring(0,time.length-1);
	if( time == '' || time == undefined ){
		alert('请填写时间段！');
		return false;
	}
	$('#delivery_time').val(time);
	
	var $delivery_limit0 = $('#delivery_limit0'),
		$delivery_limit1 = $('#delivery_limit1'),
		earliest_hour = $('#earliest_hour').val(),
		latest_hour = $('#latest_hour').val(),
		custom_date = $('#custom_date').val();
		
	if( $delivery_limit0.is(':checked') ){
		if( earliest_hour == '' ){
			alert('请填写最早时间！');
			return false;
		}
		
		if( latest_hour == '' ){
			alert('请填写最晚时间！');
			return false;
		}
		
		if( parseInt(earliest_hour) >= parseInt(latest_hour) ){
			alert('最早时间必须小于最晚时间！');
			return false;
		}
	}
	
	if( $delivery_limit1.is(':checked') ){
		if( custom_date == '' ){
			alert('请选择日期！');
			return false;
		}
	}
	
	$('#delivery-form').submit();
}

//正整数
function clearInt(obj){
	if(obj.value.length==1){obj.value=obj.value.replace(/[^1-9]/g,'')}else{obj.value=obj.value.replace(/\D/g,'')}
}
</script>

</body>
</html>

