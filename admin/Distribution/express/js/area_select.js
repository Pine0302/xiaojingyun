var province_id = 0;	//当前选择的省份id
var city_id = 0;		//当前选择的市id

$(document).ready(function(){
	//获取省市区
	var province = new PCAS('', '', '', '', '', '',3),	//获取所有省份
		html_p = '',
		html_c = '',
		html_a = '',
		checkAll,
		uniqueCityArr = new Array();
		
	for( j in province ){
		/*兼容旧数据*/
		if( province_arr.indexOf(province[j]) >= 0 && city_area_str == '' ){
			checkAll = 1;
		} else {
			checkAll = 0;
		}
		/*兼容旧数据*/
		
		if( j == 0 ){
			if( province_arr.indexOf(province[j]) >= 0 ){
				html_p = "<div class='cellSelected province_"+j+" selectedProvince' data-p_id='"+j+"'><img class='select' src='img/select1.png' /> <span>"+province[j]+"</span></div>";
			} else {
				html_p = "<div class='cellSelected province_"+j+"' data-p_id='"+j+"'><img class='select' src='img/select1.png' /> <span>"+province[j]+"</span></div>";
			}
		} else {
			if( province_arr.indexOf(province[j]) >= 0 ){
				html_p += "<div class='cell province_"+j+" selectedProvince' data-p_id='"+j+"'><img class='select' src='img/select1.png' /> <span>"+province[j]+"</span></div>";
			} else {
				html_p += "<div class='cell province_"+j+"' data-p_id='"+j+"'><img class='select' src='img/select1.png' /> <span>"+province[j]+"</span></div>";
			}
		}
		
		var city = new PCAS('', '', '', province[j], '', '',4);	//获取该省份的市
		
		html_c = "<div class='cellBox city_"+j+"' id='city'>";
		
		for( k in city ){
			//单独处理城市名为县的城市
			if( city[k] == '县' ){
				uniqueCityArr.push(j+'_'+k);
			}
			if( k == 0 ){
				if( city_arr.indexOf(city[k]) >= 0 || checkAll ){
					html_c += "<div class='city-div cellSelected province_city_"+j+" selectedCity' data-c_id='"+k+"' data-p_id='"+j+"'><img class='select' src='img/select1.png' /><span>"+city[k]+"</span></div>";
				} else {
					html_c += "<div class='city-div cellSelected province_city_"+j+"' data-c_id='"+k+"' data-p_id='"+j+"'><img class='select' src='img/select1.png' /><span>"+city[k]+"</span></div>";
				}
			} else {
				if( city_arr.indexOf(city[k]) >= 0 || checkAll ){
					html_c += "<div class='city-div cell province_city_"+j+" selectedCity' data-c_id='"+k+"' data-p_id='"+j+"'><img class='select' src='img/select1.png' /><span>"+city[k]+"</span></div>";
				} else {
					html_c += "<div class='city-div cell province_city_"+j+"' data-c_id='"+k+"' data-p_id='"+j+"'><img class='select' src='img/select1.png' /><span>"+city[k]+"</span></div>";
				}
			}
			
			var area = new PCAS('', '', '', province[j], city[k], '',5);	//获取该市的镇区
			
			html_a = "<div class='bottom area_"+j+"_"+k+" area_"+j+" area' id='area'>";
			for( y in area ){
				if( city_area_arr.indexOf(city[k]+'_'+area[y]) >= 0 || checkAll ){
					html_a += "<div class='area-div cellSelected' data-c_id='"+k+"' data-p_id='"+j+"'><span>"+area[y]+"</span></div>";
				} else {
					html_a += "<div class='area-div cell' data-c_id='"+k+"' data-p_id='"+j+"'><span>"+area[y]+"</span></div>";
				}
			}
			html_a += "</div>";
			$('#city_area').append(html_a);
		}
		html_c += "</div>";
		$('.cityBox').append(html_c);
	}
	$('#province').append(html_p);
	$('.cellBox').hide();
	$('.cellBox').eq(0).show();
	$('.area').hide();
	$('.area').eq(0).show();
	//处理县是否选择状态
	for( q in uniqueCityArr ){
		var uniqueProvinceCityId = uniqueCityArr[q].split('_'),
			uniqueProvinceId = uniqueProvinceCityId[0],
			uniqueCityId = uniqueProvinceCityId[1];
			
		checkUniqueCity(uniqueProvinceId,uniqueCityId);
	}
	
	getSelected(1);
	checkAllSelected();	//检测是否全选
})

