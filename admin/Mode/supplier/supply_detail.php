<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$user_id = $_GET['user_id'];

$query = "select supply_must from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";

$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
    $supply_must = explode('_',$row->supply_must);
}
//0=>user_name,user_phone  1=>合作商地址 2=>身份证号码 3=>公司名称 4=>身份证正反两面图片 5=>营业执照图片
$sql="select user_name,user_phone,sex,id_cards_num,company_name,location_p,location_c,location_a,business_address,id_cards_pic,business_licence_pic,status from weixin_commonshop_applysupplys where user_id={$user_id}";
$result = _mysql_query($sql) or die('Query failed: ' . mysql_error());
// var_dump($result);die;
while ($row = mysql_fetch_object($result)) {
    $user_name = $row->user_name;
    $user_phone = $row->user_phone;
    $sex = $row->sex;
    // $business_address = $row->business_address;
    $id_cards_num = $row->id_cards_num;
    $company_name = $row->company_name;
    $id_cards_pic = $row->id_cards_pic;
    if($id_cards_pic!=''){
        $id_cards_pic=explode('|', $id_cards_pic);
    }
    $business_licence_pic = $row->business_licence_pic;

    if($business_licence_pic!=''){
        $business_licence_pic=explode('|', $business_licence_pic);
    }
    $status = $row->status;
    $location_p = $row->location_p;
    $location_c = $row->location_c;
    $location_a = $row->location_a;
    $business_address = $row->business_address;

}

 $id = $configutil->splash_new($_GET["id"]);
 $parent_id = $configutil->splash_new($_GET["parent_id"]);
 $pagenum = $configutil->splash_new($_GET["pagenum"]);

// var_dump($id_cards_pic);die;
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
<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>



