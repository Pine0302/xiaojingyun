
var QJTTYPE=0;
var QJOTYPE=0;
var QJPTYPE=0;
var QJGTYPE=0;
var QJFTYPE=0;
var QJDTYPE=0;
var ADSTYPE=0;
var QJTTIME=0;
var QJOTIME=0;
var QJPTIME=0;
var QJGTIME=0;
var QJFTIME=0;
var QJDTIME=0;
var ADSTIME=0;
$(function(){
    var chart_width = $('.statistics_ul05').width();
    $('.chart').css('width',chart_width+"px");
    QJTTYPE=0;
    QJOTYPE=0;
    QJPTYPE=0;
    QJGTYPE=0;
    QJFTYPE=0;
    QJDTYPE=0;
    ADSTYPE=0;
//-----------
    QJTTIME=0;
    QJOTIME=0;
    QJPTIME=0;
    QJGTIME=0;
    QJFTIME=0;
    QJDTIME=0;
    ADSTIME=0;
    TTsale();
    TOsale();
    TPsale();
    TGpromoters();
    TGfens();
    TDDay();
    // ADSsale();
}); 
function replacell(str){
    return str.replace('"',"0~a~l"); 
    
}
function replacelp(str){
    return str.replace("<","0~a~p"); 
    
}
function replacelq(str){
    return str.replace(">","0~a~q"); 
    
}
function replaceTl(str){
    return str.replace(/"/g,""); 
    
}
function replaceTp(str){
    return str.replace(/</g,""); 
    
}
function replaceTq(str){
    str=str.replace(/>/g,","); 
    return str.replace(/,,/g,","); 
    
}
function replaceRl(str){
    return str.replace("0~a~l",'"'); 
    
}
function replaceRp(str){
    return str.replace("0~a~p","<"); 
    
}
function replaceRq(str){
    return str.replace("0~a~q",">"); 
    
}
function reTOtal(str){
    str=str+"";
    var w=str.indexOf("\.");
    if(w>6){
        return (parseFloat(str)/10000).toFixed(2)+'万'
    }
    return str; 
    
}
var index;
//<==================================(销售统计)================================================>
function TTsale(){  //分销商数
        var begintime=$("#TSbegintime").val();
        var endtime=$("#TSendtime").val();
        var customer_id=$("#customer_id").val();
        QJTTIME=$("#STYPE").val();
        var SYF=$("#SYF").val() ;
    $.ajax({
        type: "post",
        url: "get_BaseStatistics.php",
        dataType: "json",
        //begintime:begintime,endtime:endtime,
        data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:QJTTIME,CONtype:3,SOtype:SYF},
        success: function (result) {
            //class='display'
            var length=result.length;
            var Str="<input id='ksize' class='display'  value="+(length-2)+">";
            for(var i=0;i<(length-2);i++){
                var key=result[i][0];
                
                Str=Str+"<input id='key"+i+"' class='display' value="+key+"><input id='val"+i+"' class='display' value="+result[i][1]+">";
            }
            // Str=Str+"<input id='TTOTAL' class='display' value="+result[length-3]+">";
            $("#TTsale").html(Str);
            $("#TSbegintime").val(result[length-2]);
            $("#TSendtime").val(result[length-1]);
            TTsaleIchartjs();

             
        }
    });
}
function search_SQ(obj){
    QJTTIME=$("#STYPE").val();
    var ktitle=$(obj).val();
    if(ktitle=="搜 索"){
        QJTTYPE=0;
        TTsale();
    }
    if(ktitle=="刷新数据"){
        QJTTYPE=0;
        $("#TSbegintime").val('');
        TTsale();
    }
    if(ktitle=="转曲线图"){
        QJTTYPE=0;
        TTsaleIchartjs();
    }
    if(ktitle=="转柱形图"){
        QJTTYPE=5;
        TTsaleIchartjs();
    }
    if(ktitle=="列表查看"){
        QJTTYPE=6;
        TTsaleIchartjs();
    }
    if(ktitle=="详细查看"){
        QJTTYPE=7;
        search_LB(obj,3);
    }
    if(ktitle=="导出订单"){
        QJTTYPE=8;
        excel_OD(obj,3);
    }
    if(ktitle=="导出飞豆"){
        QJTTYPE=9;
        excel_FD(obj,3);
    }
}
function TTsaleIchartjs(){
    var currencyUnit = $('#currency-unit').val();
            //======================(title动态赋值)==========
            var title="";
            if(QJTTIME==1){
                title=$("#TSbegintime").val()+'~'+$("#TSendtime").val()+'每日销售额';
            }else if(QJTTIME==2 ){
                var Month=$("#TSbegintime").val();
                var year=Month.substring(0,Month.indexOf("-"));
                Month=Month.substring(Month.indexOf("-")+1,Month.lastIndexOf("-"));
                var Q="";
                if(parseInt(Month)==1){
                    Q="第一季度";
                }else if(parseInt(Month)==4){
                    Q="第二季度";
                }else if(parseInt(Month)==7){
                    Q="第三季度";
                }else if(parseInt(Month)==10){
                    Q="第四季度";
                }
                title=year+'年'+Q+'月销售分析';
            }else if(QJTTIME==3 ){
                var year=$("#TSbegintime").val();
                year=year.substring(0,year.indexOf("-"));
                title=year+'年度销售分析';
            }else if(QJTTIME==4 ){
                var year=$("#TSbegintime").val();
                title=year+'本周内销售分析';
            }
            //=======================(正文)========================
            var labels = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
            var data1 = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];
            var size=$("#ksize").val();
            var TTets=['一','二','三','四','五','六','天'];
            var Tlabels=[];
            var Slabels=[];
            if(size>0){
                labels = [];
                data1 = [];
            }
            var total=0;
            var max=0;

            if(QJTTYPE<5){
                QJTTYPE=0;
            for(var i=0;i<size;i++){
                var key=$("#key"+i).val();
                var val=$("#val"+i).val();
                total+=parseInt(val*100);
                if(max<parseInt(val)){
                    max=parseInt(val);
                }
                if(QJTTIME==1){
                    labels.push(key.substring(key.lastIndexOf("-")+1)); 
                    Slabels.push(key);  
                }
                if(QJTTIME==2 || QJTTIME==3){
                    labels.push(key.substring(key.indexOf("-")+1,key.lastIndexOf("-")));    
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                }
                if(QJTTIME==4){
                    labels.push(key.substring(key.indexOf("-")+1)+',星期'+TTets[i]);  
                    Slabels.push(key);  
                }
                Tlabels.push(key);
                val=parseInt(val*100);
                val=val/100;
                data1.push(val);
            }
            
            //==================（坐标轴单位）==============
            var XZ="";
            if(QJTTIME==1){
                XZ="号";
            }else if(QJTTIME==2 ){
                XZ="月";
            }else if(QJTTIME==3 ){
                XZ="月";
            }
            var XT="";
            if(QJTTIME==1){
                XT="本月份";
            }else if(QJTTIME==2 ){
                XT="本季度";
            }else if(QJTTIME==3 ){
                XT="本年度";
            }else{
                XT="本星期";
            }
            // var TTOTAL=$("#TTOTAL").val();
            // TTOTAL=parseInt(TTOTAL*100);
            // TTOTAL=TTOTAL/100;
            
            
            //==================（注解解释）==============
            
            //=================(计算Y轴值)==================
            total=total/100;
            
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            // TTOTAL=reTOtal(TTOTAL);
            total=reTOtal(total);
            //===================================
                var data = [
                        {
                            name : 'PV',
                            value:data1,
                            color:'#06A7E1',
                            line_width:1.5
                        }
                     ];
            var chart = new iChart.LineBasic2D({
                render : 'canvasDiv',
                data: data,
                align:'center',
                title : {
                    text:title,
                    font : '微软雅黑',
                    fontsize:24,
                    color:'#333333'
                },
                subtitle : {
                    text:XT+'总销售'+total+currencyUnit,//+'        总销售额：'+TTOTAL+currencyUnit,
                    font : '微软雅黑',
                    color:'#333333'
                },
        
                width : 800,
                height : 400,
                shadow:false,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-60,
                    color : '#666666'
                },
                shadow_color : '#202020',
                shadow_blur : 8,
                shadow_offsetx : 0,
                shadow_offsety : 0,
                background_color:'#fff',
                tip:{
                    enable:true,
                    shadow:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            return "<span style='color:#005268;font-size:12px;'>"+Slabels[i]+"销售额:<br/>"+
                            " </span><span style='color:#005268;font-size:20px;'>"+value+currencyUnit+"</span>";
                        }
                    }
                },
                crosshair:{
                    enable:true,
                    line_color:'#dadada'
                },
                sub_option : {
                    smooth : true,
                    label:false,
                    hollow:false,
                    hollow_inside:false,
                    point_size:8
                },
                coordinate:{
                    width:640,
                    height:260,
                    striped_factor : 0.10,
                    grid_color:'#dadada',
                    axis:{
                        color:'#9f9f9f',
                        width:[0,0,2,2]
                    },
                    scale:[{
                         position:'left',   
                         start_scale:0,
                         end_scale:step*5,
                         scale_space:step,
                         scale_size:2,
                         scale_enable : false,
                         label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                         scale_color:'#333333'
                    },{
                        position:'bottom',  
                        label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                        scale_enable : true,
                        labels:labels
                    }]
                }
            });
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy'),
                            w = coo.width,
                            h = coo.height;
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText('销售额('+currencyUnit+')',x-40,y-12,false,'#333333')
                        .textBaseline('top')
                        .fillText('('+XZ+')',x+w+12,y+h+10,false,'#333333');
                        
                    }
            }));
        //开始画图
        chart.draw();
        }
        if(QJTTYPE==5){
            QJTTYPE=0;
            var data = [];
            for(var i=0;i<size;i++){
                var key=$("#key"+i).val();
                 Tlabels.push(key);
                var val=$("#val"+i).val();
                total+=parseInt(val*100);
                if(max<parseInt(val)){
                    max=parseInt(val);
                }
                val=parseInt(val*100);
                val=val/100;
                if(QJTTIME==1){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1);  
                    var kd={name : key+'号',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                if(QJTTIME==2 || QJTTIME==3){
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                    key=key.substring(key.indexOf("-")+1,key.lastIndexOf("-")); 
                    var kd={name : key+'月',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                    if(QJTTIME==4){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1)+',星期'+TTets[i];   
                    var kd={name : key,value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                
            }       
            //=================(计算Y轴值)==================
            total=total/100;
            
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            //===================================       
            
            
            var chart = new iChart.Column2D({
                render : 'canvasDiv',
                data : data,
                title : {
                    text : title,
                    font:'微软雅黑',
                    color : '#333333'
                },
                subtitle : {
                    text : '总销售：'+total+currencyUnit,
                    font:'微软雅黑',
                    color : '#333333'
                },
            
                width : 800,
                height : 400,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-60,
                    color : '#333333'
                },
                tip:{
                    enable:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            //将数字进行千位格式化
                            var f = new String(value);
                            
                            
                            return Slabels[i]+"销售额:<br/>"+f+currencyUnit+"<br/>占整个比重:"+(value/this.get('total') * 100).toFixed(2)+'%';
                        }
                    }
                },
                shadow : true,
                shadow_blur : 2,
                shadow_color : '#aaaaaa',
                shadow_offsetx : 1,
                shadow_offsety : 0,
                column_width : 62,
                sub_option : {
                    label : false,
                    border : {
                        width : 2,
                        color : '#ffffff'
                    },
                    listeners:{
                            /**
                             * r:iChart.Sector2D对象
                             * e:eventObject对象
                             * m:额外参数
                             */
                            click:function(s,e,m){
                                QJTTIME=$("#STYPE").val();
                                var TTO=722;
                                if(QJTTIME==1){
                                    TTO=674;
                                }
                                if(QJTTIME==3){
                                    TTO=674;
                                }
                                var OTT=8;
                                if(QJTTIME==1){
                                    OTT=56;
                                }
                                if(QJTTIME==3){
                                    OTT=25;
                                }
                                var TTi=TTO/(parseInt(size)+1);
                                var Ti=Math.round((s.get('originx')-OTT)/TTi);
                                if(QJTTIME==1 || QJTTIME==4){// title=$("#TSbegintime").val()+'~'+$("#TSendtime").val()+'每日销售额';
                                    $("#TSbegintime").val(Tlabels[Ti-1]);
                                    if(Ti<Tlabels.length){
                                        $("#TSendtime").val(Tlabels[Ti]);
                                    }
                                    $(".TS").click();
                                }
                                if(QJTTIME==2 || QJTTIME==3){
                                    $("#TSbegintime").val(Tlabels[Ti-1]);
                                    if(Ti<Tlabels.length){
                                        $("#TSendtime").val(Tlabels[Ti]);
                                    }
                                    $("#STYPE").val(1);
                                    QJTTYPE=0;
                                    TTsale();
                                }

                                
                                
                                

                            }
                        }
                },
                coordinate : {
                    background_color : null,
                    grid_color : '#dadada',
                    width : 660,
                    height:240,
                    axis : {
                        color : '#9f9f9f',
                        width : [0, 0, 1, 0]
                    },
                    scale : [{
                        position : 'left',
                        start_scale : 0,
                        end_scale : step*5,
                        scale_space : step,
                        scale_enable : false,
                        label : {
                            fontsize:11,
                            color : '#333333'
                        },
                        listeners:{
                            parseText:function(t,x,y){
                                return {text:t}
                            }
                         }
                    }]
                }
            });
            
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy');
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText('销售额('+currencyUnit+')',x-40,y-10,false,'#333333');
                        
                    }
            }));
            var mwidth=800;
            var mheight=400;
            $("#canvasDiv").children().children().attr("width",mwidth);
            $("#canvasDiv").children().children().attr("height",mheight);
            chart.draw();
        }
        if(QJTTYPE==6){
            QJTTYPE=0;
             $("#canvasDiv").html("");
             
             var total=0;
             for(var i=0;i<size;i++ ){
                 var VAL=$("#val"+i).val();
                total+=parseInt(VAL*100);
             }
             total=total/100;
            //Str+="</div>";
            var Str="<div class='WSY_weixinbox' id='TTSHOW' style='width:100%;height:100%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;margin-left: 0px;margin-top: 0px;'>";
            Str+="<div class='WSY_weixin'>";
            Str+="<a>"+title+"</a>";
            Str+="</div>";
            Str+="<div style='padding:20px;overflow:auto;height:85%;width:95%'>";
            Str+="<div style='background-color: #FFE7BA;border-radius: 10px;'>";
            Str+=" <table width='100%' border='0' cellspacing='0' cellpadding='0'>";
            Str+="<thead>";
            Str+="<tr style='line-height: 30px;background-color: rgb(6, 167, 225);'>";
            if(QJTTIME<2){
                Str+="<th scope='col'>时间(日)</th>";
            }
            if(QJTTIME>1){
                Str+="<th scope='col'>时间(月)</th>";
            }
            Str+="<th scope='col' style='display:none'>到期时间</th>";
            Str+="<th scope='col'>销售额</th>";
            Str+="<th scope='col'>百分比</th>";
            Str+="<th scope='col'>查看详情</th>";
            Str+="</tr>";
            Str+="</thead>";
            Str+="<tbody>";
            
            for(var i=0;i<size;i++ ){
                Str+="<tr style='line-height: 30px;'>";
                var KEY=$("#key"+i).val();
                if(QJTTIME>1 && QJTTIME<4){
                    KEY=KEY.substring(0,KEY.lastIndexOf("-"));  
                }
                var ksize=size-1;
                if(i<ksize){
                    var k=i+1;
                    var JKEY=$("#key"+k).val();
                    if(QJTTIME>1  && QJTTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                if(i==ksize){
                    var JKEY=$("#TSendtime").val();
                    if(QJTTIME>1  && QJTTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                
                var VAL=$("#val"+i).val();
                var BFB=(VAL/total)*100;
                VAL=parseInt(VAL*100);
                VAL=VAL/100;
                VAL=VAL.toFixed(2);
                BFB=BFB.toFixed(2);
                BFB=BFB+'%';
                if(total==0){
                    BFB='100%';
                }
                Str+="<td  style='text-align:center' valign='middle' >"+KEY+"</td>";
                Str+="<td   valign='middle'  style='display:none'>"+JKEY+"</td>";
                Str+="<td  style='text-align:center' valign='middle' >"+VAL+"</td>";
                Str+="<td  style='text-align:center' valign='middle' >"+BFB+"</td>";
                Str+="<td  style='text-align:center' valign='middle' >" +
                    "<a onclick='SEE_TY(this)' class='wsy_cost_style' title='列表查看' style='margin:2px;cursor:pointer;'><img src='../../Common/images/Data/qushiicon/btn_08.png' style='margin-top: 5px;' /></a>" +
                    "</td>";
                Str+="</tr>";
            }
            Str+="<tr style='line-height: 30px;'>";
            Str+="<td style='text-align:center' valign='middle' > 总计</td>";
            Str+="<td style='text-align:center' valign='middle' >"+total+"</td>";
            Str+="</tr>";
            Str+="</tbody>";
            Str+="</table>";
            Str+="</div>";
            Str+="</div>";
            Str+="</div>";
            $("#canvasDiv").html(Str);
            $("#TTSHOW").css({width:0,height:0});
            $("#TTSHOW").animate({width:800,height:400},"slow");    
            
        }
    }
function SEE_TY(obj){
    var DOCobj=$(obj).parent().parent();
    var STIME=DOCobj.children("td").eq(0).html();
    var ETIME=DOCobj.children("td").eq(1).html();
    if(QJGTIME>1){
        STIME+="-01";
        ETIME+="-01";
    }
    var PSID=$("#customer_id").val();
    var begintime=STIME;
    var endtime =ETIME;
    var province = "";
    var city = "" ;
    var area = "";
    var search_status = "3";
    var url="../Ostatistics/order_BarChart_detailed.php?customer_id="+PSID+"&search_status="+search_status;
    
    if(province !=""){
        url=url+'&province='+province;
    }
    if(city !=""){
        url=url+'&city='+city;
    }
    if(area !=""){
        url=url+'&area='+area;
    }
    if(begintime !=""){
        url=url+'&begintime='+begintime;
    }
    if(endtime !=""){
        url=url+'&endtime='+endtime;
    }
    document.location=url;
}
//<==================================(销售统计结束)================================================>
//<==================================(订单统计)================================================>
function TOsale(){  //分销商数
        var begintime=$("#TObegintime").val();
        var endtime=$("#TOendtime").val();
        var customer_id=$("#customer_id").val();
        QJOTIME=$("#OSTYPE").val();
        var CONtype=$("#status_O").val();
    $.ajax({
        type: "post",
        url: "get_BaseStatistics.php",
        dataType: "json",
        //begintime:begintime,endtime:endtime,
        data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:QJOTIME,CONtype:CONtype,SOtype:2},
        success: function (result) {
            //class='display'
            var length=result.length;
            var Str="<input id='Oksize' class='display'  value="+(length-2)+">";
            for(var i=0;i<(length-2);i++){
                Str=Str+"<input id='Okey"+i+"' class='display' value="+result[i][0]+"><input id='Oval"+i+"' class='display' value="+result[i][1]+">";
            }
            $("#TOsale").html(Str);
            $("#TObegintime").val(result[length-2]);
            $("#TOendtime").val(result[length-1]);
            TOsaleIchartjs();

             
        }
    });
}
function search_SO(obj){
        QJOTIME=$("#OSTYPE").val();
    var ktitle=$(obj).val();
    var CONtype=$("#status_O").val();
    if(ktitle=="搜 索"){
        QJOTYPE=0;
        TOsale();
    }
    if(ktitle=="刷新数据"){
        QJOTYPE=0;
        $("#TObegintime").val('');
        TOsale();
    }
    if(ktitle=="转曲线图"){
        QJOTYPE=0;
        TOsaleIchartjs();
    }
    if(ktitle=="转柱形图"){
        QJOTYPE=5;
        TOsaleIchartjs();
    }
    if(ktitle=="列表查看"){
        QJOTYPE=6;
        TOsaleIchartjs();
    }
    if(ktitle=="详细查看"){
        QJOTYPE=7;
        search_LB(obj,CONtype);
    }
    if(ktitle=="导出订单"){
        QJOTYPE=8;
        excel_OD(obj,CONtype);
    }
    if(ktitle=="导出飞豆"){
        QJOTYPE=9;
        excel_FD(obj,CONtype);
    }
}
function TOsaleIchartjs(){
            //======================(title动态赋值)==========
            var title="";
            if(QJOTIME==1){
                title=$("#TObegintime").val()+'~'+$("#TOendtime").val()+'每日订单情况';
            }else if(QJOTIME==2 ){
                var Month=$("#TObegintime").val();
                var year=Month.substring(0,Month.indexOf("-"));
                Month=Month.substring(Month.indexOf("-")+1,Month.lastIndexOf("-"));
                var Q="";
                if(parseInt(Month)==1){
                    Q="第一季度";
                }else if(parseInt(Month)==4){
                    Q="第二季度";
                }else if(parseInt(Month)==7){
                    Q="第三季度";
                }else if(parseInt(Month)==10){
                    Q="第四季度";
                }
                title=year+'年'+Q+'月订单情况';
            }else if(QJOTIME==3 ){
                var year=$("#TObegintime").val();
                year=year.substring(0,year.indexOf("-"));
                title=year+'年度订单情况';
            }else if(QJOTIME==4 ){
                var year=$("#TObegintime").val();
                title=year+'本周内订单情况';
            }
            //=======================(正文)========================
            var labels = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
            var data1 = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];
            var size=$("#Oksize").val();
            var TTets=['一','二','三','四','五','六','天'];
            var Tlabels=[];
            var Slabels=[];
            if(size>0){
                labels = [];
                data1 = [];
            }
            var total=0;
            var max=0;

            if(QJOTYPE<5){
                QJOTYPE=0;
            for(var i=0;i<size;i++){
                var key=$("#Okey"+i).val();
                Tlabels.push(key);
                var val=$("#Oval"+i).val();
                total+=parseInt(val*100);
                if(max<parseInt(val)){
                    max=parseInt(val);
                }
                if(QJOTIME==1){
                    labels.push(key.substring(key.lastIndexOf("-")+1)); 
                    Slabels.push(key);  
                }
                if(QJOTIME==2 || QJOTIME==3){
                    labels.push(key.substring(key.indexOf("-")+1,key.lastIndexOf("-")));    
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                }
                if(QJOTIME==4){
                    labels.push(key.substring(key.indexOf("-")+1)+',星期'+TTets[i]);  
                    Slabels.push(key);  
                }
                
                val=parseInt(val*100);
                val=val/100;
                data1.push(val);
            }
            
            //==================（坐标轴单位）==============
            var XZ="";
            if(QJOTIME==1){
                XZ="号";
            }else if(QJOTIME==2 ){
                XZ="月";
            }else if(QJOTIME==3 ){
                XZ="月";
            }
            //==================（注解解释）==============
            
            //=================(计算Y轴值)==================
            total=total/100;
            
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            //===================================
                var data = [
                        {
                            name : 'PV',
                            value:data1,
                            color:'#06A7E1',
                            line_width:1.5
                        }
                     ];
            var chart = new iChart.LineBasic2D({
                render : 'OcanvasDiv',
                data: data,
                align:'center',
                title : {
                    text:title,
                    font : '微软雅黑',
                    fontsize:24,
                    color:'#333333'
                },
                subtitle : {
                    text:'总订单：'+total+'单',
                    font : '微软雅黑',
                    color:'#333333'
                },
        
                width : 800,
                height : 400,
                shadow:false,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-60,
                    color : '#666666'
                },
                shadow_color : '#202020',
                shadow_blur : 8,
                shadow_offsetx : 0,
                shadow_offsety : 0,
                background_color:'#fff',
                tip:{
                    enable:true,
                    shadow:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            return "<span style='color:#005268;font-size:12px;'>"+Slabels[i]+"<br/>订单:"+
                            " </span><span style='color:#005268;font-size:20px;'>"+value+"单</span>";
                        }
                    }
                },
                crosshair:{
                    enable:true,
                    line_color:'#dadada'
                },
                sub_option : {
                    smooth : true,
                    label:false,
                    hollow:false,
                    hollow_inside:false,
                    point_size:8
                },
                coordinate:{
                    width:640,
                    height:260,
                    striped_factor : 0.5,
                    grid_color:'#dadada',
                    axis:{
                        color:'#9f9f9f',
                        width:[0,0,2,2]
                    },
                    scale:[{
                         position:'left',   
                         start_scale:0,
                         end_scale:step*5,
                         scale_space:step,
                         scale_size:2,
                         scale_enable : false,
                         label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                         scale_color:'#333333'
                    },{
                         position:'bottom', 
                         label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                         scale_enable : false,
                         labels:labels
                    }]
                }
            });
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy'),
                            w = coo.width,
                            h = coo.height;
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText('订单(单)',x-40,y-12,false,'#333333')
                        .textBaseline('top')
                        .fillText('('+XZ+')',x+w+12,y+h+10,false,'#333333');
                        
                    }
            }));
        //开始画图
        chart.draw();
        }
        if(QJOTYPE==5){
            QJOTYPE=0;
            var data = [];
            for(var i=0;i<size;i++){
                var key=$("#Okey"+i).val();
                 Tlabels.push(key);
                var val=$("#Oval"+i).val();
                total+=parseInt(val*100);
                if(max<parseInt(val)){
                    max=parseInt(val);
                }
                val=parseInt(val*100);
                val=val/100;
                if(QJOTIME==1){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1);  
                    var kd={name : key+'号',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                if(QJOTIME==2 || QJOTIME==3){
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                    key=key.substring(key.indexOf("-")+1,key.lastIndexOf("-")); 
                    var kd={name : key+'月',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                    if(QJOTIME==4){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1)+',星期'+TTets[i];   
                    var kd={name : key,value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                
            }       
            //=================(计算Y轴值)==================
            total=total/100;
            
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            //===================================       
            
            
            var chart = new iChart.Column2D({
                render : 'OcanvasDiv',
                data : data,
                title : {
                    text : title,
                    font:'微软雅黑',
                    color : '#333333'
                },
                subtitle : {
                    text : '总订单：'+total+'单',
                    font:'微软雅黑',
                    color : '#333333'
                },
        
                width : 800,
                height : 400,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-60,
                    color : '#333333'
                },
                tip:{
                    enable:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            //将数字进行千位格式化
                            var f = new String(value);
                            
                            
                            return Slabels[i]+"<br/>订单:"+f+"单<br/>占整个比重:"+(value/this.get('total') * 100).toFixed(2)+'%';
                        }
                    }
                },
                shadow : true,
                shadow_blur : 2,
                shadow_color : '#aaaaaa',
                shadow_offsetx : 1,
                shadow_offsety : 0,
                column_width : 62,
                sub_option : {
                    label : false,
                    border : {
                        width : 2,
                        color : '#ffffff'
                    },
                    listeners:{
                            /**
                             * r:iChart.Sector2D对象
                             * e:eventObject对象
                             * m:额外参数
                             */
                            click:function(s,e,m){
                                QJOTIME=$("#OSTYPE").val();
                                var TTO=722;
                                if(QJOTIME==1){
                                    TTO=674;
                                }
                                if(QJOTIME==3){
                                    TTO=674;
                                }
                                var OTT=8;
                                if(QJOTIME==1){
                                    OTT=56;
                                }
                                if(QJOTIME==3){
                                    OTT=25;
                                }
                                var TTi=TTO/(parseInt(size)+1);
                                var Ti=Math.round((s.get('originx')-OTT)/TTi);
                                if(QJOTIME==1 || QJOTIME==4){// title=$("#TSbegintime").val()+'~'+$("#TSendtime").val()+'每日销售额';
                                    $("#TObegintime").val(Tlabels[Ti-1]);
                                    if(Ti<Tlabels.length){
                                        $("#TOendtime").val(Tlabels[Ti]);
                                    }
                                    $(".TO").click();
                                }
                                if(QJOTIME==2 || QJOTIME==3){
                                    $("#TObegintime").val(Tlabels[Ti-1]);
                                    if(Ti<Tlabels.length){
                                        $("#TOendtime").val(Tlabels[Ti]);
                                    }
                                    $("#OSTYPE").val(1);
                                    QJOTYPE=0;
                                    TOsale();
                                }

                                
                                
                                

                            }
                        }
                },
                coordinate : {
                    background_color : null,
                    grid_color : '#dadada',
                    width : 660,
                    height:240,
                    axis : {
                        color : '#9f9f9f',
                        width : [0, 0, 1, 0]
                    },
                    scale : [{
                        position : 'left',
                        start_scale : 0,
                        end_scale : step*5,
                        scale_space : step,
                        scale_enable : false,
                        label : {
                            fontsize:11,
                            color : '#333333'
                        },
                        listeners:{
                            parseText:function(t,x,y){
                                return {text:t}
                            }
                         }
                    }]
                }
            });
            
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy');
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText('订单(单)',x-40,y-10,false,'#333333');
                        
                    }
            }));
    
            chart.draw();
        }
        if(QJOTYPE==6){
            QJOTYPE=0;
             $("#OcanvasDiv").html("");
            
             var total=0;
             for(var i=0;i<size;i++ ){
                 var VAL=$("#Oval"+i).val();
                total+=parseInt(VAL*100);
             }
             total=total/100;
            //Str+="</div>";
            var Str="<div class='WSY_weixinbox' id='TOSHOW' style='width:100%;height:100%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;margin-left: 0px;margin-top: 0px;'>";
            Str+="<div class='WSY_weixin'>";
            Str+="<a>"+title+"</a>";
            Str+="</div>";
            Str+="<div style='padding:20px;overflow:auto;height:85%;width:95%'>";
            Str+="<div style='background-color: #FFE7BA;border-radius: 10px;'>";
            Str+=" <table width='100%' border='0' cellspacing='0' cellpadding='0'>";
            Str+="<thead>";
            Str+="<tr style='line-height: 30px;background-color: rgb(6, 167, 225);'>";
            if(QJOTIME<2){
                Str+="<th scope='col'>时间(日)</th>";
            }
            if(QJOTIME>1){
                Str+="<th scope='col'>时间(月)</th>";
            }
            Str+="<th scope='col' style='display:none'>到期时间</th>";
            Str+="<th scope='col'>订单数</th>";
            Str+="<th scope='col'>百分比</th>";
            Str+="<th scope='col'>查看详情</th>";
            Str+="</tr>";
            Str+="</thead>";
            Str+="<tbody>";
            
            for(var i=0;i<size;i++ ){
                Str+="<tr style='line-height: 30px;'>";
                var KEY=$("#Okey"+i).val();
                if(QJOTIME>1 && QJOTIME<4){
                    KEY=KEY.substring(0,KEY.lastIndexOf("-"));  
                }
                var ksize=size-1;
                if(i<ksize){
                    var k=i+1;
                    var JKEY=$("#Okey"+k).val();
                    if(QJOTIME>1  && QJOTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                if(i==ksize){
                    var JKEY=$("#TOendtime").val();
                    if(QJOTIME>1  && QJOTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                
                var VAL=$("#Oval"+i).val();
                var BFB=(VAL/total)*100;
                VAL=parseInt(VAL*100);
                VAL=VAL/100;
                //VAL=VAL.toFixed(2);
                BFB=BFB.toFixed(2);
                BFB=BFB+'%';
                if(total==0){
                    BFB='100%';
                }
                Str+="<td style='text-align:center' valign='middle' >"+KEY+"</td>";
                Str+="<td  valign='middle'  style='display:none'>"+JKEY+"</td>";
                Str+="<td style='text-align:center' valign='middle' >"+VAL+"</td>";
                Str+="<td style='text-align:center' valign='middle' >"+BFB+"</td>";
                Str+="<td style='text-align:center' valign='middle' >" +
                    "<a onclick='SEE_TO(this)' class='wsy_cost_style' title='列表查看' style='margin:2px;cursor:pointer;'><img src='../../Common/images/Data/qushiicon/btn_08.png' style='margin-top: 5px;' /></a>" +
                    "</td>";
                Str+="</tr>";
            }
            Str+="<tr style='line-height: 30px;'>";
            Str+="<td style='text-align:center' valign='middle' > 总计</td>";
            Str+="<td style='text-align:center' valign='middle' >"+total+"</td>";
            Str+="</tr>";
            Str+="</tbody>";
            Str+="</table>";
            Str+="</div>";
            Str+="</div>";
            Str+="</div>";
            $("#OcanvasDiv").html(Str);
            $("#TOSHOW").css({width:0,height:0});
            $("#TOSHOW").animate({width:800,height:400},"slow");    
        }
    }
function SEE_TO(obj){
    var DOCobj=$(obj).parent().parent();
    var STIME=DOCobj.children("td").eq(0).html();
    var ETIME=DOCobj.children("td").eq(1).html();
    if(QJOTIME>1){
        STIME+="-01";
        ETIME+="-01";
    }
    var CONtype=$("#status_O").val();
    var PSID=$("#customer_id").val();
    var begintime=STIME;
    var endtime =ETIME;
    var province = "";
    var city = "" ;
    var area = "";
    var search_status = CONtype;
    var url="../Ostatistics/order_BarChart_detailed.php?customer_id="+PSID+"&search_status="+search_status;
    
    if(province !=""){
        url=url+'&province='+province;
    }
    if(city !=""){
        url=url+'&city='+city;
    }
    if(area !=""){
        url=url+'&area='+area;
    }
    if(begintime !=""){
        url=url+'&begintime='+begintime;
    }
    if(endtime !=""){
        url=url+'&endtime='+endtime;
    }
    document.location=url;
}
//<==================================(订单统计结束)================================================>

//<==================================(产品销售统计)================================================>
var  PTYPE=4;
var  PID;
var  PPNAME;
var  PST=0;
function TPsale(){  //分销商数
        var begintime=$("#TPbegintime").val();
        var endtime=$("#TPendtime").val();
        var customer_id=$("#customer_id").val();
        QJPTIME=$("#PSTYPE").val();
        var CONtype=$("#status_P").val();
        if(PTYPE==4){
            PID=0;
            PPNAME="";
        }
    $.ajax({
        type: "post",
        url: "get_BaseStatistics.php",
        dataType: "json",
        //begintime:begintime,endtime:endtime,
        data: {customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:QJPTIME,CONtype:CONtype,SOtype:PTYPE,PPID:PID},
        success: function (result) {
            //class='display'
            var length=result.length;
            if(PTYPE==4){
                QJPTYPE=5;
                for(var i=0;i<(length-2);i++){
                    for(var j=(i+1);j<(length-2);j++){
                        var keyi=result[i][0];
                        var vali=result[i][1];
                        var PIDi=result[i][2];
                        var SAli=result[i][3];
                        if(parseInt(vali)<parseInt(result[j][1])){
                            result[i][0]=result[j][0];
                            result[i][1]=result[j][1];
                            result[i][2]=result[j][2];
                            result[i][3]=result[j][3];
                            result[j][0]=keyi;
                            result[j][1]=vali;
                            result[j][2]=PIDi;
                            result[j][3]=SAli;
                        }
                    }
                }
            }
            var Str="<input id='Pksize' class='display'  value="+(length-2)+">";
            for(var i=0;i<(length-2);i++){
                var keyi=result[i][0];
                keyi=replaceTl(keyi);
                keyi=replaceTp(keyi);
                keyi=replaceTq(keyi);
                Str=Str+"<input id='Pkey"+i+"' class='display' value="+keyi+"><input id='Pval"+i+"' class='display' value="+result[i][1]+">";
                Str=Str+"<input id='PPID"+i+"' class='display' value="+result[i][2]+">";
                Str=Str+"<input id='PSAl"+i+"' class='display' value="+result[i][3]+">";
                
            }
            $("#TPsale").html(Str);
            $("#TPbegintime").val(result[length-2]);
            $("#TPendtime").val(result[length-1]);
            TPsaleIchartjs();


             
        }
    });
}
function search_SP(obj){
    QJPTIME=$("#PSTYPE").val();
    var ktitle=$(obj).val();
    var CONtype=$("#status_P").val();
    if(ktitle=="搜 索"){
        PST=0;
        PTYPE=4;
        QJPTYPE=0;
        TPsale();
    }
    if(ktitle=="刷新数据"){
        PST=0;
        PTYPE=4;
        QJPTYPE=0;
        $("#TPbegintime").val('');
        TPsale();
    }
    if(ktitle=="转曲线图"){
        QJPTYPE=0;
        TPsaleIchartjs();
    }
    if(ktitle=="转柱形图"){
        QJPTYPE=5;
        TPsaleIchartjs();
    }
    if(ktitle=="转销售图"){
        PST=1;
        TPsaleIchartjs();
    }
    if(ktitle=="转数量图"){
        PST=0;
        TPsaleIchartjs();
    }
    if(ktitle=="列表查看"){
        QJPTYPE=6;
        TPsaleIchartjs();
    }
    if(ktitle=="详细查看"){
        QJPTYPE=7;
        search_LB(obj,CONtype);
    }
    if(ktitle=="导出订单"){
        QJPTYPE=8;
        excel_OD(obj,CONtype);
    }
    if(ktitle=="导出飞豆"){
        QJPTYPE=9;
        excel_FD(obj,CONtype);
    }
}
function TPsaleIchartjs(){
            //======================(title动态赋值)==========
            var title="TTTTTTTTTTTTTTTTTTTTTT";
            var currencyUnit = $('#currency-unit').val();
            var ADDtitle=PPNAME+"产品销售数量统计";
                if(PST==1){
                    ADDtitle=PPNAME+"产品销售额统计";
                }else{
                    ADDtitle=PPNAME+"产品销售数量统计";
                }
            if(PTYPE==4){
                if(PST==1){
                    ADDtitle="产品销售额统计";
                }else{
                    ADDtitle="产品销售数量统计";
                }
                
            }
            if(QJPTIME==1){
                title=$("#TPbegintime").val()+'~'+$("#TPendtime").val()+ADDtitle;
            }else if(QJPTIME==2 ){
                var Month=$("#TPbegintime").val();
                var year=Month.substring(0,Month.indexOf("-"));
                Month=Month.substring(Month.indexOf("-")+1,Month.lastIndexOf("-"));
                var Q="";
                if(parseInt(Month)==1){
                    Q="第一季度";
                }else if(parseInt(Month)==4){
                    Q="第二季度";
                }else if(parseInt(Month)==7){
                    Q="第三季度";
                }else if(parseInt(Month)==10){
                    Q="第四季度";
                }
                title=year+'年'+Q+ADDtitle;
            }else if(QJPTIME==3 ){
                var year=$("#TPbegintime").val();
                year=year.substring(0,year.indexOf("-"));
                title=year+'年度'+ADDtitle;
            }else if(QJPTIME==4 ){
                var year=$("#TPbegintime").val();
                title=year+'本周'+ADDtitle;
            }
            //=======================(正文)========================
            var labels = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
            var data1 = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];
            var size=$("#Pksize").val();
            var TTets=['一','二','三','四','五','六','天'];
            var Tlabels=[];
            var Slabels=[];
            if(size>0){
                labels = [];
                data1 = [];
            }
            var total=0;
            var max=0;

            if(QJPTYPE<5){
            for(var i=0;i<size;i++){
                var key=$("#Pkey"+i).val();
                var val=$("#Pval"+i).val();
                var SAl=$("#PSAl"+i).val();
                total+=parseInt(val*100);
                //PTYPE==4
                    if(PTYPE==4){
                        if(i<25){
                            labels.push("");
                            Slabels.push(key);
                            Tlabels.push(key);
                            val=parseInt(val*100);
                            val=val/100;
                            //data1.push(val);
                            if(PST==1){
                                SAl=parseInt(SAl*100);
                                SAl=SAl/100;
                                if(max<parseInt(SAl)){
                                max=parseInt(SAl);
                                }
                                data1.push(SAl);
                            }else{
                                if(max<parseInt(val)){
                                max=parseInt(val);
                                }
                                data1.push(val);
                            }
                        }else{
                            break;
                        }
                    }else{
                    if(QJPTIME==1){
                        labels.push(key.substring(key.lastIndexOf("-")+1)); 
                        Slabels.push(key);  
                    }
                    if(QJPTIME==2 || QJPTIME==3){
                        labels.push(key.substring(key.indexOf("-")+1,key.lastIndexOf("-")));    
                        Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                    }
                    if(QJPTIME==4){
                        labels.push(key.substring(key.indexOf("-")+1)+',星期'+TTets[i]);  
                        Slabels.push(key);  
                    }
                        Tlabels.push(key);
                        val=parseInt(val*100);
                        val=val/100;
                        //data1.push(val);
                        if(PST==1){
                            SAl=parseInt(SAl*100);
                            SAl=SAl/100;
                            if(max<parseInt(SAl)){
                            max=parseInt(SAl);
                            }
                            data1.push(SAl);
                        }else{
                            if(max<parseInt(val)){
                            max=parseInt(val);
                            }
                            data1.push(val);
                        }
                    }
            
                
                }

            
            //==================（坐标轴单位）==============
            var XZ="";
            if(QJPTIME==1){
                XZ="号";
            }else if(QJPTIME==2 ){
                XZ="月";
            }else if(QJPTIME==3 ){
                XZ="月";
            }
            if(PTYPE==4){
                XZ="客户";
            }
            //==================（注解解释）==============
            
            //=================(计算Y轴值)==================
            total=total/100;
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            
            //===================================
                var data = [
                        {
                            name : 'PV',
                            value:data1,
                            color:'#06A7E1',
                            line_width:1.5
                        }
                     ];
            var chart = new iChart.LineBasic2D({
                render : 'PcanvasDiv',
                data: data,
                align:'center',
                title : {
                    text:title,
                    font : '微软雅黑',
                    fontsize:24,
                    color:'#333333'
                },
                subtitle : {
                    text:'总订单：'+total+'单',
                    font : '微软雅黑',
                    color:'#333333'
                },
        
                width : 800,
                height : 400,
                shadow:false,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-60,
                    color : '#666666'
                },
                shadow_color : '#202020',
                shadow_blur : 8,
                shadow_offsetx : 0,
                shadow_offsety : 0,
                background_color:'#fff',
                tip:{
                    enable:true,
                    shadow:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            var kturn= "<span style='color:#005268;font-size:12px;'>"+Slabels[i]+"<br/>数量:"+
                            " </span><span style='color:#005268;font-size:20px;'>"+value+"个</span>";
                            if(PST==1){
                                kturn= "<span style='color:#005268;font-size:12px;'>"+Slabels[i]+"<br/>销售额:"+
                            " </span><span style='color:#005268;font-size:20px;'>"+value+currencyUnit+"</span>";
                            }
                            return kturn;
                        }
                    }
                },
                crosshair:{
                    enable:true,
                    line_color:'#dadada'
                },
                sub_option : {
                    smooth : true,
                    label:false,
                    hollow:false,
                    hollow_inside:false,
                    point_size:8
                },
                coordinate:{
                    width:640,
                    height:260,
                    striped_factor : 0.5,
                    grid_color:'#dadada',
                    axis:{
                        color:'#9f9f9f',
                        width:[0,0,2,2]
                    },
                    scale:[{
                         position:'left',   
                         start_scale:0,
                         end_scale:step*5,
                         scale_space:step,
                         scale_size:2,
                         scale_enable : false,
                         label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                         scale_color:'#333333'
                    },{
                         position:'bottom', 
                         label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                         scale_enable : false,
                         labels:labels
                    }]
                }
            });
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var FText="数量(个)";
                        if(PST==1){
                            FText="销售额("+currencyUnit+")";
                        }
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx') ,
                            y = coo.get('originy'),
                            w = coo.width,
                            h = coo.height;
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText(FText,x-40,y-12,false,'#333333')
                        .textBaseline('top')
                        .fillText('('+XZ+')',x+w+12,y+h+10,false,'#333333');
                        
                    }
            }));
        //开始画图
        chart.draw();
        }
        if(QJPTYPE==5){
            var data = [];
            var ki=0;
            var total31=0;
            for(var i=0;i<size;i++){
                ki=i;
                var key=$("#Pkey"+i).val();
                var val=$("#Pval"+i).val();
                total+=parseInt(val*100);
                if(PST!=1){
                    if(max<parseInt(val)){
                        max=parseInt(val);
                    }
                }
                val=parseInt(val*100);
                val=val/100;
                
                if(PTYPE==4){
                    if(i<25){
                    var PPID=$("#PPID"+i).val();
                    Slabels.push(key);  
                    if(PST==1){
                        val=$("#PSAl"+i).val();
                        if(max<parseInt(val)){
                            max=parseInt(val);
                        }
                        val=parseInt(val*100);
                        val=val/100;
                    }
                    var kd={name : key,value : val,color:'#06A7E1',PPID:PPID};
                    data.push(kd);
                    }else{
                    total31+=parseInt(val);
                    }
                }else{
                if(QJPTIME==1){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1);  
                    if(PST==1){
                        val=$("#PSAl"+i).val();
                        if(max<parseInt(val)){
                            max=parseInt(val);
                        }
                        val=parseInt(val*100);
                        val=val/100;
                    }
                    var kd={name : key+'号',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                if(QJPTIME==2 || QJPTIME==3){
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                    key=key.substring(key.indexOf("-")+1,key.lastIndexOf("-")); 
                    if(PST==1){
                        val=$("#PSAl"+i).val();
                        if(max<parseInt(val)){
                            max=parseInt(val);
                        }
                        val=parseInt(val*100);
                        val=val/100;
                    }
                    var kd={name : key+'月',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                    if(QJPTIME==4){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1)+',星期'+TTets[i];   
                    if(PST==1){
                        val=$("#PSAl"+i).val();
                        if(max<parseInt(val)){
                            max=parseInt(val);
                        }
                        val=parseInt(val*100);
                        val=val/100;
                    }
                    var kd={name : key,value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                }
                
                 Tlabels.push(key);
            }   
            if(total31>0){
                Slabels.push('其他'); 
                if(max<parseInt(total31)){
                    max=parseInt(total31);
                }
                var kd={name : '其他',value : total31,color:'#06A7E1'};
                data.push(kd);
            }
            if(PTYPE==4 && ki<5){
                for(var i=(ki+2);i<6;i++){
                    Slabels.push( '产品('+i+')'); 
                    var kd={name : '产品('+i+')',value : 0,color:'#06A7E1'};
                    data.push(kd);
                }
            }
            //=================(计算Y轴值)==================
            total=total/100;
            
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            //===================================       
            
            
            var chart = new iChart.Column2D({
                render : 'PcanvasDiv',
                data : data,
                title : {
                    text : title,
                    font:'微软雅黑',
                    color : '#333333'
                },
                subtitle : {
                    text : '总销售数量：'+total+'个',
                    font:'微软雅黑',
                    color : '#333333'
                },
        
                width : 800,
                height : 400,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-30,
                    color : '#333333'
                },
                tip:{
                    enable:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            //将数字进行千位格式化
                            var f = new String(value);
                            
                            
                            return '商品:'+Slabels[i]+"<br/>销售数量:"+f+"个<br/>占百分比:"+(value/this.get('total') * 100).toFixed(2)+'%';
                        }
                    }
                },
                shadow : true,
                shadow_blur : 2,
                shadow_color : '#aaaaaa',
                shadow_offsetx : 1,
                shadow_offsety : 0,
                column_width : 62,
                sub_option : {
                    listeners:{
                            /**
                             * r:iChart.Sector2D对象
                             * e:eventObject对象
                             * m:额外参数
                             */
                            click:function(s,e,m){
                                if(PTYPE==4){
                                    PTYPE=3;
                                    QJPTYPE=0;
                                    PID=s.get('PPID');
                                    PPNAME=s.get('name');
                                    TPsale();
                                }
                                
                            }
                        }
                },
                coordinate : {
                    background_color : null,
                    grid_color : '#dadada',
                    width : 660,
                    height:240,
                    axis : {
                        color : '#9f9f9f',
                        width : [0, 0, 1, 0]
                    },
                    scale : [{
                        position : 'left',
                        start_scale : 0,
                        end_scale : step*5,
                        scale_space : step,
                        scale_enable : false,
                        label : {
                            fontsize:11,
                            color : '#333333'
                        },
                        listeners:{
                            parseText:function(t,x,y){
                                return {text:t}
                            }
                         }
                    }]
                }
            });
            
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy');
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText('数量(个)',x-40,y-10,false,'#333333');
                        
                    }
            }));
    
            chart.draw();
        }
        if(QJPTYPE==6){
             $("#PcanvasDiv").html("");
                
             var total=0;
             for(var i=0;i<size;i++ ){
                 var VAL=$("#Pval"+i).val();
                 if(PST==1){
                    VAL=$("#PSAl"+i).val();
                }
                VAL=Number(VAL);
                VAL=VAL.toFixed(2);
                total+=parseInt(VAL*100);
             }
             total=total/100;
            //Str+="</div>";
            var Str="<div class='WSY_weixinbox' id='TPSHOW' style='width:100%;height:100%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;margin-left: 0px;margin-top: 0px;'>";
            Str+="<div class='WSY_weixin'>";
            Str+="<a>"+title+"</a>";
            Str+="</div>";
            Str+="<div style='padding:20px;overflow:auto;height:85%;width:95%'>";
            Str+="<div style='background-color: #FFE7BA;border-radius: 10px;'>";
            Str+=" <table width='100%' border='0' cellspacing='0' cellpadding='0'>";
            Str+="<thead>";
            Str+="<tr style='line-height: 30px;background-color: rgb(6, 167, 225);'>";
            if(PTYPE==3){
                if(QJPTIME<2){
                    Str+="<th scope='col'>时间(日)</th>";
                }
                if(QJPTIME>1){
                    Str+="<th scope='col'>时间(月)</th>";
                }
            }else{
                if(QJPTIME<2){
                    Str+="<th scope='col' style='display:none'>时间(日)</th>";
                }
                if(QJPTIME>1){
                    Str+="<th scope='col' style='display:none'>时间(月)</th>";
                }   
            }
            Str+="<th scope='col' style='display:none'>到期时间</th>";
            Str+="<th scope='col' style='display:none'>PID</th>";
            if(PTYPE==3){}else{
                Str+="<th scope='col' >商品名称</th>";
            }
            if(PST==1){
                    Str+="<th scope='col'>销售额</th>";
                }else{
                    Str+="<th scope='col'>销售数量</th>";
                }
            
            Str+="<th scope='col'>百分比</th>";
            Str+="<th scope='col'>查看详情</th>";
            Str+="</tr>";
            Str+="</thead>";
            Str+="<tbody>";
            
            for(var i=0;i<size;i++ ){
                Str+="<tr style='line-height: 30px;'>";
                var KEY=$("#Pkey"+i).val();
                if(QJPTIME>1 && QJPTIME<4){
                    KEY=KEY.substring(0,KEY.lastIndexOf("-"));  
                }
                var ksize=size-1;
                if(i<ksize){
                    var k=i+1;
                    var JKEY=$("#Pkey"+k).val();
                    if(QJPTIME>1  && QJPTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                if(i==ksize){
                    var JKEY=$("#TPendtime").val();
                    if(QJPTIME>1  && QJPTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                
                var VAL=$("#Pval"+i).val();
                if(PST==1){
                    VAL=$("#PSAl"+i).val();
                }
                var BFB=(VAL/total)*100;
                VAL=parseInt(VAL*100);
                VAL=VAL/100;
                if(PST==1){
                    VAL=VAL.toFixed(2);
                }
                BFB=BFB.toFixed(2);
                BFB=BFB+'%';
                if(total==0){
                    BFB='100%';
                }
                if(PTYPE==3){}else{
                    KEY=$("#TPbegintime").val();
                    JKEY=$("#TPendtime").val();
                }
                if(PTYPE==3){
                Str+="<td style='text-align:center' valign='middle' >"+KEY+"</td>"; 
                }else{
                Str+="<td valign='middle' style='display:none'>"+KEY+"</td>";   
                }
                
                Str+="<td  valign='middle'  style='display:none'>"+JKEY+"</td>";
                var TPID=$("#PPID"+i).val();
                Str+="<td  valign='middle'  style='display:none'>"+TPID+"</td>";
                if(PTYPE==3){}else{
                    var KPname =$("#Pkey"+i).val();
                    Str+="<td  style='text-align:center'  valign='middle'>"+KPname+"</td>";
                }
                Str+="<td style='text-align:center' valign='middle' >"+VAL+"</td>";
                Str+="<td style='text-align:center' valign='middle' >"+BFB+"</td>";
                Str+="<td style='text-align:center' valign='middle' >" +
                    "<a onclick='SEE_TP(this)' class='wsy_cost_style' title='列表查看' style='margin:2px;cursor:pointer;'><img src='../../Common/images/Data/qushiicon/btn_08.png' style='margin-top: 5px;' /></a>" +
                    "</td>";
                Str+="</tr>";
            }
            Str+="<tr style='line-height: 30px;'>";
            Str+="<td style='text-align:center' valign='middle' > 总计</td>";
            Str+="<td style='text-align:center' valign='middle' >"+total+"</td>";
            Str+="</tr>";
            Str+="</tbody>";
            Str+="</table>";
            Str+="</div>";
            Str+="</div>";
            Str+="</div>";
            $("#PcanvasDiv").html(Str);
            $("#TPSHOW").css({width:0,height:0});
            $("#TPSHOW").animate({width:800,height:400},"slow");    
        }
    }
function SEE_TP(obj){
    var DOCobj=$(obj).parent().parent();
    var STIME=DOCobj.children("td").eq(0).html();
    var ETIME=DOCobj.children("td").eq(1).html();
    var CONtype=$("#status_P").val();
    if(QJPTIME>1){
        STIME+="-01";
        ETIME+="-01";
    }
    var PSID=$("#customer_id").val();
    var TPID=DOCobj.children("td").eq(2).html();
    var begintime=STIME;
    var endtime =ETIME;
    var province = "";
    var city = "" ;
    var area = "";
    var search_status = CONtype;
    var url="../Ostatistics/order_BarChart_detailed.php?customer_id="+PSID+"&search_status="+search_status;
    
    if(province !=""){
        url=url+'&province='+province;
    }
    if(city !=""){
        url=url+'&city='+city;
    }
    if(area !=""){
        url=url+'&area='+area;
    }
    if(begintime !=""){
        url=url+'&begintime='+begintime;
    }
    if(endtime !=""){
        url=url+'&endtime='+endtime;
    }
    if(TPID !=""){
        url=url+'&PID='+TPID;
    }
    document.location=url;
}
//<==================================(产品统计结束)================================================>
//<==================================(推广员统计)================================================>
function TGpromoters(){ 
        var begintime=$("#TGbegintime").val();
        var endtime=$("#TGendtime").val();
        var customer_id=$("#customer_id").val();
        QJGTIME=$("#GTYPE").val();
    $.ajax({
        type: "post",
        url: "get_BaseStatistics.php",
        dataType: "json",
        //begintime:begintime,endtime:endtime,
        data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:QJGTIME,SOtype:5},
        success: function (result) {
            //alert(result);
            //class='display'
            var length=result.length;
            var Str="<input id='Gsize' class='display'  value="+(length-3)+">";
            for(var i=0;i<(length-3);i++){
                Str=Str+"<input id='Gkey"+i+"' class='display' value="+result[i][0]+"><input id='Gval"+i+"' class='display' value="+result[i][1]+">";
                Str=Str+"<input id='GVAI"+i+"' class='display'  value="+result[i][2]+">";
            }
            Str=Str+"<input id='total' class='display'  value="+result[length-3]+">";
            $("#TGsale").html(Str);
            $("#TGbegintime").val(result[length-2]);
            $("#TGendtime").val(result[length-1]);
            TGpromotersIchartjs();

             
        }
    });
}
function search_SG(obj){
    QJGTIME=$("#GTYPE").val();
    var ktitle=$(obj).val();
    if(ktitle=="搜 索"){
        QJGTYPE=0;
        TGpromoters();
    }
    if(ktitle=="刷新数据"){
        QJGTYPE=0;
        $("#TGbegintime").val('');
        TGpromoters();
    }
    if(ktitle=="转柱形图"){
        QJGTYPE=0;
        TGpromotersIchartjs();
    }
    if(ktitle=="转曲线图"){
        QJGTYPE=5;
        TGpromotersIchartjs();
    }
    if(ktitle=="列表查看"){
        QJGTYPE=6;
        TGpromotersIchartjs();
    }
    if(ktitle=="详细查看"){
        QJGTYPE=7;
        search_LB(obj,3);
    }

}
function TGpromotersIchartjs(){
                //======================(title动态赋值)==========
            var title="TTTT";
            var ADDtitle="推广员统计";
            if(QJGTIME==1){
                title=$("#TGbegintime").val()+'~'+$("#TGendtime").val()+ADDtitle;
            }else if(QJGTIME==2 ){
                var Month=$("#TGbegintime").val();
                var year=Month.substring(0,Month.indexOf("-"));
                Month=Month.substring(Month.indexOf("-")+1,Month.lastIndexOf("-"));
                var Q="";
                if(parseInt(Month)==1){
                    Q="第一季度";
                }else if(parseInt(Month)==4){
                    Q="第二季度";
                }else if(parseInt(Month)==7){
                    Q="第三季度";
                }else if(parseInt(Month)==10){
                    Q="第四季度";
                }
                title=year+'年'+Q+ADDtitle;
            }else if(QJGTIME==3 ){
                var year=$("#TGbegintime").val();
                year=year.substring(0,year.indexOf("-"));
                title=year+'年度'+ADDtitle;
            }else if(QJGTIME==4 ){
                var year=$("#TGbegintime").val();
                title=year+'本周'+ADDtitle;
            }
            //=======================(正文)========================
            var labels = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
            var data1 = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];
            var data2 = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];
            var data3 = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];

            var size=$("#Gsize").val();
            var TTets=['一','二','三','四','五','六','天'];
            var Tlabels=[];
            var Slabels=[];
            if(size>0){
                labels = [];
                data1 = [];
                data2 = [];
                data3 = [];
            }
            var total=0;
            total=$("#total").val();
            var max=0;
            
            for(var i=0;i<size;i++){
                var key=$("#Gkey"+i).val();
                var val=$("#Gval"+i).val();
                var VAI=$("#GVAI"+i).val();
            
                if(QJGTIME==1){
                    if(key.substring(key.lastIndexOf("-")+2)==5 || key.substring(key.lastIndexOf("-")+2)==0){
                        labels.push(key);   
                    }else{
                        labels.push("");    
                    }
                    
                    Slabels.push(key+"号");  
                }
                if(QJGTIME==2 || QJGTIME==3){
                    labels.push(key.substring(0,key.lastIndexOf("-"))+'月'); 
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                    
                }
                if(QJGTIME==4){
                    labels.push(key.substring(key.indexOf("-")+1)+',星期'+TTets[i]);  
                    Slabels.push(key);  
                }
                
            
                Tlabels.push(key);
                val=parseInt(val);
                VAI=parseInt(VAI);
                if(max<parseInt(val+VAI)){
                    max=parseInt(val+VAI);
                }
                data1.push(val);
                data2.push(VAI);
                data3.push(val+VAI);
            }
            //==================（坐标轴单位）==============
            var XZ="";
            if(QJGTIME==1){
                XZ="号";
            }else if(QJGTIME==2 ){
                XZ="月";
            }else if(QJGTIME==3 ){
                XZ="月";
            }
            if(PTYPE==4){
                XZ="客户";
            }
            //==================（注解解释）==============
            
            //=================(计算Y轴值)==================

            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
             var  ki=0;
            if(QJGTYPE<5){
            //===================================
            
            var data = [
                        {
                            name : '原始推广员',
                            value:data2,
                            color:'#06A7E1'
                        },
                        {
                            name : '新增推广员',
                            value:data1,
                            color:'#E4214A'
                        }
                     ];
            var chart = new iChart.ColumnStacked3D({
                    render : 'GcanvasDiv',
                    data: data,
                    labels:labels,
                    title : {
                        text : title,
                        font:'微软雅黑',
                        color : '#333333'
                    },
                    subtitle : {
                        text : '总推广员：'+total+'个',
                        font:'微软雅黑',
                        color : '#333333'
                    },
                    
                    width : 800,
                    height : 400,
                    column_width:70,
                    //animation : true,//开启过渡动画
                    background_color : '#fff',
                    shadow : true,
                    shadow_blur : 3,
                    shadow_color : '#aaaaaa',
                    shadow_offsetx : 1,
                    shadow_offsety : 0, 
                    sub_option:{
                        label:false,
                        listeners:{
                            /**
                             * r:iChart.Sector2D对象
                             * e:eventObject对象
                             * m:额外参数
                             */
                            click:function(s,e,m){
                                
                            }
                        }                       
                    },
                    label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-30,
                    color : '#333333'
                    },
                    legend:{
                        enable:true,
                        background_color : null,
                        line_height:25,
                        color:'#333333',
                        fontsize:12,
                        fontweight:600,
                        border : {
                            enable : false
                        },
                        offsety:-180
                    },
                    
                
                    
                    tip:{
                        enable :true,
                        listeners:{
                        //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                            parseText:function(tip,name,value,text,i){
                                if(i==0){
                                    ki++;
                                }
                                return Slabels[ki-1]+"<br/><span style='color:#06A7E1'>原有推广员:"+data2[ki-1]+ "个</span><br/><span  style='color:#E4214A'>新增推广员:"+data1[ki-1]+ '个</span><br/>总推广员:'+(parseInt(data2[ki-1])+parseInt(data1[ki-1]));
                            }
                        } 
                    },
                    text_space : 16,//坐标系下方的label距离坐标系的距离。
                    zScale:0.8,
                    xAngle : 50,
                    bottom_scale:1.1, 
                    coordinate:{
                        width:'74%',
                        height:'80%',
                        board_deep:10,//背面厚度
                        pedestal_height:10,//底座高度
                        left_board:false,//取消左侧面板 
                        shadow:true,//底座的阴影效果
                        grid_color:'#6a6a80',//网格线
                        wall_style:[{//坐标系的各个面样式
                        color : '#6a6a80'
                        },{
                        color : '#C2C2C2'
                        }, {
                        color : '#a6a6cb'
                        },{
                        color : '#6a6a80'
                        },{
                        color : '#74749b'
                        },{
                        color : '#a6a6cb'
                        }], 
                        axis : {
                            color : '#c0d0e0',
                            width : 0
                        }, 
                        scale:[{
                             position:'left',   
                             scale_enable : false,
                             start_scale:0,
                             scale_space:step,
                             end_scale:step*5,
                             label:{color:'#254d70',fontsize:11,fontweight:600}
                        }]
                    }
            });

            //利用自定义组件构造左上侧单位
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy');
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('end')
                        .textBaseline('bottom')
                        .textFont('600 12px 微软雅黑')
                        .fillText('单位(个)',x+10,y-20,false,'#333333')
                        
                        
                    }
            }));
            
            chart.draw();
            }
            if(QJGTYPE==5){
                var data = [
                            {
                                name : '原始推广员',
                                value:data2,
                                color:'#06A7E1',
                                line_width:2
                            },
                            {
                                name : '新增推广员',
                                value:data1,
                                color:'#E4214A',
                                line_width:2
                            },
                            {
                                name : '现有推广员',
                                value:data3,
                                color:'#68ba17',
                                line_width:2
                            }
                         ];
                 
                var labels = labels;
                
                var line = new iChart.LineBasic2D({
                    render : 'GcanvasDiv',
                    data: data,
                    align:'center',
                        title : {
                        text : title,
                        font:'微软雅黑',
                        color : '#333333'
                    },
                    subtitle : {
                        text : '总销推广员：'+total+'个',
                        font:'微软雅黑',
                        color : '#333333'
                    },
                
                    width : 800,
                    height : 400,
                    //animation : true,//开启过渡动画
                    sub_option:{
                        label:false,
                        smooth : true,//平滑曲线
                        hollow_inside:false,
                        point_size:3
                    },
                    tip:{
                        enable :true,
                        listeners:{
                            //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                            parseText:function(tip,name,value,text,i){
                                return Slabels[i]+"<br/>"+name+":"+value+ '个';
                            }
                        } 
                    },
                    legend : {
                        enable : true,
                        offsety:-180
                    },
                    crosshair:{
                        enable:true,
                        line_color:'#dadada'
                    },
                    coordinate:{
                        width:600,
                        valid_width:580,
                        height:260,
                        axis:{
                            color:'#9f9f9f',
                            width:[0,0,2,2]
                        },
                        grids:{
                            vertical:{
                                way:'share_alike',
                                value:12
                            }
                        },
                        scale:[{
                             position:'left',   
                             start_scale:0,
                             end_scale:step*5,
                             scale_space:step,
                             scale_size:2,
                             scale_color:'#9f9f9f'
                        },{
                             position:'bottom', 
                             labels:labels
                        }]
                    }
                });
            //开始画图
            line.draw();
            }
    
    
}
//<==================================(推广员统计结束)================================================>
//<==================================(粉丝统计)================================================>
function TGfens(){  
        var begintime=$("#TFbegintime").val();
        var endtime=$("#TFendtime").val();
        var customer_id=$("#customer_id").val();
        QJFTIME=$("#FSTYPE").val();
        $.ajax({
            type: "post",
            url: "get_BaseStatistics.php",
            dataType: "json",
            //begintime:begintime,endtime:endtime,
            data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:QJFTIME,SOtype:6},
            success: function (result) {
                //class='display'
                var length=result.length;
                var Str="<input id='Fsize' class='display'  value="+(length-4)+">";
                for(var i=0;i<(length-4);i++){
                    Str=Str+"<input id='Fkey"+i+"' class='display' value="+result[i][0]+"><input id='Fval"+i+"' class='display' value="+result[i][1]+">";
                    Str=Str+"<input id='FVAI"+i+"' class='display'  value="+result[i][2]+">";
                    Str=Str+"<input id='FVAK"+i+"' class='display'  value="+result[i][3]+">";
                }
                Str=Str+"<input id='Ftol' class='display'  value="+result[length-4]+">";
                Str=Str+"<input id='Ftal' class='display'  value="+result[length-3]+">";
                if (!index){
                    index = result[length-3];
                }
                $("#TFsale").html(Str);
                $("#TFbegintime").val(result[length-2]);
                $("#TFendtime").val(result[length-1]);
                TGfensIchartjs();


            }
        });
}
function search_SF(obj){
    QJFTIME=$("#GTYPE").val();
    var ktitle=$(obj).val();
    if(ktitle=="搜 索"){
        QJFTYPE=0;
        TGfens();
    }
    if(ktitle=="刷新数据"){
        QJFTYPE=0;
        $("#TFbegintime").val('');
        TGfens();
    }
    if(ktitle=="转柱形图"){
        QJFTYPE=0;
        TGfensIchartjs();
    }
    if(ktitle=="转曲线图"){
        QJFTYPE=5;
        TGfensIchartjs();
    }
    if(ktitle=="列表查看"){
        QJFTYPE=6;
        TGfensIchartjs();
    }
    if(ktitle=="详细查看"){
        QJFTYPE=7;
        search_LB(obj,3);
    }

}
function  TGfensIchartjs(){
        //======================(title动态赋值)==========
        
            var title="TTTT";
            var ADDtitle="粉丝统计";
            if(QJFTIME==1){
                title=$("#TFbegintime").val()+'~'+$("#TFendtime").val()+ADDtitle;
            }else if(QJFTIME==2 ){
                var Month=$("#TFbegintime").val();
                var year=Month.substring(0,Month.indexOf("-"));
                Month=Month.substring(Month.indexOf("-")+1,Month.lastIndexOf("-"));
                var Q="";
                if(parseInt(Month)==1){
                    Q="第一季度";
                }else if(parseInt(Month)==4){
                    Q="第二季度";
                }else if(parseInt(Month)==7){
                    Q="第三季度";
                }else if(parseInt(Month)==10){
                    Q="第四季度";
                }
                title=year+'年'+Q+ADDtitle;
            }else if(QJFTIME==3 ){
                var year=$("#TFbegintime").val();
                year=year.substring(0,year.indexOf("-"));
                title=year+'年度'+ADDtitle;
            }else if(QJGTIME==4 ){
                var year=$("#TFbegintime").val();
                title=year+'本周'+ADDtitle;
            }
            //==========================
            var size=$("#Fsize").val();
            var labels=["MAY\n2011","JUN\n2011","JUL\n2011","AUG\n2011","SEP\n2011","SEP\n2011","OCT\n2011","DEC\n2011","JAN\n2011"];
            var Tabels=["MAY\n2011","JUN\n2011","JUL\n2011","AUG\n2011","SEP\n2011","SEP\n2011","OCT\n2011","DEC\n2011","JAN\n2011"];
            var Sabels=["MAY\n2011","JUN\n2011","JUL\n2011","AUG\n2011","SEP\n2011","SEP\n2011","OCT\n2011","DEC\n2011","JAN\n2011"];
            var data1=["0","0","0","0","0","0","0","0","0"];
            var data2=["0","0","0","0","0","0","0","0","0"];
            var data3=["0","0","0","0","0","0","0","0","0"];
            var TTets=['一','二','三','四','五','六','天'];
            if(size>0){
                Sabels=[];
                Tabels=[];
                labels=[];
                data1=[];
                data2=[];
                data3=[];
            }
            var Lmax=0;
            var Rmax=0;
            for(var i=0;i<size;i++){
                var key=$("#Fkey"+i).val();
                Tabels.push(key);
                var val=$("#Fval"+i).val();
                var vai=$("#FVAI"+i).val();
                var vak=$("#FVAK"+i).val();
                if(QJFTIME==1){
                    if(key.substring(key.lastIndexOf("-")+2)==5 || key.substring(key.lastIndexOf("-")+2)==0 || key.substring(key.lastIndexOf("-")+1)=='01'){
                        labels.push(key);   
                    }else{
                        labels.push("");    
                    }
                    Sabels.push(key+"号");   
                }
                if(QJFTIME==2 || QJFTIME==3){
                    labels.push(key.substring(0,key.lastIndexOf("-"))+'月'); 
                    Sabels.push(key.substring(0,key.lastIndexOf("-"))+'月'); 
                    
                }
                if(QJFTIME==4){
                    labels.push(key.substring(key.indexOf("-")+1)+',星期'+TTets[i]);  
                    Sabels.push(key);   
                }
                val=parseInt(val);
                vai=parseInt(vai);
                if(Lmax<parseInt(val+vai)){
                    Lmax=parseInt(val+vai);
                }
                vak=parseInt(vak);
                if(Rmax<parseInt(vak)){
                    Rmax=parseInt(vak);
                }
                data1.push(val);
                data2.push(vai);
                var kf=false;
                if(QJFTIME==4 || QJFTIME==1){
                var myDate = new Date();
                var Nyear=myDate.getFullYear(); 
                var NMonth=myDate.getMonth();  
                var NDate=myDate.getDate();
                var Eendtime=key;
                var Eyear=Eendtime.substring(0,Eendtime.indexOf("-"));
                var EMonth=Eendtime.substring(Eendtime.indexOf("-")+1,Eendtime.lastIndexOf("-"));
                var EDate=Eendtime.substring(Eendtime.lastIndexOf("-")+1);
                if(parseInt(Eyear)>parseInt(Nyear)){
                    kf=true;
                }else if(parseInt(Eyear)==parseInt(Nyear)){
                    if(parseInt(EMonth)>(parseInt(NMonth)+1)){
                        kf=true;
                    }else if(parseInt(EMonth)==(parseInt(NMonth)+1)){
                        if(parseInt(EDate)>parseInt(NDate)){
                            kf=true;
                        }
                    }
                }
                }
                if(kf){
                }else{
                    if(parseInt(vak)==0){
                        vak=0.1;
                    }
                }
                data3.push(vak);
            }
            //=================(计算Y轴值)==================

            var TLmax=Lmax;
            var TL=0;
            var KLmax=1;
            for(var i=0;i<100;i++){
                TLmax=parseInt(TLmax/10);
                if(TLmax<10){
                    TL=i;
                    break;
                }
            }
            TLmax=(TLmax+1)*10;
            for(var i=0;i<TL;i++){
                KLmax=KLmax*10;
            }
            var Lstep=(parseInt(TLmax/5)*KLmax);
            if(Lstep==0){
                Lstep=1;
            }
            
            var TRmax=Rmax+Lstep;
            var TR=0;
            var KRmax=1;
            for(var i=0;i<100;i++){
                TRmax=parseInt(TRmax/10);
                if(TRmax<10){
                    TR=i;
                    break;
                }
            }
            TRmax=(TRmax+1)*10;
            for(var i=0;i<TR;i++){
                KRmax=KRmax*10;
            }
            var Rstep=(parseInt(TRmax/5)*KRmax);
            if(Rstep==0){
                Rstep=1;
            }
            
            var data = [
                        {
                            name : '原始粉丝',
                            value:data2,
                            color:'#06A7E1'
                        },
                        {
                            name : '新增粉丝',
                            value:data1,
                            color:'#E4214A'
                        }
                     ];
                    
            var data4 = [
                            {
                                name : '关注粉丝',
                                value:data3,
                                color:'#68ba17',
                                line_width:5
                            }
                       ];
               
            var chart = new iChart.ColumnStacked2D({
                    render : 'FcanvasDiv',
                    data: data,
                    labels:labels,
                    title : {
                        text:title,
                        color:'#333333',
                        textAlign:'left',
                        padding:'0 40',
                        font:'微软雅黑',
                        border:{
                            enable:true,
                            width:[0,0,4,0],
                            color:'#698389'
                        },
                        height:40
                    },
                    
                    padding:'8 0',
                    width : 800,
                    height : 400,
                    //animation : true,//开启过渡动画
                    column_width:70,
                    gradient : true,//应用背景渐变
                    gradient_mode:'LinearGradientDownUp',//渐变类型
                    color_factor : 0.1,//渐变因子
                    background_color : '#fff',
                    sub_option:{
                        label:false,
                        border : false
                    },
                    label : {
                    font:'微软雅黑',
                    fontweight:600,
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-30,
                    color : '#333333'
                    },
                    legend:{
                        enable:true,
                        background_color : null,
                        line_height:25,
                        color:'#333333',
                        fontsize:12,
                        font:'微软雅黑',
                        fontweight:600,
                        border : {
                            enable : false
                        },
                        offsety:-145
                    },
                    column_width:80,
                    coordinate:{
                        background_color : 0,
                        grid_color:'#dadada',
                        axis : {
                            color : '#c0d0e0',
                            width : 0
                        }, 
                        scale:[{
                             position:'left',   
                             scale_enable : false,
                             start_scale:0,
                             scale_space:Lstep,
                             end_scale:Lstep*5,
                             label:{color:'#333333',fontsize:11,fontweight:600}
                        },{
                         position:'right',  
                         scale_enable : false,
                         start_scale:0,
                         scale_space:Rstep,
                         end_scale:Rstep*5,
                         scaleAlign:'right',
                         label:{
                            color:'#68ba17'
                         },
                         listeners:{
                            parseText:function(t,x,y){
                                //自定义右侧坐标系刻度文本的格式。
                                return {text:''+t+''}
                            }
                         }
                    }
                        ],
                        width:'80%',
                        height:'70%'
                    }
            });


            //构造折线图
            var line = new iChart.LineBasic2D({
                z_index:1000,
                data: data4,
                label:{
                    color:'#4c4f48'
                },
                tip:{
                    enable :true,
                    listeners:{
                        //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            var kv=index;
                            return Sabels[i]+"<br/><span style='color:#06A7E1'>原始粉丝:"+data2[i]+"</span><br/><span style='color:#E4214A'>新增粉丝:"+data1[i]+"</span><br/><span style='color:#68ba17'>"+name+":"+parseInt(value)+ '</span><br/>总粉丝数:'+kv;
                        }
                    } 
                },
                listeners:{
                /**
                * d:相当于data[0],即是一个线段的对象
                * v:相当于data[0].value
                * x:计算出来的横坐标
                * x:计算出来的纵坐标
                * j:序号 从0开始
                */
                
                parsePoint:function(d,v,x,y,j){
                    //利用序号进行过滤春节休息期间 
                    if(QJFTIME==1 || QJFTIME==4){
                    var kft=false;
                    var myDate = new Date();
                    var Nyear=myDate.getFullYear(); 
                    var NMonth=myDate.getMonth();  
                    var NDate=myDate.getDate();
                    var Eendtime=$("#TFendtime").val();
                    var Eyear=Eendtime.substring(0,Eendtime.indexOf("-"));
                    var EMonth=Eendtime.substring(Eendtime.indexOf("-")+1,Eendtime.lastIndexOf("-"));
                    var EDate=Eendtime.substring(Eendtime.lastIndexOf("-")+1);
                    if(parseInt(Eyear)>parseInt(Nyear)){
                        kft=true;
                    }else if(parseInt(Eyear)==parseInt(Nyear)){
                        if(parseInt(EMonth)>(parseInt(NMonth)+1)){
                            kft=true;
                        }else if(parseInt(EMonth)==(parseInt(NMonth)+1)){
                            if(parseInt(EDate)>parseInt(NDate)){
                                kft=true;
                            }
                        }
                    }
                    if(kft &&(v==0))
                    return {ignored:true}//ignored为true表示忽略该点
                    }else{
                        return {ignored:false}//ignored为true表示忽略该点
                    }
                }
                },
                //animation : true,//开启过渡动画
                legend:{
                        enable:true,
                        background_color : null,
                        line_height:25,
                        color:'#333333',
                        fontsize:12,
                        font:'微软雅黑',
                        fontweight:600,
                        border : {
                            enable : false
                        },
                        offsety:-145
                        
                },
                point_space:chart.get('column_width')+chart.get('column_space'),
                scaleAlign : 'right',
                sub_option : {
                    label:false,
                    point_size:5
                },
                coordinate:chart.coo//共用坐标系
            });
            
            chart.plugin(line);
            
            
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy');
                        //在左上侧的位置，渲染一个单位的文字
                        /*
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 16px 微软雅黑')
                        .fillText('总粉丝数:'+$("#Ftol").val(),x-20,y-20,false,'#c52120')
                        */

                        //在右上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('end')
                        .textBaseline('bottom')
                        .textFont('600 16px 微软雅黑')
                        .fillText('总关注粉丝数:'+index,x+coo.width,y-20,false,'#E4214A')
                        
                    }
            }));
            
            chart.draw();
    
    
}
//<==================================(粉丝统计结束)================================================>
//<==================================(本日统计)================================================>
function TDDay(){   
        var begintime=$("#TDbegintime").val();
        var endtime=$("#TDendtime").val();
        var customer_id=$("#customer_id").val();
        var search_SOtype=$("#search_SOtype").val();
        if(search_SOtype==1){
            SOtype=1;
        }
        if(search_SOtype==2){
            SOtype=5;
        }
        $.ajax({
            type: "post",
            url: "get_BaseStatistics.php",
            dataType: "json",
            //begintime:begintime,endtime:endtime,
            data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:5,SOtype:SOtype},
            success: function (result) {
                var Str="";
                if(search_SOtype==1){
                     Str+="<input id='Dsal' class='display'  value="+result[0][1]+">";
                    $.ajax({
                        type: "post",
                        url: "get_BaseStatistics.php",
                        dataType: "json",
                        //begintime:begintime,endtime:endtime,
                        data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:5,CONtype:3,SOtype:2},
                        success: function (result) {
                            if(search_SOtype==1){
                                 Str+="<input id='DSDL' class='display'  value="+result[0][1]+">";
                                 $.ajax({
                                    type: "post",
                                    url: "get_BaseStatistics.php",
                                    dataType: "json",
                                    //begintime:begintime,endtime:endtime,
                                    data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:5,CONtype:4,SOtype:2},
                                    success: function (result) {
                                        if(search_SOtype==1){
                                             Str+="<input id='DNDL' class='display'  value="+result[0][1]+">";
                                            $.ajax({
                                                type: "post",
                                                url: "get_BaseStatistics.php",
                                                dataType: "json",
                                                //begintime:begintime,endtime:endtime,
                                                data: {customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:5,CONtype:3,SOtype:4},
                                                success: function (result) {
                                                    //class='display'
                                                    var length=result.length;       
                                                    for(var i=0;i<(length-2);i++){
                                                        for(var j=(i+1);j<(length-2);j++){
                                                            var keyi=result[i][0];
                                                            var vali=result[i][1];
                                                            var PIDi=result[i][2];
                                                            var SAli=result[i][3];
                                                            if(parseInt(vali)<parseInt(result[j][1])){
                                                                result[i][0]=result[j][0];
                                                                result[i][1]=result[j][1];
                                                                result[i][2]=result[j][2];
                                                                result[i][3]=result[j][3];
                                                                result[j][0]=keyi;
                                                                result[j][1]=vali;
                                                                result[j][2]=PIDi;
                                                                result[j][3]=SAli;
                                                            }
                                                        }
                                                    }
                                                    
                                                    Str+="<input id='Dsize' class='display'  value="+(length-2)+">";
                                                    for(var i=0;i<(length-2);i++){
                                                        var keyi=result[i][0];
                                                            keyi=replaceTl(keyi);
                                                            keyi=replaceTp(keyi);
                                                            keyi=replaceTq(keyi);
                                                        Str=Str+"<input id='Dkey"+i+"' class='display' value="+keyi+"><input id='Dval"+i+"' class='display' value="+result[i][1]+">";
                                                        Str=Str+"<input id='DPID"+i+"' class='display' value="+result[i][2]+">";
                                                        Str=Str+"<input id='DPSA"+i+"' class='display' value="+result[i][3]+">";
                                                    }
                                                    $("#TDsale").html(Str);
                                                    $("#TDbegintime").val(result[length-2]);
                                                    $("#TDendtime").val(result[length-1]);
                                                    
                                                    TDDayIchartjs();
                                                    
                                                }
                                            });
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
                if(search_SOtype==2){
                     Str+="<input id='Dspr' class='display'  value="+result[0][1]+">";
                     Str+="<input id='Dzpr' class='display'  value="+result[0][2]+">";
                     $.ajax({
                        type: "post",
                        url: "get_BaseStatistics.php",
                        dataType: "json",
                        //begintime:begintime,endtime:endtime,
                        data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:5,SOtype:6},
                        success: function (result) {
                            //class='display'
                            var length=result.length;
                             Str+="<input id='Dsize' class='display'  value="+(length-4)+">";
                            for(var i=0;i<(length-4);i++){
                                Str=Str+"<input id='Dkey"+i+"' class='display' value="+result[i][0]+"><input id='Dval"+i+"' class='display' value="+result[i][1]+">";
                                Str=Str+"<input id='DVAI"+i+"' class='display'  value="+result[i][2]+">";
                                Str=Str+"<input id='DVAK"+i+"' class='display'  value="+result[i][3]+">";
                            }
                            Str=Str+"<input id='Dtol' class='display'  value="+result[length-4]+">";
                            Str=Str+"<input id='Dtal' class='display'  value="+result[length-3]+">";
                            $("#TDsale").html(Str);
                            $("#TDbegintime").val(result[length-2]);
                            $("#TDendtime").val(result[length-1]);
                            TDDayIchartjs();
                        }
                    });
                }
            }
        });
}
function search_SD(obj){
    var ktitle=$(obj).val();
    if(ktitle=="搜 索"){
        DH_addit("uli");
        QJDTYPE=0;
        TDDay();
    }
    if(ktitle=="刷新数据"){
        QJDTYPE=0;
        $("#TDbegintime").val('');
        TDDay();
    }
    if(ktitle=="转柱形图"){
        QJFTYPE=0;
        TGfensIchartjs();
    }
    if(ktitle=="转曲线图"){
        QJFTYPE=5;
        TGfensIchartjs();
    }
    if(ktitle=="列表查看"){
        QJFTYPE=6;
        TDDay_addit_LB();
    }
    if(ktitle=="详细查看"){
        QJFTYPE=7;
        search_LB(obj,3);
    }

}
$(function(){
    DH_addit("uli");
});
function DH_addit(obj){
    
    var v=$("."+obj ).children("ul").children("li").eq(0);
    //v.css({"position":"relative","top":"200px","left":"200px","animation":"myfirst 2s","-moz-animation":"myfirst 2s","-webkit-animation":"myfirst 2s"});
    v.css({"position":"relative"});
    if($("#search_SOtype").val()==1){
        var k=v.animate({"top":"-50px","left":"100px","opacity":"0"}, 10).animate({"top":"0px","left":"0px","opacity":"1"}, 800);
        //k;
    }else{
        v.animate({'top':"50px",'left':"100px",'opacity':"0"}, 800);
    }
    
    //alert("00000v:" );
}
function TDDayIchartjs(){
    var search_SOtype=$("#search_SOtype").val();
    var currencyUnit = $('#currency-unit').val();
    var data = [
                        {name : 'Alex',value : 10,color:'#8658a5'},
                        {name : 'Mark',value : 30,color:'#4ac2db'},
                        {name : 'David',value : 40,color:'#dd4b4b'},
                        {name : 'Graham',value : 55,color:'#add14f'},
                        {name : 'John',value : 70,color:'#f47721'}
                    ];
    var data1 = [
                        {name : 'Alex',value : 10,color:'#8658a5'},
                        {name : 'Mark',value : 30,color:'#4ac2db'},
                        {name : 'David',value : 40,color:'#dd4b4b'},
                        {name : 'Graham',value : 55,color:'#add14f'},
                        {name : 'John',value : 70,color:'#f47721'}
                    ];
    var Sabels=[];
    var Slabels=[];
    var Tabels=[];
    var tital="TTTTTTT";
    var subtital="TTTTTTT";
    var ytital="TTTTTTT";
    var max=0;
    var Kwidth=0;
    var Kheigth=0;
    if(search_SOtype==1){
        Kwidth=500;
        Kheigth=70;
        var size=$("#Dsize").val();
        if(size>0){
            data=[];
        }else{
            data=[{name : '产品(1)',value : 0,color:'#8658a5'},
            {name : '产品(2)',value : 0,color:'#8658a5'},
            {name : '产品(3)',value : 0,color:'#8658a5'},
            {name : '产品(4)',value : 0,color:'#8658a5'},
            {name : '产品(5)',value : 0,color:'#8658a5'}];
        }
        var I20=0;
        
        for(var i=0;i<size;i++){
            var KEY=$("#Dkey"+i).val();
            Slabels.push(KEY);
            var val=$("#Dval"+i).val();
            var PID=$("#DPID"+i).val();
            Sabels.push(PID);
            var PSA=$("#DPSA"+i).val();
            Tabels.push(PSA);
            if(max<parseInt(val)){
                max=parseInt(val);
            }
            if(i<20){
                var kt={name : KEY,value : val,color:'#06A7E1'};
                data.push(kt);
            }
            I20=I20+parseInt(val);
            
        }
        
        var DSDL=$("#DSDL").val();
        var DNDL=$("#DNDL").val();
        if((parseInt(DSDL)+parseInt(DNDL))>0){
            data1=[];
            var kDNDT={name : "产品销售数量",value : 0,color:'#06A7E1'};
            data1.push(kDNDT);
        }else{
            data1=[];
        }
        var kDNDL={name : "未支付",value : DNDL,color:'#E4214A'};
        data1.push(kDNDL);
        var kDSDL={name : "已支付",value : DSDL,color:'#68ba17'};
        data1.push(kDSDL);
        var Dsal=$("#Dsal").val();
        Dsal=parseInt(Dsal*100);
        Dsal=Dsal/100;
        tital="各产品销售-订单情况";
        subtital="总销售量(已支付):"+I20+"    未支付订单:"+DNDL+"       今日总销售:"+Dsal+currencyUnit;
        ytital="销售量(个)";
        
    }   
    if(search_SOtype==2){
        data1=[];
        Kwidth=30;
        Kheigth=70;
        var Dspr=$("#Dspr").val();
        data1.push({name : "新增推广员",value : Dspr,color:'#E4214A'});
        var Dzpr=$("#Dzpr").val();
        data1.push({name : "原有推广员",value : Dzpr,color:'#68ba17'});
        
        
        data=[];
        var Dval0=$("#Dval0").val();
        var DVAK0=$("#DVAK0").val();
        var DVAI0=$("#DVAI0").val();
        
        
        data.push({name : "新增粉丝",value : Dval0,color:'#AB82FF'});
        Slabels.push("新增粉丝");
        data.push({name : "关注粉丝",value : DVAK0,color:'#CD3700'});
        Slabels.push("关注粉丝");
        data.push({name : "原有粉丝",value : DVAI0,color:'#8B6914'});
        Slabels.push("原有粉丝");
        data.push({name : "现有粉丝",value :(parseInt(DVAI0)+parseInt(Dval0)),color:'#06A7E1'});
        Slabels.push("现有粉丝");
        max=parseInt(DVAI0)+parseInt(Dval0);
        tital="粉丝-推广员统计";
        subtital="原有推广员:"+Dzpr+"    新增推广员:"+Dspr+"    现有推广员:"+(parseInt(Dzpr)+parseInt(Dspr));
        ytital="粉丝数(个)";

        }
        var TLmax=max;
            var TL=0;
            var KLmax=1;
            for(var i=0;i<100;i++){
                TLmax=parseInt(TLmax/10);
                if(TLmax<10){
                    TL=i;
                    break;
                }
            }
        TLmax=(TLmax+1)*10;
            for(var i=0;i<TL;i++){
                KLmax=KLmax*10;
            }
        var Lstep=(parseInt(TLmax/5)*KLmax);
            if(Lstep==0){
                Lstep=1;
            }
    
    
            
            //是否启用动画
            var animation = false;
            
            var chart = new iChart.Column2D({
                render : 'DcanvasDiv',
                data: data,
                title : {
                    text :tital,
                    font:'微软雅黑',
                    color : '#333333'
                },
                subtitle : {
                    text : subtital,
                    font:'微软雅黑',
                    color : '#333333'
                },
            
                width : 800,
                height : 400,
                //animation : true,//开启过渡动画
                //animation_duration:600,
                shadow : true,
                shadow_blur : 2,
                shadow_color : '#aaaaaa',
                shadow_offsetx : 1,
                shadow_offsety : 0,
                column_width : 68,
                background_color : '#fff',
                label : {
                    font:'微软雅黑',
                    fontweight:600,
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-30,
                    color : '#333333'
                    },
                sub_option:{
                    label : {
                        color : '#4c4f48'
                    },
                    listeners:{
                        parseText:function(r,t){
                            //自定义柱形图上方label的格式。
                            return ''+t+'';
                        }
                    }
                },
                tip:{
                    enable:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            //将数字进行千位格式化
                            var f = new String(value);
                            var strre="";
                            if(search_SOtype==1){
                                strre=name+":<br/>销售数量:"+f+"个<br/>占百分比:"+(value/this.get('total') * 100).toFixed(2)+'%';
                            }
                            if(search_SOtype==2){
                                strre=name+":"+f;
                            }
                            return strre;
                        }
                    }
                },
                coordinate:{
                    background_color : '#fff',
                    grid_color : '#676a73',
                    striped_factor:0.06,
                    height:'84%',
                    width:'84%',
                    scale:[{
                         position:'left',   
                         start_scale:0,
                         scale_space:Lstep,
                         end_scale:Lstep*5,
                         label:{
                            color:'#4c4f48'
                         },
                         listeners:{
                            parseText:function(t,x,y){
                            //自定义左侧坐标系刻度文本的格式。
                                return {text:''+t+''}
                            }
                         }
                    }]
                }
            });
            
            var pie = new iChart.Pie3D({
                data: data1,
                label:{
                    color:'#4c4f48'
                },
                //animation : true,//开启过渡动画
                //animation_duration:600,
                sub_option:{
                    mini_label_threshold_angle : 60,//迷你label的阀值,单位:角度
                    mini_label:{//迷你label配置项
                        fontsize:10,
                        fontweight:600,
                        color : '#ffffff'
                    },
                    label : {
                        background_color:null,
                        sign:false,//设置禁用label的小图标
                        padding:'0 4',
                        border:{
                            enable:false,
                            color:'#666666'
                        },
                        fontsize:10,
                        fontweight:600,
                        color : '#333333'
                    },
                    listeners:{
                        parseText:function(d, t){
                            var Rturn=d.get('value');
                            if(d.get('name')=='产品销售数量'){
                                Rturn="起点";
                            }
                            return Rturn;//自定义label文本
                        }
                    } 
                },
                legend:{
                        enable:true,
                        background_color : null,
                        line_height:25,
                        color:'#333333',
                        fontsize:12,
                        font:'微软雅黑',
                        fontweight:600,
                        border : {
                            enable : false
                        },
                        offsety:-180
                        
                },
                text_space : 8,
                showpercent:true,
                decimalsnum:1,
                align : 'left',
                offsetx:chart.coo.get('originx')+Kwidth,
                offsety:-(chart.get('centery')-chart.coo.get('originy')-Kheigth),
                animation : animation,
                radius:70
            });
            
            chart.plugin(pie);
            
             //利用自定义组件构造左侧说明文本。
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                         //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy'),
                            H = coo.height;
                        //在左侧的位置，渲染说明文字。
                        chart.target.textAlign('center')
                        .textBaseline('middle')
                        .textFont('600 13px 微软雅黑')
                        .fillText(ytital,x,y-20,false,'#6d869f', true,true,false,0);
                        
                    }
            }));

            chart.draw();
}
function TDDay_addit_LB(){
    var Str="<div class='WSY_weixinbox' id='TDSHOW' style='position:relative;width:100%;height:100%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;margin-left: 0px;margin-top: 0px;'>";
    Str+="<div class='WSY_weixin'>";
            Str+="<a>"+$("#TDbegintime").val()+"单日销售统计"+"</a>";
            Str+="</div>";
            Str+="<div style='padding:20px;overflow:auto;height:85%;width:95%'>";
            Str+="<div style='background-color: #FFE7BA;border-radius: 10px;'>";
            Str+=" <table width='100%' border='0' cellspacing='0' cellpadding='0'>";
            Str+="<thead>";
            Str+="<tr style='line-height: 30px;background-color: rgb(6, 167, 225);'>";
            Str+="<th scope='col'>产品名称</th>";
            Str+="<th scope='col'>销售数量</th>";
            Str+="<th scope='col'>占百分比</th>";
            //Str+="<th scope='col'>查看详情</th>";
            Str+="</tr>";
            Str+="</thead>";
            Str+="<tbody>";
            var size=$("#Dsize").val();
            var TTDaytotal=0;
            for(var i=0;i<size;i++ ){
                var val=$("#Dval"+i).val();
                    TTDaytotal=TTDaytotal+parseInt(val);
            }
            for(var i=0;i<size;i++ ){
                Str+="<tr style='line-height: 30px;'>";
                Str+="<td style='text-align:center' valign='middle' >"+$("#Dkey"+i).val()+"</td>";
                var TTDayval=$("#Dval"+i).val();
                Str+="<td style='text-align:center' valign='middle' >"+TTDayval+"</td>";
                var TTDayBank=(TTDayval*100/TTDaytotal).toFixed(2)+"%";
                Str+="<td style='text-align:center' valign='middle' >"+TTDayBank+"</td>";
                //Str+="<td style='text-align:center' valign='middle' >" +
                //  "<a onclick='SEE_TO(this)' class='wsy_cost_style' title='列表查看' style='margin:2px;cursor:pointer;'><img src='../../Common/images/Data/qushiicon/btn_08.png' //style='margin-top: 5px;' /></a>" +
                //  "</td>";
                Str+="</tr>";
            }
            Str+="<tr style='line-height: 30px;'>";
            Str+="<td style='text-align:center' valign='middle' > 总计</td>";
            Str+="<td style='text-align:center' valign='middle' >"+TTDaytotal+"</td>";
            Str+="</tr>";
            Str+="</tbody>";
            Str+="</table>";
            Str+="</div>";
            Str+="</div>";
    Str+="</div>";
    $("#DcanvasDiv").html(Str);
}
//<==================================(本日统计结束)================================================>
//=================================(com)===========================================
function excel_OD(obj,k){
    
    var DOCobj=$(obj).parent().parent().parent().parent().parent().children("div").eq(0);
    var DOCtime=DOCobj.children("li").eq(0);
    var ID=$("#PScustomer_id").val();
    var begintime=DOCtime.children("a").eq(0).children("input").val();
    var endtime =DOCtime.children("a").eq(1).children("input").val();
    var search_status = k;
    var S_status=0;
    if(search_status==-1){
        S_status=0;
    }
    if(search_status==4){
        S_status=1;
    }
    if(search_status==6){
        S_status=2;
    }
    if(search_status==1){
        S_status=3;
    }
    if(search_status==8){
        S_status=5;
    }
    if(search_status==2){
        S_status=7;
    }
    if(search_status==3){
        S_status= 2;
    }
    if(search_status==5){
        S_status=0.5;
    }
    var pay_endtime=0;
    var search_batchcode=-1;
    var orgin_from=-1;
    var search_paystyle=-1;
    var search_order_ascription=-2;
    var search_name=-1;
    var search_name_type=1;
    var url='/weixin/plat/app/index.php/Excel/commonshop_excel/customer_id/'+ID+'/status/'+S_status+'/pay_endtime/'+pay_endtime+'/search_batchcode/'+search_batchcode+'/orgin_from/'+orgin_from+'/search_paystyle/'+search_paystyle+'/search_order_ascription/'+search_order_ascription+'/search_name/'+search_name+'/search_name_type/'+search_name_type+'/';
    if(begintime !=""){
        url=url+'begintime/'+begintime+'/';
    }
    if(endtime !=""){
        url=url+'endtime/'+endtime+'/';
    }
    
    if(QJTTYPE>0){
        url=url+'paytime/paytime/';
    }
    //console.log(url);
    document.location=url;
}
function excel_FD(obj,k){
    var DOCobj=$(obj).parent().parent().parent().parent().parent().children("div").eq(0);
    var DOCtime=DOCobj.children("li").eq(0);
    var ID=$("#PScustomer_id").val();
    var SP=$("#shopname").val();
    var begintime=DOCtime.children("a").eq(0).children("input").val();
    var endtime =DOCtime.children("a").eq(1).children("input").val();

    var search_status = k;
        var S_status=0;
    if(search_status==-1){
        S_status=0;
    }
    if(search_status==4){
        S_status=1;
    }
    if(search_status==6){
        S_status=2;
    }
    if(search_status==1){
        S_status=3;
    }
    if(search_status==8){
        S_status=5;
    }
    if(search_status==2){
        S_status=7;
    }
    if(search_status==3){
        S_status= 2;
    }
    if(search_status==5){
        S_status=0.5;
    }
    var pay_endtime=0;
    var search_batchcode=-1;
    var orgin_from=-1;
    var search_paystyle=-1;
    var search_order_ascription=-2;
    var search_name=-1;
    var search_name_type=1;
    var url='/weixin/plat/app/index.php/Excel/commonshop_feidou_excel/customer_id/'+ID+'/status/'+S_status+'/pay_endtime/'+pay_endtime+'/search_batchcode/'+search_batchcode+'/orgin_from/'+orgin_from+'/search_paystyle/'+search_paystyle+'/search_order_ascription/'+search_order_ascription+'/search_name/'+search_name+'/search_name_type/'+search_name_type+'/';
    
    if(begintime !=""){
        url=url+'begintime/'+begintime+'/';
    }
    if(endtime !=""){
        url=url+'endtime/'+endtime+'/';
    }
    //console.log(url);
    document.location=url;
    
}