$('.area_select_btn').click(function(){
	$(this).hide();
	$('.selected-areaBox').hide();
	$('.province').show();
	$("#openProvince").css("height",($('#openProvince').height() - 41)+'px');
});

$('.confirmBtn').click(function(){
	// if( !getSelected(2) ){
		// alert('请选择区域！');
		// return;
	// }
	getSelected(2);
	/*$('.province').hide();
	$('.area_select_btn').show();
	$('.selected-areaBox').show();*/
});

//显示更多省份
function openProvince(){
	$("#openProvince").hide();
	$("#province").css("height","100%").css("overflow","auto");
	$("#province").css("height",($('#province').height() - 41)+'px');
}

$('body').on('click','#province div',function(){
	getCityArea($(this).find('span'));
	$(this).parent().find(".cellSelected").removeClass("cellSelected").addClass("cell");
	$(this).removeClass("cell").addClass("cellSelected");
});
$('body').on('click','#province img',function () {
	getCityArea(this);
	$(this).parent().parent().find(".cellSelected").removeClass("cellSelected").addClass("cell");
	$(this).parent().removeClass("cell").addClass("cellSelected");
	// if( $(this).parent().hasClass("selectedProvince") ){
	if( $(this).attr("src") == "img/select2.png" ){
		$(this).attr("src","img/select1.png");
		$(this).parent().removeClass("selectedProvince");
		$('.city_'+province_id).find('img').attr('src','img/select1.png');
		$('.city_'+province_id).find('div').removeClass("selectedCity");
		$('.area_'+province_id).find('div').removeClass('cellSelected').addClass('cell');
	} else {
		$(this).attr("src","img/select2.png");
		$(this).parent().addClass("selectedProvince");
		$('.city_'+province_id).find('img').attr('src','img/select2.png');
		$('.city_'+province_id).find('div').addClass("selectedCity");
		$('.area_'+province_id).find('div').removeClass('cell').addClass('cellSelected');
	}
	checkAllSelectedBtn();
});
$('body').on('click','#city div',function(){
	getArea($(this).find('span'));
	$(this).parent().find(".cellSelected").removeClass("cellSelected").addClass("cell");
	$(this).removeClass("cell").addClass("cellSelected");
})
$('body').on('click','#city img',function () {
	getArea(this);
	$(this).parent().parent().find(".cellSelected").removeClass("cellSelected").addClass("cell");
	$(this).parent().removeClass("cell").addClass("cellSelected")
	// if( $(this).parent().hasClass("selectedCity") ){
	if( $(this).attr("src") == "img/select2.png" ){
		$(this).attr("src","img/select1.png");
		$(this).parent().removeClass("selectedCity");
		$('.area_'+province_id+'_'+city_id).find('div').removeClass('cellSelected').addClass('cell');
	} else {
		$(this).attr("src","img/select2.png");
		$(this).parent().addClass("selectedCity");
		$('.area_'+province_id+'_'+city_id).find('div').removeClass('cell').addClass('cellSelected');
	}
	checkCity();
	checkSingleSelected(province_id,city_id);
});
$('body').on('click','#area div',function(){
	if( $(this).hasClass('cell') ){
		$(this).removeClass("cell").addClass("cellSelected");
	} else {
		$(this).removeClass("cellSelected").addClass("cell");
	}
	checkArea();
	checkSingleSelected(province_id,city_id);
});

	var indexImg=1,
        imgSize = 100,         //图片尺寸 宽度
		moveTime = 500,        //切换动画时间
		perCount=6,
		saveMovePx = 0,
		lastCityId = 0,		//当前显示的第一个城市id
		lastCityIdArr = [0],
		lastLeftPx = [],	//记录上次左移像素
		lastRightPx = [],	//记录上次右移像素
		totalMovePx = 0,	//记录一共右移像素
		isMoving = 0;		//是否移动中
		
	$('body').on('click','.arrowLeft',function(){
		/*totalImg = $('.city_'+province_id).find("div").length;    //图片总数量
        if (indexImg>1) {
			indexImg--;
            $('.city_'+province_id).animate({
                left: -((indexImg - 1)*imgSize*perCount) + 'px'
            }, moveTime);
        }*/
		cityListLeft();
    });
    $('body').on('click','.arrowRight',function(){
		/*totalImg = $('.city_'+province_id).find("div").length;    //图片总数量
        if (indexImg<totalImg/perCount) { 
            $('.city_'+province_id).animate({
                left: -(indexImg*imgSize*perCount) + 'px'
            }, moveTime);
            indexImg++;
        }*/
		cityListRight();
    });

