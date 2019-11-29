var href = location.href.indexOf('#') >= 0 ? location.href.substr(0, location.href.indexOf('#')) : location.href
localStorage.CurButtonTabTypeIndex = "0";
var IndexSetPage = (parseInt(localStorage.LastModelPage) > 0 ? parseInt(localStorage.LastModelPage) : 1);
if (localStorage.CurWebSelCityName == undefined || localStorage.CurWebSelCityName == "undefined") { localStorage.CurWebSelCityName = "" }
var IndexSetLoad = true;
var IndexSetIsLoad = false;
var Page = (parseInt(localStorage.LastShopGoodsPage) > 0 ? parseInt(localStorage.LastShopGoodsPage) : 1);
//alert(IndexSetPage + "--" + Page);
var Size = 10;
var ShopGoodsLoad = false;
var LoadDataPageArr = [];
var LoadShopGoodsDataPageArr = [];
var FirstLoadData = true;
var FirstLoadGoodsData = true;
var IndexModelPageLower = true;
var IndexShopGoodsPageLower = true;

var IndexModelPageUp = true;
var IndexShopGoodsPageUp = true;
var Bmaplocation = false;
if (window.localStorage["IndexBodyHtml" + Sid] != undefined && localStorage.loadIndex == "0") {

    setTimeout(function () {
        LoadSwipt()
        $(".photo-block img").lazyload({
            effect: "fadeIn"
        });
        // alert(document.getElementById("shop_goods_list").length);

        CountDown()//计算倒计时
        isLoadShopGoodsTop = true;
        ShopGoodsLoad = true;
        isLoadShopGoods = true;
        if (isLoadShopGoodsTop) {
            LoadShopTop(isLoadShopGoods);
        } else
            if (isLoadShopGoods) {
                LoadShopGoods();
                ScrollRun();
            }
    }, 1000)
}
$(function () {

    try {
        new Swipe(document.getElementById("imageswipet"), { speed: 500, auto: 3000 }, 1);
    } catch (e) { }
    //    $('.one').unbind("click")
    //    $(".one").click(function () {


    //        if ($(this).children().eq(1).css("display") == "block") {
    //            $(this).children().eq(1).css({ display: "none" });
    //        }
    //        else {


    //            for (var i = 0; i < $(".one").length; i++)
    //                $(".one").eq(i).children().eq(1).css({ display: "none" });
    //            var left = ($(this).offset().left - 10);
    //            if ($(this).attr("data-i") == "2") left - +10;

    //            $(this).children().eq(1).css({ display: "block" });
    //        }
    //    });


    var srcList = [];

    srcList.push($("#shop_logo").attr("src"));

    $("#shop_logo").click(function () {
        WeixinJSBridge.invoke('imagePreview', {
            'current': $(this).attr("src"),
            'urls': srcList
        });
    })
    $('#localFile').on('click', function () {
        if (typeof FileReader == 'undefined') {
            alert('抱歉，您的浏览器不支持FileReader');
            document.getElementById('file').setAttribute('disabled', 'disabled');
        }
    });
    $('#localFile').on('change', function () {

        var Obj = $(this);
        var file = this.files[0], reader = new FileReader();
        reader.onload = function (e) {
            OpenMsg("正在上传...(最大1M)");
            $("#emptyTips").show();

            $.ajax({
                type: "POST",
                url: "/vshop/_marketingsave.html?sid=" + Sid + "&action=SaveImage",
                data: { imgdata: this.result },
                success: function (result) {
                    CloseMsg();
                    $("#emptyTips").hide();
                    var imgsrc = result;
                    $("#divuserheader").css("background-image", "url(" + result + ")");
                    // $("#shop_logo").attr('src', result);
                    $.ajax({
                        type: "POST",
                        url: href + "&action=SaveShopLogo",
                        data: { src: result },
                        success: function (result) {
                            if (result == "true") {
                                $("#divuserheader").css("background-image", "url(" + imgsrc + ")");
                                // $("#shop_logo").attr('src', imgsrc);
                            } else {
                                $("#divuserheader").css("background-image", "url()");
                                // $("#shop_logo").attr('src', '');
                                alert("上传失败,请重试!");
                            }
                        }
                    })
                }
            })
        }
        reader.onprogress = function (e) { }
        reader.onloadend = function (e) { }
        reader.readAsDataURL(file);
        //loadswipt();
    });


    $("#top_img").click(function () {
        window.scrollTo(0, 0);
    })

    $(".xyicon").each(function () {
        if ($(this).attr("img_src") != "") {

            var img = new Image(); //创建一个Image对象，实现图片的预下载
            img.src = $(this).attr("img_src");

            if (img.complete) { // 如果图片已经存在于浏览器缓存，直接调用回调函数

            }
            $(this).click(function () {

                $("#xyimage").attr("src", $(this).attr("img_src"));
                $("#divxyimage").show();

                $("#divxyimage").animate({ "top": ($(window).height() - $("#xyimage").height()) + "px" }, 300, function () {
                    $("#divxyimage").css({ "top": ($(window).height() - $("#xyimage").height()) + "px" });
                });

                setTimeout(function () {
                    $("#modelback").fadeIn();
                    //$("#divxyimage").css({ "top": ($(window).height() - $("#xyimage").height()) + "px" });
                }, 300)
            })
        }
    })
})



function fun_sign() {
    $.ajax({
        type: "get",
        url: "/vshop/manager.html?sid=" + Sid + "&action=sign",
        success: function (result) {
            if (result == "login") {
                location.href = "/vshop/login.html?sid=" + Sid;
            } else if (result == "true") {
                alert("恭喜您签到成功!");
            } else if (parseInt(result) > 0) {
                alert("恭喜您签到成功,积分+" + result + "分!");
            }
            else {
                alert("签到失败,请重试!");
            }
        }
    })
}



