
// JavaScript Document
      var reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;  
	/*16进制颜色转为RGB格式*/  
	String.prototype.colorRgb = function(){  
		var sColor = this.toLowerCase();  
		if(sColor && reg.test(sColor)){  
			if(sColor.length === 4){  
				var sColorNew = "#";  
				for(var i=1; i<4; i+=1){  
					sColorNew += sColor.slice(i,i+1).concat(sColor.slice(i,i+1));     
				}  
				sColor = sColorNew;  
			}  
			//处理六位的颜色值  
			var sColorChange = [];  
			for(var i=1; i<7; i+=2){  
				sColorChange.push(parseInt("0x"+sColor.slice(i,i+2)));    
			}  
			return  sColorChange.join(",");  
		}else{  
			return sColor;    
		}  
	};
	
	
	
	var Float_obj = {   //对象自面量  减少全局变量（干扰）
	        
			pub_fun: function(float_mods,Mod_id){			  
			   if(float_mods.IsCompute){
				  float_mods.Stretch ?  $("#"+Mod_id+" .widget_wrap > img").css({"height":"460px","width":"100%"}) : null;			     
			   }else{
				  var bodyheight = $(window).height()+"px";				
				  float_mods.Stretch ?  $("#"+Mod_id+" .widget_wrap > img").css({"height":bodyheight,"width":"100%"}) : null;				    
			   }					
			},
			
			pub_weizhi:function(){
				
				      var weizhi;
					  if($(window).width() >= 640){	
					    weizhi = 0.3;
					  }else if($(window).width() < 640 && $(window).width() > 375){						  
					      weizhi = 0.2;						  
					  }else if($(window).width() <= 375 && $(window).width() >320 ){						  
						  weizhi = 0.15;						
					  }else if ($(window).width() <=320 ){						  
						  weizhi = 0.1;
					  }
					  return weizhi
		    },

			float_1 : function(float_mods,Mod_id){		
				 if(float_mods.float_1_type){
					  
					  var Mod_length =  $("#"+Mod_id+" ul li").length;
					  for(var i = 0; i < Mod_length; i++){
					     if( i%2 == 0){
							 $("#"+Mod_id+" ul li").eq(i).addClass("a-fadeinL");
						 }else{
						     $("#"+Mod_id+" ul li").eq(i).addClass("a-fadeinR");
						 }
					  }		
					 
				 }else{
					  $("#"+Mod_id+" .fixed").addClass("a-fadein");		
				 }					 
				 
				 Float_obj.pub_fun(float_mods,Mod_id);	
				 
				 $("#"+Mod_id+" .widget_wrap ul li").css("background-color", "rgba("+float_mods.backgroundbj.colorRgb()+",0.4)"); 		
			},
			float_2 : function(float_mods,Mod_id){	
	 		
				 Float_obj.pub_fun(float_mods,Mod_id);
				
				 $("#"+Mod_id+" .menuHolder .menu").css("background-color", "rgba("+float_mods.xyuanbj.colorRgb()+",0.4)"); //模块2大圆背景
				 $("#"+Mod_id+" .menuHolder ul.p2 > li").eq(0).find("a").css("background-color", "rgba("+float_mods.dyuanbj.colorRgb()+",0.4)"); //模块2小圆背景
				 $("#"+Mod_id+" .p2 li").addClass("s"+ $("#"+Mod_id+" .p2 li").length);
				 
			
				 if( 0 == float_mods.Stretch){		
				      
					 var fwidth = $("#"+Mod_id+" .widget_wrap > img").width(),
					     fheight = $("#"+Mod_id+" .widget_wrap > img").height() ;
						 
					 
						 
					 if( (fheight/fwidth).toFixed(2) <= 1.1){					  
						
					  $("#"+Mod_id+" .menuHolder").css({"transform":"scale("+(fheight/fwidth*0.8).toFixed(2)+")","-webkit-transform": "scale("+(fheight/fwidth*0.8).toFixed(2)+")","margin-top":(fheight*Float_obj.pub_weizhi()).toFixed(2)+"px"});
					  	   
					 }else{
						 
					   $("#"+Mod_id+" .menuHolder").css({"top":"50%","margin-top":"-"+$("#"+Mod_id+" .menuWindow").height()/2+"px"})
					 }
					 
				 }else{
				    $("#"+Mod_id+" .menuHolder").css({"top":"50%","margin-top":"-"+$("#"+Mod_id+" .menuWindow").height()/2+"px"})
				 }
				
				 
				
			},
			float_3 : function(float_mods,Mod_id){	
			
			     Float_obj.pub_fun(float_mods,Mod_id);
				 
				 $("#"+Mod_id+" .widget_wrap .linxing").css("background-color", "rgba("+float_mods.backgroundbj.colorRgb()+",0.4)"); //模块5背景
				 
				 if(!float_mods.Stretch){	
				      var fwidth = $("#"+Mod_id+" .widget_wrap > img").width(),
					      fheight = $("#"+Mod_id+" .widget_wrap > img").height();
						  
					  if( (fheight/fwidth).toFixed(2) < 1.1){		
									
					     $("#"+Mod_id+" .linxingcon").css({"-webkit-transform":"scale("+(fheight/fwidth*0.7).toFixed(1)+")","transform":"scale("+(fheight/fwidth*0.7).toFixed(1)+")","left":Math.round(fheight/fwidth*14)+"px","right":Math.round(fheight/fwidth*14)+"px"});								
					
						var fheights = ((fheight -fheight /fwidth*70*0.82*$("#"+Mod_id+" .linxing").length)/2).toFixed(1);
						$("#"+Mod_id+" .linxingcon").css({"margin-top":fheights+"px"});
                     
									   		
					 }else{
				
						 
					    $("#"+Mod_id+" .linxingcon").css({"top":"50%","margin-top":"-"+100*$("#"+Mod_id+" .linxing").length/2*0.9+"px"});					
					 }			  	 
				 }else{	
						 
				$("#"+Mod_id+" .linxingcon").css({"top":"50%","margin-top":"-"+100*$("#"+Mod_id+" .linxing").length/2*0.9+"px"});				
				 }
				
			}
			
		};   //最后要有分号