//城市列表左移，若要传参数，参数1必须是城市id
function cityListLeft(){
	if( isMoving ){
		return;
	}
	isMoving = 1;
	var cityId = -1,
		$cityDiv = $('.city_'+province_id).find(".city-div"),
		totalCity = -1;    //城市数量
	
	if( arguments[0] != undefined ){
		cityId = parseInt(arguments[0]);
		totalCity = cityId - 1;
	}
	
	// if( lastCityId < 0 ){
		// lastCityId = 0;
	// }
	var leftMoveTimes = 0;
	for( var i = lastCityId - 1; i > totalCity; i-- ){
		var cityDivWidth = $cityDiv.eq(i).width() + 24;	//实际宽度 + 外边距
		if( i == 0 ){
			var lastCityDivWidth = 679;
		} else if( i > 0 ){
			var lastCityDivWidth = $cityDiv.eq(i-1).width() + 24;	//实际宽度 + 外边距
		} else {
			var lastCityDivWidth = 0;
		}
		
		saveMovePx -= cityDivWidth;
		
		if( lastCityDivWidth == 679 ){
			lastCityId = 0;
			saveMovePx = 0;
		}
		
		/*if( lastCityDivWidth == 0 ){
			lastCityId = 0;
			saveMovePx = 0;
			return;
		}*/
		
		if( saveMovePx - lastCityDivWidth < totalMovePx - 679 ){
			$('.city_'+province_id).animate({
                left: -saveMovePx + 'px'
            }, moveTime);
			
			lastLeftPx.push( totalMovePx - saveMovePx );	//记录当前移动的像素
			lastCityId = i;
			totalMovePx = saveMovePx;
			leftMoveTimes++;
			
			if( lastCityIdArr.indexOf(lastCityId) == -1 ){
				lastCityIdArr.push(lastCityId);
			}
			
			if( cityId == -1 ){
				isMoving = 0;
				return;
			}
		} else if( cityId > -1 && cityId < lastCityId && lastCityId > 0 && i == totalCity + 1 ){
			totalMovePx -= lastRightPx[lastRightPx.length-1-leftMoveTimes];
			$('.city_'+province_id).animate({
                left: -totalMovePx + 'px'
            }, moveTime);
			saveMovePx = totalMovePx;
			
			for( var j = 0; j < lastCityIdArr.length; j++ ){
				if( lastCityIdArr[j] <= cityId && ((lastCityIdArr[j+1] != undefined && lastCityIdArr[j+1] > cityId) || lastCityIdArr[j+1] == undefined) ){
					lastCityId = lastCityIdArr[j];
				}
			}
		}
	}
	
	if( cityId > -1 ){
		saveMovePx = totalMovePx;
	}
	isMoving = 0;
}