function closemodel() {
    $("#divxyimage").animate({ "top": "100%" }, 200);
    setTimeout(function () {
        $("#divxyimage").hide();
        $("#modelback").fadeOut();
    }, 100)
}
window.onscroll = function () {
    //显示置顶
    if (document.body.scrollTop > 0) {
        $("#top_img").show();
    }
    else {
        $("#top_img").hide();
    }
}
Load();
LoadIndexSet();
function Load() {
    if (logId != "0") {
        setInterval(function () {
            jsonp("/vshop/visit.html?id=" + logId);
        }, 5000)
    }
    jsonp("/vshop/async.html?sid=" + Store_sid + "&action=baison");
}
function robCoupon(cid) {

    $.ajax({
        type: "GET",
        url: "/vshop/couponlist.html?sid=" + Sid + "&action=get&cid=" + cid,
        success: function (result) {
            result = result.replace("('", "").replace("')", "");
            alert(result);
        }
    });

}
function jsonp(url) {
    $.ajax({
        type: "GET",
        url: url,
        success: function (result) { }
    });
}
//LoadCoupon();
var acti = 0;
function LoadCoupon() {
    $.ajax({
        type: "POST",
        url: href + "&action=GetCouponList",
        success: function (result) {

            var JsonResult = eval("(" + result + ")")
            var Json = JsonResult.coupon;
            var JsonAct = JsonResult.activity;
            var Str = '';
            Str += '<div class="app-field clearfix"> <center> <div id="image" name="swipeimagecoupon" class="swipe" style="visibility: visible; width: 100%;text-align:left;"><div class="" id="imgs" style="-webkit-transform: translate3d(0px, 0px, 0px); transition: -webkit-transform 500ms ease-out;-webkit-transition: -webkit-transform 500ms ease-out; list-style: none; width: 100%;">';

            for (var i = 0; i < Json.length; i = i + 3) {


                try {
                    Str += '<div class="swipe-item ui-swipeslide-slide"  style="vertical-align: top;width:100%;text-align:left;">' +
                            '<div class="divimg tpl-11-11" style="width: 100%; border: 0px solid #fff; border-radius: 5px;text-align:left;">' +
                            '<ul class="tpl-11-11-coupon clearfix" style="width:100%;text-align:left;">' +
                            WriteCouponHtml(Json, i) +

                            (Json[i + 1] === undefined ? WriteActivityHtml(JsonAct, acti) : WriteCouponHtml(Json, i + 1))
                            +
                             (Json[i + 2] === undefined ? WriteActivityHtml(JsonAct, acti) : WriteCouponHtml(Json, i + 2))
                            +
                            '</ul>' +
                            '</div>' +
                            '<div class="bottom">' +
                            '<div class="title">' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                } catch (e) {
                    Str += '';
                }
            }
            for (var i = acti; i < JsonAct.length; i = i + 3) {
                try {
                    Str += '<div class="swipe-item ui-swipeslide-slide"  style="vertical-align: top;width:100%;text-align:left;">' +
                            '<div class="divimg tpl-11-11" style="width: 100%; border: 0px solid #fff; border-radius: 5px;text-align:left;">' +
                            '<ul class="tpl-11-11-coupon clearfix" style="width:100%;text-align:left;">' +
                            WriteActivityHtml(JsonAct, i) +
                            WriteActivityHtml(JsonAct, i + 1) +
                            WriteActivityHtml(JsonAct, i + 2) +
                            '</ul>' +
                            '</div>' +
                            '<div class="bottom">' +
                            '<div class="title">' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                } catch (e) {
                    Str += '';
                }
            }
            Str += '</div></div></center></div>';

            $("#spancoupon").append(Str);
            LoadSwipt()
            var swipeimages = document.getElementsByName("swipeimagecoupon");
            for (var i = 0; i < swipeimages.length; i++) {
                new Swipe(swipeimages[i], { speed: 500 }, 1);
            }

        }
    });

}

function WriteCouponHtml(Json, i) {

    try {
        return '<li><a onclick="robCoupon(' + Json[i].c_id + ')">' +
               '<div class="tpl-11-11-coupon-meta">' +
               '<div class="tpl-11-11-coupon-meta-price">' +
                (Json[i].c_type == "0" ? "<span>￥</span>" + Json[i].c_type_val : Json[i].c_type_val + "折") +
               '</div>' +
               '<div class="tpl-11-11-coupon-meta-desc">' +
                (parseFloat(Json[i].c_money) > 0 ? "满" + Json[i].c_money + "可用" : (Json[i].c_goods != "" ? "(指定商品可用)" : "(全店通用)")) +
               '</div>' +
               '</div>' +
               '<div class="tpl-11-11-coupon-get">马上领取</div>' +
               '</a>' +
               '</li>';

    } catch (e) {
        return "";
    }
}
function WriteActivityHtml(Json, i) {
    acti = i + 1;
    try {
        return '<li><a href="//' + Json[0].weixindomain + '/getwebaward?mid=' + Json[i].ai_mid + '&aid=' + Json[i].ai_id + '&oper=' + Json[i].ai_type + '&sid=' + Sid + '&OpenId=' + Json[0].openid + '&mi_id=' + Json[0].mi_id + '&loginurl=' + Json[0].loginurl + '">' +
               '<div class="tpl-11-11-coupon-meta">' +
               '<div class="tpl-11-11-coupon-meta-price">' +
                (Json[i].ai_title) +
               '</div>' +
               '<div class="tpl-11-11-coupon-meta-desc">' +

               '</div>' +
               '</div>' +
               '<div class="tpl-11-11-coupon-get">马上参与</div>' +
               '</a>' +
               '</li>';

    } catch (e) {
        return "";
    }
}

function LoadIndexSet() {


    //    var ArrIsLoad = "0";
    //    try {
    //        ArrIsLoad = LoadDataPageArr[IndexSetPage] == undefined ? "0" : "1";
    //    } catch (e) { }
    //  alert(IndexSetPage);
    if ($("#app-field-model-page-" + IndexSetPage).length <= 0) {
        //        if (IndexSetLoad == false) {
        //            return;
        //        }
        if (IndexSetIsLoad) {
            return;
        }

        IndexSetIsLoad = true;
        $("#emptyTips").show();
        var CityName = localStorage.CurWebSelCityName == undefined || localStorage.CurWebSelCityName == "undefined" || localStorage.CurWebSelCityName == null || localStorage.CurWebSelCityName == "null" ? "" : localStorage.CurWebSelCityName;

        var RequestUrl = href + "&action=GetCustomIndex&Page=" + IndexSetPage + "&key=" + CityName;
        var CachData = "";

        try {
            CachData = window.localStorage[RequestUrl].toString();
        } catch (e) { }
        if (CachData.length > 50) {
            ModelDataParse(CachData);
        } else {
            $.ajax({
                type: "POST",
                url: RequestUrl,
                success: function (result) {
                    localCookie(RequestUrl, result);
                    ModelDataParse(result);
                }
            })
        }
    } else {
        //alert("已加载");
    }
}


