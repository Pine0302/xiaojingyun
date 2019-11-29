//弹出窗口
//var weixinUrl = "//27.54.228.81/vision/";



function gweiUrl_detail(rurl,num,parent_id){
    top.frames["iframe_main"].location = rurl;
	/*if(num==1){
		if(top.document.all.middle.cols == "210,0,*") top.frames["menu"].location.reload();
		top.document.all.middle.cols = "210,200,*";
	}else{
		top.document.all.middle.cols = '210,0,*';
	}*/
}

function gweiUrl2(rurl,num,baseurl){
    document.location=baseurl+rurl; 
}


function goUrl(url){
   document.location = url;
}

function goExcel(url){
	console.log(url);
    document.location=url;
}