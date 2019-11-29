$(function () {
/* 	var pc=$('#pc').val();
	var pc2=pc.split(",");
	var pc_num=$('#pc_num').val();
	var pc_num2=pc_num.split(",");
	
	var pc_num3=new Array();
	for(var i=0;i<pc_num2.length;i++){
		pc_num3[i]=parseInt(pc_num2[i]);
	}
	 */
	 if(location_p !=""){
		 newCounts=counts;
	 }
	 console.log(oldArr);
			console.log(newCounts);
    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: '微商城地区订单数统计表'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
          /*   categories: ['河北省', '山东省', '辽宁省', '黑龙江省', '吉林省', '甘肃省', '青海省', '河南省', '江苏省', '湖北省', '湖南省', '江西省', '浙江省', '广东省', '云南省', '福建省', '台湾省', '海南省', '山西省', '四川省', '陕西省', '贵州省', '安徽省', '重庆市', '北京市', '上海市', '天津市', '广西壮族自治区', '内蒙古自治区', '西藏自治区', '新疆维吾尔自治区', '宁夏回族自治区', '香港特别行政区', '澳门特别行政区', '其他'], */
			categories: oldArr,
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: '订单数',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ' 单'
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 80,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: [{
            name: '微商城地区订单数',
       /*      data: [hb,sd,ln,hlj,jl,gs,qh,hen,js,hub,hn,jx,zj,gd,yn,fj,tw,hain,sx,xc,xx,gz,ah,cq,bj,sh,tj,gxzz,nmg,xz,xjwwe,nx,xg,om,qt] */
			data: newCounts
        }]
    });
});