function ModelDataParse(result) {
    IndexSetIsLoad = false;

    var json = eval(result);
    var isLoadShopGoods = false;
    var isLoadShopGoodsTop = false;
    if (json.length <= 0) {
        IndexSetLoad = false;
    } else {
        localStorage.LastModelPage = IndexSetPage;
    }

    LoadDataPageArr[IndexSetPage] = "1";


    var BodyId = "divbody";

    if (LoadDataPageArr[IndexSetPage + 1] != undefined) {
        BodyId = "app-field-model-page-" + (IndexSetPage + 1);
    }

    //IndexSetPage += 1;
    //#region 处理JSON数据，返回对应的HTML
    // $("#" + BodyId).append("<div id='app-field-model-page-" + IndexSetPage + "' style='width:100%'>");
    var Str = "";
    try {
        for (var i = 0; i < json.length; i++) {


            switch (json[i].ix_type) {
                case "customcolumn":
                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;
                    var jsonData = eval("(" + ix_value + ")");
                    Str += '<div class="app-field clearfix app-preview-anmin" style=""><div style="width:100%;">';
                    for (var j = 0; j < jsonData.length; j++) {
                        Str += '<a href="' + FormatSid(jsonData[j].href) + '" ><img src="' + jsonData[j].img + '" style="width:' + jsonData[j].width + '%;float:left;"></a>';
                    }
                    Str += '</div><div style="clear:both;"></div></div>';

                    break;
                case "logosearch":
                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;
                    var jsonData = eval("(" + ix_value + ")");
                    Str += '<div class="app-field clearfix app-preview-anmin" style="background:' + jsonData[0].bgcolor + '"><table style="width:100%;"><tr><td align="left" valign="middle" style="width:20%">' +
                              '<img src="' + jsonData[0].img + '" style="width:100%"/>' +
                              '</td>' +
                              '<td align="center" valign="middle" onclick="openFrameProvince()" style="width:25%;">' +
                              '<span id="span_city_name" onclick="openFrameProvince()" ></span>' +
                              '<div onclick="openFrameProvince()" >切换&nbsp;<img src="/moban/custom/img/arrow_bottom.png" style="width:9px;margin-top:1px;"/>&nbsp;</div>' +
                              '</td>' +
                              '<td align="center" valign="middle">' +
                              '<form action="clist.html"><div style="width:98%;border:0px solid #C0C0C0;">' +
                              '<input type="hidden" name="sid" value="' + Sid + '"/><input type="search" class="custom-search-input" placeholder="请输入商品关键字" name="key" id="key" value="" style="width:100%;height:32px;border:0px solid #C0C0C0;border-radius:3px;">' +
                              '<img src="//kdt-static.qiniudn.com/v2/image/wap/search_icon.png" style="width:16px; margin-top:8px;right:15px; position:absolute;z-index:500"/>' +
                              '</div></form>' +
                              '</td>' +
                              '</tr></table></div>';
                    Bmaplocation = true;
                    break;
                case "topmenu":

                    Str += '<div class="app-field clearfix clearfix_list b_white app-preview-anmin"><div style="height: 10px;"></div>';
                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;

                    var jsonData = eval("(" + ix_value + ")");

                    for (var j = 0; j < jsonData.length; j++) {
                        Str += '<div style="width:25%;float:left;text-align:center;"><a href="' + FormatSid(jsonData[j].href) + '"><img src="' + jsonData[j].img + '" style="width: 42%" /><div></div><div>' + jsonData[j].title + '</div></a></div>';
                    }
                    Str += '<div style="clear:both;"></div><div style="height: 10px;"></div></div>';
                    break;
                case "slide":
                    Str += '';
                    break;
                case "html":
                    var text_html = json[i].ix_value;
                    var StoreReg = new RegExp("{销售门店}", "g");
                    text_html = text_html.replace(StoreReg, Store_Name);
                    StoreReg = new RegExp("{门店电话}", "g");
                    text_html = text_html.replace(StoreReg, Store_Phone);
                    StoreReg = new RegExp("{门店地址}", "g");
                    text_html = text_html.replace(StoreReg, Store_Address);
                    Str += '<div class="app-field clearfix clearfix_list div_html app-preview-anmin"><div class="control-group"><div class="custom-richtext" style="">' + text_html + '</div><div class="component-border"></div></div></div>';
                    break;
                case "goodslist":
                    Str += '<div class="app-field clearfix clearfix_list  b_white app-preview-anmin"><div class="control-group"><ul class="sc-goods-list clearfix size-1 card pic">';
                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;

                    var jsonData = eval("(" + ix_value + ")");

                    for (var j = 0; j < jsonData.length; j++) {
                        var imgsrc = "/moban/custom/img/noimgwhite.jpg";
                        if (j < 2) {
                            imgsrc = jsonData[j].img.toString().split(',')[0];
                        }
                        Str += '<li class="goods-card small-pic card"><a href="/vshop/detail.html?sid=' + Sid + '&gid=0&iid=' + jsonData[j].gid + '" class="link js-goods clearfix">' +
                                   '<div class="photo-block">' + (parseFloat(jsonData[j].sale) < 10 && jsonData[j].dc_id != "" ? '<div class="mod_corner mod_corner_' + ss_discount + '">' + jsonData[j].sale + '<sup>折</sup></div>' : '') +
                                   '<img class="goods-photo js-goods-lazy" name="shopgoodslist' + IndexSetPage + '" onerror="this.src="' + jsonData[j].img.toString().split(',')[0] + '"" src="' + imgsrc + '" data-original="' + jsonData[j].img.toString().split(',')[0] + '">' +
                                   '</div>' +
                                   '<div class="info clearfix info-title">' +
                                   (showsuppliername != "false" ? '<div style="color:#000;">' + (jsonData[j].companyname == "" ? "&nbsp;" : jsonData[j].companyname) + '</div>' : '') + '<p class="goods-title goods-title_' + jsonData[j].gid + ' goods-list-title" data-id="' + jsonData[j].gid + '">' + jsonData[j].title + '</p>' +
                                   '<p class="goods-price goods-price-icon"><em>￥' + jsonData[j].saleprice + '</em>' + (parseFloat(jsonData[j].marketprice) > parseFloat(jsonData[j].saleprice) ? '<del style="color:#c5c5c5;font-size:11px;">￥' + jsonData[j].marketprice + '</del>' : '<del style="color:#c5c5c5;font-size:11px;">&nbsp;</del>') + '</p>' +
                                   '<p class="goods-price-taobao"></p>' +
                                   '</div><div></div><table style="color:#c5c5c5;font-size:10px;width:100%; margin-top:-10px;"><tr><td align="right">售' + jsonData[j].vg_sale + '笔&nbsp;</td></tr></table></a></li>';
                        //'</div><div class="goods-buy btn1"><table style="width:100%"><tr><td align="right"><table><tr><td valign="middle"><img src="/moban/custom/img/icon_sc.png" style="width:15px;" /></td><td valign="middle">&nbsp;' + jsonData[j].gi_collect_num + '<div style="height:3px;"></div></td></tr></table></td></tr></table></div></a></li>';

                    }
                    Str += '</ul><div class="component-border"></div></div></div>';
                    break;
                case "ad":
                    Str += '<div class="app-field clearfix clearfix_list  b_white app-preview-anmin" style="width:100%"> <center> <div name="swipeimage" class="swipe" style="visibility: visible; width: 100%;"><div class="" id="imgs" style="-webkit-transform: translate3d(0px, 0px, 0px); transition: -webkit-transform 500ms ease-out;-webkit-transition: -webkit-transform 500ms ease-out; list-style: none; width: 100%;">';
                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;
                    var jsonData = eval("(" + ix_value + ")");
                    for (var j = 0; j < jsonData.length; j++) {
                        Str += '<div class="swipe-item ui-swipeslide-slide"  style="vertical-align: top;width:100%;">' +
                                   '<center>' +
                                   '<div class="divimg" style="width: 100%; border: 0px solid #fff; border-radius: 5px;">' +
                                   '<a href="' + FormatSid(jsonData[j].href) + '"><img class="img" src="' + jsonData[j].img + '" ' +
                                   'style="width:100%;"></a></div>' +
                                   '</center>' +
                                   '</div>';
                    }
                    Str += '</div></div></center></div>';
                    break;
                case "class":

                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;
                    var jsonData = eval("(" + ix_value + ")");
                    var more_icon = "/moban/xfc/img/xfc_index/more_r.jpg";
                    try {
                        more_icon = jsonData[0].class_more_icon;
                    }
                    catch (e) { }
                    Str += '<div class="app-field clearfix clearfix_list  b_white app-preview-anmin"><div style="height:10px"></div><div style="width:100%;background-color:#fff"><div style="height:5px"></div><div style="width:100%;border-bottom:1px solid #f1f1f1"><a href="/vshop/classlist.html?sid=' + Sid + '&cid=' + jsonData[0].cids + '"><table style="width:100%"><tr><td align="right" valign="middle" style="width:5px"><img src="/moban/xfc/img/xfc_index/class_ico.jpg" style="width:18px;display:none;"></td><td align="left" valign="middle">&nbsp;' + jsonData[0].title + '</td><td align="right" valign="middle">更多</td><td align="right" valign="middle" style="width:22px"><img src="' + more_icon + '" style="width:15px"></td><td style="width:8px"></td></tr></table></a><div style="height:5px"></div></div>' +
                            '<div style="width:100%"><table style="width:100%"><tr><td valign="top" align="center" style="width:45%;vertical-align:top"><div style="width:95%;background-color:' + jsonData[0].class_bg_color + ';color:#fff"><div style="height:5px"></div>';
                    var JsonClass = eval(jsonData[0].class_json)
                    for (var j = 0; j < JsonClass.length; j++) {
                        Str += '<a href="/vshop/classlist.html?sid=' + Sid + '&cid=' + JsonClass[j].gc_id + '" style="color:#fff;"><div style="min-width:33%;float:left;' + (JsonClass[j].gc_name.length > 3 ? "font-size:11px;" : "") + '">' + JsonClass[j].gc_name + '</div></a>';
                    }

                    Str += '<div style="clear:both"></div><div style="height:5px"></div></div><div style="height:5px"></div><div style="width:95%;text-align:left">';
                    JsonClass = eval(jsonData[0].brand_ids)

                    for (var j = 0; j < JsonClass.length; j++) {
                        Str += '<a href="/vshop/brand_index.html?sid=' + Sid + '&bid=' + JsonClass[j].gb_id + '"><img src="' + JsonClass[j].gb_logo + '" style="width:31%;float:left;border:1px solid #c5c5c5;margin-left:1px;margin-top:1px"></a>';
                    }

                    Str += '</div></td><td valign="top" align="center"><div style="width:100%">';

                    JsonClass = eval(jsonData[0].ad_img)
                    for (var j = 0; j < JsonClass.length; j++) {
                        Str += '<img onclick="location.href=\'' + FormatSid(JsonClass[j].href) + '\'" src="' + JsonClass[j].img + '" style="width:50%;float:left;border:0px solid #fff;"> ';
                    }
                    Str += '</div></td></tr></table></div><div style="clear:both"></div></div></div>';


                    break;
                case "customad":
                    Str += ' <div class="app-field clearfix clearfix_list  b_white app-preview-anmin"><div style="width: 100%; background-color: #fff;">';
                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;
                    var jsonData = eval("(" + ix_value + ")");
                    if (jsonData[0].title != "") {
                        Str += ' <div style="width: 100%; border-bottom: 1px solid #f1f1f1;">' +
                                        '<table style="width: 100%;">' +
                                            '<tr><td align="left" valign="middle">&nbsp;&nbsp;' + jsonData[0].title + '</td><td align="right" valign="middle"></td><td align="right" valign="middle" style="width: 22px;"></td><td style="width: 8px;"></td></tr>' +
                                        '</table>' +
                                        '<div style="height: 5px;"></div></div>';
                    }
                    Str += '<div style="width: 100%">';

                    //判断排版方式
                    switch (jsonData[0].ad_type) {
                        case "21":
                            var JsonImg = eval(jsonData[0].ad_img);
                            for (var j = 0; j < JsonImg.length - 1; j++) {
                                Str += '<img onclick="location.href=\'' + FormatSid(JsonImg[j].href) + '\'" src="' + JsonImg[j].img + '" style="width: 50%; float: left;" />';
                            }
                            Str += '<img onclick="location.href=\'' + FormatSid(JsonImg[2].href) + '\'" src="' + JsonImg[2].img + '" style="width: 100%; float: left;" />';

                            break;
                        case "12":
                            var JsonImg = eval(jsonData[0].ad_img);
                            Str += '<img onclick="location.href=\'' + FormatSid(JsonImg[0].href) + '\'" src="' + JsonImg[0].img + '" style="width: 100%; float: left;" />';

                            for (var j = 1; j < JsonImg.length; j++) {
                                Str += '<img onclick="location.href=\'' + FormatSid(JsonImg[j].href) + '\'" src="' + JsonImg[j].img + '" style="width: 50%; float: left;" />';
                            }
                            break;
                        case "4":
                        case "213":
                            var JsonImg = eval(jsonData[0].ad_img);
                            for (var j = 0; j < JsonImg.length; j++) {
                                Str += '<img onclick="location.href=\'' + FormatSid(JsonImg[j].href) + '\'" src="' + JsonImg[j].img + '" style="width: 50%; float: left;" />';
                            }
                            break;
                        case "123":
                            var JsonImg = eval(jsonData[0].ad_img);
                            Str += '<div style="width:50%;float:left;"><a href="' + FormatSid(JsonImg[0].href) + '"><img src="' + JsonImg[0].img + '" style="width: 100%; float: left;" /></a>' +
                                    '<a href="' + FormatSid(JsonImg[1].href) + '"><img src="' + JsonImg[1].img + '" style="width: 100%; float: left;" /></a></div><div style="width:50%;float:left;"><a href="' + FormatSid(JsonImg[2].href) + '"><img src="' + JsonImg[2].img + '" style="width: 100%;" /></a></div>';
                            break;
                    }
                    Str += ' </div><div style="clear: both;"></div></div></div>';
                    break;
                case "nav":
                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;
                    var jsonData = eval("(" + ix_value + ")");
                    if (jsonData.length <= 4) {
                        Str += '<div class="app-field clearfix app-preview-anmin"><center><div class="control-group" style="width:98%"><ul class="custom-nav-4 clearfix" style="padding:0px;">';
                        for (var j = 0; j < jsonData.length; j++) {
                            var show_border = "1";
                            try {
                                show_border = jsonData[j].show_border;
                            } catch (e) { }
                            Str += '<li style="width:' + (parseFloat(100 / jsonData.length).toFixed(2)) + '%"><a href="' + FormatSid(jsonData[j].href) + '" style="padding:2px;width:95%;"><img   src="' + jsonData[j].img + '" style="width:100%;' + (show_border != "0" ? "border:1px solid #f4f2ef;" : "") + '"></a></li>';
                        }
                        Str += '</ul></div></center></div>';
                    } else {
                        Str += '<div class="app-field clearfix"><div id="imageswipet" class="swipe" name="swipeimage" style="visibility: visible;">' +
                                       '<div style="-webkit-transform: translate3d(0px, 0px, 0px); transition: -webkit-transform 500ms ease-out; -webkit-transition: -webkit-transform 500ms ease-out; list-style: none;">';
                        var num = 0;
                        for (var j = 0; j < jsonData.length; j++) {
                            if (num == 0) {
                                Str += '<div class="swipe-item ui-swipeslide-slide" style="display: table-cell; vertical-align: top;width: 100%;"><center><div class="control-group" style="width:98%"><ul class="custom-nav-4 clearfix" style="padding:0px;">';
                            }
                            num++;
                            var show_border = jsonData[j].show_border;
                            Str += '<li style="width:25%"><a href="' + FormatSid(jsonData[j].href) + '" style="padding:2px;width:95%;"><img src="' + jsonData[j].img + '" style="width:100%;' + (show_border != "0" ? "border:1px solid #f4f2ef;" : "") + '"></a></li>';
                            if (num == 4) {
                                num = 0;
                                Str += '</ul></div></center></div>';
                            }
                        }
                        Str += '</div></div></div>';
                    }

                    break;
                case "goodssearch":
                    Str += '<div class="app-field clearfix clearfix_list  b_white app-preview-anmin"><div class="custom-search">' +
                               ' <form action="clist.html">' +
                               '<input type="search" class="custom-search-input" placeholder="商品搜索：请输入商品关键字" name="key" id="key" value="">' +
                               '<input type="hidden" name="sid" value="' + Sid + '"/>' +
                               '<button type="submit" class="custom-search-button">搜索</button>' +
                               '</form>' +
                               '</div></div>';
                    break;
                case "activity":

                    var ix_value = json[i].ix_value == "" ? "[]" : json[i].ix_value;
                    var jsonData = eval("(" + ix_value + ")");
                    var JsonAd = eval(jsonData[0].ad_img);

                    Str += ' <div class="app-field clearfix clearfix_list  b_white app-preview-anmin">' +
                                    '<a href="/vshop/brand_index.html?sid=' + Sid + '&bid=' + jsonData[0].brand_id + '"><div style="width: 100%; background-color: #fff; border-bottom: 1px solid #f1f1f1;">' +
                                        '<div style="width: 30%; float: left;">' +
                                            '<img src="' + JsonAd[0].img + '"  style="width: 100%" />' +
                                            '<div style="width: 30%; text-align: center; position: absolute; margin-top: -4px;font-size: 10px;">' +
                                            (jsonData[0].dc_remark == "" ? "&nbsp;" : jsonData[0].dc_remark) +
                                            '<div class="countDown" start="' + jsonData[0].dc_startdate + '" end="' + jsonData[0].dc_enddate + '" timer="' + (parseInt(jsonData[0].timer)) + '">loading...' +
                                            '</div>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div style="width: 70%; float: left;">' +
                                            '<img src="' + JsonAd[1].img + '" style="width: 100%;" />' +
                                        '</div>' +
                                        '<div style="clear: both">' +
                                        '</div>' +
                                    '</div></a>' +
                                '</div>';

                    break;
                case "hr":
                    Str += '<div class="app-field clearfix clearfix_list  b_white"><div class="control-group"><div class="custom-line-wrap"><hr class="custom-line"></div></div></div>';
                    break;
                case "nbsp":
                    var div_hei;
                    var div_html = '';
                    if (parseInt(json[i].ix_value) > 0) {
                        div_hei = json[i].ix_value;
                    } else {
                        try {
                            var Json = eval(json[i].ix_value);
                            div_html = 'background-color:' + Json[0].bg_color;
                            div_hei = Json[0].height;
                        } catch (e) { }
                    }
                    Str += '<div class="app-field clearfix clearfix_list" ><div class="control-group"><div class="custom-white text-center" style="height: ' + div_hei + 'px;' + div_html + '"></div><div class="component-border"></div></div></div>';
                    break;
                case "shopgoods":
                    Str += '<div  id="shop_goods_list" class="app-field clearfix clearfix_list  b_white"><div class="control-group"><ul id="div_shopgoodslist" class="sc-goods-list clearfix size-1 card pic"></ul><div id="div_shopgoodslistfooter"></div><div class="component-border"></div></div></div>';
                    isLoadShopGoods = true;
                    break;
                case "shopgoodstop":
                    Str += '<div class="app-field clearfix clearfix_list  b_white"><div class="control-group"><ul id="div_shopgoodstoplist" class="sc-goods-list clearfix size-1 card pic"></ul><div class="component-border"></div></div></div>';
                    isLoadShopGoodsTop = true;
                    break;

            }


            // $("#" + BodyId).append(Str);
        }
    } catch (e) { }
    if (BodyId == "divbody") {
        $("#" + BodyId).append("<div id='app-field-model-page-" + IndexSetPage + "' style='width:100%'>" + Str + "</div>");
    } else {

        $("#" + BodyId).before("<div id='app-field-model-page-" + IndexSetPage + "' style='width:100%'>" + Str + "</div>");

    }

    if (FirstLoadData == true && IndexSetPage != 1) {
        setTimeout(function () {
            if (document.getElementById("div_header")) {
                var h = document.getElementById("div_header").offsetTop + document.getElementById("div_header").offsetHeight + 50;
                if (parseInt(localStorage.LastscrollTop) < h)
                    window.scrollTo(0, h);
            }
        }, 500)

    }
    FirstLoadData = false;

    //    setTimeout(function () {
    //        LoadDataPageArr[IndexSetPage] = $("#app-field-model-page-" + IndexSetPage).offset().top + document.getElementById("app-field-model-page-" + IndexSetPage).offsetHeight;
    //    }, 800)

    //#endregion
    $("#emptyTips").hide();

    LoadSwipt()
    $("img[name=shopgoodslist" + IndexSetPage + "]").lazyload({
        effect: "fadeIn"
    });
    // alert(document.getElementById("shop_goods_list").length);
    ScrollRun(); //滚动条事件
    CountDown()//计算倒计时
    if (isLoadShopGoodsTop) {
        LoadShopTop(isLoadShopGoods);
    } else
        if (isLoadShopGoods) {
            LoadShopGoods();
            ScrollRun();
        }
    $(".div_html a").each(function () {
        if ($(this).attr("href").toString().indexOf("sid=") < 0) {
            $(this).attr("href", FormatSid($(this).attr("href")));
        }
    })


    window.localStorage["IndexBodyHtml" + Sid] = document.getElementById("divbody").innerHTML;
    if (Bmaplocation && (localStorage.MeLng == undefined || localStorage.MeLng.length < 10) && autogetposition == "1") {
        getMyLocation();
    }
    $("#span_city_name").html(localStorage.CurWebSelCityName);
    GetGoodsTitle()
}


