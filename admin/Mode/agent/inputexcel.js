function inputtext(table,filename){

	/*导出自行安装订单筛选框*/
	var excelArray = [
						["1","代理商编号"],
						["2","姓名"],
						["3","直接会员人数"],
						["4","个人申请信息"],
						["5","代理级别"],
						["6","库存记录"],
						["7","进账记录"],
						["8","状态"],
						["9","个人总消费金额"],
						["10","申请时间"]
					 ];
	exportBox(excelArray);
	$(".floatbox").show();
	$(".floatinputs").click(function(){
		var str="";
		$("input[name='excel_field[]']:checkbox").each(function(){ 
            if($(this).is(':checked')){
                str += $(this).val()+","
            }
        })
        str = str.substring(0,str.length-1);	
		sstr = str.split(",");
		dataIntArr=sstr.map(function(data){  
			return +data;  
		}); 
	
	//构建excel内容	
	var table = $('#WSY_t1');
	var excel="<table>";
	//表头开始
	table.children('thead').children('tr').each(function(){
		excel += '<tr>';
		$(this).children('th').each(function(i){
			//清除第9列
			if(i!=12 && (dataIntArr.indexOf(i)!=-1)){
				excel += '<th>';
				excel += $(this).text();
				excel += '</th>';
			}
		})
		excel += '</tr>';
	})
	//表头结束，内容开始
	table.children('tbody').children('tr').each(function(){
		excel += '<tr>';
		$(this).children('td').each(function(i){
			if(i!=11 && (dataIntArr.indexOf(i)!=-1)){
				excel += '<td>';
				excel += $(this).html();
				excel += '</td>';
			}
		})
		excel += '</tr>';
	})
	excel += '</table>';
	excel=excel.replace(/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/g ,"$2").replace(/[\s]+/g," ").replace(/<i[^>]*class="([^"]*)"[^>]*>(.*?)<\/i>/g,"");
	//构建excel内容结束
	form = $("<form></form>")
	form.attr('action','inputexl.php')
	form.attr('method','post')
	input1 = $("<input type='hidden' name='excel' />")
	input1.attr('value',excel)
	input2 = $("<input type='text' name='filename' />")
	input2.attr('value','代理商')
	form.append(input1)
	form.append(input2)
	form.appendTo("body")
	form.css('display','none')
	form.submit()
		$(".floatbox").hide();
		$(".floatbox").remove();
	});		
}