//城市列表右移，若要传参数，参数1必须是城市id
function cityListRight(){
	if( isMoving ){
		return;
	}
	isMoving = 1;
	var cityId = -1;
		$cityDiv = $('.city_'+province_id).find(".city-div"),
		totalCity = $cityDiv.length;    //城市数量
	
	if( arguments[0] != undefined ){
		cityId = parseInt(arguments[0]);
		totalCity = cityId + 1;
	}
	
	for( var i = lastCityId; i < totalCity; i++ ){
		var cityDivWidth = $cityDiv.eq(i).width() + 24;	//实际宽度 + 外边距
		if( i == totalCity - 1 ){
			var nextCityDivWidth = 0;
		} else {
			var nextCityDivWidth = $cityDiv.eq(i+1).width() + 24;	//实际宽度 + 外边距
		}
		
		if( nextCityDivWidth == 0 ){
			// lastCityId = totalCity - 1;
			saveMovePx = totalMovePx;
			isMoving = 0;
			return;
		}
		saveMovePx += cityDivWidth;
		
		if( saveMovePx + nextCityDivWidth > totalMovePx + 679 ){
			$('.city_'+province_id).animate({
                left: -saveMovePx + 'px'
            }, moveTime);
			
			lastRightPx.push( saveMovePx - totalMovePx );	//记录当前移动的像素
			lastCityId = i + 1;
			totalMovePx = saveMovePx;
			
			if( lastCityIdArr.indexOf(lastCityId) == -1 ){
				lastCityIdArr.push(lastCityId);
			}
			
			if( cityId == -1 ){
				isMoving = 0;
				return;
			}
		}
	}
	if( cityId > -1 ){
		saveMovePx = totalMovePx;
	}
	isMoving = 0;
}

//显示该省份的市区
function getCityArea(obj){
	var p_id = $(obj).parent().data('p_id');
	var province_id_bak = province_id;
	if( p_id == province_id ){
		return;
	}
	province_id = p_id;
	city_id = 0;
	indexImg = 1;
	saveMovePx = 0;
	lastCityId = 0;		//当前显示的第一个城市id
	totalMovePx = 0;	//记录一共右移像素
	lastCityIdArr = [0];
	lastLeftPx = [];	//记录上次左移像素
	lastRightPx = [];	//记录上次右移像素
	
	$('.city_'+province_id).children('div').removeClass("cellSelected").addClass("cell");
	$('.city_'+province_id).children('div').eq(0).removeClass("cell").addClass("cellSelected");
	$('.cellBox').hide();
	$('.city_'+province_id).show();
	$('.area').hide();
	$('.area_'+province_id+'_'+city_id).show();
	$('.city_'+province_id_bak).css('left','0px');
}

//显示该市的镇区
function getArea(obj){
	var c_id = $(obj).parent().data('c_id');
	city_id = c_id;
	$('.area').hide();
	$('.area_'+province_id+'_'+city_id).show();
}

//检测该市是否有选择镇区，若无，则不勾选该市，反之勾选该市
function checkArea(){
	var areaCellSelected = $('.area_'+province_id+'_'+city_id).find('.cellSelected');
	if( areaCellSelected.length > 0 ){
		// $('.city_'+province_id).find('img').eq(city_id).attr('src','img/select2.png');
		$('.city_'+province_id).find('div').eq(city_id).addClass("selectedCity");
	} else {
		// $('.city_'+province_id).find('img').eq(city_id).attr('src','img/select1.png');
		$('.city_'+province_id).find('div').eq(city_id).removeClass("selectedCity");
	}
	checkCity();
}

function checkCity(){
	var cityCellSelected = $('.city_'+province_id).find('.selectedCity');
	if( cityCellSelected.length > 0 ){
		$('.province_'+province_id).addClass("selectedProvince");
		// $('.province_'+province_id).find('img').attr('src','img/select2.png');
	} else {
		$('.province_'+province_id).removeClass("selectedProvince");
		// $('.province_'+province_id).find('img').attr('src','img/select1.png');
	}
}