function getMyLocation() {
    // jQuery.getScript("http://api.map.baidu.com/api?v=2.0&ak=vI53Lfnts9X9nYMg76eCgSEm", function () {
    var geolocation = new BMap.Geolocation();
    geolocation.getCurrentPosition(function (r) {
        if (this.getStatus() == BMAP_STATUS_SUCCESS) {
            var mk = new BMap.Marker(r.point);

            localStorage.MeLng = r.point.lng;
            localStorage.MeLat = r.point.lat;
            var geoc = new BMap.Geocoder();
            var pt = r.point;
            geoc.getLocation(pt, function (rs) {
                var addComp = rs.addressComponents;
                if (localStorage.CurWebSelCityName == "" || localStorage.CurWebSelCityName == undefined) {
                    localStorage.CurWebSelCityName = addComp.district;
                    if (localStorage.CurWebSelCityName == null || localStorage.CurWebSelCityName.toString() == "null") {
                        localStorage.CurWebSelCityName = "";
                    } else {
                        location.reload();
                        $("#span_city_name").html(localStorage.CurWebSelCityName);
                    }

                }
                //alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
            });
        }
        else {
        }
    }, { enableHighAccuracy: true })


    // });

}

function localCookie(key, value) {
    window.localStorage[key] = value;
}
//#region 倒计时
var CountDownTimer;
function CountDown() {
    var len = $(".countDown").length;
    $(".countDown").each(function () {
        len--;
        timerCountDown(parseInt($(this).attr("timer")), function (day, hour, minute, second, obj) {
            $(obj).attr("timer", parseInt($(obj).attr("timer")) - 1)
            $(obj).html("" + (day > 0 ? day + "天" : "") + hour + "时" + minute + "分" + second + "秒");
        }, this);

        if (len == 0) {
            CountDownTimer = setTimeout(function () {
                clearTimeout(CountDownTimer);
                CountDown();
            }, 1000)
        }
    })

}

