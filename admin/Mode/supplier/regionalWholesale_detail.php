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

$sql = "SELECT location_p,location_c,location_a from weixin_commonshop_applysupplys where user_id = '{$user_id}' and isvalid=true";
$app_data = $database->getFields($sql);
 
$sql = "SELECT DISTINCT a.name from weixin_commonshop_wholesaler_areas w
        inner join address a on a.id=w.province
        where w.user_id = '{$user_id}' and w.isvalid=true";
$provinces = implode(',',$database->getArray($sql));
// echo $provinces;die;
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

$is_kefu    = 0;//是否开启品牌供应商在线客服
$kefu_type      = 0;//客服类型
$supply_qq      = -1;//qq类型
$supply_id      = -1;//供应商编号
$brand_kefu="select supply_id,is_kefu,kefu_type,supply_qq from weixin_commonshop_supply_kefu where isvalid=true and customer_id=".$customer_id." and supply_id=".$user_id; 
$result=_mysql_query($brand_kefu) or die ('brand_kefu faild' .mysql_error());
if($row=mysql_fetch_object($result)){
    $supply_id      = $row->supply_id;
    $is_kefu        = $row->is_kefu;
    $kefu_type      = $row->kefu_type;
    $supply_qq      = $row->supply_qq;
}else{  
    $sql_ins = "insert into weixin_commonshop_supply_kefu(supply_id,is_kefu,kefu_type,supply_qq,createtime,isvalid,customer_id)values(".$user_id.",0,1,'',now(),true,".$customer_id.")";
    //echo $sql_ins;
    _mysql_query($sql_ins) or die ('L44  faild' .mysql_error());
}

$supply_qq_arr = json_decode($supply_qq);
//var_dump($supply_qq_arr);
$supply_qq = $supply_qq_arr->supply_qq;
$xiaoneng = $supply_qq_arr->xiaoneng;


// var_dump($data['wholesaler_status']);die;

?>
<!DOCTYPE html>
<html>

<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="css/css2.css" media="all">
<link href="css/add/css/global.css" rel="stylesheet" type="text/css">
<link href="css/add/css/main.css" rel="stylesheet" type="text/css">
<link href="css/add/css/shop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="css/css_V6.0/contentblue.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_SERVER['DOCUMENT']?>/weixin/plat/Public/assets/css/area_select.css">

<script type="text/javascript" src="js/tis.js"></script>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="layer/layer.js"></script>

