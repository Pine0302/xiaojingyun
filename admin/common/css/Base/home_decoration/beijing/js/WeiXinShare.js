var param;
function GetData(url, title, desc, img_url) {
    var desc2 = desc.replace("<br />", "\r").replace("<br />", "\r") + "\r收藏热度：★★★★★";
    param = {
        "appid": "",
        "img_url": img_url,
        "desc": desc2,
        "link": url,
        "title": title,
        "img_width": "120",
        "img_height": "120"
    };
}
function wxReady(e) {
    if (window.WeixinJSBridge) {
        e();
    }
    else {
        document.addEventListener("WeixinJSBridgeReady", e, false);
    }
} !

function () {
    wxReady(function () {
        //WeixinJSBridge.call(e.notShare ? "hideOptionMenu": "showOptionMenu"),
        //WeixinJSBridge.call(window._global.js.hide_wx_nav ? "hideToolbar": "showToolbar"),

        WeixinJSBridge.on("menu:share:timeline",
        function () {
            WeixinJSBridge.invoke("shareTimeline", param,
                function (callback) {
                })
        }
        ),
        WeixinJSBridge.on("menu:share:facebook",
        function () {
            WeixinJSBridge.invoke("shareTimeline", param,
            function (callback) { })
        }),
        WeixinJSBridge.on("menu:share:appmessage",
        function () {
            WeixinJSBridge.invoke("sendAppMessage", param,
                function (callback) {
                })
        })
    })

}();