var CountDownNum = 1;
function timerCountDown(intDiff, cb, obj) {

    var day = 0,
		hour = 0,
		minute = 0,
		second = 0; //时间默认值		
    if (intDiff > 0) {
        day = Math.floor(intDiff / (60 * 60 * 24));
        hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
        minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
        second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
    }
    if (minute <= 9) minute = '0' + minute;
    if (second <= 9) second = '0' + second;
    cb(day, hour, minute, second, obj);

    intDiff--;

}
//#endregion

//#region 商品推荐列表
function LoadShopTop(loadGoodsList) {
    $("#emptyTips").show();
    var RequestUrl = href + "&action=top&sort=gi_sort&key=" + localStorage.CurWebSelCityName;
    var CachData = "";
    try {
        CachData = window.localStorage[RequestUrl].toString();
    } catch (e) { }
    if (CachData.length > 50) {
        ShopTopGoodsParse(CachData, loadGoodsList);
    } else {
        $.ajax({
            type: "POST",
            url: RequestUrl,
            success: function (result) {
                localCookie(RequestUrl, result);
                ShopTopGoodsParse(result, loadGoodsList);
            }
        })
    }
}

function ShopTopGoodsParse(result, loadGoodsList) {
    $("#emptyTips").hide();
    var Str = '';
    var Json = eval("(" + result + ")");
    if (Json.length > 0) {
        ShopGoodsLoad = true;
        for (var i = 0; i < Json.length; i++) {
            var imgsrc = "/moban/custom/img/noimgwhite.jpg";
            if (i < 2) {
                imgsrc = Json[i].gi_imgs;
            }
            Str += '<li class="goods-card small-pic card"><a href="/vshop/detail.html?sid=' + Sid + '&gid=' + Json[i].vg_id + '&iid=' + Json[i].gi_id + '" class="link js-goods clearfix">' +
                                   '<div class="photo-block">' + (parseFloat(Json[i].sale) < 10 && Json[i].dc_Id != "" ? '<div class="mod_corner mod_corner_' + ss_discount + '">' + Json[i].sale + '<sup>折</sup></div>' : '') +
                                   '<img class="goods-photo js-goods-lazy" name="goodslist' + Page + '" src="' + imgsrc + '" data-original="' + Json[i].gi_imgs + '" >' +
                                   '</div>' +
                                   '<div class="info clearfix info-title">' +
                                   (showsuppliername != "false" ? '<div style="color:#000;">' + (Json[i].companyname == "" ? "&nbsp;" : Json[i].companyname) + '</div>' : '') + '<p class="goods-title goods-title_' + Json[i].gi_id + ' goods-list-title" data-id="' + Json[i].gi_id + '">' + Json[i].gi_title + '</p>' +
                                   '<p class="goods-price goods-price-icon"><em>￥' + Json[i].gb_salesprice + ' </em>' + (parseFloat(Json[i].gb_marketprice) > parseFloat(Json[i].gb_salesprice) ? '<del style="color:#c5c5c5;font-size:11px;">￥' + Json[i].gb_marketprice + '</del>' : '') + '</p>' +
                                   '<p class="goods-price-taobao"></p>' +
                                   '</div><div></div><table style="color:#c5c5c5;font-size:10px;width:100%; margin-top:-10px;"><tr><td align="right">售' + Json[i].vg_sale + '笔&nbsp;</td></tr></table></a></li>';




            // '</div><div class="goods-buy btn1"><table style="width:100%"><tr><td align="right"><table><tr><td valign="middle"><img src="/moban/custom/img/icon_sc.png" style="width:15px;" /></td><td valign="middle">&nbsp;' + Json[i].gi_collect_num + '<div style="height:3px;"></div></td></tr></table></td></tr></table></div><div class="mod_rec mod_rec_' + ss_recommend + '" style="' + ss_recommendpathcss + '"></div></a></li>';
        }
        $("#div_shopgoodstoplist").append(Str);
        $("img[name=goodslist" + Page + "]").lazyload({
            effect: "fadeIn"
        });
    }
    if (loadGoodsList) {

        LoadShopGoods();
        ScrollRun();
    }
    GetGoodsTitle()
}
//#endregion