<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<style type="text/css">
a:hover{text-decoration: none;}   
.button_blue{cursor: pointer;margin-left: 10px;font-size: 14px;line-height: 30px;background-color: #06a7e1;padding-left: 15px;padding-right: 15px;border-radius: 3px 3px 3px 3px;margin-top:20px;color: #fff;}
.button_blue:hover{background:#0e98c9;}
.name{  margin-top: 10px;height: 30px;line-height: 30px;font-size: 13px;text-align: left;font-weight: bolder;margin-left: 19px;}
.button_box{width: 296px;display: block;text-align: right;}
.button_box .WSY_button{border-radius:2px;border:none;}
.WSY_remind_dl02{margin: 15px 0 !important;}
.WSY_remind_dl02 dt{min-width: 120px;text-align: right;}
.WSY_remind_dl02 .img-right{margin-right: 10px;}
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
.WSY_remind_dl01, .WSY_remind_dl02, .WSY_remind_dl03{margin:10px 0px;}
.textcss{width:400px;height:200px;border:1px solid #dddddd;}
</style>
</head>

<body> 
    <div class="WSY_content">
        <div class="WSY_columnbox">
            <div class="WSY_column_header">
                <div class="WSY_columnnav">
                    <a  class="white1">合作商详情</a>
                </div>
            </div>  
<!--             <form action="shop_supply_user_save.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>" id="brand_supply" method="post">
 -->
                <div id="products" class="r_con_wrap">
                    <div style="margin-top:20px">
                        
                        <dl class="WSY_remind_dl02"> 
                            <dt>合作商姓名：</dt>
                            <dd> 
                                <?php if($user_name!=''){ ?>
                                    <input type="text" name="user_id"  value="<?php echo $user_name;?>" readonly="readonly" > 
                                <?php }else{ ?>
                                    <input type="text" name="user_id"  value="无" readonly="readonly" > 
                                <?php } ?>
                            </dd>
                        </dl>
                        <dl class="WSY_remind_dl02"> 
                            <dt>合作商联系电话：</dt>
                            <dd>
                                <?php if($user_phone!=''){ ?>
                                    <input type="text" name="brand_name"  value="<?php echo $user_phone;?>" readonly="readonly">
                                <?php }else{ ?>
                                    <input type="text" name="brand_name"  value="无" readonly="readonly">
                                <?php } ?>                                
                            </dd>
                        </dl>
                        <dl class="WSY_remind_dl02"> 
                            <dt>性别：</dt>
                            <dd>                        
                                <select name="sex"  style="width:250px;">
                                    <?php if($sex==1) {?>
                                    <option value="1"  selected="selected" readonly="readonly" >男</option>
                                    <?php }elseif($sex==2) {?>
                                    <option value="2"  selected="selected" readonly="readonly" >女</option>
                                    <?php }elseif($sex==0) { ?>
                                    <option value="0"  selected="selected" readonly="readonly" >未知</option>
                                    <?php } ?>
                                </select>                            
                            </dd>
                        </dl>
                        <dl class="WSY_remind_dl02"> 
                            <dt>身份证号：</dt>
                            <dd> 
                                <?php if($id_cards_num!=''){ ?>
                                    <input type="text" name="brand_supply_name"  value="<?php echo $id_cards_num;?>" readonly="readonly">
                                <?php }else{ ?>
                                <input type="text" name="brand_supply_name"  value="无" readonly="readonly">   
                                <?php } ?> 
                            </dd>
                        </dl>                       
                        <dl class="WSY_remind_dl02"> 
                            <dt>常驻地址：</dt>
                            <dd>    
                                <?php if($location_p==''&&$location_c==''&& $location_a==''&&$business_address=='') {?>
                                <input type="" name="" value="无">  
                                <?php }else{ ?>
                                <input type="text" name=""  value="<?php echo $location_p.$location_c.$location_a.$business_address;?>" readonly="readonly" >  
                            <?php } ?>
                            </dd>
                        </dl>
                        <dl class="WSY_remind_dl02"> 
                            <dt>公司名称：</dt>
                            <dd>       
                                <?php if($company_name!=''){ ?>
                                <input type="text" name="brand_tel"  value="<?php echo $company_name;?>" readonly="readonly" >  
                                <?php }else{ ?>
                                <input type="text" name="brand_tel"  value="无" readonly="readonly" >  
                                <?php } ?> 
                            </dd>
                        </dl>

                        <dl class="WSY_remind_dl02"> 
                            <dt>身份证正反两面：</dt>
                        </dl>
                        <dl class="WSY_remind_dl02"> 
                            <dt></dt>
                            <dd>
                                 <?php
                                 // var_dump($id_cards_pic);die;
                                 if($id_cards_pic==NULL) {?>
                                <img class="img-right" src="img/gift.png" style="max-width:200px;" onMouseOver="toolTip('<img src=img/gift.png>')" onMouseOut="toolTip()"/>
                                <img class="img-right" src="img/gift.png" style="max-width:200px;" onMouseOver="toolTip('<img src=img/gift.png>')" onMouseOut="toolTip()"/>
                                <?php }else{    
                                    foreach ($id_cards_pic as $value) {?>
                                        <img class="img-right" src="<?php echo  $value;?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo  $value;?>>')" onMouseOut="toolTip()"/>
                                <?php }
                            }?>
                            </dd>
                        </dl> 

                        <dl class="WSY_remind_dl02"> 
                            <dt>公司营业执照：</dt>
                        </dl>
                        <dl class="WSY_remind_dl02"> 
                            <dt></dt>
                            <dd>                        
                                 <?php 
                                 if($business_licence_pic==NULL) {?>
                                <img class="img-right" src="img/gift.png" style="max-width:200px;" onMouseOver="toolTip('<img src=img/gift.png>')" onMouseOut="toolTip()"/>
                                <img class="img-right" src="img/gift.png" style="max-width:200px;" onMouseOver="toolTip('<img src=img/gift.png>')" onMouseOut="toolTip()"/>
                                <img class="img-right" src="img/gift.png" style="max-width:200px;" onMouseOver="toolTip('<img src=img/gift.png>')" onMouseOut="toolTip()"/>
                                <?php }else{    
                                    foreach ($business_licence_pic as $value) {?>
                                    <img class="img-right" src="<?php echo $value;?>" style="max-width:200px;" onMouseOver="toolTip('<img src=<?php echo $value;?>>')" onMouseOut="toolTip()"/>
                                <?php }
                            }?>
                            </dd>
                        </dl>
                    </div>

                    <span class="button_box">
                    <?php if($status==0){?>
                        <input id="pass" type='button' class="WSY_button"  value="审核通过" onclick="check(this);"  style="float:none;" satus_value="1" userid="<?php echo $user_id;?>"/>
                        &nbsp;  
                        <a href="javascript:showReason('supply.php?op=status&id=<?php echo $id; ?>&status=1&isAgent=-1&parent_id=<?php echo $parent_id; ?>&user_id=<?php echo $user_id; ?>&pagenum=<?php echo $pagenum; ?>');">
                            <input id="refuse" type='button' class="WSY_button"  value="驳回" style="float:none"/>
                            <!-- <input id="refuse" type='button' class="WSY_button"  value="驳回" onclick="replyFun();" style="float:none"/> -->
                        </a>
                        
                    <?php }elseif($status==1){ ?> 
                        <input type=button class="WSY_button"  value="返回" onclick="goback()" style="float:none" />
                    </span>
                    <?php }?>

<!--                      <div id="aa" style="display:none;height: auto;background-color:#D7F1FF;width: 200px;margin:0 auto;margin-top:160px">
                          <div style="float:right;cursor: pointer;"><a onclick="showTextArea(0)">关闭</a></div>
                              <b>请输入驳回原因：</b>
                              <textarea id="bb"></textarea><br>
                          <input type="button" satus_value="0" userid="<?php echo $user_id;?>" value="确定" onclick="check(this)"/>
                          <input type="button"  value="取消" onclick="showTextArea(0)"/>
                    </div> -->

                    <div class="modal fade reply" style="display: none;" aria-hidden="true" aria-labelledby="newShangpin" role="dialog" tabindex="-1">
                        <div class="modal-dialog modal-center">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="关闭">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title">请输入驳回原因：</h4>
                                </div>
                                <div class="modal-body padding-top-20" style="padding-bottom: 140px;">
                                    <div class="text-right pull-left" style="width: 20%;">回复内容：</div>
                                    <div class="pull-left" style="width: 80%;">
                                        <div class="textarea-count-box">
                                            <textarea id="reason" placeholder="请输入" rows="5" class="form-control  textarea-count-input" autocomplete="off"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer text-right ">
                                    <button type="button" class="btn btn-default margin-horizontal-10" data-dismiss="modal">取消</button>
                                    <button type="button" satus_value="0" userid="<?php echo $user_id;?>" class="btn btn-primary margin-horizontal-10" onclick="commitFun();check(this);">确定</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> 

<!--             </form>
 -->
        </div>
    </div>
</body>
<script type="text/javascript" src="js/tis.js"></script>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="layer/layer.js"></script>
<script language="javascript" src="js/ToolTip.js"></script>
<script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>

<!-- status -->
<script>
    function goback(){
        window.history.go(-1);
    }

    //隐藏 驳回理由 弹出框
    function replyFun() {
        $(".reply").modal('show');
    }
    function commitFun(){
        $(".reply").modal('hide');
    }

    // function getBtnVal(obj){
    //     var btn_val=$(obj).attr('btn_value')
    //     if(btn_val==0){
    //     $("#aa").hide();
    //     }else if(btn_val==1){
    //     var text_val=$('#bb').val();
    //     console.log(text_val);
    //     return text_val;
    //     }
    // }
    // 驳回内容
      function showReason(url){
        var str=prompt("请输入驳回/暂停理由","您不符合合作商条件，请联系客服");
        if(str)
        {
           document.location = url+"&reason="+str;
        }
      }

    //提交审核
    function check(obj){
        var btn=obj;
        var status=$(btn).attr('satus_value');
        var user_id=$(btn).attr('userid');
        console.log(status);
        console.log(user_id);
        if(status==0){
                var reason=$('#reason').val();
                console.log('in 0');
                if(reason==''){ 
                    alert('驳回理由不能为空');
                    return;
                }
                $.ajax({
                    type: "post",
                    url: "./shop_supply_user_save.php",
                    dataType: "json",
                    data: {'user_id': user_id,'status':status,'reason':reason},
                    success: function (result){
                        if(result){
                            alert(result.msg,1);
                        }
                    }
                });
        }
        if(status==1){
            console.log('in 1')
            $.ajax({
                type: "post",
                url: "./shop_supply_user_save.php",
                dataType: "json",
                data: {'user_id': user_id,'status':status},
                success: function (result){
                    if(result){
                        alert(result.msg);
                        document.location='supply_detail.php?user_id=<?php echo $user_id; ?>&id=<?php echo $id; ?>&parent_id=<?php echo $parent_id; ?>';
                    }
                }
            });
        }
    }

   // function submitV(){ 
   //     $("#brand_supply").submit();
   // }
</script>
</html>