function search_LB(obj,k){
    var DOCobj=$(obj).parent().parent().parent().parent().parent().children("div").eq(0);
    var DOCtime=DOCobj.children("li").eq(0);
    var PSID=$("#customer_id").val();
    var begintime=DOCtime.children("a").eq(0).children("input").val();
    var endtime =DOCtime.children("a").eq(1).children("input").val();
    var province = "";
    var city = "" ;
    var area = "";
    var search_status = k;
    var url="../Ostatistics/order_BarChart_detailed.php?customer_id="+PSID+"&search_status="+search_status;
    
    if(province !=""){
        url=url+'&province='+province;
    }
    if(city !=""){
        url=url+'&city='+city;
    }
    if(area !=""){
        url=url+'&area='+area;
    }
    if(begintime !=""){
        url=url+'&begintime='+begintime;
    }
    if(endtime !=""){
        url=url+'&endtime='+endtime;
    }
    document.location=url;
}



//<==================================(标签销售额统计)================================================>
var all_total_money = 0;
function ADSsale(){ //标签销售额统计
        var begintime=$("#ADSbegintime").val();    //开始时间
        var endtime=$("#ADSendtime").val();        //结束时间
        var customer_id=$("#customer_id").val();   //商户ID
        ADSTIME=$("#ADSTYPE").val();               //时间类型
        var ADtype=$("#status_ADS").val();         //标签ID
    $.ajax({
        type: "post",
        url: "get_BaseStatistics.php",
        dataType: "json",
        //begintime:begintime,endtime:endtime,
        data: { customer_id: customer_id,begintime:begintime,endtime:endtime,id:1,type:ADSTIME,CONtype:-1,SOtype:7,ADtype:ADtype},
        success: function (result) {
            //class='display'
            var length=result.length;
            var Str="<input id='ADSksize' class='display'  value="+(length-3)+">";
            for(var i=0;i<(length-3);i++){
                Str=Str+"<input id='ADSkey"+i+"' class='display' value="+result[i][0]+"><input id='ADSval"+i+"' class='display' value="+result[i][1]+">";
            }
            $("#ADSsale").html(Str);
            $("#ADSbegintime").val(result[length-2]);
            $("#ADSendtime").val(result[length-1]);
            all_total_money = result[length-3];
            ADSsaleIchartjs();

             
        }
    });
}
function search_ADS(obj){
        ADSTIME=$("#ADSTYPE").val();
    var ktitle=$(obj).val();
    var CONtype=$("#status_ADS").val();
    if(ktitle=="搜 索"){
        ADSTYPE=0;
        ADSsale();
    }
    if(ktitle=="刷新数据"){
        ADSTYPE=0;
        $("#TObegintime").val('');
        ADSsale();
    }
    if(ktitle=="转曲线图"){
        ADSTYPE=0;
        ADSsaleIchartjs();
    }
    if(ktitle=="转柱形图"){
        ADSTYPE=5;
        ADSsaleIchartjs();
    }
    if(ktitle=="列表查看"){
        ADSTYPE=6;
        ADSsaleIchartjs();
    }
    if(ktitle=="详细查看"){
        ADSTYPE=7;
        var CONtype=$("#status_ADS").val();
        var PSID=$("#customer_id").val();
        var begintime=$("#ADSbegintime").val();
        var endtime =$("#ADSendtime").val();
        var province = "";
        var city = "" ;
        var area = "";
        var search_status = CONtype;
        var url="../../MarkPro/advertise_tag/order.php?customer_id="+PSID;
        if(CONtype != -1){
            url=url+'&search_tags_id='+CONtype;
        }
        if(begintime !=""){
            url=url+'&begintime='+begintime+'%2000:00';
        }
        if(endtime !=""){
            url=url+'&endtime='+endtime+'%2023:59';
        }
        document.location=url;
    }
}
function ADSsaleIchartjs(){
            //======================(title动态赋值)==========
            var title="";
            var currencyUnit = $('#currency-unit').val();
            if(ADSTIME==1){
                title=$("#ADSbegintime").val()+'~'+$("#ADSendtime").val()+'每日订单情况';
            }else if(ADSTIME==2 ){
                var Month=$("#ADSbegintime").val();
                var year=Month.substring(0,Month.indexOf("-"));
                Month=Month.substring(Month.indexOf("-")+1,Month.lastIndexOf("-"));
                var Q="";
                if(parseInt(Month)==1){
                    Q="第一季度";
                }else if(parseInt(Month)==4){
                    Q="第二季度";
                }else if(parseInt(Month)==7){
                    Q="第三季度";
                }else if(parseInt(Month)==10){
                    Q="第四季度";
                }
                title=year+'年'+Q+'月订单情况';
            }else if(ADSTIME==3 ){
                var year=$("#TObegintime").val();
                year=year.substring(0,year.indexOf("-"));
                title=year+'年度订单情况';
            }else if(ADSTIME==4 ){
                var year=$("#TObegintime").val();
                title=year+'本周内订单情况';
            }
            //=======================(正文)========================
            var labels = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31"];
            var data1 = ["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"];
            var size=$("#ADSksize").val();
            var TTets=['一','二','三','四','五','六','天'];
            var Tlabels=[];
            var Slabels=[];
            if(size>0){
                labels = [];
                data1 = [];
            }
            var total=0;
            var max=0;

            if(ADSTYPE<5){
                ADSTYPE=0;
            for(var i=0;i<size;i++){
                var key=$("#ADSkey"+i).val();
                Tlabels.push(key);
                var val=$("#ADSval"+i).val();
                total+=parseInt(val*100);
                if(max<parseInt(val)){
                    max=parseInt(val);
                }
                if(ADSTIME==1){
                    labels.push(key.substring(key.lastIndexOf("-")+1)); 
                    Slabels.push(key);  
                }
                if(ADSTIME==2 || ADSTIME==3){
                    labels.push(key.substring(key.indexOf("-")+1,key.lastIndexOf("-")));    
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                }
                if(ADSTIME==4){
                    labels.push(key.substring(key.indexOf("-")+1)+',星期'+TTets[i]);  
                    Slabels.push(key);  
                }
                
                val=parseInt(val*100);
                val=val/100;
                data1.push(val);
            }
            
            //==================（坐标轴单位）==============
            var XZ="";
            if(ADSTIME==1){
                XZ="号";
            }else if(ADSTIME==2 ){
                XZ="月";
            }else if(ADSTIME==3 ){
                XZ="月";
            }
            //==================（注解解释）==============
            
            //=================(计算Y轴值)==================
            total=total/100;
            
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            //===================================
                var data = [
                        {
                            name : 'PV',
                            value:data1,
                            color:'#06A7E1',
                            line_width:1.5
                        }
                     ];
            var chart = new iChart.LineBasic2D({
                render : 'ADScanvasDiv',
                data: data,
                align:'center',
                title : {
                    text:title,
                    font : '微软雅黑',
                    fontsize:24,
                    color:'#333333'
                },
                subtitle : {
                    text:'本月份总销售'+total+currencyUnit+'     累计总销售'+all_total_money+currencyUnit,
                    font : '微软雅黑',
                    color:'#333333'
                },
        
                width : 800,
                height : 400,
                shadow:false,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-60,
                    color : '#666666'
                },
                shadow_color : '#202020',
                shadow_blur : 8,
                shadow_offsetx : 0,
                shadow_offsety : 0,
                background_color:'#fff',
                tip:{
                    enable:true,
                    shadow:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            return "<span style='color:#005268;font-size:12px;'>"+Slabels[i]+"<br/>销售额:"+
                            " </span><span style='color:#005268;font-size:20px;'>"+value+currencyUnit+"</span>";
                        }
                    }
                },
                crosshair:{
                    enable:true,
                    line_color:'#dadada'
                },
                sub_option : {
                    smooth : true,
                    label:false,
                    hollow:false,
                    hollow_inside:false,
                    point_size:8
                },
                coordinate:{
                    width:640,
                    height:260,
                    striped_factor : 0.5,
                    grid_color:'#dadada',
                    axis:{
                        color:'#9f9f9f',
                        width:[0,0,2,2]
                    },
                    scale:[{
                         position:'left',   
                         start_scale:0,
                         end_scale:step*5,
                         scale_space:step,
                         scale_size:2,
                         scale_enable : false,
                         label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                         scale_color:'#333333'
                    },{
                         position:'bottom', 
                         label : {color:'#333333',font : '微软雅黑',fontsize:11,fontweight:600},
                         scale_enable : false,
                         labels:labels
                    }]
                }
            });
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy'),
                            w = coo.width,
                            h = coo.height;
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText('销售额('+currencyUnit+')',x-40,y-12,false,'#333333')
                        .textBaseline('top')
                        .fillText('('+XZ+')',x+w+12,y+h+10,false,'#333333');
                        
                    }
            }));
        //开始画图
        chart.draw();
        }
        if(ADSTYPE==5){
            ADSTYPE=0;
            var data = [];
            for(var i=0;i<size;i++){
                var key=$("#ADSkey"+i).val();
                 Tlabels.push(key);
                var val=$("#ADSval"+i).val();
                total+=parseInt(val*100);
                if(max<parseInt(val)){
                    max=parseInt(val);
                }
                val=parseInt(val*100);
                val=val/100;
                if(ADSTIME==1){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1);  
                    var kd={name : key+'号',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                if(ADSTIME==2 || ADSTIME==3){
                    Slabels.push(key.substring(0,key.lastIndexOf("-"))+'月');    
                    key=key.substring(key.indexOf("-")+1,key.lastIndexOf("-")); 
                    var kd={name : key+'月',value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                    if(ADSTIME==4){
                    Slabels.push(key);  
                    key=key.substring(key.lastIndexOf("-")+1)+',星期'+TTets[i];   
                    var kd={name : key,value : val,color:'#06A7E1'};
                    data.push(kd);
                }
                
            }       
            //=================(计算Y轴值)==================
            total=total/100;
            
            var Tmax=max;
            var T=0;
            var Kmax=1;
            for(var i=0;i<100;i++){
                Tmax=parseInt(Tmax/10);
                if(Tmax<10){
                    T=i;
                    break;
                }
            }
            Tmax=(Tmax+1)*10;
            for(var i=0;i<T;i++){
                Kmax=Kmax*10;
            }
            var step=(parseInt(Tmax/5)*Kmax);
            if(step==0){
                step=1;
            }
            //===================================       
            
            
            var chart = new iChart.Column2D({
                render : 'ADScanvasDiv',
                data : data,
                title : {
                    text : title,
                    font:'微软雅黑',
                    color : '#333333'
                },
                subtitle : {
                    text : '本月份总销售'+total+currencyUnit+'     累计总销售'+all_total_money+currencyUnit,
                    font:'微软雅黑',
                    color : '#333333'
                },
        
                width : 800,
                height : 400,
                //animation : true,//开启过渡动画
                //animation_duration:600,//600ms完成动画
                label : {
                    fontsize:11,
                    textAlign:'right',
                    textBaseline:'middle',
                    rotate:-60,
                    color : '#333333'
                },
                tip:{
                    enable:true,
                    listeners:{
                         //tip:提示框对象、name:数据名称、value:数据值、text:当前文本、i:数据点的索引
                        parseText:function(tip,name,value,text,i){
                            //将数字进行千位格式化
                            var f = new String(value);
                            
                            
                            return Slabels[i]+"<br/>订单:"+f+"单<br/>占整个比重:"+(value/this.get('total') * 100).toFixed(2)+'%';
                        }
                    }
                },
                shadow : true,
                shadow_blur : 2,
                shadow_color : '#aaaaaa',
                shadow_offsetx : 1,
                shadow_offsety : 0,
                column_width : 62,
                sub_option : {
                    label : false,
                    border : {
                        width : 2,
                        color : '#ffffff'
                    },
                    listeners:{
                            /**
                             * r:iChart.Sector2D对象
                             * e:eventObject对象
                             * m:额外参数
                             */
                            click:function(s,e,m){
                                ADSTIME=$("#ADSTYPE").val();
                                var TTO=722;
                                if(ADSTIME==1){
                                    TTO=674;
                                }
                                if(ADSTIME==3){
                                    TTO=674;
                                }
                                var OTT=8;
                                if(ADSTIME==1){
                                    OTT=56;
                                }
                                if(ADSTIME==3){
                                    OTT=25;
                                }
                                var TTi=TTO/(parseInt(size)+1);
                                var Ti=Math.round((s.get('originx')-OTT)/TTi);
                                if(ADSTIME==1 || ADSTIME==4){// title=$("#TSbegintime").val()+'~'+$("#TSendtime").val()+'每日销售额';
                                    $("#ADSbegintime").val(Tlabels[Ti-1]);
                                    if(Ti<Tlabels.length){
                                        $("#ADSendtime").val(Tlabels[Ti]);
                                    }
                                    $(".ADS").click();
                                }
                                if(ADSTIME==2 || ADSTIME==3){
                                    $("#ADSbegintime").val(Tlabels[Ti-1]);
                                    if(Ti<Tlabels.length){
                                        $("#ADSendtime").val(Tlabels[Ti]);
                                    }
                                    $("#ADSTYPE").val(1);
                                    ADSTYPE=0;
                                    ADSsale();
                                }

                                
                                
                                

                            }
                        }
                },
                coordinate : {
                    background_color : null,
                    grid_color : '#dadada',
                    width : 660,
                    height:240,
                    axis : {
                        color : '#9f9f9f',
                        width : [0, 0, 1, 0]
                    },
                    scale : [{
                        position : 'left',
                        start_scale : 0,
                        end_scale : step*5,
                        scale_space : step,
                        scale_enable : false,
                        label : {
                            fontsize:11,
                            color : '#333333'
                        },
                        listeners:{
                            parseText:function(t,x,y){
                                return {text:t}
                            }
                         }
                    }]
                }
            });
            
            //利用自定义组件构造左侧说明文本
            chart.plugin(new iChart.Custom({
                    drawFn:function(){
                        //计算位置
                        var coo = chart.getCoordinate(),
                            x = coo.get('originx'),
                            y = coo.get('originy');
                        //在左上侧的位置，渲染一个单位的文字
                        chart.target.textAlign('start')
                        .textBaseline('bottom')
                        .textFont('600 11px 微软雅黑')
                        .fillText('销售额('+currencyUnit+')',x-40,y-10,false,'#333333');
                        
                    }
            }));
    
            chart.draw();
        }
        if(ADSTYPE==6){
            ADSTYPE=0;
             $("#ADScanvasDiv").html("");
            
             var total=0;
             for(var i=0;i<size;i++ ){
                 var VAL=$("#ADSval"+i).val();
                total+=parseInt(VAL*100);
             }
             total=total/100;
            //Str+="</div>";
            var Str="<div class='WSY_weixinbox' id='ADSSHOW' style='width:100%;height:100%;border: 2px solid rgba(0, 0, 0, 0.25);border-radius: 5px;margin-left: 0px;margin-top: 0px;'>";
            Str+="<div class='WSY_weixin'>";
            Str+="<a>"+title+"</a>";
            Str+="</div>";
            Str+="<div style='padding:20px;overflow:auto;height:85%;width:95%'>";
            Str+="<div style='background-color: #FFE7BA;border-radius: 10px;'>";
            Str+=" <table width='100%' border='0' cellspacing='0' cellpadding='0'>";
            Str+="<thead>";
            Str+="<tr style='line-height: 30px;background-color: rgb(6, 167, 225);'>";
            if(ADSTIME<2){
                Str+="<th scope='col'>时间(日)</th>";
            }
            if(ADSTIME>1){
                Str+="<th scope='col'>时间(月)</th>";
            }
            Str+="<th scope='col' style='display:none'>到期时间</th>";
            Str+="<th scope='col'>销售额</th>";
            Str+="<th scope='col'>百分比</th>";
            Str+="<th scope='col'>查看详情</th>";
            Str+="</tr>";
            Str+="</thead>";
            Str+="<tbody>";
            
            for(var i=0;i<size;i++ ){
                Str+="<tr style='line-height: 30px;'>";
                var KEY=$("#ADSkey"+i).val();
                if(ADSTIME>1 && QJOTIME<4){
                    KEY=KEY.substring(0,KEY.lastIndexOf("-"));  
                }
                var ksize=size-1;
                if(i<ksize){
                    var k=i+1;
                    var JKEY=$("#ADSkey"+k).val();
                    if(ADSTIME>1  && ADSTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                if(i==ksize){
                    var JKEY=$("#ADSendtime").val();
                    if(ADSTIME>1  && ADSTIME<4){
                    JKEY=JKEY.substring(0,JKEY.lastIndexOf("-"));   
                    }
                }
                
                var VAL=$("#ADSval"+i).val();
                var BFB=(VAL/total)*100;
                VAL=parseInt(VAL*100);
                VAL=VAL/100;
                //VAL=VAL.toFixed(2);
                BFB=BFB.toFixed(2);
                BFB=BFB+'%';
                if(total==0){
                    BFB='100%';
                }
                Str+="<td style='text-align:center' valign='middle' >"+KEY+"</td>";
                Str+="<td  valign='middle'  style='display:none'>"+JKEY+"</td>";
                Str+="<td style='text-align:center' valign='middle' >"+VAL+"</td>";
                Str+="<td style='text-align:center' valign='middle' >"+BFB+"</td>";
                Str+="<td style='text-align:center' valign='middle' >" +
                    "<a onclick='SEE_ADS(this)' class='wsy_cost_style' title='列表查看' style='margin:2px;cursor:pointer;'><img src='../../Common/images/Data/qushiicon/btn_08.png' style='margin-top: 5px;' /></a>" +
                    "</td>";
                Str+="</tr>";
            }
            Str+="<tr style='line-height: 30px;'>";
            Str+="<td style='text-align:center' valign='middle' > 总计</td>";
            Str+="<td style='text-align:center' valign='middle' >"+total+"</td>";
            Str+="</tr>";
            Str+="</tbody>";
            Str+="</table>";
            Str+="</div>";
            Str+="</div>";
            Str+="</div>";
            $("#ADScanvasDiv").html(Str);
            $("#ADSSHOW").css({width:0,height:0});
            $("#ADSSHOW").animate({width:800,height:400},"slow");    
        }
    }
function SEE_ADS(obj){
    var DOCobj=$(obj).parent().parent();
    var STIME=DOCobj.children("td").eq(0).html();
    var ETIME=DOCobj.children("td").eq(1).html();
    if(ADSTIME>1){
        STIME+="-01";
        ETIME+="-01";
    }
    var CONtype=$("#status_ADS").val();
    var PSID=$("#customer_id").val();
    var begintime=STIME;
    var endtime =ETIME;
    var province = "";
    var city = "" ;
    var area = "";
    var search_status = CONtype;
    var url="../../MarkPro/advertise_tag/order.php?customer_id="+PSID;
    if(CONtype != -1){
        url=url+'&search_tags_id='+CONtype;
    }
    if(begintime !=""){
        url=url+'&begintime='+begintime+'%2000:00';
    }
    if(endtime !=""){
        url=url+'&endtime='+endtime+'%2023:59';
    }
    document.location=url;
}
//<==================================(标签销售额统计结束)================================================>




//<==================================(订单量统计、占比统计)================================================>
$(function(){
    ome=GetDateStr(-1);//昨天
    two=GetDateStr(-2);
    there=GetDateStr(-3);
    four=GetDateStr(-4);
    five=GetDateStr(-5);
    six=GetDateStr(-6);
    seven=GetDateStr(-7);

}); 

function GetDateStr(AddDayCount) {
    var dd = new Date();
    dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
    var y = dd.getFullYear();
    var m = dd.getMonth()+1;//获取当前月份的日期
    var d = dd.getDate();
    return y+"-"+m+"-"+d;
}
function total_order_num(){ //昨天总订单数
    var customer_id=$("#customer_id").val();
    var CONtype=$("#status_ADO").val();
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:28,search_tags_id:CONtype},
        success: function (result) {
            $(".total_order_num").html(result);
        }
    })
}
function yes_total_order_num(){ //前天总订单数
    var customer_id=$("#customer_id").val();
    var CONtype=$("#status_ADO").val();
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:29,search_tags_id:CONtype},
        success: function (result) {
            $(".yes_total_order_num").html(result);
        }
    })
}
function container_charts(){    //投放订单统计
    var customer_id=$("#customer_id").val();
    var CONtype=$("#status_ADO").val();
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:30,search_tags_id:CONtype},
        dataType: "json",
        success: function (result) {
            //console.log(result);
            var counts = new Array();
             for(var i=0;i<result.length;i++){
                // console.log(result[i]);
                counts.push(parseInt(result[i])); 
            }  
            require.config({
                paths: {
                    echarts: '../../Common/js/Data/js/echarts'
                }
            });
            require(
                [
                    'echarts',
                    'echarts/chart/line'
                ],
                function (ec) {
                    var myChart = ec.init(document.getElementById('container_charts'));
                    myChart.setOption({
            tooltip : {
                                trigger: 'axis'
                            },
                            grid:{
                                x:20,
                                y:10,
                                x2:40,
                                y2:20
                            },
                            calculable : true,
                            xAxis : [
                                {
                                    type : 'category',
                                    boundaryGap : false,
                                    data : [seven,six,five,four,there,two,ome]
                                }
                            ],
                            yAxis : [
                                {
                                    type : 'value'
                                }
                            ],
                            series : [
                                {
                                    name:'订单数',
                                    type:'line',
                                    smooth:true,
                                    itemStyle: {
                                        normal: {
                                            color:'#06A7E1',
                                            areaStyle: {
                                                color : 'rgba(205,237,249,0.6)'
                                            },
                                            lineStyle:{
                                                color:'#06A7E1'
                                            }
                                        }
                                    },
                                    data:counts
                                }
                            ]
                            
                            
                    });
                    window.onresize = myChart.resize;
                }
            );
        }
    })
}
function search_ADO(){
    total_order_num();
    yes_total_order_num();
    container_charts();
}

