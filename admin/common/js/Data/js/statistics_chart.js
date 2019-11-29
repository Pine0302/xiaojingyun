$(function(){
	customer_id = $("#customer_id").val();
	ome=GetDateStr(0);//今天
	two=GetDateStr(-1);
	there=GetDateStr(-2);
	four=GetDateStr(-3);
	five=GetDateStr(-4);
	six=GetDateStr(-5);
	seven=GetDateStr(-6);

}); 

function GetDateStr(AddDayCount) {
	var dd = new Date();
	dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
	var y = dd.getFullYear();
	var m = dd.getMonth()+1;//获取当前月份的日期
	var d = dd.getDate();
	return y+"-"+m+"-"+d;
}
