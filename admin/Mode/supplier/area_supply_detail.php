<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');

// 数据库操作类
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();

// 连接数据库
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);

require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

$user_id = $database->init($_REQUEST['user_id']);
$sql = "SELECT * from weixin_commonshop_wholesalers where user_id = '{$user_id}' and isvalid=true";
$data = $database->getFields($sql);
 
$sql = "SELECT DISTINCT a.name from weixin_commonshop_wholesaler_areas w
        inner join address a on a.id=w.province
        where w.user_id = '{$user_id}' and w.isvalid=true";
$provinces = implode(',',$database->getArray($sql));
// echo $provinces;
$sql = "SELECT concat(a.name,'_',aa.name) 
        from weixin_commonshop_wholesaler_areas w 
        INNER JOIN address a ON a.id = w.city 
        INNER JOIN address aa ON aa.id = w.district 
        where w.user_id = '{$user_id}' and w.isvalid=true";
$area = implode(',',$database->getArray($sql));

$category = $data['business_category'];
if( $category ){
    $sql = "SELECT name 
    from weixin_commonshop_area_category
    where id in({$category}) and isvalid=true";
    $categorys = implode(',',$database->getArray($sql));
}
// var_dump($categorys);

$sql = "select * from address ";
$result = _mysql_query($sql) or die("L11 Query failed：".mysql_error());
if($result){
    while($row = mysql_fetch_assoc($result)){
        $address[]=$row;
    }
}

$is = 1;
foreach ($address as $key => $value) {
    if( $value['LevelType'] == 1 ){
        if($is==1){
            $pro .= $value['Name'].'$';
        }else{
            $pro .= '#'.$value['Name'].'$';
        }
        $is++;
        $parentid = $value['ID'];
        foreach ($address as $keys => $values) {
            if( $values['LevelType']==2 && $values['ParentId']==$parentid ){
                $i ++;
                $pro .= '|'.$values['Name'];
                $parentids = $values['ID'];
                foreach ($address as $keyss => $valuess) {
                    if( $valuess['LevelType']==3 && $valuess['ParentId']==$parentids ){
                        // $pros[] = $valuess['Name'];
                        $pro .= ','.$valuess['Name'];
                    }
                }
                // $area .= implode(",",$areas);
            }
        }
    }
}
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../../../css/css2.css" media="all">
<link href="../../../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../../../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">

<link rel="stylesheet" type="text/css" href="<?php echo $_SERVER['DOCUMENT']?>/weixin/plat/Public/assets/css/area_select.css">
<script type="text/javascript" src="../../../js/tis.js"></script>

