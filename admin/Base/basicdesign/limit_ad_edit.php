<?php
 header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");
require('../../../../weixinpl/proxy_info.php');
$head=4;//头部文件---限时广告图管理
    // echo '<pre>';
    // var_dump($_SESSION);
    //     echo '</pre>';
$id=$_GET['id'];
if (!empty($id)) {
    $query="select a.id,a.name,a.show_type,a.timelimit_type,a.start_time,a.end_time,b.show_time,b.imgurl,b.link,b.link_type,b.select_value,b.detail_value from weixin_commonshop_ads as a left join weixin_commonshop_ad_imgs as b on a.id = b.ad_id where a.id=".$id." and a.isvalid=true and a.customer_id=".$customer_id;
    $result=_mysql_query($query) or die ('query faild' .mysql_error());
    while($row=mysql_fetch_object($result)){
        $name = $row->name;
        $show_type = $row->show_type;
        $timelimit_type = $row->timelimit_type;
        $start_time = $row->start_time;
        $end_time = $row->end_time;
        $show_time = $row->show_time;
        $imgurl = $row->imgurl;
        $link = $row->link;
        $link_type = $row->link_type;
        $select_value = $row->select_value;
        $detail_value = $row->detail_value;
    }
    $imgurl = explode('|',$imgurl);
    $link = explode('|',$link);
    $link_type = explode('|',$link_type);
    $select_value = explode('|',$select_value);
    $detail_value = explode('|',$detail_value);

    // var_dump($detail_value);
}
//商城链接
$fixedlink['16']="首页";
$fixedlink['6']="全部产品";
$fixedlink['2']="新品上市";
$fixedlink['3']="热卖产品";
$fixedlink['4']="购物车";
$fixedlink['8']="个人中心";
$fixedlink['18']="我的订单";
$fixedlink['9']="我的微店";
$fixedlink['7']="产品分类页";
$fixedlink['17']="产品分类页2";
$fixedlink['33']="区域批发商列表";
$fixedlink['5']="限时抢购";
$fixedlink['10']="商城在线客服";
$fixedlink['11']="礼包列表";
$fixedlink['12']="VP产品";
$fixedlink['15']="积分专区";
$fixedlink['19']="拼团列表";
$fixedlink['20']="人气团列表";
$fixedlink['21']="续费专区";
$fixedlink['22']="电商直播";
$fixedlink['23']="语音直播";
$fixedlink['24']="票务特价机票";
$fixedlink['25']="票务特价火车票";
$fixedlink['26']="F2C系统中心";
$fixedlink['27']="订货系统登录";
$fixedlink['28']="订货系统申请";
$fixedlink['29']="订货系统中心";

//分类排序
$sort_str = "";
$type_sort = "SELECT sort_str FROM weixin_commonshop_type_sort WHERE customer_id=".$customer_id;
$result_sort = _mysql_query($type_sort) or die ('type_sort failed:'.mysql_error());
while( $row_sort = mysql_fetch_object($result_sort) ){
   $sort_str = $row_sort -> sort_str;
}

$query = "select id, name from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=-1 and customer_id=".$customer_id;

if( $sort_str ){
    $query .= ' order by field(id'.$sort_str.')';
}
$type_arr = array();
$ctype_arr = array();
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $pt_id = $row->id;
    $pt_name = $row->name;
    // $type_str = $pt_id."_".$pt_name;
    // $type_arr[] = $type_str;
    $type_arr[$row->id] = $row->name;

    $query_child = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pt_id;
    $result_child = _mysql_query($query_child) or die("Query child failed:".mysql_error());
    while($row_child = mysql_fetch_object($result_child)){
        $pc_id = $row_child->id;
        $ctype_arr[$pt_id][$pc_id] = $row_child->name;

        $query_child3 = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pc_id;
        $result_child3 = _mysql_query($query_child3) or die("Query child failed3:".mysql_error());
        while($row_child3 = mysql_fetch_object($result_child3)){
            $pc_id3 = $row_child3->id;
            $ctype_arr[$pc_id][$pc_id3] = $row_child3->name;

            $query_child4 = "select id,name from weixin_commonshop_types where isvalid=true and is_shelves=1 and customer_id=".$customer_id." and parent_id=".$pc_id3;
            $result_child4 = _mysql_query($query_child4) or die("Query child failed4:".mysql_error());
            while($row_child4 = mysql_fetch_object($result_child4)){
                $pc_id4 = $row_child4->id;
                $ctype_arr[$pc_id3][$pc_id4] = $row_child4->name;
            }
        }
    }
}
/* 8.1分类 */

//图文信息
$query = 'SELECT id,title FROM weixin_subscribes where isvalid=true and parent_id=-1 and is_message=0 and customer_id='.$customer_id;
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
      $sub_id =  $row->id ;
      $title = $row->title;
      $tu[$row->id] = $row->title;
}

//城市商圈，渠道开关
$is_cityarea=0;
$is_cityarea_count=0;
$query="select count(1) as is_cityarea_count from customer_funs cf inner join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and (c.sys_name='商圈-美食' or c.sys_name='商圈-外卖' or c.sys_name='商圈-金融保险' or c.sys_name='商圈-酒店' or c.sys_name='商圈-ktv' or c.sys_name='商圈-线下商城')";
$result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $is_cityarea_count = $row->is_cityarea_count;
}
if($is_cityarea_count>0){
   $is_cityarea=1;
}

$is_cityarea_caterer = 0;
$is_cityarea_ktv     = 0;
$is_cityarea_hotel   = 0;
$is_cityarea_shop    = 0;

if($is_cityarea){
    //城市商圈（美食），渠道开关
    $query="select count(1) as is_cityarea_caterer from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-美食'";
    $result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
       $is_cityarea_caterer = $row->is_cityarea_caterer;
    }

    if($is_cityarea_caterer){
        //店铺数据
        $query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=2 and customer_id=".$customer_id;
        $result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
        while($supply_row = mysql_fetch_object($result)){
            $m[$supply_row->id] = $supply_row->shop_name;
        }
        //店铺数据 End
        $cityarea_industry[]="美食";
    }
    //城市商圈（美食），渠道开关 End

    //城市商圈（KTV），渠道开关
    $query="select count(1) as is_cityarea_ktv from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-ktv'";
    $result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
       $is_cityarea_ktv = $row->is_cityarea_ktv;
    }

    if($is_cityarea_ktv){
        //店铺数据
        $query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=30 and customer_id=".$customer_id;
        $result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
        while($supply_row = mysql_fetch_object($result)){
            $ktv[$supply_row->id] = $supply_row->shop_name;
        }
        //店铺数据 End
        $cityarea_industry[]="KTV";
    }
    //城市商圈（KTV），渠道开关 End
    //