function LoadShopGoods() {

    if ($("#goods-card-li-0-" + Page).length <= 0) {
        $("#emptyTips").show();
        var RequestUrl = href + "&action=host&page=" + Page + "&num=" + Size + "&sort=gi_sort" + "&key=" + localStorage.CurWebSelCityName;
        var CachData = "";
        try {
            CachData = window.localStorage[RequestUrl].toString();
        } catch (e) { }
        if (CachData.length > 50) {
            ShopGoodsParse(CachData);
        } else {
            $.ajax({
                type: "POST",
                url: RequestUrl,
                success: function (result) {
                    localCookie(RequestUrl, result);
                    ShopGoodsParse(result);
                }
            })
        }
    }
}

function ShopGoodsParse(result) {
    $("#emptyTips").hide();
    ShopGoodsLoad = false;
    var Str = '';
    var Json = eval("(" + result + ")");
    if (Json.length > 0) {

        ShopGoodsLoad = true;

        localStorage.LastShopGoodsPage = Page;


        LoadShopGoodsDataPageArr[Page] = "1";

        var divId = "div_shopgoodslist";
        if (LoadShopGoodsDataPageArr[Page + 1] != undefined) {
            divId = "goods-card-li-0-" + (Page + 1);
        }
        //  alert(divId);
        for (var i = 0; i < Json.length; i++) {
            var imgsrc = "/moban/custom/img/noimgwhite.jpg";
            if (i < 2) {
                imgsrc = Json[i].gi_imgs;
            }
            Str += '<li id="goods-card-li-' + i + "-" + Page + '" class="goods-card small-pic card"><a href="/vshop/detail.html?sid=' + Sid + '&gid=' + Json[i].vg_id + '&iid=' + Json[i].gi_id + '" class="link js-goods clearfix">' +
                                   '<div   class="photo-block">' + (parseFloat(Json[i].sale) < 10 && Json[i].dc_Id != "" ? '<div class="mod_corner mod_corner_' + ss_discount + '">' + Json[i].sale + '<sup>折</sup></div>' : '') +
                                   '<img class="goods-photo js-goods-lazy" name="goodslist' + Page + '" src="' + imgsrc + '" data-original="' + Json[i].gi_imgs + '" >' +
                                   '</div>' +
                                   '<div class="info clearfix info-title">' +
                                    (showsuppliername != "false" ? '<div style="color:#000;">' + (Json[i].companyname == "" ? "&nbsp;" : Json[i].companyname) + '</div>' : '') + '<p class="goods-title  goods-title_' + Json[i].gi_id + ' goods-list-title" data-id="' + Json[i].gi_id + '">' + Json[i].gi_title + '</p>' +
                                   '<p class="goods-price goods-price-icon"><em>￥' + Json[i].gb_salesprice + ' </em>' + (parseFloat(Json[i].gb_marketprice) > parseFloat(Json[i].gb_salesprice) ? '<del style="color:#c5c5c5;font-size:11px;">￥' + Json[i].gb_marketprice + '</del>' : '<del style="color:#c5c5c5;font-size:11px;">&nbsp;</del>') + '</p>' +
                                   '<p class="goods-price-taobao"></p>' +
                                   '</div><div></div><table style="color:#c5c5c5;font-size:10px;width:100%; margin-top:-10px;"><tr><td align="right">售' + Json[i].vg_sale + '笔&nbsp;</td></tr></table></a></li>';

            // '</div><div class="goods-buy btn1"><table style="width:100%"><tr><td align="right"><table><tr><td valign="middle"><img src="/moban/custom/img/icon_sc.png" style="width:15px;" /></td><td valign="middle">&nbsp;' + Json[i].gi_collect_num + '<div style="height:3px;"></div></td></tr></table></td></tr></table></div></a></li>';
        }
        //$("#div_shopgoodslist").append(Str);
        if (divId == "div_shopgoodslist") {
            $("#div_shopgoodslist").append(Str);
        } else {
            $("#" + divId).before(Str);
        }
        $("img[name=goodslist" + Page + "]").lazyload({
            effect: "fadeIn"
        });
        //        if (FirstLoadGoodsData == true && Page != 1) {
        //            window.scrollTo(0, $("#div_shopgoodslist").offset().top + 120);
        //            setTimeout(function () {
        //                window.scrollTo(0, $("#div_shopgoodslist").offset().top + 120);
        //            }, 300)

        //        }
        //        FirstLoadGoodsData = false;
        window.localStorage["IndexBodyHtml" + Sid] = document.getElementById("divbody").innerHTML;
        GetGoodsTitle()
    }
}

