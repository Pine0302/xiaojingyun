$(document).ready(function(){
/* 轮播图*/
    $(".flexslider").hover(function(){
        $("#btn_prev,#btn_next").fadeIn()
    },function(){
        $("#btn_prev,#btn_next").fadeOut()
    });
    
    $dragBln = false;
    
    $(".slides").touchSlider({
        flexible : true,
        speed : 500,
        btn_prev : $("#btn_prev"),
        btn_next : $("#btn_next"),
        paging : $(".flicking_con a"),
        counter : function (e){
            $(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
        }
    });
    
    $(".slides").bind("mousedown", function() {
        $dragBln = false;
    });
    
    $(".slides").bind("dragstart", function() {
        $dragBln = true;
    });
    
    $(".slides a").click(function(){
        if($dragBln) {
            return false;
        }
    });
    
    timer = setInterval(function(){
        $("#btn_next").click();
    }, 3000);
    
    $(".flexslider").hover(function(){
        clearInterval(timer);
    },function(){
        timer = setInterval(function(){
            $("#btn_next").click();
        },3000);
    });
    
    $(".slides").bind("touchstart",function(){
        clearInterval(timer);
    }).bind("touchend", function(){
        timer = setInterval(function(){
            $("#btn_next").click();
        }, 3000);
    });
    //页面设置
    $(".j-page-addModule").click(function() {
        $('.type-ctrl-item').hide();
        $('.diy-ctrl-item-b').show();
         $("html,body").scrollTop(40);
    });

    //color picker
    $('#bgColor').ColorPicker({
    onShow: function (colpkr) {
        $(colpkr).fadeIn(400);
        return false;
    },
    onHide: function (colpkr) {
        $(colpkr).fadeOut(400);
        return false;
    },
    onChange: function (hsb, hex, rgb) {
        $('#bgColor div').css('backgroundColor', '#' + hex);
        $('.WSY_homeleft_middle').css('backgroundColor', '#' + hex);
        $("input[name='bgColor']").val('#' + hex);
    }
});



}); 