// echo  $is_cityarea_ktv."===<br>";
// var_dump($cityktv);
// die;

    //城市商圈（酒店），渠道开关
    $query="select count(1) as is_cityarea_hotel from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-酒店'";
    $result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
       $is_cityarea_hotel = $row->is_cityarea_hotel;
    }

    if($is_cityarea_hotel){
        //店铺数据
        $query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=60 and customer_id=".$customer_id;
        $result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
        while($supply_row = mysql_fetch_object($result)){
            $j[$supply_row->id] = $supply_row->shop_name;
        }
        //店铺数据 End
        $cityarea_industry[]="酒店";
    }
    //城市商圈（酒店），渠道开关 End

    //城市商圈（线下商城），渠道开关
    $query="select count(1) as is_cityarea_shop from customer_funs cf left join columns c on c.id=cf.column_id where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商圈-线下商城'";
    $result = _mysql_query($query) or die('W_is_supplier Query failed: ' . mysql_error());
    while ($row = mysql_fetch_object($result)) {
       $is_cityarea_shop = $row->is_cityarea_shop;
    }

    if($is_cityarea_shop){
        //店铺数据
        $query = "select id,shop_name from weixin_cityarea_supply where isvalid=true and types=20 and customer_id=".$customer_id;
        $result = _mysql_query($query) or die("L7357 : query error  : ".mysql_error());
        while($supply_row = mysql_fetch_object($result)){
            $x[$supply_row->id] = $supply_row->shop_name;
        }
        //店铺数据 End
        $cityarea_industry[]="线下商城-首页";
        $cityarea_industry[]="线下商城-商家列表";
    }
    //城市商圈（线下商城），渠道开关 End

    //城市商圈（金融）
    $cityarea_industry[]="金融-贷款";
    $cityarea_industry[]="金融-信用卡";
    $cityarea_industry[]="金融-保险";
    //城市商圈（金融） End
}

//品牌供应商店铺
$isOpenBrandSupply=0;//是否开启品牌供应商
$user_id=0;//供应商ID
$brand_supply_name="";//供应商名称
$check_brand="select isOpenBrandSupply from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$check_brand_result=_mysql_query($check_brand) or die ('check_brand faild ' .mysql_error());
while($row=mysql_fetch_object($check_brand_result)){
    $isOpenBrandSupply=$row->isOpenBrandSupply;
}
if($isOpenBrandSupply){//开启品牌供应商就查询品牌供应商店铺信息
    $brand="select user_id,brand_supply_name from weixin_commonshop_brand_supplys where isvalid=true and brand_status=1 and customer_id=".$customer_id."";
    $brand_result=_mysql_query($brand) or die ('brand faild' .mysql_error());
    while($row=mysql_fetch_object($brand_result)){
        $p[$row->user_id] = $row->brand_supply_name;
    }

}

//微视直播房间
$query_weishi = "select r.id,r.title from weixin_os_room r inner join weixin_os_anchor a on r.anchor_id=a.id where r.isvalid=true and a.isvalid=true and a.customer_id=".$customer_id;
$result_weishi = _mysql_query($query_weishi) or die('query_weishi failed:'.mysql_error());
while( $row_weishi = mysql_fetch_object($result_weishi) ){
    $a[$row_weishi->id] = $row_weishi->title;
}

?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title>限时广告图管理</title>
	<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
	<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/limit_ad.css">
	<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/swiper.min.css">
	<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
