<?php   
header("Content-type: text/html; charset=utf-8"); //svn
// require('../../config.php');
// require('../select_skin.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>我的满赠</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta content="no" name="apple-touch-fullscreen">
	<meta name="MobileOptimized" content="320"/>
	<meta name="format-detection" content="telephone=no">
	<meta name=apple-mobile-web-app-capable content=yes>
	<meta name=apple-mobile-web-app-status-bar-style content=black>
	<meta http-equiv="pragma" content="nocache">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
	<link type="text/css" rel="stylesheet" href="/weixinpl/mshop/assets/css/amazeui.min.css" />
	<link type="text/css" rel="stylesheet" href="/mshop/web/static/css/style.css">
	<link type="text/css" rel="stylesheet" href="/weixinpl/mshop/css/css_green.css" />
	<link type="text/css" rel="stylesheet" href="/weixinpl/mshop/css/goods/global.css" />
	<style type="text/css">
		body{margin:0;}
		.flex{-webkit-display:flex;display:flex;}
		.ellipsis{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
		.full-search{background-color:#fff;margin:8px 10px;height:34px;border-radius:15px;overflow:hidden;font-size:1.1rem;}
		.full-search .search-ipt{border:0;background-color:#fff;display:none;line-height:34px;box-sizing:border-box;width:100%;padding:0 10px;color:#888;outline:none;}
		.full-search .search-text{text-align:center;line-height:34px;color:#b1b1b1;}
		.full-search .search-text img{height:14px;vertical-align:middle;margin:-2px 5px 0 0;}
		.count-content{position:fixed;bottom:0;left:0;width:100%;background-color:#fff;box-sizing:border-box;padding:7px 15px;justify-content:space-between;align-items:center;}
		.count-content .car{position:relative;width:30px;height:30px;}
		.count-content .car .skin-bg{display:block;width:30px;height:30px;}
		.count-content .car .skin-bg img{width:100%;height:100%;}
		.count-content .car .car-num{display:block;height:15px;width:15px;color:#fff;background-color:#f4212b;border-radius:8px;line-height:15px;text-align:center;font-size:1rem;top:-5px;right:-5px;position:absolute;}
		.count-content .num{color:#1c1f20;font-size:1.2rem;width:calc(100% - 130px);box-sizing:border-box;padding:0 15px;}
		.count-content .num .price{color:#f4212b;}
		.count-content .btn{height:50px;width:100px;font-size:1.2rem;color:#fff;}
		.traded-title{font-size:1.4rem;color:#1c1f20;background-color:#fff;padding:0 15px;height:45px;line-height:45px;margin:0 0 1px 0;}
		.traded-title .icon{vertical-align:middle;margin:-2px 10px 0 0;height:23px;}

		.product-content{margin:0 0 80px 0;}
		.product-content .list{justify-content:space-between;background-color:#fff;margin:0 0 1px 0;padding:10px 15px;}
		.product-content .list .img-box{width:105px;height:105px;justify-content:center;align-items:center;}
		.product-content .list .img{width:100%;}
		.product-content .list .details{width:calc(100% - 105px);box-sizing:border-box;padding:0 0 0 15px;}
		.product-content .list .title{font-size:1.2rem;color:#1c1f20;margin:0 0 3px 0;}
		.product-content .list .small{font-size:1rem;color:#707070;margin:0 0 10px 0;}
		.product-content .list .tips{justify-content:space-between;margin:10px 0 0 0;}
		.product-content .list .tips .skin-bg{display:block;width:30px;height:30px;}
		.product-content .list .price{font-size:1.5rem;color:#f4212b;}
		.product-content .list .car-btn{width:100%;height:100%;}

		.product-content .list .change-num{font-size:0;text-align:right;color:#2a2a2a;}
		.product-content .list .change-num button{width:28px;height:28px;box-sizing:border-box;border:solid 1px #b2b2b2;font-size:1.35rem;outline:none;}
		.product-content .list .change-num input{text-align:center;width:28px;height:28px;border-top:solid 1px #b2b2b2;border-bottom:solid 1px #b2b2b2;box-sizing:border-box;font-size:1.35rem;outline:none;}
		.hidden{display: none}
		.loading{text-align:center;margin:3rem 0 0 0;display:none;}
		.loading .img{width:3.2rem;margin:0 0 .5rem 0;}
		.loading .tips{color:#666;font-size:1.2rem;}
		.no-data{display:none;}
		.prompt-content{text-align:center;padding:.6rem 0;color:#fff;font-size:1.1rem;}
	</style>
</head>
<body style="background:#efeff4;">
	<div id='app' class='hidden'>
		<!-- <div class="full-search">
			<input type="text" name="search" placeholder="请输入您想寻找的商品" class="search-ipt" />
			<div class="search-text"><img src="/weixinpl/mshop/images/goods_image/icon_search.png" />搜索您想寻找的商品</div>
		</div> -->

		<div class="traded-title">
			<img src="/weixinpl/mshop/images/icon-traded.png" class="icon">我的满赠
		</div>

		<div class="product-content" >
			<div class="list flex product" v-for="data in product_list" :data-id='data.id' :data-storenum='data.storenum' :data-exchange_id='data.exchange_id' :data-exchange_num='data.exchange_num' :data-count='data.count' :data-num_per_person='data.num_per_person' :data-num_per_time='data.num_per_time' :data-pros='data.pros'>
				<div class="img-box flex"><img :src="data.default_imgurl" class="img"/></div>
				<div class="details">
					<p class="title ellipsis">{{ data.name}}</p>
					<div class="small">{{ data.pro }}</div>
					<p class="price">
        				<?php if(OOF_P != 2) echo OOF_S ?>
						{{ data.exchange_price }}
        				<?php if(OOF_P == 2) echo OOF_S ?>
					</p>
					<div class="change-num">
						<button class="reduce" onclick="add_product(this)">-</button>
						<input onblur="modify(this);" name="pd-num" type="text" :value="data.count" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" readonly="true">
						<button class="add" onclick="add_product(this)">+</button>
					</div>
				</div>
			</div>
		</div>

		<div class="loading">
			<img src="/mshop/web/static/images/loadimg.gif" class="img">
			<p class="tips">数据加载中，请稍后...</p>
		</div>

		<div class="no-data">
			<img src="/mshop/web/static/images/no-data.png">
		</div>

		<div class="count-content flex">
			<!-- <div class="car"><span class="skin-bg"><img src="images/full-car.png"/></span><span class="car-num">11</span></div> -->
			<div class="num">合计 
				<span class="price" id="all_price">
        			<?php if(OOF_P != 2) echo OOF_S ?>
					{{ total_price }}
        			<?php if(OOF_P == 2) echo OOF_S ?>
				</span>
			</div>
			<button class="btn skin-bg" onclick="submit()">提交</button>
		</div>
	</div>

	<form action="/weixinpl/mshop/order_form.php?customer_id=<?php echo $this->customer_id_en ?>" method="get" accept-charset="utf-8" class='hidden' id='ex_form'>
		<input type="text" name="exchange">
	</form>
</body>
<script type="text/javascript" src="/weixinpl/mshop/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="/weixinpl/js/vue.min.js"></script>  
<script type="text/javascript" src="/weixinpl/mshop/js/global.js?v=<?php echo time(); ?>"></script>
<script type="text/javascript">
	var customer_id = '<?php echo $this->customer_id ?>';
	var customer_id_en = '<?php echo $this->customer_id_en ?>';
	var user_id = '<?php echo $this->user_id ?>';
	var local_id = customer_id+'_'+user_id+'_ex';
	var exchange_list = localStorage.getItem(local_id);
	if( exchange_list != null ){
		exchange_list = JSON.parse(exchange_list)
	}
	pro_arr = exchange_list
	if( pro_arr == null ){
		pro_arr = new Array();
	}
	if( pro_arr.length == 0 ){
		window.setTimeout(clear,500)
	}

	init()
	function init(){
		$.ajax({
			url:"/mshop/admin/index.php?m=exchange&a=get_traded",
			data:{'exchange_list':exchange_list,'search':'<?php echo $search ?>'},
			async: false,
			dataType: 'json',
			success:function(result){
				console.log(result)
				_pl = new Vue({
					el: '#app',
					data: {
						product_list: result.data.exchange_list,
						total_price: result.data.total_price.toFixed(2)
					}
				})
			}
		});
		$('#app').show()
	}

	// 添加产品
	function add_product(obj){
		var is_del = false;
		pro_info = new Array()
		pro_info_arr = new Array()
		is_add = true

		type = $(obj).hasClass('add')
		var self = $(obj).parents('.product')

		global_exchange_num = self.data('exchange_num');
		global_exchange_id = self.data('exchange_id');

		num_per_time = self.data('num_per_time')
		exchange_pros = self.data('pros')

		add_pid = self.data('id');
		add_storenum = self.data('id');
		add_count = self.find('[name="pd-num"]').val()
		if( type ){
			add_count = parseInt(add_count) + 1;
		}else{
			add_count = parseInt(add_count) - 1;
		}
		if( add_count < 1 ){
			showConfirmMsg(
				"删除产品",
				"你要删除吗?",
				"确定删除",
				"取消",
				function(){/*确认删除事件*/
					build_exchange()
					window.setTimeout(function(){
						location.href = '/mshop/admin/index.php?m=exchange&a=my_traded&customer_id=<?php echo $this->customer_id_en ?>'
					},500)
					if( pro_arr.length == 0 ){
						window.setTimeout(clear,500)
					}
				},
				function(){/*取消事件*/
					add_count = parseInt(add_count) + 1;
					// 数量变动
					var box = $(obj).parent('.change-num');
					box.find('input').val(add_count);
				},
				function(){/*取消事件*/
					add_count = parseInt(add_count) + 1;
					// 数量变动
					var box = $(obj).parent('.change-num');
					box.find('input').val(add_count);
				}
			);
		}else{
			build_exchange()
		}

		// 数量变动
		var box = $(obj).parent('.change-num');
		box.find('input').val(add_count);
	}

	// 构造换赠数据
	function build_exchange(){
		var total_price = 0;
		var select_exchange_count = 0;
		var del_index = -1;
		
		$.each(pro_arr,function(index,e){
			if( e[1][8] == global_exchange_id ){
				if( e[1][0] == add_pid && e[1][1]==exchange_pros ){
					select_exchange_count += add_count;
				}else{
					select_exchange_count += parseInt(e[1][2]);
				}
			}
		})
		console.log(select_exchange_count,num_per_time,add_storenum)
		if( num_per_time != -1 && select_exchange_count > num_per_time && type ){
			add_count  -= 1;
			window.setTimeout(function(){
				showAlertMsgNoclose('提示','超过商品每次可选数量',"知道了");
			},500)
			return false;
		}
		if( add_storenum != -1 && select_exchange_count > add_storenum && type ){
			add_count  -= 1;
			window.setTimeout(function(){
				showAlertMsgNoclose('提示','库存不足',"知道了");
			},500)
			return false;
		}
		if( global_exchange_num != -1 && select_exchange_count > global_exchange_num && type ){
			add_count  -= 1;
			window.setTimeout(function(){
				showAlertMsgNoclose('提示','超过活动每次可选数量',"知道了");
			},500)
			return false;
		}
		$.each(pro_arr,function(index,e){
			console.log(e[1][0] , add_pid , e[1][1],exchange_pros , e[1][8] , global_exchange_id)
			if( e[1][0] == add_pid && e[1][1]==exchange_pros && e[1][8] == global_exchange_id ){
				e[1][2] = parseInt(add_count);
				if( add_count < 1 ){
					del_index = index;
				}
			}
			
			price = parseInt(e[1][2]) * parseFloat(e[1][10]);
			console.log(price,total_price)
			total_price = parseFloat(total_price) + parseFloat(price);
		})
		if( del_index != -1 ){
			pro_arr.splice(del_index,1);
			$.ajax({
				url:"/mshop/admin/index.php?m=exchange&a=get_traded",
				data:{'exchange_list':pro_arr},
				async: false,
				dataType: 'json',
				success:function(result){
					_pl._data.product_list = result.data.exchange_list
					_pl._data.total_price = result.data.total_price
				}
			});
		}

		$('#all_price').text('￥'+total_price.toFixed(2))
		localStorage.setItem(local_id,JSON.stringify(pro_arr));
		save_exchange()
	}

	function clear(){
		showAlertMsgNoclose('提示','已清空满赠专区购物车,自动跳转至满赠专区!',"知道了",function(){
			location.href = '/mshop/admin/index.php?m=exchange&a=full_give&customer_id='+customer_id_en
		})
	}

	// 保存满赠数据
	function save_exchange(){
		$.ajax({
			url:"/mshop/admin/index.php?m=exchange&a=save_exchange_list",
			data:{'exchange':JSON.stringify(pro_arr)},
			async: false,
			dataType: 'json',
			success:function(data){
			}
		});
	}

	// 提交
	function submit(){
		$('[name="exchange"]').val(JSON.stringify(pro_arr))
		$('#ex_form').submit()
	}

	// post提交
	function post(url,value){
	    var objform = document.createElement("form");
	    // 循环参数
	    url_arr = value.split('&');
	    for (var i = 0; i < url_arr.length; i++) {
	        value_arr = url_arr[i].split('=');
	        var objInput = document.createElement("input");
	        objInput.type = "hidden";
	        objform.appendChild(objInput);
	        objInput.value = value_arr[1];
	        objInput.name = value_arr[0];
	        // objInput = '';
	    }
	    objform.action = url;
	    objform.target = "fraGrid"
	    objform.method = "POST"
	    document.body.appendChild(objform);
	    objform.submit();
	}

	/*搜索效果*/
	$('.full-search .search-text').on('click',function(){
		$(this).hide();
		$('.full-search .search-ipt').show();
		$('.full-search .search-ipt').focus();
	});
	$('.full-search .search-ipt').on('blur',function(){
		$(this).hide();
		$('.full-search .search-text').show();
	});
	/*回车键搜索*/
	document.onkeydown = function (e) {
        var e = e || window.event;
        if ((e.keyCode || e.which) == 13) {
            var search = $('.search-ipt').val();
            location.href = '/mshop/admin/index.php?m=exchange&a=my_traded&exchange_list='+exchange_list+'&search='+search
   //          $.ajax({
			// 	url:"/mshop/admin/index.php?m=exchange&a=get_traded",
			// 	data:{'exchange_list':exchange_list,'search':search},
			// 	async: false,
			// 	dataType: 'json',
			// 	success:function(result){
			// 		console.log(result.data.exchange_list)
			// 		_pl._data.product_list = result.data.exchange_list
			// 	}
			// });
        }
    }

	function clearNoNum(obj){
		//先把非数字的都替换掉，除了数字
		obj.value = obj.value.replace(/[^\d]/g,"");
		count();
	}

	function modify(obj){
	    var a = parseInt($(obj).val(), 10);
	    if ("" == $(obj).val()) {
	        $(obj).val(1);
	        return
	    }
	    if (!isNaN(a)) {
	        if (1 > a || a > 999) {
	            $(obj).val(1);
	            return
	        } else {
	            $(obj).val(a);
	            return
	        }
	    } else {
	        $(obj).val(1);
	    }
	    count();
	};
	/*统计总价*/
	function count(){
		// var all_price = 0;
		// $('.product-content .list').each(function(){
		// 	var price = parseFloat($(this).find('.price').text().replace(/[^0-9]/ig,""));
		// 	var num = $(this).find('input[name="pd-num"]').val();
		// 	all_price += price*num;
		// });
		// var attrPrice = all_price.toString().split("").reverse(); 
		// var result = [];
		// for(var i = 0;i < attrPrice.length; i = i+3){
		// 	result.push(attrPrice.slice(i,i+3).reverse().join(""));
		// }
		// $('#all_price').html('￥'+result.reverse().join(","));
	}
	
</script>
</html>	