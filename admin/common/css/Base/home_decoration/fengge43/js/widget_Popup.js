var Popup = { 
		    
	  fengxianged:0, 
	  fengxiang : function(){
		  if(Popup.fengxianged){
			 document.getElementById("fengxiang").style.display = "block";
		  }else{
			 var div = document.createElement("div");
			 div.innerHTML = '<div data-role="widget" data-widget="fengxiang" id="fengxiang" style="width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; position:fixed; color:#fff; top:0; max-width:640px; display:block;"><div style=" overflow:hidden; margin-bottom:-15px"><img src="/vshop/Assets/imgs/widget_fengxiang.png" style="max-width:120px; float:right; margin-top:65px; margin-right:40px;"></div><section style="margin:5%; width:90%; display:block; font-size:15px;">1、点击右上角按钮<span style="display:inline-block; width:40px; background:url(/vshop/Assets/imgs/widget_icons_2.png); background-repeat:no-repeat; background-size:40px auto; height:18px; background-position:0 -385px"></span><br /><br />2、点击　<span style="display:inline-block; width:40px; background:url(/vshop/Assets/imgs/widget_icons_2.png); background-repeat:no-repeat; background-size:40px auto; height:40px; background-position:0 -236px; vertical-align:middle"></span>　发送给朋友<br /><br />&nbsp;&nbsp;　点击　<span style="display:inline-block; width:40px; background:url(/vshop/Assets/imgs/widget_icons_2.png); background-repeat:no-repeat; background-size:40px auto; height:40px; background-position:0 -290px; vertical-align:middle"></span>　分享到朋友圈<br /><br />&nbsp;&nbsp;　点击　<span style="display:inline-block; width:40px; background:url(/vshop/Assets/imgs/widget_icons_2.png); background-repeat:no-repeat; background-size:40px auto; height:40px; background-position:0 -340px; vertical-align:middle"></span>　将店铺收藏至微信<br /><br /></section></div>';
			 document.getElementsByClassName("body")[0].appendChild(div);				   
			 Popup.fengxianged =1;
		  }
		  document.getElementById("fengxiang").onclick=function(){
			   this.style.display = "none";
		  }
		},
	  guanzhued:0, 
	  guanzhu : function(){
		  
		  if(Popup.guanzhued){
		  
			  document.getElementById("guanzhu").style.display = "block";
		  }else{
			  
			 var div = document.createElement("div");
			 div.innerHTML = '<div data-role="widget" data-widget="guanzhu" id="guanzhu" style="width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; position:fixed; color:#fff; top:0; max-width:640px; display:block;"><div style=" overflow:hidden; margin-bottom:-15px"><img src="/vshop/Assets/imgs/widget_fengxiang.png" style="max-width:120px; float:right; margin-top:65px; margin-right:40px;"></div><section style="margin:5%; width:90%; display:block; font-size:15px;">1、点击右上角按钮<span style="display:inline-block; width:40px; background:url(/vshop/Assets/imgs/widget_icons_2.png); background-repeat:no-repeat; background-size:40px auto; height:18px; background-position:0 -385px"></span><br /><br />2、点击　<span style="display:inline-block; width:40px; background:url(/vshop/Assets/imgs/widget_icons_2.png); background-repeat:no-repeat; background-size:40px auto; height:40px; background-position:0 -186px; vertical-align:middle"></span>　查看公众号<br /><br />3、点击　<span style="display:inline-block; width:80px; background:#06ba04; text-align:center; line-height:25px; border-radius:3px; vertical-align:middle">关注</span>　关注我们</section></div>';
			 document.getElementsByClassName("body")[0].appendChild(div);				   
			 Popup.guanzhued =1;
		  }
		  document.getElementById("guanzhu").onclick=function(){
			   this.style.display = "none";
		  }
		}

};   			  