function GetGoodsTitle() {
    var gids = "";
    $(".goods-list-title").each(function () {
        gids += $(this).attr("data-id") + ",";
    })
    $.ajax({
        type: "POST",
        url: "/vshop/goodsed.html?sid=" + Sid + "&action=getGoodsTitle",
        data: { goodsids: gids },
        success: function (result) {
            var Json = eval(result);
            for (var i = 0; i < Json.length; i++) {
                $(".goods-title_" + Json[i].gi_id).html(Json[i].vg_title);
                $(".goods-title_" + Json[i].gi_id).removeClass("goods-list-title");
            }
        }
    })
}

function ScrollRun() {
    //$(window).unbind("scroll");
    $(window).bind("scroll", function () {
        window.localStorage["IndexBodyScrollTop" + Sid] = document.body.scrollTop;
        if (IndexSetLoad) {
            if (document.body.scrollTop + window.screen.height + 200 >= $("#div_footer").offset().top) {

                if (IndexModelPageUp == true) {
                    IndexModelPageUp = false;
                    setTimeout(function () {
                        IndexModelPageUp = true;
                    }, 1000)
                    IndexSetPage += 1;
                    LoadIndexSet();
                }
            }
        }

        //        var b = document.documentElement.scrollTop == 0 ? document.body.scrollTop : document.documentElement.scrollTop;
        //        // OpenMsg(b + "--" + (document.getElementById("div_header").offsetTop + document.getElementById("div_header").offsetHeight + 100));
        //        if (b <= document.getElementById("div_header").offsetTop + document.getElementById("div_header").offsetHeight + 20 && IndexSetPage > 1) {
        //            if (IndexSetIsLoad == false && IndexModelPageLower == true) {
        //                IndexModelPageLower = false;
        //                setTimeout(function () {
        //                    IndexModelPageLower = true;
        //                }, 1000)
        //                IndexSetPage = IndexSetPage - 1;
        //                LoadIndexSet();
        //            }
        //        }
        //        try {
        //           // var LastModelPage = getPageTop(b);
        //            // OpenMsg(LastModelPage);
        //            //localStorage.LastModelPage = LastModelPage;
        //        } catch (e) { }
        //        localStorage.LastscrollTop = b;
        try {
            if (ShopGoodsLoad) {
                if (document.body.scrollTop + window.screen.height + 100 >= $("#div_shopgoodslistfooter").offset().top) {
                    if (IndexShopGoodsPageUp) {
                        IndexShopGoodsPageUp = false;
                        setTimeout(function () {
                            IndexShopGoodsPageUp = true;
                        }, 1000)
                        Page++;
                        LoadShopGoods();
                    }
                }
            }

            //            if (b <= document.getElementById("shop_goods_list").offsetTop + 100 && Page > 1) {
            //              
            //                Page = Page - 1;
            //                LoadShopGoods();
            //               

            //            }
        } catch (e) { }

        try {
            // var LastShopGoodsPage = getShopGoodsPageTop(b);
            // OpenMsg(LastShopGoodsPage);
            // localStorage.LastShopGoodsPage = LastShopGoodsPage;
        } catch (e) { }
    });

}
function LoadSwipt() {
    var swipeimages = document.getElementsByName("swipeimage");
    for (var i = 0; i < swipeimages.length; i++) {
        new Swipe(swipeimages[i], { speed: 500, auto: 3000 }, 1);
    }
}
function getPageTop(top) {
    for (var num in LoadDataPageArr) {
        try {
            LoadDataPageArr[num] = $("#app-field-model-page-" + num).offset().top + document.getElementById("app-field-model-page-" + num).offsetHeight;
        } catch (e) { }
        if (top < parseInt(LoadDataPageArr[num])) {
            return num;
        }
    }
}
function getShopGoodsPageTop(top) {

    for (var num in LoadShopGoodsDataPageArr) {
        // try {

        LoadShopGoodsDataPageArr[num] = $("#goods-card-li-0-" + num).offset().top + 1200;

        // } catch (e) { }
        if (top < parseInt(LoadShopGoodsDataPageArr[num])) {
            return num;
        }
    }
}
function FormatSid(url) {
    // url = url.toLowerCase();
    if (url.indexOf("?sid=") < 0 && url.indexOf("&sid=") < 0 && url.indexOf(".") > 0) {
        if ($.trim(url) == "" || $.trim(url) == "?sid=" + Sid) {
            return "javascript:void(0)";
        }
        if (url.indexOf("?") > 0) {
            return url + "&sid=" + Sid;
        } else {
            return url + "?sid=" + Sid;
        }
    }
    else {
        if ($.trim(url) == "" || $.trim(url) == "?sid=" + Sid) {
            return "javascript:void(0)";
        }
        return url;
    }
}