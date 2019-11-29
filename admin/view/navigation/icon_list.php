<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/custom.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/contentblue.css">
    <link href="/weixinpl/back_commonshop/css/global.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/colorpicker.css">
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGreen.css">-->
    <!--内容CSS配色·绿色-->
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentOrange.css">-->
    <!--内容CSS配色·橙色-->
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentbgreen.css">-->
    <!--内容CSS配色·蓝绿-->
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGGreen.css">-->
    <!--内容CSS配色·草绿-->
    <link rel="stylesheet" type="text/css" href="/weixinpl/mshop/css/floatingwindow.css">
    <script type="text/javascript" src="/weixinpl/back_newshops/Base/personalization/custom/js/jquery-1.12.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.13/dist/vue.js"></script>
    <script type="text/javascript" src="/weixinpl/common/js/jquery.form.min.js"></script>
    <style>
    .selectitem {
        position: relative;
    }
    .selectitem:after {
        background-image: url(/weixinpl/mshop/images_red/goods_image/2016042705.png);
        background-repeat: no-repeat;
        background-position: 100% 100%;
        background-size: 18px 18px;
        border: 2px solid #ec2935;
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }
    .tips-box {
        margin-top: 40px;
        margin-bottom: 5px;
        margin-left: 20px;
    }
    .tips-box p,
    .tips-box p span
     {
        color: red;
        font-size: 15px;
    }
    .main-content {
        width: 320px;
        height: 480px;
        position: relative;
        overflow: hidden;
        background-color: #fff;
        /*background-color: rgba(0, 0, 0, 0.4);*/
    }
    .type-ctrl-box {
        padding: 10px;
        position: relative;
        min-width: 550px;
        /*float: left;*/
        margin: 20px;
    }
    .desp1 {
        font-size: 14px;
        color: #333;
        line-height: 32px;
    }
    .ctrl-title {
        font-size: 15px;
        color: #333;
        padding-top: 4px;
        padding-bottom: 12px;
        border-bottom: 1px solid #ccc;
    }
    .ctrl-ipt-box {
        font-size: 0;
        margin-top: 10px;
    }
    .ctrl-ipt-box label {
        width: 80px;
        text-align: left;
        font-size: 14px;
        color: #333;
        display: inline-block;
        vertical-align: middle;
    }
    .ctrl-ipt-box .ctrl-ipt {
        width: 200px;
        border-radius: 4px;
        color: #000;
        display: inline-block;
        font-size: 13px;
        margin-right: 5px;
        padding: 2px 8px;
        vertical-align: middle;
        height: 26px;
        line-height: 26px;
    }
    .clear {
        clear: both;
    }
    .frame_image_tips {
        font-size: 14px;
        color: #333;
        display: inline-block;
        margin-left: 15px;
    }
    .frame_image {
        width: 60px;

        height: 60px;
        display: inline-block;
        position: relative;
        z-index: 1;
    }
    .frame_image img {
        width: 100%;
        height: 100%;
    }

    .frame_image .frame_image_select {
        width: 60px;
        height: 60px;
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
    }
    .right-box,
    .left-box {
        display: inline-block;
        vertical-align: top;
        padding-bottom: 40px;
    }
    .right-box{
        background-color: #f8f8f8;
        border: 1px solid #ccc;
        margin: 20px;
        border-radius: 5px;
    }
    .btn-box {
        text-align: center;
    }
    .btn-box .WSY_button:first-of-type {
        margin-right: 25px;
    }
    .btn-box .WSY_button {
        width: 105px;
        height: 36px;
        border-radius: 3px;
        border: none;
        float: none;
        outline: none;
    }
    #template1 .icon-box1 {
        max-width: 56px;
    }
    .graybtn {
        background-color: #ccc;
        color: #000;
    }
    .link-choose {
        color: #fff!important;
        font-size: 12px;
        border: 0!important;
        height: 30px;
        line-height: 30px;
        padding: 0 10px 0 10px;
        border-radius: 3px;
        vertical-align: middle;
    }
    .main-content .position-abs {
        position: absolute!important;
    }
    #template5 .floating-window-box5 .floating-window-abs5{
        display:block;
    }
    </style>
</head>