//区域关键字搜索
function searchArea(){
	var searchVal = $('.searchVal').val();
	if( searchVal == '' ){
		return;
	}
	//先搜索省，再搜索市，最后搜索区
	//省
	var $provinceDiv = $('#province').children('div');
	var provinceDivLen = $provinceDiv.length;
	for( var i = 0; i < provinceDivLen; i++ ){
		var provinceName = $provinceDiv.eq(i).find('span').text();
		//模糊搜索
		if( provinceName.indexOf(searchVal) >= 0 ){
			if( i > 11 ){	//大于11，显示更多省份
				openProvince();
			}
			var provinceDivScroll = 42 * i;
			$('#province').animate({
				scrollTop: provinceDivScroll
			}, 500);
			
			$provinceDiv.eq(i).find('span').click();
			return;
		}
	}
	//市
	var $cityBox = $('.cellBox');
	var cityBoxLen = $cityBox.length;
	for( var j = 0; j < cityBoxLen; j++ ){
		var $cityBoxDiv = $cityBox.eq(j).children('div');
		var cityBoxDivLen = $cityBoxDiv.length;
		
		for( var k = 0; k < cityBoxDivLen; k++ ){
			var cityName = $cityBoxDiv.eq(k).find('span').text();
			var p_id = $cityBoxDiv.eq(k).data('p_id');
			
			if( cityName.indexOf(searchVal) >= 0 ){
				if( p_id > 11 ){	//大于11，显示更多省份
					openProvince();
				}
				var provinceDivScroll = 42 * p_id;
				$('#province').animate({
					scrollTop: provinceDivScroll
				}, 500);
				
				$provinceDiv.eq(p_id).find('span').click();
				$cityBoxDiv.eq(k).find('span').click();
				cityListLeft(k);
				cityListRight(k);
				/*var cityPerCount = Math.ceil((k+3)/6);
				if( cityPerCount > 1 ){
					if( indexImg < cityPerCount ){
						for( var ii = indexImg; ii < cityPerCount; ii++ ){
							$('.arrowRight').click();
						}
					}
					if( indexImg > cityPerCount ){
						for( var ii = indexImg; ii > cityPerCount; ii-- ){
							$('.arrowLeft').click();
						}
					}
				}*/
				return;
			}
		}
	}
	//区
	var $areaBox = $('#city_area .area');
	var areaBoxLen = $areaBox.length;
	for( var y = 0; y < areaBoxLen; y++ ){
		var $areaBoxDiv = $areaBox.eq(y).children('div');
		var areaBoxDivLen = $areaBoxDiv.length;
		
		for( var p = 0; p < areaBoxDivLen; p++ ){
			var areaName = $areaBoxDiv.eq(p).find('span').text();
			var p_id = $areaBoxDiv.eq(p).data('p_id');
			var c_id = $areaBoxDiv.eq(p).data('c_id');
			
			if( areaName.indexOf(searchVal) >= 0 ){
				if( p_id > 11 ){	//大于11，显示更多省份
					openProvince();
				}
				var provinceDivScroll = 42 * p_id;
				$('#province').animate({
					scrollTop: provinceDivScroll
				}, 500);
				
				$provinceDiv.eq(p_id).find('span').click();
				$('.province_city_'+p_id).eq(c_id).find('span').click();
				cityListLeft(c_id);
				cityListRight(c_id);
				/*var cityPerCount = Math.ceil((c_id+3)/6);
				if( cityPerCount > 1 ){
					if( indexImg < cityPerCount ){
						for( var ii = indexImg; ii < cityPerCount; ii++ ){
							$('.arrowRight').click();
						}
					}
					if( indexImg > cityPerCount ){
						for( var ii = indexImg; ii > cityPerCount; ii-- ){
							$('.arrowLeft').click();
						}
					}
				}*/
				return;
			}
		}
	}
	alert('没有此区域，请重新搜索！');
}
//显示更多
$('body').on('click','.moreProvince',function(){
	$('.selected-province').removeClass('hidden-content');
	$(this).hide();
	$('.hiddenProvince').show();
});
$('body').on('click','.moreCity',function(){
	$('.selected-city').removeClass('hidden-content');
	$(this).hide();
	$('.hiddenCity').show();
});
$('body').on('click','.moreArea',function(){
	$('.selected-area').removeClass('hidden-content');
	$(this).hide();
	$('.hiddenArea').show();
});
//隐藏更多
$('body').on('click','.hiddenProvince',function(){
	$('.selected-province').addClass('hidden-content');
	$(this).hide();
	$('.moreProvince').show();
});
$('body').on('click','.hiddenCity',function(){
	$('.selected-city').addClass('hidden-content');
	$(this).hide();
	$('.moreCity').show();
});
$('body').on('click','.hiddenArea',function(){
	$('.selected-area').addClass('hidden-content');
	$(this).hide();
	$('.moreArea').show();
});

