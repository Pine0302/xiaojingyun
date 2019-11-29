/*
 	*第一个参数add_data是数据,仅接受数组,provinces_id,city_id,area_id分别为省市区下拉框的ID，这四项是必填项
 	*default_p,default_c,default_a为默认选择的省市区，不填或者填错，都将会空白显示，填写正确将正常显示
 	* author : 微三云前端组 2016-9-18	by 叉
	* edit	 : 2016-9-20	by 威
*/
//调用方法：	ctrl_address(areaData,"WSY_provinces","WSY_city","WSY_area",'天津','天津市','河东区');

function ctrl_address(add_data,provinces_id,city_id,area_id,default_p,default_c,default_a){
	var num=add_data.length;//获取数组长度
	var $provinces=$("#"+provinces_id);
	var $city=$("#"+city_id);
	var $area=$("#"+area_id);
	(function(){ //此匿名函数用于获取所有省份
		var provinces_opt='';
		for(var i=0;i<num;i++){			
			if(add_data[i].LevelType==1){ //LevelType==1代表省
				provinces_opt='<option value='+add_data[i].name+'>'+add_data[i].name+'</option>'+provinces_opt;
			}
		}
		$provinces.append(provinces_opt);
	})();
	$provinces.val(default_p).attr("selected",true);//初始化省选项框
	get_city(default_p);//根据省获取城市列表
	$city.val(default_c).attr("selected",true);//初始化市选项框
	get_area(default_c);//根据市获取县城列表
	$area.val(default_a).attr("selected",true);//初始化县选项框
	//监听省改变，则下属城市改变
	$provinces.change(function(){
		// $city.html('<select id='+city_id+'><option>市</option></select>');//初始化下属选项框	//2016.9.20
		// $area.html('<select id='+area_id+'><option>县</option></select>');//同上	//2016.9.20
		$city.html('<select id='+city_id+'></select>');//初始化下属选项框
		$area.html('<select id='+area_id+'></select>');//同上
		var provinces_index=this.selectedIndex;					
		var provinces_text=$(this).find("option").eq(provinces_index).text();//获取选中省名称
		// console.log(provinces_text);
		get_city(provinces_text);
	})
	//监听市改变，则下属县城改变
	$city.change(function(){
		$area.html('<select id='+area_id+'><option>县</option></select>');
		var city_index=this.selectedIndex;	
		var area_text=$(this).find("option").eq(city_index).text();		//获取选中市名称	
		// console.log(area_text);
		get_area(area_text);
	})
	function get_city(provinces_name){//此方法：根据省名称获取下属城市
		var city_opt='';
		for(var i=0;i<num;i++){			
			if(add_data[i].LevelType==2&&add_data[i].MergerName.match(provinces_name)){//LevelType==2代表市
				city_opt='<option value='+add_data[i].name+'>'+add_data[i].name+'</option>'+city_opt;
			}
		}
		$city.append(city_opt);		
	}
	function get_area(city_name){//此方法：根据市名称获取下属县城
		var area_opt='';
		for(var i=0;i<num;i++){			
			if(add_data[i].LevelType==3&&add_data[i].MergerName.match(city_name)){//LevelType==3代表县
				area_opt='<option value='+add_data[i].name+'>'+add_data[i].name+'</option>'+area_opt;
			}
		}
		$area.append(area_opt);		
	}
}