<body>
    <!--内容框架开始-->
    <div class="WSY_content" id="iconList">
        <!--微商城统计代码结束-->
        <!--列表内容大框开始-->
        <div class="WSY_columnbox" style="position:relative">
            <!--首页设置代码开始-->
            <div class="main">
                <div class="WSY_data">
                    <div class="WSY_homebox">
                        <div class="tips-box">
                            <p>提示：导航最多<span id="tipnumber">15</span>个。</p>
                        </div>
                        <div class="left-box">
                            <div class="WSY_homeleft">
                                <li class="WSY_homeleft_top">
                                    <p></p>
                                </li>
                                <li class="WSY_homeleft_middle">
                                    <!--模块开始-->
                                    <div class="main-content">
                                        <div id="template1" style="display: none;">
                                            <div class="floating-window-box1 position-abs <?php if($position==2) echo 'floating-left'; ?>">
                                                <div class="pack-up1" id="packUp1" @click="showteml1">
                                                    <img class="pack-up-icon1" src="/weixinpl/mshop/images/icon-rightw.png" />
                                                    <div class="pack-up-title1">收起</div>
                                                </div>
                                                <div class="floating-window-abs1" v-show="temp1flag==true">
                                                    <div class="floating-window1">
                                                        <div class="icon-box1" v-for="(item, index) in maindatetel" v-bind:index="index" v-bind:imgUrl="item.imgUrl" @click="menuselect($event,index,-1)" v-bind:id="item.id" :class="item.id" v-bind:diyid="item.id"><a><img :src="item.imgUrl"></a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="template2" style="display: none;">
                                            <div class="floating-window-box2 position-abs  <?php if($position==2) echo 'floating-left'; ?>">
                                                <div class="floating-window-abs2">
                                                    <div class="floating-window2">
                                                        <div class="icon-box2" v-for="(item, index) in maindatetel" v-bind:index="index" v-bind:imgUrl="item.imgUrl" @click="menuselect($event,index,-1)" v-bind:id="item.id" :class="item.id" v-bind:diyid="item.id"><a><img :src="item.imgUrl"></a></div>
                                                    </div>
                                                </div>
                                                <div class="close2" id="close2" @click="close2">
                                                    <img class="close-icon2" src="/weixinpl/mshop/images/icon-closeg.png" />
                                                </div>
                                            </div>
                                            <div class="add-show2 position-abs <?php if($position==2) echo 'floating-left'; ?>" id="add-show2" @click="showteml2">
                                                <img src="/weixinpl/mshop/images/icon-addw.png" />
                                            </div>
                                        </div>
                                        <div id="template3" style="display: none;">
                                            <div class="floating-window-box3 position-abs <?php if($position==2) echo 'floating-left'; ?>">
                                                <div class="floating-window-abs3">
                                                    <div class="floating-window3">
                                                        <div class="icon-box3" v-for="(item, index) in maindatetel" v-bind:index="index" v-bind:imgUrl="item.imgUrl" @click="menuselect($event,index,-1)" v-bind:id="item.id" :class="item.id" v-bind:diyid="item.id"><a><img :src="item.imgUrl"></a></div>
                                                    </div>
                                                </div>
                                                <div class="close3" id="close3" @click="close3">
                                                    <img class="close-icon3" src="/weixinpl/mshop/images/icon-closeg.png" />
                                                </div>
                                            </div>
                                            <div class="add-show3 position-abs <?php if($position==2) echo 'floating-left'; ?>" id="add-show3" @click="showteml3">
                                                <img src="/weixinpl/mshop/images/icon-addw.png" />
                                            </div>
                                        </div>
                                        <div id="template4" style="display: none;">
                                            <div class="floating-window-box4 position-abs <?php if($position==2) echo 'floating-left'; ?>">
                                                <div class="floating-window-abs4">
                                                    <div class="floating-window4">
                                                        <div class="icon-box4" v-for="(item, index) in maindatetel" v-bind:index="index" v-bind:imgUrl="item.imgUrl" @click="menuselect($event,index,-1)" v-bind:id="item.id" :class="item.id" v-bind:diyid="item.id"><a><img :src="item.imgUrl"></a></div>
                                                    </div>
                                                </div>
                                                <div class="close4" id="close4" @click="close4">
                                                    <img class="close-icon4" src="/weixinpl/mshop/images/icon-closew.png" />
                                                </div>
                                            </div>
                                            <div class="add-show4 position-abs <?php if($position==2) echo 'floating-left'; ?>" id="add-show4" @click="showteml4">
                                                <img src="/weixinpl/mshop/images/icon-addw.png" />
                                            </div>
                                        </div>
                                        <div id="template5" style="display: none;">
                                            <div class="floating-window-box5 position-abs <?php if($position==2) echo 'floating-left'; ?>">
                                                <div class="floating-window-abs5" v-show="temp5flag==true">
                                                    <div class="floating-window5">
                                                        <div class="icon-box5" v-for="(item, index) in maindatetel" v-bind:index="index" v-bind:imgUrl="item.imgUrl" @click="menuselect($event,index,-1)" v-bind:id="item.id" v-bind:diyid="item.id" :class="item.id" :style="widfun5()"><a><img :src="item.imgUrl"></a></div>
                                                    </div>
                                                </div>
                                                <div class="add-show5 position-abs <?php if($position==2) echo 'floating-left'; ?>" @click="showteml5">
                                                    <img src="/weixinpl/mshop/images/icon-addw.png" />
                                                </div>
                                            </div>
                                        </div>
                                        <div id="template7" style="display: none;">
                                            <div class="floating-window-box7 position-abs <?php if($position==2) echo 'floating-left'; ?>">
                                                <div class="add-show7" @click="showteml7">
                                                    <img :class="temp7flag==true?'':'transform180'" src="/weixinpl/mshop/images/icon-arroww.png" />
                                                </div>
                                                <div class="floating-window-abs7">
                                                    <div class="floating-window7">
                                                        <div class="icon-box7" v-for="(item, index) in maindatetel" v-bind:index="index" v-bind:imgUrl="item.imgUrl" @click="menuselect($event,index,-1)" :class="index==0?'customerService':item.id" :id="item.id" v-if="index!=0 && temp7flag==false?'':'display:none'"><a><img :src="item.imgUrl"></a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--模块结束-->
                                </li>
                                <li class="WSY_homeleft_bottom">
                                    <p></p>
                                </li>
                                <li class="WSY_foot">
                                </li>
                            </div>
                            <div class="clear"></div>
                            <div class="btn-box">
                                <input type="button" class="WSY_button" value="添加菜单" style="cursor:pointer;" @click="addflag && addmenu()" id="addmenu" :class="addflag==false ? 'graybtn':''">
                                <input type="button" class="WSY_button" value="保存模板" style="cursor:pointer;" @click="saveAll()">
                                <div class="clear"></div>
                            </div>
                        </div>
                        <div class="right-box">
                            <div class="tips-box" id="starttips" style="display: none;">
                                <p>请先选择一个菜单来进行编辑。</p>
                            </div>
                            <div class="type-ctrl-box" v-for="(item, index) in maindatetel" v-show="selectright==index">
                                <form class="search" enctype="multipart/form-data"  method="post" action="/mshop/admin/index.php?m=navigation&a=icon_edit">
                                <input type=hidden name="id"  v-model="item.id" />
                                <!-- <p class="ctrl-title">菜单</p> -->
                                <p class="ctrl-title">菜单{{capital[index]}}</p>
                                <p class="desp1">选中的样式：</p>
                                <div class="ctrl-main">
                                    <div class="frame_image" id="">
                                        <img id="img_0" :src="item.imgUrl">
                                        <input type="file" id="image1" class="frame_image_select" name="filedata" value="" onchange="fileSelect_banner(this)">
                                        <input type=hidden name="imgurl"  v-model="item.imgUrl" />
                                    </div>
                                    <p class="frame_image_tips">建议尺寸：<?php if($style==4){echo '320px*120px';}else if($style==3){echo '120px*160px';}else{echo '160px*160px';} ?>。</p>
                                </div>
                                <div class="ctrl-ipt-box">
                                    <label>排序：</label>
                                    <input type="text" name="sort" class="ctrl-ipt" v-model="item.orderby">
                                </div>
                                <div class="ctrl-ipt-box">
                                    <label>栏目：</label>
                                    <input type="text" name="" class="ctrl-ipt" v-model="item.column_title" id="selector_title" disabled>
                                    <button type="button" class="link-choose WSY-skin-bg" onclick="showSelector(this)">请选择</button>
                                    <input type=hidden name="selector_id" id="selector_id" v-model="item.column_id" />
                                </div>
                                <div class="ctrl-ipt-box">
                                    <label>URL：</label>
                                    <input type="text" name="page_url" class="ctrl-ipt" v-model="item.url">
                                </div>
                                </form>
                            </div>
                            <div class="clear"></div>
                            <div class="btn-box" style="text-align: left;margin-left: 120px;">
                                <input type="button" class="WSY_button" value="删除菜单" style="cursor:pointer;" @click="deleteflag && deletemenu()" id="deletemenu" :class="deleteflag==false ? 'graybtn':''">
                                <input type="button" class="WSY_button" @click="submitForm()" value="保存菜单" style="cursor:pointer;">
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--内容框架结束-->
    <script type="text/javascript" src="/weixinpl/back_newshops/Base/personalization/custom/js/layer/layer.js"></script>
    <script type="text/javascript">
    var is_publish = 1;
    var template = <?php 
        switch ($style) {
            case 1:
                echo '7';
                break;
            case 2:
                echo '1';
                break;
            case 3:
                echo '2';
                break;
            case 4:
                echo '3';
                break;
            case 5:
                echo '4';
                break;
            case 6:
                echo '5';
                break;
            default:
                break;
        }
     ?>;
    if (template === 1) {
        $("#tipnumber").text(12);
        $("#template1").show();
    } else if (template === 2) {
        $("#tipnumber").text(15);
        $("#template2").show();
    } else if (template === 3) {
        $("#tipnumber").text(15);
        $("#template3").show();
    } else if (template === 4) {
        $("#tipnumber").text(12);
        $("#template4").show();
    } else if (template === 5) {
        $("#tipnumber").text(15);
        $("#template5").show();
    } else if (template === 7) {
        $("#tipnumber").text(15);
        $("#template7").show();
    }             
    </script>
    <script type="text/javascript">
    function fileSelect_banner(evt) {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            currfile = evt;
            var files = evt.files; //直接传入file对象，evt.target改成evt
            var pid = $(evt).data("pid"); //现在选择的商品的pid
            var file;
            file = files[0];
            if (!file.type.match('image.*')) {
                return;
            }
            reader = new FileReader();
            reader.onload = (function(tFile) {
                return function(evt) {
                    dataURL = evt.target.result;
                    $(currfile).prev("img").eq(0).attr("src", dataURL);
                    if (template === 1) {
                        var indselect = $(".icon-box1").index($('.selectitem'));
                    } else if (template === 2) {
                        var indselect = $(".icon-box2").index($('.selectitem'));
                    } else if (template === 3) {
                        var indselect = $(".icon-box3").index($('.selectitem'));
                    } else if (template === 4) {
                        var indselect = $(".icon-box4").index($('.selectitem'));
                    } else if (template === 5) {
                        var indselect = $(".icon-box5").index($('.selectitem'));
                    } else if (template === 7) {
                        var indselect = $(".icon-box7").index($('.selectitem'));
                    }
                    $(".selectitem").find("img").attr("src", dataURL);
                    maindatetel[indselect].imgUrl = dataURL;
                }
            }(file));
            reader.readAsDataURL(file);
            sendFile = file;
        } else {
            alert('该浏览器不支持文件管理。');
        }
    }
    </script>
    <script type="text/javascript">
    var maxsort=<?php echo $maxsort; ?>;
    var maindatetel = new Object();
    maindatetel=<?php echo $icon_list; ?>;