<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="../../../common/js/layer/layer.js"></script>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
a:hover{text-decoration: none;}   
.button_blue{cursor: pointer;margin-left: 10px;font-size: 14px;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:20px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.name{  margin-top: 10px;height: 30px;line-height: 30px;font-size: 13px;text-align: left;font-weight: bolder;margin-left: 19px;}
.button_box{width: 296px;display: block;text-align: right;}
.button_box .WSY_button{border-radius:2px;border:none;}
.WSY_remind_dl02 input {
    width: 400px;
    height: 24px;
    border: 1px solid #dddddd;
    border-radius: 2px;
    padding-left: 5px;
}
.WSY_remind_dl02 dd ul {
	float: left;
    overflow: hidden;
    background-color: #cbd2d8;
    width: 50px;
    height: 20px;
    border-radius: 300px;
    position: relative;
}
.WSY_remind_dl02 dd ul p {
    position: absolute;
    font-size: 12px;
    font-family: "Arial";
    line-height: 20px;
}
.WSY_remind_dl02 dd ul li {
    width: 16px;
    height: 16px;
    border-radius: 300px;
    background: #fff;
    position: absolute;
    z-index: 999;
    margin-left: 2px;
    margin-top: 2px;
    cursor: pointer;
}
.WSY_remind_dl02 dd ul span {
    width: 16px;
    height: 16px;
    border-radius: 300px;
    background: #fff;
    position: absolute;
    margin-left: 2px;
    margin-top: 2px;
    cursor: pointer;
}
.kf_type_div i {
    display: block;
    float: left;
    margin-right: 10px;
    height: 20px;
}
.kf_type {
    margin-right: 2px;
    margin-top: 6px;
    display: block;
    float: left;
}
.kf_input {
    width: 150px;
    height: 24px;
    border: solid 1px #ccc;
    border-radius: 2px;
    margin-left: 2px;
}
.WSY_remind_dl02 dt{
    width: 90px !important;
}
.WSY_remind_dl01, .WSY_remind_dl02, WSY_remind_dl03{margin:10px 0px;}
.textcss{width:400px;height:200px;border:1px solid #dddddd;}
.box{margin-left: 98px;}
</style>
</head>

<body> 
    <div class="WSY_content">
		<div class="WSY_columnbox">
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a  class="white1">品牌合作商申请资料</a>
				</div>
			</div>  
			<form action="area_operation.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&supply_id=<?php echo $supply_id; ?>" enctype="multipart/form-data" id="brand_supply" method="post">
                <input type="hidden" name="op" value="9">
				<div id="products" class="r_con_wrap">
					<div style="margin-top:20px">
						
						<dl class="WSY_remind_dl02"> 
							<dt>合作商ID：</dt>
							<dd>						
								<input type="text" name="user_id" id="user_id"  value="<?php echo $data['user_id'] ?>" readonly>	
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>公司名称：</dt>
							<dd>						
								<input type="text" name="company_name" id="company_name"  value="<?php echo $data['company_name'];?>"> 	
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>批发商名称：</dt>
							<dd>						
								<input type="text" name="wholesaler_name" id="wholesaler_name"  value="<?php echo $data['wholesaler_name'];?>"> 	
							</dd>
						</dl>						
						<dl class="WSY_remind_dl02"> 
							<dt>店铺电话：</dt>
							<dd>						
								<input type="text" name="wholesaler_tel" id="wholesaler_tel"  value="<?php echo $data['wholesaler_tel'];?>" > 	
							</dd>
						</dl>
						<!-- <dl class="WSY_remind_dl02"> 
							<dt>所在地区：</dt>
							<dd>						
								<input type="text" name="wholesaler_address" id="wholesaler_address"  value="<?php echo $data['wholesaler_address'];?>"> 	
							</dd>
						</dl> -->
						<dl class="WSY_remind_dl02"> 
							<dt>品牌图标：</dt>
							<dd>						
								<img src="<?php echo $new_baseurl.$data['wholesaler_logo'];?>" style="max-width:200px;" >
							</dd>
						</dl>
						<dl class="WSY_remind_dl02"> 
							<dt>营业执照：</dt>
							<dd>						
								<img src="<?php echo $new_baseurl.$data['wholesaler_business_license'];?>" style="max-width:200px;" >
							</dd>
						</dl>
                        <dl class="WSY_remind_dl02"> 
                            <dt>店铺介绍：</dt>
                            <dd>                        
                                <textarea name="wholesaler_intro" id="wholesaler_intro" value="<?php echo $data['wholesaler_intro'];?>" style="width:400px;height:200px;border:1px solid #dddddd;" maxlength="200"></textarea> <span style="color:#FF0B0B;">（最多输入200个字符）</span>  
                            </dd>
                        </dl>
						<dl class="WSY_remind_dl02"> 
                            <dt>类目：</dt>
                            <dd>                        
                                <?php echo $categorys ?>
                            </dd>
                        </dl>

                        <dl class="WSY_remind_dl02"> 
							<dt>批发区域选择：</dt>
							<dd>						
								<!-- <div class="aa_btn"><span></span> <div class="area_select_btn">选择</div><div> -->
                                <div class="province" style="display:none;">
                                    <div class="box">
                                        <div class="boxContent">
                                            <div class="header">
                                                <div class="searchBox">
                                                    <span>关键字搜索 :</span>
                                                    <input class="searchVal" placeholder="请输入关键字" onkeydown="if(event.keyCode==13){searchArea();}" />
                                                    <div class="searchBtn WSY-skin-bg" onclick="searchArea()">搜索</div>
                                                </div>
                                                <div class="confirmBtn">
                                                    <span class="WSY-skin-bg">确定</span>
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <div class="left">
                                                    <div class="all-select"><img class="select" src="<?php echo $_SERVER['DOCUMENT'] ?>/weixin/plat/Public/assets/images/select1.png" /><span>全选</span></div>
                                                    <div class="top" id="province">
                                                        <!--省-->
                                                    </div>
                                                    <div class="bottom" id="openProvince" onclick="openProvince()">
                                                        <img src="<?php echo $_SERVER['DOCUMENT'] ?>/weixin/plat/Public/assets/images/arrow3.png" />
                                                    </div>
                                                </div>
                                                <div class="right" id="city_area">
                                                    <div class="top">
                                                        <img class="arrowLeft" src="<?php echo $_SERVER['DOCUMENT'] ?>/weixin/plat/Public/assets/images/arrow4.png" />
                                                        <img class="arrowRight" src="<?php echo $_SERVER['DOCUMENT'] ?>/weixin/plat/Public/assets/images/arrow5.png" />
                                                        <div class="cityBox">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="region" id="region" value="<?php echo $provinces ?>" />
                                <input type="hidden" name="city_area" id="region_city_area" value="<?php echo $area ?>" />

                                <!-- <div class="selected-areaBox">
                                    <label>已选</label>
                                    <div>省：<span class="selected-province hidden-content" data-province_num="0"></span><label class="showMore moreProvince">↓更多</label><label class="showMore hidden-btn hiddenProvince">↑隐藏</label></div>
                                    <div>市：<span class="selected-city hidden-content" data-city_num="0"></span><label class="showMore moreCity">↓更多</label><label class="showMore hidden-btn hiddenCity">↑隐藏</label></div>
                                    <div>区/县/镇：<span class="selected-area hidden-content" data-area_num="0"></span><label class="showMore moreArea">↓更多</label><label class="showMore hidden-btn hiddenArea">↑隐藏</label></div>
                                    <div class="checked-div" style="width:72%;display:inline-block;"></div>
                                </div> -->
                                <div class="clear"></div>
							</dd>
						</dl>
						
						
						
					</div>
					<span class="button_box">
						<input type='submit' class="WSY_button"  value="提交"   style="float:none"/>
						&nbsp;	
						<input type=button class="WSY_button"  value="取消" onclick="document.location='area_supply.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>';" style="float:none" />
					</span>
				</div> 

			</form>

		</div>
	</div>
</body>
<script>
    var pro = '<?php echo $pro ?>';
    var imgPath = "<?php echo $_SERVER['DOCUMENT'] ?>/weixin/plat/Public/assets/images";
    var province_str = '<?php echo $provinces ?>';
    var city_area_str = '<?php echo $area ?>';
    var city_arr = new Array();
    province_arr = province_str.split(',');
    city_area_arr = city_area_str.split(',');
    for( i in city_area_arr ){
        city_arr.push(city_area_arr[i].split('_')[0]);
    }
</script>
<script type="text/javascript" src="<?php echo $_SERVER['DOCUMENT']?>/weixin/plat/Public/assets/js/region_select2.js"></script>
<script type="text/javascript" src="<?php echo $_SERVER['DOCUMENT']?>/weixin/plat/Public/assets/js/area_select.js"></script>
<script>
    $('.selected-areaBox').hide();
    $('.province').show();
    $("#openProvince").css("height",($('#openProvince').height() - 42)+'px');
	function submitV(){
		$("#brand_supply").submit();
	}
	
	function set_need_kefu(obj){
	 $("#is_kefu").val(obj);
	//console.log('obj='+obj);
	if(obj==0){
		$("#kf_type_div").hide();
	}else{
		$("#kf_type_div").show();
	}
}
	// --------显示控制开关效果
	$(function(){
	$(".WSY_bot").click(function(){
		$(this).animate({left : '30px'});
		$(this).parent().find(".WSY_bot2").animate({left : '30px'});
		$(this).hide();
		$(this).parent().find(".WSY_bot2").show();
		$(this).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
		
		$(this).parent().find("p").html('关');
		$(this).parent().css({backgroundColor : '#cbd2d8'});
		$(this).parent().find("p").css({color : '#7f8a97'});
		})
		
	$(".WSY_bot2").click(function(){
		$(this).parent().find(".WSY_bot").animate({left : '0px'});
		$(this).animate({left : '0px'});
		$(this).parent().find(".WSY_bot").show();
		$(this).hide();
		$(this).parent().find("p").animate({margin : '0 0 0 27px'}, 500);
		
		$(this).parent().find("p").html('开');
		$(this).parent().css({backgroundColor : '#ff7170'});
		$(this).parent().find("p").css({color : '#fff'});
		})
	})
</script>
<script language="javascript" src="../../Common/js/Mode/supplier/ToolTip.js"></script>
</html>