function rejection_total_order_num(){   //昨日拒收订单数（笔）
    var customer_id=$("#customer_id").val();
    var CONtype=$("#status_ADR").val();
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:31,search_tags_id:CONtype},
        success: function (result) {
            $(".rejection_total_order_num").html(result);
        }
    })
}
function yes_rejection_total_order_num(){   //前日拒收订单数（笔）
    var customer_id=$("#customer_id").val();
    var CONtype=$("#status_ADR").val();
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:32,search_tags_id:CONtype},
        success: function (result) {
            $(".yes_rejection_total_order_num").html(result); 
        }
    })
}
function rejection_charts(){    //拒收订单统计
    var customer_id=$("#customer_id").val();
    var CONtype=$("#status_ADR").val();
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:33,search_tags_id:CONtype},
        dataType: "json",
        success: function (result) {
            //console.log(result);
            var counts = new Array();
             for(var i=0;i<result.length;i++){
                counts.push(parseInt(result[i])); 
            }  
            require.config({
                paths: {
                    echarts: '../../Common/js/Data/js/echarts'
                }
            });
            require(
                [
                    'echarts',
                    'echarts/chart/line'
                ],
                function (ec) {
                    var myChart = ec.init(document.getElementById('rejection_charts'));
                    myChart.setOption({
                            tooltip : {
                                trigger: 'axis'
                            },
                            grid:{
                                x:20,
                                y:10,
                                x2:50,
                                y2:20
                            },
                            calculable : true,
                            xAxis : [
                                {
                                    type : 'category',
                                    boundaryGap : false,
                                    data : [seven,six,five,four,there,two,ome]
                                }
                            ],
                            yAxis : [
                                {
                                    type : 'value'
                                }
                            ],
                            series : [
                                {
                                    name:'订单数',
                                    type:'line',
                                    smooth:true,
                                    itemStyle: {
                                        normal: {
                                            color:'#06A7E1',
                                            areaStyle: {
                                                color : 'rgba(205,237,249,0.6)'
                                            },
                                            lineStyle:{
                                                color:'#06A7E1'
                                            }
                                        }
                                    },
                                    data:counts
                                }
                            ]
                            
                    });
                }
            );
        }
    })
}
function search_ADR(){
    rejection_total_order_num();
    yes_rejection_total_order_num();
    rejection_charts();
}