console.log(maindatetel)
    /*maindatetel = [{
            id: "id0",
            imgUrl: '/weixinpl/up/1/3243/Base/personalization/navigation/1503048698.png',
            orderby: 0,
            column: '',
            url: ''
        },
        {
            id: "id1",
            imgUrl: '/weixinpl/up/1/3243/Base/personalization/navigation/1503048698.png',
            orderby: 1,
            column: '',
            url: ''
        }
    ];*/
    var vm = new Vue({
        el: '#iconList',
        data: {
            length: 0,
            addflag: true,
            deleteflag: true,
            temp1flag:true,
            temp5flag:true,
            temp7flag:true,
            selectright:'',
            selectid: '',
            capital: ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二', '十三', '十四', '十五', '十六', '十七', '十八', '十九', '二十'],
            maindatetel: maindatetel
        },
        methods: {
            saveAll: function() {
                layer.alert("保存成功",function(){
//                    history.back();
                    window.location.href='/mshop/admin/index.php?m=navigation&a=template_list&customer_id='+customer_id_en;
                });
            },
            addmenu: function() {
                var self=this;       
                // 添加数据
                var icon_url_defalt='/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png';
                    if (template === 3) {
                       icon_url_defalt= '/mshop/admin/static/images/icon-typec-1.png';
                    }else if (template === 4) {
                      icon_url_defalt='/mshop/admin/static/images/gift.png';
                    }else{
                        icon_url_defalt='/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png';
                    }

                $.post("/mshop/admin/index.php?m=navigation&a=icon_add",{tmp_id:<?php echo $id; ?>,icon_url:icon_url_defalt,sort:++maxsort},function(result){
                   if(result.errcode!=0){
                    layer.alert(result.errmsg);
                   }else{
                    if(self.length==0){
                        self.removeAllcla();
                        setTimeout(function() {
                            self.menuselect("",0,"");
                        }, 20); 
                    }
                    if (template === 3) {
                        maindatetel.push({
                            id: result.insertid,
                            imgUrl: '/mshop/admin/static/images/icon-typec-1.png',
                            orderby: maxsort,
                            column_title: '',
                            column_id: '',
                            url: ''
                        });
                    }else if (template === 4) {
                        maindatetel.push({
                            id: result.insertid,
                            imgUrl: '/mshop/admin/static/images/gift.png',
                            orderby: maxsort,
                            column_title: '',
                            column_id: '',
                            url: ''
                        });
                    }else{
                        maindatetel.push({
                            id: result.insertid,
                            imgUrl: '/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png',
                            orderby: maxsort,
                            column_title: '',
                            column_id: '',
                            url: ''
                        });
                    }
                    self.maindatetel = maindatetel;
                    self.length=maindatetel.length;
                    // console.log(self.length)
                    if (template === 1) {
                        self.cretemp1();
                    }else if (template === 2) {
                        self.cretemp2();
                    }else if (template === 3) {
                        self.cretemp3();
                    }else if (template === 4) {
                        self.cretemp4();
                    }else if (template === 5) {
                        self.temp5flag=true;
                        self.cretemp5();
                    }else if (template === 7) {
                        self.temp7flag==true;
                        self.cretemp7();
                    }
                   }
                },'json');
            },
            menuselect: function(event, index,selectid) {
                var self = this;
                self.removeAllcla();
                $("#starttips").hide();
                console.log(self.selectid)

                if(selectid!=-1){
                    self.selectid = self.maindatetel[0].id;
                }else{
                    self.selectid = event.currentTarget.getAttribute("id");
                }
                    if (template === 1) {
                        $("#template1").find("#" + self.selectid).addClass("selectitem");
                    } else if (template === 2) {
                        $("#template2").find("#" + self.selectid).addClass("selectitem");
                    } else if (template === 3) {
                        $("#template3").find("#" + self.selectid).addClass("selectitem");
                    } else if (template === 4) {
                        $("#template4").find("#" + self.selectid).addClass("selectitem");
                    } else if (template === 5) {
                        $("#template5").find("#" + self.selectid).addClass("selectitem");
                    }  else if (template === 7) {
                        $("#template7").find("#" + self.selectid).addClass("selectitem");
                    }
                    self.selectright=index;
            },
            deletemenu: function() {
                var self = this;
                for (var j = 0; j < self.maindatetel.length; j++) {
                    if (self.maindatetel[j].id === self.selectid) {
                        self.maindatetel.splice(j, 1);
                        self.length--;
                    }
                }
                self.removeAllcla();
                setTimeout(function() {
                    if(self.length>0){
                        self.menuselect("",0,"");
                    }
                }, 20); 
                console.log(self.selectid)
                console.log(self.selectright)
                if (template === 1) {
                    self.cretemp1();
                } else if (template === 2) {
                    self.cretemp2();
                }else if (template === 3) {
                    self.cretemp3();
                }else if (template === 4) {
                    self.cretemp4();
                }else if (template === 5) {
                    self.cretemp5();
                }else if (template === 7) {
                    self.temp7flag==true;
                    self.cretemp7();
                }
                $.post("/mshop/admin/index.php?m=navigation&a=icon_del",{id:self.selectid},function(result){
                   if(result.errcode!=0){
                    layer.alert(result.errmsg);
                   }
                },'json');
            },            
            widfun5: function() {
                var self = this;
                var width = $(".icon-box5").width();
                if (self.length < 6) {
                    $("#template5 .floating-window-box5 .floating-window5 .icon-box5").width(100 + "%");
                    $("#template5 .floating-window-box5").css("maxWidth", 14.5 + "%");
                } else if (self.length < 11) {
                    $("#template5 .floating-window-box5 .floating-window5 .icon-box5").width(100 / 2 + "%");
                    $("#template5 .floating-window-box5").css("maxWidth", 14.5 * 2 + "%");
                } else {
                    $("#template5 .floating-window-box5 .floating-window5 .icon-box5").width(100 / 3 + "%");
                    $("#template5 .floating-window-box5").css("maxWidth", 14.5 * 3 + "%");
                }
            },
            iptchange: function(selector_id, selector_title) {
                var self = this;
                var ind = $(".selectitem").index();
                console.log(ind)
                self.maindatetel[ind].column_title = selector_title;
                self.maindatetel[ind].column_id = selector_id;
                console.log(selector_id);
                console.log(selector_title);
            },
            removeAllcla: function() {
                if (template === 1) {
                    $(".icon-box1").removeClass("selectitem");
                } else if (template === 2) {
                    $(".icon-box2").removeClass("selectitem");
                } else if (template === 3) {
                    $(".icon-box3").removeClass("selectitem");
                } else if (template === 4) {
                    $(".icon-box4").removeClass("selectitem");
                } else if (template === 5) {
                    $(".icon-box5").removeClass("selectitem");
                } else if (template === 7) {
                    $(".icon-box7").removeClass("selectitem");
                }
            },
            submitForm: function(){
                var self = this;
                var form=$(".right-box .type-ctrl-box form").eq(self.selectright);
                console.log(self.selectright+"oo")
                form.ajaxSubmit(function(response){
                    response=JSON.parse(response);
                    layer.alert(response.errmsg);
                });
            },
            cretemp1: function(){
                var self = this;
                var width = $(".main-content").width();
                var pickup1w = $("#packUp1").outerWidth();
                $("#template1 .floating-window-box1").css("opacity", 1);
                if(self.length===0){
                    $("#template1 .floating-window-box1").css("width", pickup1w);
                    $("#template1 .floating-window-box1 .floating-window-abs1").css("width", 0);
                    $("#starttips").find("p").text("您还没有添加菜单，请先添加一个菜单来进行编辑。");
                    $("#starttips").show();
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length<2){
                    $("#template1 .floating-window-box1").css({ "width": Math.ceil((width * 0.7 / 4 * self.length) + 51) });
                    $("#template1 .floating-window-box1 .floating-window-abs1").css({ "width": Math.ceil(width * 0.7 / 4 * self.length) });
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length<5){
                    $("#template1 .floating-window-box1").css({ "width": Math.ceil((width * 0.7 / 4 * self.length) + 51) });
                    $("#template1 .floating-window-box1 .floating-window-abs1").css({ "width": Math.ceil(width * 0.7 / 4 * self.length) });
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length<12){
                    $("#template1 .floating-window-box1").css({ "width": Math.ceil((width * 0.7 / 4 * 4) + 51) });
                    $("#template1 .floating-window-box1 .floating-window-abs1").css({ "width": Math.ceil(width * 0.7 / 4 * 4) });
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length<13){
                    $("#template1 .floating-window-box1").css({ "width": Math.ceil((width * 0.7 / 4 * 4) + 51) });
                    $("#template1 .floating-window-box1 .floating-window-abs1").css({ "width": Math.ceil(width * 0.7 / 4 * 4) });
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = false;
                }else{
                    self.deleteflag = true;
                    self.addflag = false;
                }
            },
            showteml1:function(){
                var self=this;
                self.temp1flag=!self.temp1flag;
            },
            cretemp2: function(){
                var self=this;
                self.showteml2();                
            },
            showteml2:function(){
                var self=this;
                if(self.length===0){
                    $("#template2 .floating-window-box2").css({ "height": 0, "opacity": 0 });
                    $("#starttips").find("p").text("您还没有添加菜单，请先添加一个菜单来进行编辑。");
                    $("#starttips").show();
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 2){
                    $("#template2 .floating-window-box2").css({ "height": "auto","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 15){
                    $("#template2 .floating-window-box2").css({ "height": "auto","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length < 16){
                    $("#template2 .floating-window-box2").css({ "height": "auto","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = false;
                }else{
                    self.deleteflag = true;
                    self.addflag = false;
                }
            },
            close2:function(){
                $("#template2 .floating-window-box2").animate({ "height": 0,"opacity": 0 });
            },
            cretemp3: function(){
                var self=this;
                self.showteml3();                
            },
            showteml3:function(){
                var self=this;
                var winheight = $(".main-content").height();
                if (is_publish == '1') {} else {
                    console.log($(".footer").outerHeight());
                }
                $("#template3 .floating-window-box3").css({ "height": winheight });
                $("#template3 .floating-window-box3 .floating-window-abs3").css({ "height": winheight - $("#template3 #close3").outerHeight() - 10});
                $("#template3 .floating-window-box3 .floating-window-abs3 .floating-window3").css({ "height": winheight - $("#template3 #close3").outerHeight() - 10});
                if(self.length===0){
                    $("#template3 .floating-window-box3").css({ "width": 0, "opacity": 0 });
                    $("#starttips").find("p").text("您还没有添加菜单，请先添加一个菜单来进行编辑。");
                    $("#starttips").show();
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 2){
                    $("#template3 .floating-window-box3").css({ "width": "43%","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 15){
                    $("#template3 .floating-window-box3").css({ "width": "43%","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length < 16){
                    $("#template3 .floating-window-box3").css({ "width": "43%","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = false;
                }else{
                    self.deleteflag = true;
                    self.addflag = false;
                }
            },
            close3:function(){
                $("#template3 .floating-window-box3").animate({ "width": 0,"opacity": 0 });
            },
            cretemp4: function(){
                var self=this;
                self.showteml4();                
            },
            showteml4:function(){
                var self=this;
                if(self.length===0){
                    $("#template4 .floating-window-box4").css({ "height": 0, "opacity": 0 });
                    $("#starttips").find("p").text("您还没有添加菜单，请先添加一个菜单来进行编辑。");
                    $("#starttips").show();
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 2){
                    $("#template4 .floating-window-box4").css({ "height": "auto","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 12){
                    $("#template4 .floating-window-box4").css({ "height": "auto","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length < 13){
                    $("#template4 .floating-window-box4").css({ "height": "auto","opacity":1});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = false;
                }else{
                    self.deleteflag = true;
                    self.addflag = false;
                }
            },
            close4:function(){
                $("#template4 .floating-window-box4").animate({ "height": 0,"opacity": 0 });
            },
            cretemp5: function(){
                var self=this;
                if(self.length===0){
                    $("#template5 .floating-window-abs5").hide();
                    $("#starttips").find("p").text("您还没有添加菜单，请先添加一个菜单来进行编辑。");
                    $("#starttips").show();
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 2){
                    var num5 = Math.ceil(self.length / 5);
                    setTimeout(function() {
                        $("#template5 .floating-window-box5").css("maxWidth", 14.5 * num5 + "%");
                        $("#template5 .floating-window-box5 .floating-window5 .icon-box5").width(100 / num5 + "%");
                    }, 20);                    
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 15){
                    var num5 = Math.ceil(self.length / 5);
                    setTimeout(function() {
                        $("#template5 .floating-window-box5").css("maxWidth", 14.5 * num5 + "%");
                        $("#template5 .floating-window-box5 .floating-window5 .icon-box5").width(100 / num5 + "%");
                    }, 20);                    
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length < 16){
                    var num5 = Math.ceil(self.length / 5);
                    setTimeout(function() {
                        $("#template5 .floating-window-box5").css("maxWidth", 14.5 * num5 + "%");
                        $("#template5 .floating-window-box5 .floating-window5 .icon-box5").width(100 / num5 + "%");
                    }, 20);  
                    self.deleteflag = true;
                    self.addflag = false;
                }else{
                    self.deleteflag = true;
                    self.addflag = false;
                }
            },
            showteml5:function(){
                var self=this;
                if(self.length===0){
                    self.temp5flag=false;
                }else{
                    self.temp5flag=!self.temp5flag;
                }
            },
            cretemp7: function(){
                var self=this;         
                $("#template7 .floating-window-box7 .customerService").show();
                $("#template7 .floating-window-box7").css("opacity", 1);
                var width = $(".main-content").width();
                setTimeout(function() {
                if(self.length===0){
                    $("#template7 .floating-window-box7").css("opacity", 0);
                    $("#starttips").find("p").text("您还没有添加菜单，请先添加一个菜单来进行编辑。");
                    $("#starttips").show();
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 2){
                    $("#template7 .floating-window-box7").css("opacity", 1);
                    var width = $(".main-content").width();
                    var imgHeight = Math.ceil(width * 0.13) - 4;
                    $("#template7 .floating-window-box7").css({"maxWidth":imgHeight});
                    if(self.temp7flag==true){
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight});
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":imgHeight*self.length});
                        $("#template7 .floating-window-box7").css("width", "auto");
                    }else{
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":imgHeight});
                        $("#template7 .floating-window-box7").css("width", imgHeight);
                    }
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length < 6){
                    var width = $(".main-content").width();
                    var imgHeight = Math.ceil(width * 0.13) - 4;
                    $("#template7 .floating-window-box7").css({"maxWidth":imgHeight});
                    if(self.temp7flag==true){
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight});
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":imgHeight*self.length});
                        $("#template7 .floating-window-box7").css("width", "auto");
                    }else{
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":imgHeight});
                        $("#template7 .floating-window-box7").css("width", imgHeight);
                    }   
                    $("#template7 .floating-window-box7 .floating-window7 .icon-box7").css({"height":imgHeight,"width":imgHeight});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length < 11){
                    var width = $(".main-content").width();
                    var imgHeight = Math.ceil(width * 0.13) - 4;
                    $("#template7 .floating-window-box7").css({"maxWidth":imgHeight*2});
                    if(self.temp7flag==true){
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight*2});
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":"auto"});
                        $("#template7 .floating-window-box7").css("width", "auto");
                    }else{
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight});
                        $("#template7 .floating-window-box7").css("width", imgHeight);
                    }                   
                    $("#template7 .floating-window-box7 .floating-window7 .icon-box7").css({"height":imgHeight,"width":imgHeight});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length < 15){
                    var width = $(".main-content").width();
                    var imgHeight = Math.ceil(width * 0.13) - 4;
                    $("#template7 .floating-window-box7").css({"maxWidth":imgHeight*3});
                    if(self.temp7flag==true){
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight*3});
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":"auto"});
                        $("#template7 .floating-window-box7").css("width", "auto");
                    }else{
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight});
                        $("#template7 .floating-window-box7").css("width", imgHeight);
                    }
                    $("#template7 .floating-window-box7 .floating-window7 .icon-box7").css({"height":imgHeight,"width":imgHeight});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length < 16){
                    var width = $(".main-content").width();
                    var imgHeight = Math.ceil(width * 0.13) - 4;
                    $("#template7 .floating-window-box7").css({"maxWidth":imgHeight*3});
                    if(self.temp7flag==true){
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight*3});
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":"auto"});
                        $("#template7 .floating-window-box7").css("width", "auto");
                    }else{
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight});
                        $("#template7 .floating-window-box7").css("width", imgHeight);
                    }
                    $("#template7 .floating-window-box7 .floating-window7 .icon-box7").css({"height":imgHeight,"width":imgHeight});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = false;
                }else if(self.length < 20){
                    var width = $(".main-content").width();
                    var imgHeight = Math.ceil(width * 0.13) - 4;
                    $("#template7 .floating-window-box7").css({"maxWidth":imgHeight*4});
                    if(self.temp7flag==true){
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight*4});
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"height":"auto"});
                    }else{
                        $("#template7 .floating-window-box7 .floating-window-abs7").css({"width":imgHeight});
                    }
                    $("#template7 .floating-window-box7 .floating-window7 .icon-box7").css({"height":imgHeight,"width":imgHeight});
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = false;
                }else{
                    self.deleteflag = true;
                    self.addflag = false;
                }    
                }, 0.5); 

            },
            showteml7:function(){
                var self=this;
                if(self.length===0){
                    self.temp7flag=false;
                }else{
                    self.temp7flag=!self.temp7flag;
                    self.cretemp7();
                }
                self.removeAllcla();
                setTimeout(function() {
                    if(self.length>0){
                        self.menuselect("",0,"");
                    }
                }, 5); 
            }
        }, 
        created: function () {
            var self=this;
            self.length=maindatetel.length;
            console.log(self.length);
            if(template===1){
                self.cretemp1();
            }else if(template===2){
                self.cretemp2();
            }else if(template===3){
                self.cretemp3();
            }else if(template===4){
                self.cretemp4();
            }else if (template === 5) {
                self.cretemp5();
            }else if (template === 7) {
                self.cretemp7();
            }
            setTimeout(function() {
                if(self.length>0){
                    self.menuselect("",0,"");
                }
            }, 20);  

        }
    })
    var that; //标签选择
//    var customer_id_en = 'ATRba1s5V20=';
    var customer_id_en = '<?php echo $customer_id_en; ?>';

    function showSelector(obj) {
        that = obj;
        var selector_id = $(obj).parent().find('#selector_id').val();
        layer.open({
            type: 2,
            area: ['1500px', '720px'],
            fixed: false, //不固定
            maxmin: true,
            resize: true,
            title: '选择链接页面',
            content: '/mshop/admin/index.php?m=plug_link_selector&a=selector_list&customer_id=' + customer_id_en + '&selector_id=' + selector_id,
        });
    }
    //选择链接回调函数
    //[int] selector_id 链接组成ID [string] selector_title 链接名称
    function showSelectorCallback(selector_id, selector_title) {
        console.log(selector_id);
        console.log(selector_title);
        //$(that).parent().find("#selector_title").val(selector_title);
        //$(that).parent().find("#selector_id").val(selector_id);
        vm.iptchange(selector_id, selector_title);
    }

    /**
     * 生成一个用不重复的ID
     */
    function GenNonDuplicateID(randomLength) { 
        let idStr = Date.now().toString(36);
        idStr += Math.random().toString(36).substr(3, randomLength); 
        return idStr;
    }

    </script>
</body>

</html>