<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/custom.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/custom2.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/colorpicker.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/layout.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/back_newshops/Base/personalization/custom/css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/weixinpl/common/css_V6.0/contentblue.css">
    <link href="/weixinpl/back_commonshop/css/global.css" rel="stylesheet" type="text/css">
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGreen.css">-->
    <!--内容CSS配色·绿色-->
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentOrange.css">-->
    <!--内容CSS配色·橙色-->
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentbgreen.css">-->
    <!--内容CSS配色·蓝绿-->
    <!--<link rel="stylesheet" type="text/css" href="../../../../common/css_V6.0/contentGGreen.css">-->
    <!--内容CSS配色·草绿-->
    <script type="text/javascript" src="/weixinpl/back_newshops/Base/personalization/custom/js/jquery-1.12.1.min.js"></script>
    <script type="text/javascript" src="/weixinpl/back_newshops/Base/personalization/custom/js/colorpicker.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.13/dist/vue.js"></script>
    <script type="text/javascript" src="/weixinpl/common/js/jquery.form.min.js"></script>
    <style>
    .footer.hasname {
        position: absolute;
        bottom: 0px;
        left: 0px;
        width: 100%;
        height: 49px;
        background: #fff;
        z-index: 50;
        line-height: 24px;
        border-top: 1px solid #eeeeee;
        box-shadow: 0 0 10px 0 rgba(155, 143, 143, 0.6);
        -webkit-box-shadow: 0 0 10px 0 rgba(155, 143, 143, 0.6);
        padding: 0px;
    }
    .footer.hasname .footer-box {
        margin: 0 auto;
        width: 100%;
        height: 49px;
        display: -webkit-box;
    }
    .footer.hasname .footer-box .weidian {
        height: 49px;
        text-align: center;
        -webkit-box-flex: 1;
        -moz-box-flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        float: none;
    }
    .footer.hasname .footer-box .weidian p {
        font-size: 12px;
        color: #a1a1a1;
        margin: 0;
        line-height: 14px;
        overflow: hidden;
    }
    .footer.hasname .footer-box .weidian.active p {
        color: #64b83c;
        white-space: nowrap;
        text-overflow: clip;
        overflow: hidden;
    }
    .footer.hasname .footer-box .weidian p.foot_grey {
        color: #a1a1a1;
    }
    .paddingBottom {
        height: 49px;
    }
    .footer.hasname .footer-box .weidian img {
        width: 32px;
        height: 32px;
        margin:0 auto;
        vertical-align: middle;
    }
    .footer.hasname .footer-box .weidian .foot-text {
        font-size: 10px;
        line-height: 14px;
        white-space: nowrap;
        overflow: hidden;
    }
    .main {
        width: auto;
    }
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
    .tips-box p {
        color: red;
        font-size: 15px;
    }
    .main-content {
        width: 320px;
        height: 480px;
        position: relative;
        overflow: hidden;
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
        width: 90px;
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
        border-radius: 5px;
        margin: 20px;
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
    .colorpicker {
        z-index: 9;
    }
    .colorSelector {
        display: inline-block;
        vertical-align: middle;
    }
    .inline-block {
        display: inline-block;
        vertical-align: middle;
        width: 35%;
    }
    .weidian a{
        text-align: center;
    }
    .weidian .noclass{
        display: block;
    }
    .weidian .selclass{
        display: none;
    }
    .weidian.selectitem .noclass{
        display: none;
    }
    .weidian.selectitem .selclass{
        display: block;
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
                            <p>提示：最多可设置5个图标。</p>
                            <p>提示：标签数量建议在2-5个，全部上传icon图片总长限制110px以内，高度限制110px以内。</p>
                        </div>
                        <div class="left-box">
                            <div class="WSY_homeleft">
                                <li class="WSY_homeleft_top">
                                    <p></p>
                                </li>
                                <li class="WSY_homeleft_middle">
                                    <!--模块开始-->
                                    <div class="main-content">
                                        <div class="footer hasname">
                                            <div class="footer-box">
                                                <div class="weidian" v-for="(item, index) in maindatetel" v-bind:index="index" @click="menuselect($event,index,-1)" v-bind:id="item.id" v-bind:diyid="item.id">
                                                    <a>
                                                        <img class="noclass" :src="item.noimgUrl">
                                                        <img class="selclass" :src="item.selimgUrl">
                                                        <p class="noclass" :style="'color:#'+item.nocolor" class="foot-text">{{item.name}}</p>
                                                        <p class="selclass" :style="'color:#'+item.selcolor" class="foot-text">{{item.name}}</p>
                                                    </a>
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
                                <form class="search" enctype="multipart/form-data"  method="post" action="/mshop/admin/index.php?m=bottom_label&a=icon_edit">
                                <input type=hidden name="id"  v-model="item.id" />
                                <p class="ctrl-title">菜单{{capital[index]}}</p>
                                <div class="ctrl-ipt-box">
                                    <label>自定义名称：</label>
                                    <input type="text" maxlength="5" name="name" class="ctrl-ipt" placeholder="限制5个字以内" v-model="item.name">
                                </div>
                                <div class="ctrl-ipt-box inline-block">
                                    <label>选中的颜色：</label>
                                    <div class="colorSelector" :diycla="'colsel'+index" :id="'sel'+item.id">
                                        <input type=hidden name="color_selected"  v-model="item.selcolor"/>
                                        <div :style="'background-color: #'+item.selcolor"></div>
                                    </div>
                                </div>
                                <div class="ctrl-ipt-box inline-block">
                                    <label style="width: 100px;">未选中的颜色：</label>
                                    <div class="colorSelector" :diycla="'colno'+index" :id="'no'+item.id">
                                        <input type=hidden name="color"  v-model="item.nocolor"/>
                                        <div :style="'background-color: #'+item.nocolor"></div>
                                    </div>
                                </div>
                                <div class="inline-block">
                                    <p class="desp1">选中的样式：</p>
                                    <div class="ctrl-main">
                                        <div class="frame_image" id="">
                                            <img :class="'sel'+index" :src="item.selimgUrl">
                                            <input type="file" id="image1" class="frame_image_select" name="icon_file_selected" value="" onchange="fileSelect_banner(this)">
                                            <input type=hidden name="icon_url_selected"  v-model="item.selimgUrl"/>
                                        </div>
                                        <p class="frame_image_tips">建议尺寸：110 * 110px。</p>
                                    </div>
                                </div>
                                <div class="inline-block">
                                    <p class="desp1">未选中的样式：</p>
                                    <div class="ctrl-main">
                                        <div class="frame_image" id="">
                                            <img :class="'no'+index" :src="item.noimgUrl">
                                            <input type="file" id="image11" class="frame_image_select" name="icon_file" value="" onchange="fileSelect_banner(this)">
                                            <input type=hidden name="icon_url"  v-model="item.noimgUrl"/>
                                        </div>
                                        <p class="frame_image_tips">建议尺寸：110 * 110px。</p>
                                    </div>
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
                    var selnoimgcla = $(currfile).prev("img").eq(0).attr("class");
                    var indselect = $(".weidian").index($('.selectitem'));
                    var afterimg = selnoimgcla.substr(selnoimgcla.length - 1, selnoimgcla.length);
                    var beforimg = selnoimgcla.substr(0, selnoimgcla.length - 1);
                    if (beforimg === 'sel') {
                        maindatetel[indselect].selimgUrl = dataURL;
                    } else {
                        $(".selectitem").find("img").attr("src", dataURL);
                        maindatetel[indselect].noimgUrl = dataURL;
                    }
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
    maindatetel=<?php echo $icon_list; ?>;console.log(maindatetel);
    /*maindatetel = [{
        id: "id0",
        name: '名字',
        selcolor: "000000",
        nocolor: "000000",
        selimgUrl: '/weixinpl/up/1/3243/Base/personalization/navigation/1503048698.png',
        noimgUrl: '/weixinpl/up/1/3243/Base/personalization/navigation/1503048751.png',
        orderby: 0,
        column: '旅游卡办卡页面',
        url: '/o2o/web/view/travel/apply_card.html?customer_id=3243'
    }, {
        id: "id1",
        name: '名字2',
        selcolor: "000000",
        nocolor: "000000",
        selimgUrl: '/weixinpl/up/1/3243/Base/personalization/navigation/1503048698.png',
        noimgUrl: '/weixinpl/up/1/3243/Base/personalization/navigation/1503048751.png',
        orderby: 1,
        column: '旅游卡办卡页面',
        url: '/o2o/web/view/travel/apply_card.html?customer_id=3243'
    }];*/
    var vm = new Vue({
        el: '#iconList',
        data: {
            length: 0,
            addflag: true,
            deleteflag: true,
            selectid: '',
            seltrue: false,
            capital: ['一', '二', '三', '四', '五'],
            selectright:'',
            maindatetel: maindatetel
        },
        methods: {
            saveAll: function() {
                layer.alert("保存成功",function(){
//                    history.back();
                    window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list&customer_id='+customer_id_en;
                });
            },
            addmenu: function() {
                var self = this;
                if(self.length == 1){

                }else if(self.length == 2){

                }else if(self.length == 3){

                }else if(self.length == 4){

                }else if(self.length == 5){

                }
                // 添加数据
                $.post("/mshop/admin/index.php?m=bottom_label&a=icon_add",{tmp_id:<?php echo $id; ?>,name:'',selcolor:"000000",nocolor:"000000",selimgUrl: '/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png',noimgUrl: '/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png',sort:++maxsort},function(result){
                   if(result.errcode!=0){
                    layer.alert(result.errmsg);
                   }else{
                    if(self.length==0){
                        $(".weidian").removeClass("selectitem");
                        setTimeout(function() {
                            self.menuselect("",0,"");
                        }, 20); 
                    }
                    maindatetel.push({
                        id: result.insertid,
                        name: '',
                        selcolor: "000000",
                        nocolor: "000000",
                        selimgUrl: '/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png',
                        noimgUrl: '/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png',
                        orderby: maxsort,
                        column_title: '',
                        column_id: '',
                        url: ''
                    });
                    self.maindatetel = maindatetel;
                    self.length=maindatetel.length;
                    self.setcolor();
                    self.crebottom();
                   }
                },'json');
            },
            menuselect: function(event, index,selectid) {
                var self = this;
                $(".weidian").removeClass("selectitem");
                $("#starttips").hide();
                if(selectid!=-1){
                    self.selectid = self.maindatetel[0].id;
                }else{
                    self.selectid = event.currentTarget.getAttribute("id");
                }
                self.setcolor();
                $("#" + self.selectid).addClass("selectitem");
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
                $(".weidian").removeClass("selectitem");
                setTimeout(function() {
                    if(self.length>0){
                        self.menuselect("",0,"");
                    }
                }, 20); 
                self.crebottom();
                $.post("/mshop/admin/index.php?m=bottom_label&a=icon_del",{id:self.selectid},function(result){
                   if(result.errcode!=0){
                    layer.alert(result.errmsg);
                   }
                },'json');
            },
            iptchange: function(selector_id, selector_title) {
                var self = this;
                var ind = $(".selectitem").index();
                self.maindatetel[ind].column_title = selector_title;
                self.maindatetel[ind].column_id = selector_id;
                console.log(selector_id)
                console.log(selector_title)
            },
            setcolor: function() {
                var selnoid = '';
                $('.colorSelector').click(function() {
                    selnoid = $(this).attr("id");
                });
                $('.colorSelector').ColorPicker({
                    color: '#0000ff',
                    onShow: function(colpkr) {
                        $(colpkr).fadeIn(500);
                        return false;
                    },
                    onHide: function(colpkr) {
                        $(colpkr).fadeOut(500);
                        return false;
                    },
                    onChange: function(hsb, hex, rgb, el) {
                        $("#" + selnoid).children('div').css('backgroundColor', '#' + hex);console.log(hex);
                        var indselect = $(".weidian").index($('.selectitem'));
                        var selnocolors = $("#" + selnoid).attr("diycla");
                        var aftercol = selnocolors.substr(selnocolors.length - 1, selnocolors.length);
                        var beforcol = selnocolors.substr(0, selnocolors.length - 1);
                        if (beforcol === 'colsel') {
                            self.maindatetel[indselect].selcolor = hex;
                        } else {
                            $(".selectitem").find(".foot-text").css("color", '#' + hex);
                            self.maindatetel[indselect].nocolor = hex;
                        }
                    }
                });
            },
            submitForm: function(){
                var self = this;
                var form=$(".right-box .type-ctrl-box form").eq(self.selectright);
                form.ajaxSubmit(function(response){
                    console.log(response);
                    response=JSON.parse(response);
                    layer.alert(response.errmsg);
                });
            },
            crebottom:function(){
                var self=this;
                console.log(self.length);
                if(self.length===0){
                    $("#starttips").find("p").text("您还没有添加菜单，请先添加一个菜单来进行编辑。");
                    $("#starttips").show();
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length<2){
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = false;
                    self.addflag = true;
                }else if(self.length<5){
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = true;
                }else if(self.length<6){
                    $("#starttips").hide();
                    $("#starttips").find("p").text("请先选择一个菜单来进行编辑。");
                    self.deleteflag = true;
                    self.addflag = false;
                }else{
                    self.deleteflag = true;
                    self.addflag = false;
                }
            } 
        }, 
        created: function () {
            var self=this;
            self.length=maindatetel.length;
            self.crebottom();
            setTimeout(function() {
                if(self.length>0){
                    self.menuselect("",0,"");
                }
            }, 20);  
        }
        
    })
    vm.setcolor();

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
        // console.log(selector_id);
        // console.log(selector_title);
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