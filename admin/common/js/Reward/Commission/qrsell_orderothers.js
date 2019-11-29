function diy_add(dtype){
    
   var str = "";
   diy_num++;
   switch(dtype){
      case 1:
	      str = str + "<tr class=\"diy_one_two\" id=\"diy_item_"+diy_num+"\">"
	            + "<td>单行文字</td>"
				+ "<td><input type=text name=\"singletext\" id=\"singletext_"+diy_num+"\" value=\"\" placeholder=\"请输入字段名\"  /></td>"
				+ "<td><input type=text name=\"singletext_con\" id=\"singletext_con"+diy_num+"\" value=\"\" placeholder=\"请输入初始内容\"  /></td>"
				+ "<td><a title=\"删除\"  href=\"javascript:diy_del("+diy_num+",1);\"><img src=\"../../../common/images_V6.0/operating_icon/icon04.png\"></a>&nbsp;&nbsp;<a title=\"添加\" href=\"javascript:diy_add(1,"+diy_num+");\"><img src=\"../../../common/images_V6.0/operating_icon/icon05.png\"></a></td>"
				+ "</tr>";
	     break;
	  case 2:
	     str = str + "<tr class=\"diy_one_two\" id=\"diy_item_"+diy_num+"\">"
	            + "<td>日期选择</td>"
				+ "<td><input type=text name=\"singledate\" id=\"singledate_"+diy_num+"\" value=\"\" placeholder=\"请输入字段名\"  /></td>"
				+ "<td><input type=text name=\"singledate_con\" id=\"singledate_con"+diy_num+"\" value=\"\" placeholder=\"请输入初始内容\" /></td>"
				+ "<td><a title=\"删除\"  href=\"javascript:diy_del("+diy_num+",2);\"><img src=\"../../../common/images_V6.0/operating_icon/icon04.png\"></a>&nbsp;&nbsp;<a title=\"添加\" href=\"javascript:diy_add(2,"+diy_num+");\"><img src=\"../../../common/images_V6.0/operating_icon/icon05.png\"></a></td>"
				+ "</tr>";
	     break;
	  case 3:
	     str = str + "<tr class=\"diy_one_two\" id=\"diy_item_"+diy_num+"\">"
	            + "<td>下拉选择</td>"
				+ "<td><input type=text name=\"singleselect\" id=\"singleselect_"+diy_num+"\" value=\"\" placeholder=\"自定义下拉框\"   /></td>"
				+ "<td><input type=text name=\"singleselect_con\" id=\"singleselect_con"+diy_num+"\" value=\"\" placeholder=\"选择1|选择2\"  /></td>"
				+ "<td><a title=\"删除\" href=\"javascript:diy_del("+diy_num+",3);\"><img src=\"../../../common/images_V6.0/operating_icon/icon04.png\"></a>&nbsp;&nbsp;<a title=\"添加\" href=\"javascript:diy_add(3,"+diy_num+");\"><img src=\"../../../common/images_V6.0/operating_icon/icon05.png\"></a></td>"
				+ "</tr>";
	     break;
   }
   $("#WSY_t1").append(str);
}

function diy_del(num,type){
   document.getElementById("diy_item_"+num).style.display="none";
   document.getElementById("diy_item_"+num).innerHTML="";
   
}

function submitV(){
	var str = "";
	var singletext = document.getElementsByName("singletext");
	var singletext_con = document.getElementsByName("singletext_con");
	var len = singletext.length;
	for(i=0;i<len;i++){
	  
	    var v = singletext[i].value;
		var con = singletext_con[i].value;
		str = str +"1_"+v+"_"+con+",";
	}
	
	if(str!=""){
	   str = str.substring(0,str.length-1);
	}
	str = str + ",";
	
	var singledate = document.getElementsByName("singledate");
	var singledate_con = document.getElementsByName("singledate_con");
	len = singledate.length;
	for(i=0;i<len;i++){
	    var v = singledate[i].value;
		var con = singledate_con[i].value;
		str = str +"2_"+v+"_"+con+",";
	}
	if(str!=""){
	   str = str.substring(0,str.length-1);
	}
	str = str + ",";
	
	var singleselect = document.getElementsByName("singleselect");
	var singleselect_con = document.getElementsByName("singleselect_con");
	len = singleselect.length;
	for(i=0;i<len;i++){
	    var v = singleselect[i].value;
		var con = singleselect_con[i].value;
		str = str +"3_"+v+"_"+con+",";
	}
	
	if(str!=""){
	   str = str.substring(0,str.length-1);
	}
	//alert('str==========='+str);
	document.getElementById("qrsell_orderothers").value=str;
	 document.getElementById("upform").submit();
}