function consumption_cake_charts(){ //成交订单占比
    var customer_id=$("#customer_id").val();    
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:34},
        dataType: "json",
        success: function (result) {
            var item_series = new Array();
            var item_name = new Array();
            // console.log(result);
            for(var k=0;k<result.count;k++){
                item_name.push(result.tag_names[k]);
                var a = {
                            value:result.confirm_order_nums[k], 
                            name:result.tag_names[k],
                            itemStyle: {
                                        normal: {                                       
                                                color : '#'+result.color[k]                                       
                                                }
                                        },
                        };
                item_series.push(a);

            }
            require.config({
                paths: {
                    echarts: '../../Common/js/Data/js/echarts'
                }
            });
            require(
                [
                    'echarts',
                    'echarts/chart/pie'
                ],
                function (ec) {
                    var myChart = ec.init(document.getElementById('consumption_cake_charts'));
                    myChart.setOption({
                        tooltip : {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            orient : 'vertical',
                            x : 'left',
                            data:item_name
                        },
                        calculable : true,
                        series : [
                            {
                                name:'成交订单占比',
                                type:'pie',
                                radius : '55%',
                                center: ['50%', '60%'],
                                data:item_series
                            }
                        ]
                    });
                }
            );
        }
    })
}
function rejection_cake_charts(){   //拒收订单占比
    var customer_id=$("#customer_id").val();
    $.ajax({
        type: "post",
        url: "get_statistics.php",
        data: { customer_id: customer_id,id:35},
        dataType: "json",
        success: function (result) {
            var item_series = new Array();
            var item_name = new Array();
            // console.log(result);
            for(var k=0;k<result.count;k++){
                item_name.push(result.tag_names[k]);
                var a = {
                            value:result.rejected_order_num[k], 
                            name:result.tag_names[k],
                            itemStyle: {
                                        normal: {                                       
                                                color : '#'+result.color[k]                                       
                                                }
                                        },
                        };
                item_series.push(a);

            }
            require.config({
                paths: {
                    echarts: '../../Common/js/Data/js/echarts'
                }
            });
            require(
                [
                    'echarts',
                    'echarts/chart/pie'
                ],
                function (ec) {
                    var myChart = ec.init(document.getElementById('rejection_cake_charts'));
                    myChart.setOption({
                        tooltip : {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            orient : 'vertical',
                            x : 'left',
                            data:item_name
                        },
                        calculable : true,
                        series : [
                            {
                                name:'拒收订单占比',
                                type:'pie',
                                radius : '55%',
                                center: ['50%', '60%'],
                                data:item_series
                            }
                        ]
                    });
                }
            );
        }
    })
}
//<==================================(订单量统计、占比统计结束)================================================>