</head>
<body>
<form action="save_model.php?opid=<?php echo $id; ?>" id="form_model" enctype="multipart/form-data" method="post" onsubmit="return false">
<div class="WSY_content">
	<div class="WSY_columnbox">
	<?php
		include("../../../../weixinpl/back_newshops/Base/basicdesign/basic_head.php");
	?>
	<div class="limit-top-imfor">
		<div class="limit-list">
			<div class="limit-left">广告图名称：</div>
			<div class="limit-right"><input type="text" name="name" placeholder="请输入广告图名称" value="<?php if(!empty($name)) echo $name; ?>" class="limit-name limit-input"></div>
		</div>
		<div class="limit-list">
			<div class="limit-left">广告图显示时间：</div>
			<div class="limit-right" id="show-time">
				<label for="t1">
					<input type="radio" name="time" id="t1" value="1" <?php if($timelimit_type == 0) echo 'checked="checked"'; ?> > 永久
				</label>
				<label for="t2">
					<input type="radio" name="time" id="t2" value="2" <?php if($timelimit_type == 1) echo 'checked="checked"'; ?> > 自定义时间：
				</label>
				<div class="show-times" <?php if($timelimit_type == 1) {echo 'style';} else {echo 'style="display:none;"';}  ?> >
					<input class="limit-input" type="text" name="begintime" id="begintime" value="<?php if($timelimit_type != 0) echo $start_time; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'endtime\')}'});" value=""> 至
					<input class="limit-input" type="text" name="endtime" id="endtime" value="<?php if($timelimit_type != 0) echo $end_time; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'begintime\')}'});" value="">
				</div>
			</div>
		</div>
		<div class="limit-list">
			<div class="limit-left">广告图模式：</div>
			<div class="limit-right view-radio">
				<label for="s1">
					<input type="radio" name="screen" id="s1" <?php if($show_type == 0) echo 'checked="checked"'; ?> value="half"> 半屏
				</label>
				<label for="s2">
					<input type="radio" name="screen" id="s2" <?php if($show_type == 1) echo 'checked="checked"'; ?> value="full"> 全屏
				</label>
			</div>
		</div>
	</div>
	<!-- 半屏 -->
	<div class="limit-view limit-view-half" <?php if(!empty($show_type) && $show_type == 1) {echo 'style="display: none;"';} else {echo 'style="display: block;"';} ?>>
		<div class="limit-view-left">
			<div class="preview-box" style="background-image:url(../../Common/images/Base/basicdesign/limit_bg_img.jpg);">
				<div class="mask-cloce"><img src="../../Common/images/Base/basicdesign/cloce.png"></div>
				<div class="mask-box"></div>
				<div class="view-half-img" style="<?php if(!empty($imgurl)) echo "background-image: url(".$imgurl[0].")"; ?>;"></div>
			</div>
		</div>
		<div class="limit-view-right">
			<div class="upimg">
				<div class="small">
					<p>宽度：在600px以内，高度：在900px以内</p>
					<p>大小：限制在100k以内</p>
					<p>格式限制：JPG,JPEG,PNG,GIF,BMP；</p>
				</div>
				<div class="show-img">
					<img src="<?php if(!empty($imgurl)){echo $imgurl[0];}else{echo '../../Common/images/Base/basicdesign/pic_icon.png';} ?>" class="img_src0">

					<input type="hidden" name="img_src0">
				</div>
				<div class="upimg-btn">
					上传
					<input type="file" name="file_0" onchange="half_up_img(this);">
				</div>
				<div class="search">
					<input type="text" name="search">
					<button onclick="che(this);">搜索</button>
				</div>
				<div class="img-link">
					<label><input id="lian" type="radio" name="img_link" value=1 checked><span>链接页面：</span></label>
					<div class="select-list">
						<select id="link_page" onchange="links_1(this);" class="link-page">
                            <optgroup label="---------------商城类链接---------------">
                            <?php foreach($fixedlink as $key => $val) { ?>
                                <option value="1_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="---------------产品分类---------------">
                                <option value="2">多级分类</option>
                            </optgroup>
                            <optgroup label="---------------图文消息---------------">
                                <?php foreach($tu as $key => $val) { ?>
                                <option value="3_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="---------------商圈-美食---------------">
                                <?php foreach($m as $key => $val) { ?>
                                <option value="4_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="---------------商圈-KTV---------------">
                                <?php foreach($ktv as $key => $val) { ?>
                                <option value="5_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="---------------商圈-酒店---------------">
                                <?php foreach($j as $key => $val) { ?>
                                <option value="6_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="---------------商圈-线下商城---------------">
                                <?php foreach($x as $key => $val) { ?>
                                <option value="7_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="---------------商圈行业列表---------------">
                                <?php foreach($cityarea_industry as $key => $val) { ?>
                                <option value="8_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup id="9" label="---------------品牌供应商店铺---------------">
                                <?php foreach($p as $key => $val) { ?>
                                <?php if(!empty($val)) { ?>
                                <option value="9_<?php echo $key ?>"><?php echo $val ?></option>
                                <?php }} ?>
                            </optgroup>
                            <optgroup id="10" label="---------------商城直播系统---------------">
                                <option value="10">直播房间</option>
                            </optgroup>
						</select>
						<select id="type" class="link-page" style="display:none;" onchange="changeProductType()">
                            <?php foreach($type_arr as $key => $val) { ?>
                                <option value="2_<?php echo $key ?>"><?php echo $val ?></option>
    							<?php foreach($ctype_arr[$key] as $k => $v) { ?>
                                <option value="2_<?php echo $k ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v ?></option>
                                <?php foreach($ctype_arr[$k] as $kk => $vv) { ?>
                                <option value="2_<?php echo $kk ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vv ?></option>
                                <?php foreach($ctype_arr[$kk] as $kkk => $vvv) {?>
                                <option value="2_<?php echo $kkk ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vvv ?></option>
                                <?php }}}} ?>
						</select>
						<select id="ctype" onchange="ctype_che()" style="display:none;" class="link-page">
                           <!--  <option>---请选择---</option> -->
                        </select>
                        <select id="zhibo" onchange="zhibo_che()" style="display:none;" class="link-page">
                            <?php foreach($a as $key => $val) { ?>
                                <option value="10_<?php echo $key ?>"><?php echo $val ?></option>
                            <?php } ?>
                        </select>
					</div>

				</div>
				<div class="img-link">
					<label><input type="radio" name="img_link" value=2 <?php if($link_type[0]==0){echo 'checked';} ?>><span>填写链接：</span></label>
					<input type="text" name="link-url" class="link-url" placeholder="请输入链接网址"  value="<?php if($link_type[0]==0){echo $link[0];} ?>">
				</div>
			</div>
            <div class="limit-footer" style="display:inline-block;">
                <button type="submit" onclick="save()">提交保存</button>
                <button onclick="javascript:history.back(-1);">取消</button>
            </div>
		</div>
	</div>
	<!-- 全屏 -->
	<div class="limit-view limit-view-full" <?php if(!empty($show_type) && $show_type == 1) {echo 'style="display: block;"';} else {echo 'style="display: none;"';} ?>>
		<div class="limit-view-left">
			<div class="swiper-container preview-box" id="view-full" style="background-image:url(../../Common/images/Base/basicdesign/limit_bg_img.jpg);">
				<div class="mask-cloce">跳过 ></div>
				<div class="swiper-wrapper">

				</div>
			</div>
		</div>
        <div style="display:inline-block;vertical-align:top;">
    		<div class="limit-view-right">
    			<div class="view-full-box">
    				<div class="small">
    					<p>宽度：720px，高度：1280px，大小：限制在200k以内，格式要求：JPG,JPEG,PNG,GIF,BMP；</p>
    					<p>可上传5张图片</p>
    					<p class="stop-time">广告时间设置：每张图片停留时间：<input type="text" name="stop_time" oninput='clearNoNum(this,2)' value="<?php if(!empty($show_time)) echo $show_time?>">秒；<span>（备注：只有一张图片时，则不轮播）</span></p>
    				</div>
    				<ul class="view-full-list">
                    <?php if(!empty($imgurl)) {$i=1; foreach($imgurl as $k_img => $v_img) { ?>
                        <li>
                            <div class="img">
                                <img <?php echo 'src="'.$v_img.'"'; ?>>
                                <input type="hidden" name="img_src[]" <?php echo 'value="'.$v_img.'"'; ?> >
                                <input type="file" class="file" name="file[]" onchange="up_img(this);">
                            </div>
                            <div class="link">
                                <div>
                                    <label for="l1"><input type="radio" <?php if($i==1){echo 'name="img_link1" id="l1"';} else{echo 'name="img_link'.$i.'" id="l'.$i.'"';} ?> value=1 checked="checked">链接到：</label>
                                    <div class="select-list">
                                        <select onchange="change_link(this);" name="page[]" class="link-page" >
                                            <optgroup label="---------------商城类链接---------------">
                                            <?php foreach($fixedlink as $key => $val) { ?>
                                                <option value="1_<?php echo $key ?>" <?php if($link_type[$k_img] == "1_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php }?>
                                            </optgroup>
                                            <optgroup label="---------------产品分类---------------">
                                                <option value="2" <?php if($link_type[$k_img] == 2) echo 'selected="selected"'; ?>>多级分类</option>
                                            </optgroup>
                                            <optgroup label="---------------图文消息---------------">
                                                <?php foreach($tu as $key => $val) { ?>
                                                <option value="3_<?php echo $key ?>" <?php if($link_type[$k_img] == "3_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-美食---------------">
                                                <?php foreach($m as $key => $val) { ?>
                                                <option value="4_<?php echo $key ?>" <?php if($link_type[$k_img] == "4_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-KTV---------------">
                                                <?php foreach($ktv as $key => $val) { ?>
                                                <option value="5_<?php echo $key ?>" <?php if($link_type[$k_img] == "5_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-酒店---------------">
                                                <?php foreach($j as $key => $val) { ?>
                                                <option value="6_<?php echo $key ?>" <?php if($link_type[$k_img] == "6_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-线下商城---------------">
                                                <?php foreach($x as $key => $val) { ?>
                                                <option value="7_<?php echo $key ?>" <?php if($link_type[$k_img] == "7_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈行业列表---------------">
                                                <?php foreach($cityarea_industry as $key => $val) { ?>
                                                <option value="8_<?php echo $key ?>" <?php if($link_type[$k_img] == "8_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup id="9" label="---------------品牌供应商店铺---------------">
                                                <?php foreach($p as $key => $val) { ?>
                                                <?php if(!empty($val)) { ?>
                                                <option value="9_<?php echo $key ?>" <?php if($link_type[$k_img] == "9_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php }} ?>
                                            </optgroup>
                                            <optgroup id="10" label="---------------商城直播系统---------------">
                                                <option value="10" <?php if($link_type[$k_img] == 10) echo 'selected="selected"'; ?>>直播房间</option>
                                            </optgroup>
                                        </select>
                                        <select style="display:none;" onchange="change_type(this);" name="type[]"  class="link-page">
                                            <?php foreach($type_arr as $key => $val) { ?>
                                                <option value="2_<?php echo $key ?>" <?php if($select_value[$k_img] == "2_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                                <?php foreach($ctype_arr[$key] as $k => $v) { ?>
                                                <option value="2_<?php echo $k ?>" <?php if($select_value[$k_img] == "2_".$k) echo 'selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v ?></option>
                                                <?php foreach($ctype_arr[$k] as $kk => $vv) { ?>
                                                <option value="2_<?php echo $kk ?>" <?php if($select_value[$k_img] == "2_".$kk) echo 'selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vv ?></option>
                                                <?php foreach($ctype_arr[$kk] as $kkk => $vvv) {?>
                                                <option value="2_<?php echo $kkk ?>" <?php if($select_value[$k_img] == "2_".$kkk) echo 'selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vvv ?></option>
                                                <?php }}}} ?>
                                        </select>
                                        <select style="display:none;" onchange="change_ctype(this);" name="ctype[]" class="link-page">
                                            <option>---请选择---</option>
                                        </select>
                                        <select style="display:none;" onchange="change_zhibo(this);" name="zhibo[]" class="link-page">
                                            <?php foreach($a as $key => $val) { ?>
                                                <option value="10_<?php echo $key ?>" <?php if($select_value[$k_img] == "10_".$key) echo 'selected="selected"'; ?>><?php echo $val ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="search">
                                        <input type="text" <?php if($i==1){echo 'name="search_1"';} else{echo 'name="search_'.$i.'"';} ?> >
                                        <button onclick="check(this);">搜索</button>
                                    </div>
                                </div>
                                <p>
                                    <label for="p1"><input type="radio" <?php if($i==1){echo 'name="img_link1" id="p1"';} else{echo 'name="img_link'.$i.'" id="p'.$i.'"';} ?> value=2 <?php if($link_type[$i-1]==0){echo 'checked';} ?> >填写链接：</label>
                                    <input class="link-url" type="text" <?php if($i==1){echo 'name="href[]"';} else{echo 'name="href[]"';} ?> placeholder="请输入链接网址" value="<?php if($link_type[$i-1]==0){echo $link[$i-1];} ?>">
                                </p>
                            </div>
                            <div class="list-order">
                                <button onclick="go_up(this);"><img src="../../Common/images/Base/basicdesign/go_up.png"></button>
                                <button onclick="go_down(this);"><img src="../../Common/images/Base/basicdesign/go_down.png"></button>
                                <button onclick="go_del(this);"><img src="../../Common/images/Base/basicdesign/go_del.png"></button>
                            </div>
                        </li>
                    <?php $i++;}} else { ?>
                        <li>
                            <div class="img">
                                <img src="../../Common/images/Base/basicdesign/pic_icon.png" class="img_src1">
                                <input type="hidden" name="img_src[]">
                                <input type="file" class="file" name="file[]" onchange="up_img(this);">
                            </div>
                            <div class="link">
                                <div>
                                    <label for="l1"><input type="radio" name="img_link1" value=1 id="l1" checked="checked">链接到：</label>
                                    <div class="select-list">
                                        <select onchange="change_link(this);" name="page[]" class="link-page">
                                            <optgroup label="---------------商城类链接---------------">
                                            <?php foreach($fixedlink as $key => $val) { ?>
                                                <option value="1_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------产品分类---------------">
                                                <option value="2">多级分类</option>
                                            </optgroup>
                                            <optgroup label="---------------图文消息---------------">
                                                <?php foreach($tu as $key => $val) { ?>
                                                <option value="3_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-美食---------------">
                                                <?php foreach($m as $key => $val) { ?>
                                                <option value="4_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-KTV---------------">
                                                <?php foreach($ktv as $key => $val) { ?>
                                                <option value="5_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-酒店---------------">
                                                <?php foreach($j as $key => $val) { ?>
                                                <option value="6_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈-线下商城---------------">
                                                <?php foreach($x as $key => $val) { ?>
                                                <option value="7_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="---------------商圈行业列表---------------">
                                                <?php foreach($cityarea_industry as $key => $val) { ?>
                                                <option value="8_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup id="9" label="---------------品牌供应商店铺---------------">
                                                <?php foreach($p as $key => $val) { ?>
                                                <?php if(!empty($val)) { ?>
                                                <option value="9_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php }} ?>
                                            </optgroup>
                                            <optgroup id="10" label="---------------商城直播系统---------------">
                                                <option value="10">直播房间</option>
                                            </optgroup>
                                        </select>
                                        <select style="display:none;" onchange="change_type(this);" name="type[]" class="link-page">
                                            <?php foreach($type_arr as $key => $val) { ?>
                                                <option value="2_<?php echo $key ?>"><?php echo $val ?></option>
                                                <?php foreach($ctype_arr[$key] as $k => $v) { ?>
                                                <option value="2_<?php echo $k ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v ?></option>
                                                <?php foreach($ctype_arr[$k] as $kk => $vv) { ?>
                                                <option value="2_<?php echo $kk ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vv ?></option>
                                                <?php foreach($ctype_arr[$kk] as $kkk => $vvv) {?>
                                                <option value="2_<?php echo $kkk ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vvv ?></option>
                                                <?php }}}} ?>
                                        </select>
                                        <select style="display:none;" onchange="change_ctype(this);" name="ctype[]" class="link-page">
                                            <option>---请选择---</option>
                                        </select>
                                        <select style="display:none;" onchange="change_zhibo(this);" name="zhibo[]" class="link-page">
                                            <?php foreach($a as $key => $val) { ?>
                                                <option value="10_<?php echo $key ?>"><?php echo $val ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="search">
                                        <input type="text" name="search_1">
                                        <button onclick="check(this);">搜索</button>
                                    </div>
                                </div>
                                <p>
                                    <label for="p1"><input type="radio" name="img_link1" value=2 id="p1">填写链接：</label>
                                    <input class="link-url" type="text" name="href[]" placeholder="请输入链接网址" value="">
                                </p>
                            </div>
                            <div class="list-order">
                                <button onclick="go_up(this);"><img src="../../Common/images/Base/basicdesign/go_up.png"></button>
                                <button onclick="go_down(this);"><img src="../../Common/images/Base/basicdesign/go_down.png"></button>
                                <button onclick="go_del(this);"><img src="../../Common/images/Base/basicdesign/go_del.png"></button>
                            </div>
                        </li>
                        <?php }?>
                    </ul>
    				<div class="add-full-img">
    						+
    				</div>
    			</div>
                <div class="limit-footer" style="display:inline-block;">
                    <button type="submit" onclick="save()">提交保存</button>
                    <button onclick="javascript:history.back(-1);">取消</button>
                </div>
    		</div>

        </div>

	</div>

</div>
</form>
<script type="text/javascript" src="../../Common/js/layer/layer.js"></script>
<script type="text/javascript" src="../../../js/WdatePicker.js"></script>
<script type="text/javascript" src="../../../common/utility.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>
<script type="text/javascript" src="../../Common/js/Base/basicdesign/swiper.min.js"></script>
<script type="text/javascript">

    function save(){
        var d = $(".view-full-list").children().length;
        var name = $(".limit-right").children().val();
        var ckuname = /^[\u4e00-\u9fa5a-zA-Z0-9]+$/;
        var time = $("[name='stop_time']").val()
        var screen = $("[name='screen']:checked").val()
        if ($("#s1").is(':checked')) {
            if ($("#lian").is(':checked')) {
                var obj = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(0).children().prop("checked",true);
                $("#l1").prop("checked",true);
            } else {
                $("#p1").attr("checked",true);
                // $("[name='img_link']")
                var href = $("[name='link-url']").val();
                $('.view-full-list').children().eq(0).children().eq(1).children().eq(1).children().eq(1).attr("value",href);
            }
        }
        var select=[];
        for (i=1;i<d+1;i++) {
            src = $('.view-full-list').children().eq(i-1).children().eq(0).children().eq(0).attr('src');
            if (src != "../../Common/images/Base/basicdesign/pic_icon.png" && src != '') {
                select.push(src);
            }
        }

        if (d != select.length) {
            alert('请上传图片');
        } else if (name == '') {
            alert('请填写广告图名称');
        } else if(name.length < 0 || name.length > 8) {
            alert("广告图名称长度为1~8位字符！");
        } else if(!ckuname.test(name)) {
            alert("广告图名称请使用中文、英文、数字！");
        } else if( screen=='full' && $.trim(time)=='' ) {
            alert("请输入广告时间！");
        } else if(parseInt($.trim(time)) > 5 ) {
            alert("广告时间太长了！");
        } else {
            document.getElementById("form_model").submit();
        }
    }

    function che(obj) {
        var zhi = $(obj).prev().val();
        var option = $(obj).parent().next().children().eq(1).children().eq(0).find('option');
        if (zhi != '') {
            option.each(function(i){
                if( option.eq(i).text() == zhi ){
                    if ( option.eq(i).text() == '多级分类') {
                        $("#type").css("display","block");
                        $("#ctype").css("display","block");
                        $("#zhibo").css("display","none");
                    } else if ( option.eq(i).text() == '直播房间' ) {
                        $("#zhibo").css("display","block");
                        $("#type").css("display","none");
                        $("#ctype").css("display","none");
                    } else {
                        $("#zhibo").css("display","none");
                        $("#type").css("display","none");
                        $("#ctype").css("display","none");
                    }
                    option.eq(i).attr("selected",true);
                }
            });
        }
    }

    function check(obj) {
        var zhi = $(obj).prev().val();
        var option = $(obj).parent().prev().find('option');
        var aaa = $(obj).parent().prev().children('select').eq(1);
        var bbb = $(obj).parent().prev().children('select').eq(2);
        var ccc = $(obj).parent().prev().children('select').eq(3);
        var res = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(0);

        if (zhi != '') {
            option.each(function(i){
                if( option.eq(i).text() == zhi ){
                    if ( option.eq(i).text() == '多级分类') {
                        aaa.css("display","block");
                        bbb.css("display","block");
                        ccc.css("display","none");
                    } else if ( option.eq(i).text() == '直播房间' ) {
                        aaa.css("display","none");
                        bbb.css("display","none");
                        ccc.css("display","block");
                    } else {
                        aaa.css("display","none");
                        bbb.css("display","none");
                        ccc.css("display","none");
                    }
                    option.eq(i).attr("selected",true);
                }
            });
        }
    }

    for (n=0;n<5;n++) {
        var a= $('.view-full-list').children().eq(n).children().eq(1).children().eq(0).children().eq(1).children().eq(0);
        var b= $('.view-full-list').children().eq(n).children().eq(1).children().eq(0).children().eq(1).children().eq(2);
        change_link(a);
        change_type(b);
        sos(b.find('option'),n);
    }
    links_1($('#link_page'));
    ooo($('#ctype'));

    function ooo(obj){
        var s = '<?php echo $detail_value[0]; ?>';
        // console.log(obj.find('option').length);
        for (i=0;i<obj.find('option').length;i++){
            if( obj.find('option').eq(i).val() == s ){
                obj.find('option').eq(i).prop("selected",true);
                var b= $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(2).find('option');
                b.eq(i).prop("selected",true);
            }
        }
    }

    function sos(obj,n) {
        var a = '<?php echo $detail_value[0]; ?>';
        var b = '<?php echo $detail_value[1]; ?>';
        var c = '<?php echo $detail_value[2]; ?>';
        var d = '<?php echo $detail_value[3]; ?>';
        var e = '<?php echo $detail_value[4]; ?>';
        if (n == 0) {
            ex = a;
        } else if (n == 1) {
            ex = b;
        } else if (n == 2) {
            ex = c;
        } else if (n == 3) {
            ex = d;
        } else if (n == 4) {
            ex = e;
        }
        obj.each(function(i){
            // console.log(obj.eq(i).val());
            if( obj.eq(i).val() == ex ){
                obj.eq(i).prop("selected",true);
            }
        });
    }

    function changeProductType(){
        var checktype = $("#type").find("option:selected").val();
        var res = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(1);
        res.children("option[value='"+checktype+"']").attr("selected",true);
        var pro_typeid= checktype.substring(2);
        $.ajax({
            url: "get_product_list.php?callback=jsonpCallback_get_product_list&type_id="+pro_typeid,
            dataType: 'json',
            type: 'get',
            async: false,
            success: function (result) {
                if(result != null || result != 'null')
                {
                    str = '';

                    for(var i in result){
                        var pid = result[i].pid;
                        var pname = result[i].pname;
                        str += '<option value="'+pid+'">'+pname+'</option>';
                    }
                    $('#ctype').children('option').remove();
                    res.next().children('option').remove();

                    $('#ctype').append(str);
                    res.next().append(str);
                } 
            }
        });
    }

    function links_1(obj){
        var checktype = $("#link_page").find("option:selected").val();
        var res = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(0);
        res.find("option[value='"+checktype+"']").attr("selected",true);

        if (checktype == 2) {
            $("#type").css("display","block");
            $("#ctype").css("display","block");
            $("#zhibo").css("display","none");
            res.next().css("display","block");
            res.next().next().css("display","block");
            res.next().next().next().css("display","none");
            var checktype = $("#type").find("option:selected").val();
            var pro_typeid= checktype.substring(2);

            $.ajax({
                url: "get_product_list.php?callback=jsonpCallback_get_product_list&type_id="+pro_typeid,
                dataType: 'json',
                type: 'get',
                async: false,
                success: function (result) {
                    str = '';

                    for(var i in result){
                        var pid = result[i].pid;
                        var pname = result[i].pname;
                        str += '<option value="'+pid+'">'+pname+'</option>';
                    }
                    $(obj).next().next().children('option').remove();
                    res.next().next().children('option').remove();

                    $(obj).next().next().append(str);
                    res.next().next().append(str);
                }
            });
        } else if (checktype == 10) {
            $("#type").css("display","none");
            $("#ctype").css("display","none");
            $("#zhibo").css("display","block");
            res.next().css("display","none");
            res.next().next().css("display","none");
            res.next().next().next().css("display","block");
        } else {
            $("#type").css("display","none");
            $("#ctype").css("display","none");
            $("#zhibo").css("display","none");
            res.next().css("display","none");
            res.next().next().css("display","none");
            res.next().next().next().css("display","none");
        }
    }

    function change_link(obj){
        var res = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(0);
        // console.log(res.val());
        $("#link_page").find("option[value='"+res.val()+"']").attr("selected",true);
        $("#type").children("option[value='"+res.next().val()+"']").prop("selected",true);
        $("#ctype").children("option[value='"+res.next().next().val()+"']").prop("selected",true);
        $("#zhibo").children("option[value='"+res.next().next().next().val()+"']").prop("selected",true);
        var checktype = $(obj).find("option:selected").val();

        if (checktype == 2) {
            $("#type").css("display","block");
            $("#ctype").css("display","block");
            $("#zhibo").css("display","none");
            $(obj).next().css("display","block");
            $(obj).next().next().css("display","block");
            $(obj).next().next().next().css("display","none");

            var checktype = $(obj).next().find("option:selected").val();

            var pro_typeid= checktype.substring(2);

            $.ajax({
                url: "get_product_list.php?callback=jsonpCallback_get_product_list&type_id="+pro_typeid,
                dataType: 'json',
                type: 'get',
                async: false,
                success: function (result) {
                    str = '<option>---请选择---</option>';

                    for(var i in result){
                        var pid = result[i].pid;
                        var pname = result[i].pname;
                        str += '<option value="'+pid+'">'+pname+'</option>';
                    }
                    $("#ctype").children('option').remove();
                    $(obj).next().next().children('option').remove();

                    $("#ctype").append(str);
                    $(obj).next().next().append(str);
                }
            });
        } else if (checktype == 10) {
            $("#type").css("display","none");
            $("#ctype").css("display","none");
            $("#zhibo").css("display","block");
            $(obj).next().css("display","none");
            $(obj).next().next().css("display","none");
            $(obj).next().next().next().css("display","block");
        } else {
            $("#type").css("display","none");
            $("#ctype").css("display","none");
            $("#zhibo").css("display","none");
            $(obj).next().css("display","none");
            $(obj).next().next().css("display","none");
            $(obj).next().next().next().css("display","none");
        }
    }

    function change_type(obj){
        var checktype = $(obj).find("option:selected").val();

        var checktypes = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(0);

        $("#type").find("option[value='"+checktypes.next().val()+"']").attr("selected",true);
        if ( checktype != undefined) {
            var pro_typeid= checktype.substring(2);
            $.ajax({
                url: "get_product_list.php?callback=jsonpCallback_get_product_list&type_id="+pro_typeid,
                dataType: 'json',
                type: 'get',
                async: false,
                success: function (result) {

                    str = '<option>---请选择---</option>';

                    for(var i in result){
                        var pid = result[i].pid;
                        var pname = result[i].pname;
                        str += '<option value="'+pid+'">'+pname+'</option>';
                    }
                    $(obj).next().find('option').remove();
                    $("#ctype").next().find('option').remove();

                    $("#ctype").next().append(str);
                    $(obj).next().append(str);
                }
            });
        }
    }

    function change_ctype(obj){
        var checktype = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(0);
        $("#ctype").find("option[value='"+checktype.next().next().val()+"']").attr("selected",true);

    }

    function change_zhibo(obj){
        var checktype = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(0);
        $("#zhibo").find("option[value='"+checktype.next().next().next().val()+"']").attr("selected",true);
    }

    function ctype_che(){
        var checktype = $("#ctype").find("option:selected").val();
        var obj = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(2);
        $(obj).children("option[value='"+checktype+"']").attr("selected",true);
    }

    function zhibo_che(){
        var checktype = $("#zhibo").find("option:selected").val();
        var obj = $('.view-full-list').children().eq(0).children().eq(1).children().eq(0).children().eq(1).children().eq(3);

        $(obj).children("option[value='"+checktype+"']").attr("selected",true);
    }

	//上传图片(半屏)
	function half_up_img(obj){
		var file = obj.files[0];
		if(!/image\/\w+/.test(file.type)){
	        alert('请确保文件为图像类型');
	        return false;
	    }if(file.size>100*1024){
	        alert('请确保文件不大于100K');
	        return false;
	    }
	    var reader = new FileReader();
	    reader.readAsDataURL(file);
	    reader.onload = function(e) {
            var obj = $('.view-full-list').children().eq(0).children().eq(0).children().eq(0);
            obj.attr("src", this.result);
            obj.next().val(this.result);
            // $('.img_src[]').attr("src", this.result);
            // $('input[name="img_src"]').val(this.result);
            rebuild_Img();
	        $('.img_src0').attr("src", this.result);
	        $('input[name="img_src0"]').val(this.result);
	        $('.view-half-img').css("background-image", "url("+this.result+")");
	    };
	}
	//上传图片(全屏)
	function up_img(obj){
		var num = $(obj).attr('name').split('_');
		var file = obj.files[0];
		if(!/image\/\w+/.test(file.type)){
	        alert('请确保文件为图像类型');
	        return false;
	    }if(file.size>200*1024){
	        alert('请确保文件不大于100K');
	        return false;
	    }
	    var reader = new FileReader();
	    reader.readAsDataURL(file);
	    reader.onload = function(e) {
            // console.log($(obj).prev().prev().html());
            $(obj).prev().prev().attr("src", this.result);
	        $(obj).prev().val(this.result);
	        // $('input[name="img_src[]"]').val(this.result);
	        rebuild_Img();
            $('.img_src0').attr("src", this.result);
            $('input[name="img_src0"]').val(this.result);
            $('.view-half-img').css("background-image", "url("+this.result+")");
	    };
	}

	//广告图显示时间
	$('#show-time input[name="time"]').on('change',function(){
		var state = $(this).val();
		if(state === '1'){
			$('.show-times').hide();
		}else{
			$('.show-times').show();
		}
	});

	//选择半屏/全屏
	$('.view-radio input').on('change',function(){
		var state = $(this).val();
		if(state === 'half'){
			$('.limit-view-half').show();
			$('.limit-view-full').hide();
		}else{
			$('.limit-view-half').hide();
			$('.limit-view-full').show();
		}
	});

	rebuild_Imgs();
	//遍历轮播图
	function rebuild_Imgs(){
		var viewImg = [];
		var re_list = $('.view-full-list li'),
			re_len  = re_list.length,
			str     = '',
			time    = $('input[name="stop_time"]').val()*<?php if(!empty($show_time)) {echo $show_time*1000;} else {echo 1000;} ?>;//轮播时间
		if(re_len <= 1){//只有一张图片不轮播
			time = 0;
		}else if(time == ''){
			time = 3000;
		}

            <?php if(!empty($imgurl)) {
                foreach ($imgurl as $k_i=>$v_i) {
                    echo "str += '<div class=\"swiper-slide\" style=\"background-image:url(\'".$v_i."\');\"></div>';";
                }
            } ?>


		$('.swiper-wrapper').html(str);
		var view_full = new Swiper('#view-full', {
	        loop:false,//循环轮播
	        autoplay: time,//可选选项，自动滑动
	    });
	}

    //遍历轮播图
    function rebuild_Img(){
        var viewImg = [];
        var re_list = $('.view-full-list li'),
            re_len  = re_list.length,
            str     = '',
            time    = $('input[name="stop_time"]').val()*<?php if(!empty($show_time)) {echo $show_time*1000;} else {echo 1000;} ?>;//轮播时间


        if(re_len <= 1){//只有一张图片不轮播
            time = 0;
        }else if(time == ''){
            time = 3000;
        }
        s = 1;
        for(var i = 0; i < re_len; i++){
            var src = re_list.eq(i).find('input[type="hidden"]').val();
            var img = $('.view-full-list').children().eq(i).children().eq(0).children().eq(0).attr('src');
            // console.log(src);
            // console.log(img);
            viewImg.push(src);
            if (img != src) {
                str += '<div class="swiper-slide" style="background-image:url('+img+');"></div>';
            } else {
                str += '<div class="swiper-slide" style="background-image:url('+src+');"></div>';
            }
            s++;
        }

        $('.swiper-wrapper').html(str);
        var view_full = new Swiper('#view-full', {
            loop:false,//循环轮播
            autoplay: time,//可选选项，自动滑动
        });
    }

	//添加轮播图
	$('.add-full-img').on('click',function(){
		var len = $('.view-full-list li').length;
		if(len >= 5){
			layer.alert('轮播图不能超过5张！', {title: '提示'});
			return false;
		}
		var num = len+1;
		var str = '<li><div class="img"><img src="../../Common/images/Base/basicdesign/pic_icon.png" class="img_src'+num+'">';
			str += '<input type="hidden" name="img_src[]">';
			str += '<input type="file" class="file" name="file[]" onchange="up_img(this);">';
			str += '</div>';
			str += '<div class="link"><div>';
			str += '<label for="l'+num+'"><input type="radio" value=1 name="img_link'+num+'" id="l'+num+'" checked="checked">链接到：</label>';
			str += '<div class="select-list">';
			str += '<select id="link_'+num+'" onchange="change_link(this);" class="link-page" name="page[]">';
			str += '<optgroup label="---------------商城类链接---------------"><?php foreach($fixedlink as $key => $val) { ?><option value="1_<?php echo $key ?>"><?php echo $val ?></option><?php } ?></optgroup>';
            str += '<optgroup label="---------------产品分类---------------"><option value="2">多级分类</option></optgroup>';
            str += '<optgroup label="---------------图文消息---------------"><?php foreach($tu as $key => $val) { ?><option value="3_<?php echo $key ?>"><?php echo $val ?></option><?php } ?></optgroup>';
            str += '<optgroup label="---------------商圈-美食---------------"><?php foreach($m as $key => $val) { ?><option value="4_<?php echo $key ?>"><?php echo $val ?></option><?php } ?></optgroup>';
            str += '<optgroup label="---------------商圈-KTV---------------"><?php foreach($ktv as $key => $val) { ?><option value="5_<?php echo $key ?>"><?php echo $val ?></option><?php } ?></optgroup>';
            str += '<optgroup label="---------------商圈-酒店---------------"><?php foreach($j as $key => $val) { ?><option value="6_<?php echo $key ?>"><?php echo $val ?></option><?php } ?></optgroup>';
            str += '<optgroup label="---------------商圈-线下商城---------------"><?php foreach($x as $key => $val) { ?><option value="7_<?php echo $key ?>"><?php echo $val ?></option><?php } ?></optgroup>';
            str += '<optgroup label="---------------商圈行业列表---------------"><?php foreach($cityarea_industry as $key => $val) { ?><option value="8_<?php echo $key ?>"><?php echo $val ?></option><?php } ?></optgroup>';
            str += '<optgroup id="9" label="---------------品牌供应商店铺---------------"><?php foreach($p as $key => $val) { ?><?php if(!empty($val)) { ?><option value="9_<?php echo $key ?>"><?php echo $val ?></option><?php }} ?></optgroup>';
            str += '<optgroup id="10" label="---------------商城直播系统---------------"><option value="10">直播房间</option></optgroup>';
			str += '</select>';
            str += '<select id="type_'+num+'" onchange="change_type(this);" class="link-page" name="type[]" style="display:none;">';
            str += '<?php foreach($type_arr as $key => $val) { ?><option value="2_<?php echo $key ?>"><?php echo $val ?></option><?php foreach($ctype_arr[$key] as $k => $v) { ?><option value="2_<?php echo $k ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v ?></option><?php foreach($ctype_arr[$k] as $kk => $vv) { ?><option value="2_<?php echo $kk ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vv ?></option><?php foreach($ctype_arr[$kk] as $kkk => $vvv) {?><option value="2_<?php echo $kkk ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vvv ?></option><?php }}}} ?>';
            str += '</select>';
            str += '<select id="ctype_'+num+'" onchange="change_ctype(this);" name="ctype[]" style="display:none;" class="link-page">';
            str += '<option>---请选择---</option>';
            str += '</select>';
            str += '<select id="zhibo_'+num+'" onchange="change_zhibo(this);" name="zhibo[]" style="display:none;" class="link-page">';
            str += '<?php foreach($a as $key => $val) { ?><option value="10_<?php echo $key ?>"><?php echo $val ?></option><?php } ?>';
            str += '</select>';
			str += '</div>';
			str += '<div class="search">';
			str += '<input type="text" name="search_'+num+'">';
			str += '<button onclick="check(this);">搜索</button>';
			str += '</div>';
			str += '</div>';
			str += '<p><label for="p'+num+'"><input type="radio" value=2 name="img_link'+num+'" id="p'+num+'">填写链接：</label>';
			str += '<input class="link-url" type="text" name="href[]" placeholder="请输入链接网址">';
			str += '</p></div>';
			str += '<div class="list-order">';
			str += '<button onclick="go_up(this);"><img src="../../Common/images/Base/basicdesign/go_up.png"></button>';
			str += '<button onclick="go_down(this);"><img src="../../Common/images/Base/basicdesign/go_down.png"></button>';
			str += '<button onclick="go_del(this);"><img src="../../Common/images/Base/basicdesign/go_del.png"></button></div></li>';
		$('.view-full-list').append(str);
	})

	// 上移
	function go_up(obj){
		var list = $(obj).parents('li'),
            res  = $('.view-full-list'),
            prev = list.prev('li'),
            p    = list.children().eq(1).children().eq(0).children().eq(0).children('input').attr("name").substring(8);
        // if (p != 1) {
        //     i = p-2;
        //     if (res.children().eq(i).children().eq(1).children().eq(1).children().eq(0).children('input').is(':checked')) {
        //         var s = 'ok';
        //     } else {
        //         var s = 'no';
        //     }
        // }
        prev.before(list);
		rebuild_Img();
        for (o=0;o<5;o++) {
            num = o+1;
            res.children().eq(o).children().eq(1).children().eq(0).children().eq(0).children('input').attr("name","img_link"+num);
            res.children().eq(o).children().eq(1).children().eq(1).children().eq(0).children('input').attr("name","img_link"+num);
        }
        // if (s == 'ok') {
        //     res.children().eq(p).children().eq(1).children().eq(0).children().eq(0).children('input').attr("checked",true);
        // } else {
        //     res.children().eq(p).children().eq(1).children().eq(1).children().eq(0).children('input').attr("checked",true);
        // }
	}

	//下移
	function go_down(obj){
		var list = $(obj).parents('li'),
            res  = $('.view-full-list'),
			next = list.next('li');
		next.after(list);
		rebuild_Img();
        for (o=0;o<5;o++) {
            num = o+1;
            res.children().eq(o).children().eq(1).children().eq(0).children().eq(0).children('input').attr("name","img_link"+num);
            res.children().eq(o).children().eq(1).children().eq(1).children().eq(0).children('input').attr("name","img_link"+num);
        }
	}

	//删除轮播图
	function go_del(obj){
		var list = $(obj).parents('li');
        var res = $('.view-full-list');
        if (list.prev().text() == '' && list.next().text() == '') {
            alert('只有一张图片时，则不执行删除操作');
        } else {
            layer.alert('确定删除当前轮播图', {
                title: '提示',
                btn: ['确定', '取消'],
                btnAlign: 'c',
                yes: function(index, layero){
                    if (list.prev().text() == '') {
                        var i = 1;
                    } else if (list.prev().prev().text() == '') {
                        var i = 2;
                    } else if (list.prev().prev().prev().text() == '') {
                        var i = 3;
                    } else if (list.prev().prev().prev().prev().text() == '') {
                        var i = 4;
                    }
                    var p = 5;
                    for (o=i;o<p;o++) {
                        res.children().eq(o).children().eq(1).children().eq(0).children().eq(0).children('input').attr("name","img_link"+o);
                        res.children().eq(o).children().eq(1).children().eq(1).children().eq(0).children('input').attr("name","img_link"+o);
                    }
                    list.remove();
                    layer.close(index);
                    rebuild_Img();
                },
                btn2: function(index, layero){
                    layer.close(index);
                }
            });
        }
	}


</script>
</body>
</html>