<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
            a:hover {
                text-decoration: none;
            }
            
            .button_blue {
                cursor: pointer;
                margin-left: 10px;
                font-size: 14px;
                line-height: 30px;
                background-color: #06a7e1;
                padding-left: 15px;
                padding-right: 15px;
                border-radius: 3px 3px 3px 3px;
                margin-top: 20px;
                color: #fff;
            }
            
            .button_blue:hover {
                background: #0e98c9;
            }
            
            .name {
                margin-top: 10px;
                height: 30px;
                line-height: 30px;
                font-size: 13px;
                text-align: left;
                font-weight: bolder;
                margin-left: 19px;
            }
            
            .button_box {
                width: 296px;
                display: block;
                text-align: right;
            }
            
            .button_box .WSY_button {
                border-radius: 2px;
                border: none;
            }
            
            .WSY_remind_dl02 {
                margin: 15px 0 !important;
            }
            
            .WSY_remind_dl02 dt {
                min-width: 120px;
                text-align: right;
            }
            
            .WSY_remind_dl02 .img-right {
                margin-right: 10px;
            }
            
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
            
            .WSY_remind_dl01,
            .WSY_remind_dl02,
            .WSY_remind_dl03 {
                margin: 10px 0px;
            }
            
            .textcss {
                width: 400px;
                height: 200px;
                border: 1px solid #dddddd;
            }
        </style>
    </head>

    <body>
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_column_header">
                    <div class="WSY_columnnav">
                        <a class="white1">区域批发商申请资料</a>
                    </div>
                </div>
                <form action="area_operation.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&supply_id=<?php echo $supply_id; ?>" enctype="multipart/form-data" id="brand_supply" method="post">

                    <input type="hidden" name="op" value="9">

                    <div id="products" class="r_con_wrap">
                        <div style="margin-top:20px">

                            <dl class="WSY_remind_dl02">
                                <dt>合作商编码</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $data['user_id'];?>" readonly="readonly">  
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>合作商姓名</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $data['user_name'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02"> 
                                <dt>批发商名称：</dt>
                                <dd>                        
                                    <input type="text" name="wholesaler_name" id="wholesaler_name" value="<?php echo $data['wholesaler_name'];?>" readonly="readonly">     
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>合作商联系号码</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $data['user_phone'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>身份证号</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $data['id_cards_num'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>省市区</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $app_data['location_p'],$app_data['location_c'],$app_data['location_a'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>详细地址</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $data['wholesaler_address'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>公司名称</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $data['company_name'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>公司电话</dt>
                                <dd>
                                   <input type="text" name="user_id" id="user_id"  value="<?php echo $data['wholesaler_tel'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>批发类目</dt>
                                <dd>
                                    <input type="text" name="user_id" id="user_id"  value="<?php echo $categorys;?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>店铺简介</dt>
                                <dd>
                                   <input type="text" name="user_id" id="user_id"  value="<?php echo $data['wholesaler_intro'];?>" readonly="readonly">
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>公司营业执照：</dt>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt></dt>
                                <dd style="margin-left: 30px;">
                                <?php
                                $wholesaler_business_license=explode('|',$data['wholesaler_business_license']);
                                foreach ($wholesaler_business_license as $value) {?>
                                <img src="<?php echo $value;?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo $value;?>>')" onMouseOut="toolTip()" /> 
                                <?php }?>
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>身份证正反两面：</dt>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt></dt>
                                <dd style="margin-left: 30px;">
                                <?php
                                $id_cards_pic=explode('|',$data['id_cards_pic']);
                                foreach ($id_cards_pic as $value) {?>
                                <img src="<?php echo $value;?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo $value;?>>')" onMouseOut="toolTip()" /> 
                                <?php }?>
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>公司LOGO：</dt>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt></dt>
                                <dd style="margin-left: 30px;">
                                    <img class="img-right" src="<?php echo $data['wholesaler_logo'];?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo $data['wholesaler_logo'];?>>')" onMouseOut="toolTip()" />
                                </dd>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt>店铺二维码背景图：</dt>
                            </dl>
                            <dl class="WSY_remind_dl02">
                                <dt></dt>
                                <dd style="margin-left: 30px;">
                                    <img class="img-right" src="<?php echo $data['qcode_bgimg'];?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo $data['qcode_bgimg'];?>>')" onMouseOut="toolTip()" />
                                </dd>
                            </dl>

                            <?php if($data['wholesaler_status']==1){?>

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

                                <div class="selected-areaBox">
                                    <label>已选</label>
                                    <div>省：<span class="selected-province hidden-content" data-province_num="0"></span><label class="showMore moreProvince">↓更多</label><label class="showMore hidden-btn hiddenProvince">↑隐藏</label></div>
                                    <div>市：<span class="selected-city hidden-content" data-city_num="0"></span><label class="showMore moreCity">↓更多</label><label class="showMore hidden-btn hiddenCity">↑隐藏</label></div>
                                    <div>区/县/镇：<span class="selected-area hidden-content" data-area_num="0"></span><label class="showMore moreArea">↓更多</label><label class="showMore hidden-btn hiddenArea">↑隐藏</label></div>
                                    <div class="checked-div" style="width:72%;display:inline-block;"></div>
                                </div>
                                <div class="clear"></div>
                            </dd>
                        </dl>
                        <?php } ?>


                        <?php if($data['wholesaler_status']==1){?>
                        <dl class="WSY_remind_dl02"> 
                            <dt>是否开启在线客服：</dt>
                            <dd>
                                <?php if($is_kefu==1){ ?>
                                    <ul style="background-color: rgb(255, 113, 112);">
                                        <p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
                                        <li onclick="set_need_kefu(0)" class="WSY_bot" style="display: block;left: 0px;"></li>
                                        <span onclick="set_need_kefu(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
                                    </ul>
                                <?php }else{ ?>
                                    <ul style="background-color: rgb(203, 210, 216);">
                                        <p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
                                        <li onclick="set_need_kefu(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
                                        <span onclick="set_need_kefu(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
                                    </ul>                       
                                <?php } ?>

                                <div class="kf_type_div" id="kf_type_div" <?php if($is_kefu==0){ ?>style="display:none"<?php }?> >
                                        <i class="kf_type_set" style="margin-left:5px ">
                                            <input type="radio" class="kf_type" style="height:15px;width:15px;" <?php if($kefu_type==1 || $kefu_type== ''){ ?>checked<?php } ?> value="1" name="kefu_type">
                                            <span style="float:left">QQ客服</span>
                                            <input class="kf_input" style="width:230px;height:20px;" type="text" value="<?php echo $supply_qq ?>" name="supply_qq" >
                                        </i>                                    
                                        <i>
                                            <input type="radio" class="kf_type" style="height:15px;width:15px;" <?php if($kefu_type==2){ ?>checked<?php } ?> value="2" name="kefu_type">
                                        <span style="float:left">小能客服接待组</span><input class="kf_input" style="width:230px;height:20px;" type="text" value="<?php echo $xiaoneng ?>" name="xiaoneng" ></i>
                                </div>
                                        <input type="hidden" name="is_kefu" id="is_kefu" value="<?php echo $is_kefu; ?>" />
                            </dd>
                        </dl>
                        <?php } ?>

                    </div>
                    <span class="button_box">
                        <?php if($data['wholesaler_status']==0){?>
                            <input type=button class="WSY_button"  value="审核通过" onclick="check(this);" satus_value="1" userid="<?php echo $user_id;?>"  style="float:none"/>
                            &nbsp;  
                            <input type=button class="WSY_button"  value="驳回" onclick="check(this);" satus_value="0" userid="<?php echo $user_id;?>" style="float:none" />
                        <?php }elseif($data['wholesaler_status']==1){ ?>
                            <input type=button class="WSY_button"  value="提交" onclick="submitV();"  style="float:none"/>
                            &nbsp;  
                            <input type=button class="WSY_button"  value="取消" style="float:none" />
                        <?php } ?>
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
        function check(obj){
        var btn=obj;
        var status=$(btn).attr('satus_value');
        var user_id=$(btn).attr('userid');
        console.log(status);
        console.log(user_id);
        if(status==0){
            console.log('in 0')
            $.ajax({
                type: "post",
                url: "./area_operation.php",
                dataType: "json",
                data: {'user_id': user_id,'status':status},
                success: function (result){
                    if(result){
                        alert(result.msg);
                        location.reload();
                    }
                }
            });
        }
        if(status==1){
            console.log('in 1')
            $.ajax({
                type: "post",
                url: "./area_operation.php",
                dataType: "json",
                data: {'user_id': user_id,'status':status},
                success: function (result){
                    if(result){
                        alert(result.msg);
                        location.reload();
                    }
                }
            });
        }
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

    $('.province').show();
    $("#openProvince").css("height",($('#openProvince').height() - 42)+'px');

    function submitV() {
        $("#brand_supply").submit();
    }

// --------显示控制开关效果
$(function(){
    //页面进入时判断客服有没有开
//  if($is_kefu==1){
//      $("#kf_type_div i").css('display','block');
//  }
$(".WSY_bot").click(function(){
    $(this).animate({left : '30px'});
    $(this).parent().find(".WSY_bot2").animate({left : '30px'});
    $(this).hide();
    $(this).parent().find(".WSY_bot2").show();
    $(this).parent().find("p").animate({margin : '0 0 0 13px'}, 500);
    
    $(this).parent().find("p").html('关');
    $(this).parent().css({backgroundColor : '#cbd2d8'});
    $(this).parent().find("p").css({color : '#7f8a97'});
    $("#kf_type_div i").css('display','none');
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
    $("#kf_type_div i").css('display','block');
    })
})
</script>
<script language="javascript" src="js/ToolTip.js"></script>
</html>