//获取已选择的省、市和区，status参数用来选择性隐藏元素
function getSelected(status){
	var $selectedProvince = $('.selectedProvince'),
		selectedProvinceLen = $selectedProvince.length,
		$selectedCity = $('.selectedCity'),
		regionProvince = '',
		regionProvince2 = '',
		regionCityArea = '',
		regionCity = '',
		regionArea = '',
		cityNum = 0,
		areaNum = 0;
		
	for( var i = 0; i < selectedProvinceLen; i++ ){
		regionProvince += $selectedProvince.eq(i).find('span').text()+',';
		regionProvince2 += $selectedProvince.eq(i).find('span').text()+'，';
		
		var p_id = $selectedProvince.eq(i).data('p_id'),
			// $provinceCity = $('.province_city_'+p_id),
			$provinceCity = $('.city_'+p_id+' .selectedCity'),
			provinceCityLen = $provinceCity.length;
			
		for( var j = 0; j < provinceCityLen; j++ ){
			// if( $provinceCity.eq(j).hasClass('selectedCity') ){
				var c_id = $provinceCity.eq(j).data('c_id'),
					cityName = $provinceCity.eq(j).find('span').text(),
					$selectedArea = $('.area_'+p_id+'_'+c_id+' .cellSelected'),
					selectedAreaLen = $selectedArea.length;
				
				regionCity += cityName+'，';
				cityNum++;
				
				for( var k = 0; k < selectedAreaLen; k++ ){
					regionCityArea += cityName+'_'+$selectedArea.eq(k).find('span').text()+',';
					regionArea += $selectedArea.eq(k).find('span').text()+'，';
					areaNum++;
				}
			// }
		}
		
	}
	// if( regionCityArea == '' ){
		// return false;
	// }
	
	regionProvince = regionProvince.slice(0,-1);
	regionProvince2 = regionProvince2.slice(0,-1);
	regionCityArea = regionCityArea.slice(0,-1);
	regionCity = regionCity.slice(0,-1);
	regionArea = regionArea.slice(0,-1);
	$('#region').val(regionProvince);
	$('#region_city_area').val(regionCityArea);
	$('.selected-province').text(regionProvince2).attr('data-province_num',selectedProvinceLen);
	$('.selected-city').text(regionCity).attr('data-city_num',cityNum);
	$('.selected-area').text(regionArea).attr('data-area_num',areaNum);
	
	if( status > 1 ){
		$('.province').hide();
		$('.area_select_btn').show();
		$('.selected-areaBox').show();
	}
	
	//检测是否文本溢出，显示更多按钮
	var selectedProvinceWidth = $('.selected-province').css('width').slice(0,-2),
		selectedCityWidth = $('.selected-city').css('width').slice(0,-2),
		selectedAreaWidth = $('.selected-area').css('width').slice(0,-2),
		checkedDivWidth = $('.checked-div').css('width').slice(0,-2);
	
	$('.hidden-btn').click();
	if( selectedProvinceLen > 12 && parseInt(selectedProvinceWidth) > parseInt(checkedDivWidth) ){
		$('.selected-province').css('width','82%');
		$('.hiddenProvince').click();
	} else {
		$('.moreProvince').hide();
	}
	if( cityNum > 12 && parseInt(selectedCityWidth) > parseInt(checkedDivWidth) ){
		$('.selected-city').css('width','82%');
		$('.hiddenCity').click();
	} else {
		$('.moreCity').hide();
	}
	if( areaNum > 12 && parseInt(selectedAreaWidth) > parseInt(checkedDivWidth) ){
		$('.selected-area').css('width','82%');
		$('.hiddenArea').click();
	} else {
		$('.moreArea').hide();
	}
	
	return true;
}

