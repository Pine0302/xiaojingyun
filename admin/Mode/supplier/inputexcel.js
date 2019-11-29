var idTmr;
		function  getExplorer() {
			var explorer = window.navigator.userAgent ;
			//ie 
			if (explorer.indexOf("MSIE") >= 0) {
				return 'ie';
			}
			//firefox 
			else if (explorer.indexOf("Firefox") >= 0) {
				return 'Firefox';
			}
			//Chrome
			else if(explorer.indexOf("Chrome") >= 0){
				return 'Chrome';
			}
			//Opera
			else if(explorer.indexOf("Opera") >= 0){
				return 'Opera';
			}
			//Safari
			else if(explorer.indexOf("Safari") >= 0){
				return 'Safari';
			}
		}
        function inputexcel(tableid) {//整个表格拷贝到EXCEL中
			if(getExplorer()=='ie')
			{
				var curTbl = document.getElementById(tableid);
				var oXL = new ActiveXObject("Excel.Application");
				
				//创建AX对象excel 
				var oWB = oXL.Workbooks.Add();
				//获取workbook对象 
				var xlsheet = oWB.Worksheets(1);
				//激活当前sheet 
				var sel = document.body.createTextRange();
				sel.moveToElementText(curTbl);
				//把表格中的内容移到TextRange中 
				sel.select();
				//全选TextRange中内容 
				sel.execCommand("Copy");
				//复制TextRange中内容  
				xlsheet.Paste();
				//粘贴到活动的EXCEL中       
				oXL.Visible = true;
				//设置excel可见属性

				try {
					var fname = oXL.Application.GetSaveAsFilename("Excel.xls", "Excel Spreadsheets (*.xls), *.xls");
				} catch (e) {
					print("Nested catch caught " + e);
				} finally {
					oWB.SaveAs(fname);

					oWB.Close(savechanges = false);
					//xls.visible = false;
					oXL.Quit();
					oXL = null;
					//结束excel进程，退出完成
					//window.setInterval("Cleanup();",1);
					idTmr = window.setInterval("Cleanup();", 1);

				}
				
			}
			else
			{
				tableToExcel(tableid)
			}
        }
        function Cleanup() {
            window.clearInterval(idTmr);
            CollectGarbage();
        }
		var tableToExcel = (function() {
			  var uri = 'data:application/vnd.ms-excel;base64,',
			  template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="//www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
				base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) },
				format = function(s, c) {
					return s.replace(/{(\w+)}/g,
					function(m, p) { return c[p]; }) }
				return function(table, name) {
				//构建excel内容
				var table = $('#'+table);
				var excel="<table>";
				//表头开始
				table.children('thead').children('tr').each(function(){
					excel += '<tr>';
					$(this).children('th').each(function(i){
						//清除第9列
						if(i!=8){
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
						if(i!=8){
							if(i==5){
								excel += '<td>';
								excel += $(this).find('input:text').val()||' ';
								excel += '</td>';
							}else{
								excel += '<td>';
								excel += $(this).html();
								excel += '</td>';
							}	
						}
					})
					excel += '</tr>';
				})
				excel += '</table>';
				excel=excel.replace(/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/g ,"$2").replace(/[\s]+/g," ").replace(/<i[^>]*class="([^"]*)"[^>]*>(.*?)<\/i>/g,"");
				//构建excel内容结束
				console.log(excel);
				var ctx= {worksheet: name || 'Worksheet', table: excel}
				var exlestext = base64(format(template, ctx));
				window.location.href = uri + exlestext;
			  }
			})()