//检测是否全选
//单个省份
function checkSingleSelected(obj_province_id,obj_city_id){
	var $objCity = $('.province_city_'+obj_province_id).eq(obj_city_id),
		$objCityArea = $('.area_'+obj_province_id+'_'+obj_city_id),
		objCityAreaNum = $objCityArea.find('div').length,
		selectedAreaNum = $objCityArea.find('.cellSelected').length;
	
	if( objCityAreaNum == selectedAreaNum ){	//如果该市的所有区都已选，则该市为全选
		$objCity.find('img').attr('src','img/select2.png');
	} else {
		$objCity.find('img').attr('src','img/select1.png');
	}
	
	var $objProvince = $('.province_'+obj_province_id),
		$objProvinceCities = $('.province_city_'+obj_province_id),
		objProvinceCitiesNum = $objProvinceCities.length,
		allSelectedCityNum = 0;
	
	for( var i = 0; i < objProvinceCitiesNum; i++ ){
		var allSelectedCityImg = $objProvinceCities.eq(i).find('img').attr('src');
		//获取全选的市的数量，只要有一个市不是全选，则跳出循环
		if( allSelectedCityImg == 'img/select2.png' ){
			allSelectedCityNum++;
		} else {
			break;
		}
	}
	
	if( objProvinceCitiesNum == allSelectedCityNum ){	//如果该省的所有市都已选，则该省为全选
		$objProvince.find('img').attr('src','img/select2.png');
	} else {
		$objProvince.find('img').attr('src','img/select1.png');
	}
	
	checkAllSelectedBtn();
}

//所有已选的省份
function checkAllSelected(){
	var $selectedProvince = $('.selectedProvince'),
		selectedProvinceNum = $selectedProvince.length;
		
	for( var i = 0; i < selectedProvinceNum; i++ ){
		var selectedProvinceId = $selectedProvince.eq(i).data('p_id'),
		$objProvincecities = $('.province_city_'+selectedProvinceId),
		objProvinceCitiesNum = $objProvincecities.length;
		var p_name = $('.province_'+selectedProvinceId).find('span').text();
		if (p_name == '钓鱼岛' || p_name == '新加坡') 
		{
			var $objProvince = $('.province_'+selectedProvinceId);
			$objProvince.find('img').attr('src','img/select2.png');
			checkAllSelectedBtn();
		}
		else
		{
			for( var j = 0; j < objProvinceCitiesNum; j++ ){
				var objCityId = $objProvincecities.eq(j).data('c_id');
				
				checkSingleSelected(selectedProvinceId,objCityId);
			}
		}
	}
}

//全选按钮
function checkAllSelectedBtn(){
	var $selectedProvinceImg = $('#province').find('.select'),
		allProvinceNum = $('#province').find('div').length;
		
	for( var j = 0; j < allProvinceNum; j++ ){
		if( $selectedProvinceImg.eq(j).attr('src') == 'img/select1.png' ){
			$('.all-select .select').attr('src','img/select1.png');
			return;
		}
	}
	
	$('.all-select .select').attr('src','img/select2.png');
}

//全选
$('body').on('click','.all-select .select',function(){
	if( $(this).attr('src') == 'img/select1.png' ){
		$(this).attr('src','img/select2.png');
		
		$('#province').find('div').addClass('selectedProvince');
		$('#province').find('.select').attr('src','img/select2.png');
		$('.cityBox').find('.city-div').addClass('selectedCity');
		$('.cityBox').find('.select').attr('src','img/select2.png');
		$('#city_area').find('.area-div').removeClass('cell').addClass('cellSelected');
		$('#city_area').find('.select').attr('src','img/select2.png');
	} else {
		$(this).attr('src','img/select1.png');
		
		$('#province').find('div').removeClass('selectedProvince');
		$('#province').find('.select').attr('src','img/select1.png');
		$('.cityBox').find('.city-div').removeClass('selectedCity');
		$('.cityBox').find('.select').attr('src','img/select1.png');
		$('#city_area').find('.area-div').removeClass('cellSelected').addClass('cell');
		$('#city_area').find('.select').attr('src','img/select1.png');
	}
});
//单独处理城市的选择状态
function checkUniqueCity(obj_province_id,obj_city_id){
	var $objCity = $('.province_city_'+obj_province_id).eq(obj_city_id),
		$objCityArea = $('.area_'+obj_province_id+'_'+obj_city_id),
		objCityAreaNum = $objCityArea.find('div').length,
		selectedAreaNum = $objCityArea.find('.cellSelected').length;
	
	if( selectedAreaNum == 0 ){
		$objCity.removeClass('selectedCity');
	}
}




