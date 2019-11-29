<?php   
header("Content-type: text/html; charset=utf-8"); //svn
// require('../select_skin.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>满赠专区</title>
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
	<link type="text/css" rel="stylesheet" href="/mshop/web/static/css/style.css">
	<link type="text/css" rel="stylesheet" href="/weixinpl/mshop/css/css_green.css" />
	<link type="text/css" rel="stylesheet" href="/weixinpl/mshop/css/order_css/global.css" />
	<style type="text/css">
		body{margin:0;}
		.flex{-webkit-display:flex;display:flex;}
		.ellipsis{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
		.full-search{background-color:#fff;margin:8px 10px;height:34px;border-radius:15px;overflow:hidden;font-size:1.1rem;}
		.full-search .search-ipt{border:0;background-color:#fff;display:none;line-height:34px;box-sizing:border-box;width:100%;padding:0 10px;color:#888;outline:none;}
		.full-search .search-text{text-align:center;line-height:34px;color:#b1b1b1;}
		.full-search .search-text img{height:14px;vertical-align:middle;margin:-2px 5px 0 0;}
		.screen-content{background-color:#fff;overflow-x:auto;overflow-y:hidden;width:100%;height:45px;margin:0 0 1px 0;}
		.screen-content .screen-list{font-size:0;height:45px;}
		.screen-content .screen-list a{font-size:1.1rem;color:#747474;display:inline-block;margin:0 10px;line-height:43px;border-bottom:solid 2px #fff;height:100%;box-sizing:border-box;text-align:center;}
		.product-content{margin:0 0 80px 0;}
		.product-content .list{justify-content:space-between;background-color:#fff;margin:0 0 1px 0;padding:10px 15px;}
		.product-content .list .img-box{width:105px;height:105px;justify-content:center;align-items:center;}
		.product-content .list .img{width:100%;}
		.product-content .list .details{width:calc(100% - 105px);box-sizing:border-box;padding:0 0 0 15px;}
		.product-content .list .title{font-size:1.2rem;color:#1c1f20;margin:0 0 3px 0;}
		.product-content .list .small{font-size:1rem;color:#707070;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;overflow:hidden;word-break:break-all;line-height:1.2rem;height:2.4rem;}
		.product-content .list .tips{justify-content:space-between;margin:5px 0 0 0;}
		.product-content .list .tips .skin-bg{display:block;width:30px;height:30px;}
		.product-content .list .price{font-size:1.4rem;color:#f4212b;}
		.product-content .list .car-btn{width:100%;height:100%;}
		.attr-content{position:fixed;top:0;left:0;width:100%;height:100%;display:none;}
		.attr-content .attr-bg{background-color:rgba(0,0,0,.7);position:absolute;height:100%;width:100%;left:0;top:0;}
		.attr-content .attr-main{background-color:#fff;position:absolute;z-index:1;width:100%;box-sizing:border-box;padding:15px 0 0 0;bottom:-500px;}
		.attr-content .attr-main .attr-head{justify-content:space-between;padding:0 15px;}
		.attr-content .attr-head .img-box{width:82px;height:82px;}
		.attr-content .attr-head .details{width:calc(100% - 94px);box-sizing:border-box;padding:0 15px;}
		.attr-content .attr-head .title{font-size:1.2rem;color:#333;margin:0 0 10px 0;}
		.attr-content .attr-head .off-btn{width:12px;height:12px;margin:3px 0 0 0;}
		.attr-content .attr-head .price{font-size:1.5rem;color:#f12c20;}
		.attr-content .attr-list{padding:0 15px;}
		.attr-content .attr-list .list{margin:12px 0 0 0;}
		.attr-content .attr-list .name{font-size:1.1rem;color:#2a2a2a;}
		.attr-content .attr-list .column{font-size:0;}
		.attr-content .attr-list .column li{display:inline-block;font-size:1.1rem;color:#707070;box-sizing:border-box;padding:5px 15px;border:solid 1px #b7b7b7;margin:10px 10px 0 0;position:relative;overflow:hidden;}
		.attr-content .attr-list .column li i{position:absolute;width:24px;height:24px;bottom:-12px;right:-12px;transform:rotate(45deg);box-sizing:border-box;display:none;}
		.attr-content .attr-list .column li.skin-bd i{display:block;}
		.attr-content .attr-list .column li img{width:6px;transform:rotate(-45deg);position:absolute;top:9px;left:2px;}
		.attr-content .num-box{padding:0 15px;font-size:1.1rem;color:#2a2a2a;justify-content:space-between;align-items:center;margin:12px 0;}
		.attr-content .num-box .change-num{font-size:0;border-top:solid 1px #b2b2b2;border-bottom:solid 1px #b2b2b2;border-right:solid 1px #b2b2b2;}
		.attr-content .num-box .change-num button,.attr-content .num-box .change-num input{border-left:solid 1px #b2b2b2;width:28px;height:28px;box-sizing:border-box;}
		.attr-content .num-box .change-num button{background-color:#fff;}
		.attr-content .num-box .change-num input{text-align:center;}
		.attr-content .num-box .stock{flex-grow:1;text-align:right}
		.attr-content .attr-submit{width:100%;border:0;font-size:1.5rem;color:#fff;height:50px;}
		.count-content{position:fixed;bottom:0;left:0;width:100%;background-color:#fff;box-sizing:border-box;padding:7px 15px;justify-content:space-between;align-items:center;}
		.count-content .car{position:relative;width:30px;height:30px;}
		.count-content .car .skin-bg{display:block;width:30px;height:30px;}
		.count-content .car .skin-bg img{width:100%;height:100%;}
		.count-content .car .car-num{display:block;height:15px;width:15px;color:#fff;background-color:#f4212b;border-radius:8px;line-height:15px;text-align:center;font-size:.9rem;top:-5px;right:-5px;position:absolute;}
		.count-content .num{color:#1c1f20;font-size:1.2rem;width:calc(100% - 130px);box-sizing:border-box;padding:0 15px;}
		.count-content .num .price{color:#f4212b;}
		.count-content .btn{height:50px;width:100px;font-size:1.2rem;color:#fff;}
		.img-box img{ max-height: 100% }
		.w100 { width: 100% !important }
		.w33 { width: 33.3% !important }
		.hidden {display: none}
		.attr-content .off-btn { cursor: pointer }
		.attr-list .column li { cursor: pointer }
		.carts .attr-list{ max-height: 250px; overflow-y: auto; }
		.loading{text-align:center;margin:3rem 0 0 0;display:none;}
		.loading .img{width:3.2rem;margin:0 0 .5rem 0;}
		.loading .tips{color:#666;font-size:1.2rem;}
		.no-data{display:none;}
		.attr-list .column li { cursor:pointer }
		.change-num .reduce { cursor:pointer }
		.change-num .add { cursor:pointer }
	</style>
</head>
<body style="background:#efeff4;" >
	<div id='app' class='hidden'>
		<div class="full-search">
			<input type="text" name="search" placeholder="请输入您想寻找的商品" class="search-ipt" v-on:keyup.enter="search" />
			<div class="search-text"><img src="/weixinpl/mshop/images/goods_image/icon_search.png" />搜索您想寻找的商品</div>
		</div>
	
		<div class="screen-content">
			<div class="screen-list">
				<?php 
				$count = count($exchange_activities);
				foreach ($exchange_activities as $key => $value) { 
				?>
					<a href="#" data-id='<?php echo $value['id'] ?>' class="<?php if($_REQUEST['keyid']){ if($_REQUEST['keyid']==$value['id']) echo 'skin-color skin-bd';}else{if( $key == 0 ) echo 'skin-color skin-bd';} //if( $count == 1){ echo ' w100'; }else{ echo ' w33'; } ?>" v-on:click="greet" >满
						<?php if(OOF_P != 2) echo OOF_S ?>
						<?php echo $value['threshold'] ?>
    					<?php if(OOF_P == 2) echo OOF_S ?>
						专区
						</a>
				<?php } ?>
			</div>
		</div>

		<div class="product-content" >
			<a v-for="data in product_list" href="#" class="list flex" :data-id='data.id' :data-storenum='data.storenum' :data-num_per_person='data.num_per_person' :data-num_per_time='data.num_per_time' :data-propertyids='data.propertyids' :data-exchange_price='data.exchange_price' :data-can_not_select='data.can_not_select' :data-exchange_num='data.exchange_num' :data-count='data.count'>
				<div class="img-box flex"><img :src='data.default_imgurl' class="img"/></div>
				<div class="details">
					<p class="title ellipsis">{{ data.name }} </p>
					<!-- <div class="small">{{ data.description }}</div> -->
					<div class="small">{{ data.introduce }}</div>
					<div class="tips flex">
						<span class="price">
							<?php if(OOF_P != 2) echo OOF_S ?>
							{{ data.exchange_price }}
    						<?php if(OOF_P == 2) echo OOF_S ?>
						</span>
						<span class="skin-bg"  v-on:click="open_arrt" :data-propertyids='data.propertyids' :data-id='data.id' ><img :data-id='data.id' src="/weixinpl/mshop/images/full-car.png" class="car-btn" /></span>
					</div>
				</div>
			</a>
		</div>

		<div class="loading">
			<img src="/mshop/web/static/images/loadimg.gif" class="img">
			<p class="tips">数据加载中，请稍后...</p>
		</div>

		<div class="no-data">
			<img src="/mshop/web/static/images/no-data.png">
		</div>

		<?php if($is_diy_menu==0 && $price!=-1){?>
		<div class="count-content flex">
			<a href="/mshop/admin/index.php?m=exchange&a=my_traded&customer_id=<?php echo $this->customer_id_en ?>" > 
				<div class="car"><span class="skin-bg"><img src="/weixinpl/mshop/images/full-car.png"/></span><span class="car-num">{{ num }}</span></div>
			</a>
			<div class="num">合计 
				<span class="price">
	    			<?php if(OOF_P != 2) echo OOF_S ?>
					{{ total_price }}
	    			<?php if(OOF_P == 2) echo OOF_S ?>
				</span>
			</div>
			<button class="btn skin-bg" onclick="submit()">提交</button>
		</div>
		
		
		<?php }?>
	</div>

	<div class="attr-content carts" id='carts'>
		<div class="attr-bg" onclick="off_attr();"></div>
		<div class="attr-main">

			<div class="attr-head flex">
				<div class="img-box"><img :src='default_imgurl' class="img"/></div>
				<div class="details">
					<p class="title ellipsis">{{ description }}</p>
					<span class="price">
                		<?php if(OOF_P != 2) echo OOF_S ?>
						{{ price }}
                		<?php if(OOF_P == 2) echo OOF_S ?>
					</span>
				</div>
				<div class="off-btn" onclick="off_attr();"><img src="/weixinpl/mshop/images/close.png"/></div>
			</div>

			<div class="attr-list" >
				<div class="list" v-for="site in pro" >
					<p class="name">{{ site.name }}：</p>
					<ul class="column" >
						<li v-for="chi in site.chi" :data-id='chi.id'>{{ chi.name }}<i class="skin-bg"><img src="/weixinpl/mshop/images/checked.png"></i></li>
					</ul>
				</div>
			</div>

			<div class="num-box flex">
				<span class="name">数量：</span>
				<div class="change-num">
					<button class="reduce">-</button>
					<input onblur="modify(this);" id="pd-num" type="text" :value="count" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)">
					<button class="add">+</button>
				</div>
				<span class="stock">库存:{{ storenum }}</span>
			</div>

			<button class="attr-submit skin-bg" onclick='confirms()'>确认</button>

		</div>
	</div>
	<form action="/weixinpl/mshop/order_form.php?customer_id=<?php echo $this->customer_id_en ?>" method="get" accept-charset="utf-8" class='hidden' id='ex_form'>
		<input type="text" name="exchange">
	</form>

</body>
<script type="text/javascript" src="/weixinpl/mshop/assets/js/jquery.min.js"></script>  
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.8/vue.min.js"></script>  
<script type="text/javascript" src="/weixinpl/mshop/js/global.js"></script>
<script type="text/javascript">
	var customer_id = '<?php echo $this->customer_id ?>';
	var customer_id_en = '<?php echo $this->customer_id_en ?>';
	var user_id = '<?php echo $this->user_id ?>';
	var local_id = customer_id+'_'+user_id+'_ex';
	var product_lists = '<?php echo json_encode($exchange_products); ?>'
	var product_lists = eval('(' + product_lists + ')');
	var w_width = document.body.clientWidth;

	var exchange_list = localStorage.getItem(local_id);
	if( exchange_list == null || exchange_list == '' ){

	}else{
		exchange_list = JSON.parse(exchange_list)
	}
	console.log(exchange_list)


	var num = 0;
	var total_price = 0.00;
	init()

	pro_arr = exchange_list
	if( exchange_list == null || exchange_list == '' ){
		pro_arr = new Array();
	}

	function init(){
		if( exchange_list!=null && typeof(exchange_list) == 'object' ){
			$.each(exchange_list,function(index,e){
				if( e[1][9] == 1 ){
					num = parseInt(num) + parseInt(e[1][2])
					total_price = parseFloat(total_price) + ( parseFloat(e[1][10]) * parseInt(num) )
				}
			})
		}
	}

	// 商品列表
	_pl = new Vue({
		el: '#app',
		data: {
			product_list: product_lists,
			attributes: '',
			total_price: total_price.toFixed(2),
			num: num
		},
		methods: {
			greet: function (obj) {
				var ex_id = $(obj.target).data('id');
				var ex_ids = obj.target.dataset.id;
				console.log(ex_id,ex_ids)
				var self = this
				$.ajax({
					url:"/mshop/admin/index.php?m=exchange&a=get_exchange_products&exchange_id="+ex_id,
					async: false,
					dataType: 'json',
					success:function(result){
						Vue.set(self.$data,'product_list',result.data)
						// self._data.product_list = result.data
					}
				});
			},
			search: function(obj){
				var ex_id = $('.screen-list').find('.skin-bd').data('id')
				var search = $('[name="search"]').val()
				location.href = '/mshop/admin/index.php?m=exchange&a=full_give&customer_id='+customer_id_en+'&keyid='+ex_id+'&price=<?php echo $price ?>'+'&search='+search
			},
			open_arrt: function(obj){

				$('.list .column').find('li').removeClass('skin-bd skin-color');
				$('body').css('overflow','hidden');
				var is_diy_menu = "<?php echo $is_diy_menu;?>";
				var price = "<?php echo $price;?>";
				if( is_diy_menu==1 || price==-1 ){ //从商城自定义链接进入
					showAlertMsg("提示","该商品需要满相应金额才能购买！请先前往商城选购商品！<br>&nbsp;&nbsp;&nbsp;&nbsp;如果你购物车已满足金额条件，请在下单页面将此满赠特价产品加入付款即可","去选购",jump_to_shop);
					return;
				}

				var parent = $(obj.target).parents('.list');

                var pid = obj;
				// var pid = parent.data('id')
				// console.log($(obj.target).data('id'),obj.target.dataset.id)
				// var propertyids = parent.data('propertyids')
				var pid = $(obj.target).parent().attr('data-id');
				var propertyids = $(obj.target).parent().attr('data-propertyids');
				var can_not_select = parent.data('can_not_select')
				var eid = $('.screen-list').find('.skin-bd').data('id')
				add_pid = pid;

				if( can_not_select == true ){
					showAlertMsgNoclose('提示','超过商品可选次数',"知道了")
					return false;
				}

				var count = 1;
				if( pro_arr != null ){
					$.each(pro_arr,function(index,e){
						console.log(e)
						if( e[1][0] == pid){
							count = e[1][2];
						}
					})
				}
				$.ajax({
					url:"/mshop/admin/index.php?m=exchange&a=get_product_pro_html&propertyids="+propertyids+'&pid='+pid+'&eid='+eid,
					async: false,
					success:function(data){
						console.log(data)
						$('#carts').html(data);
					}
				});
				$('body').css({'overflow':'hidden','height':'100%','position':'fixed'});
				$('.attr-content').fadeToggle();
				$('.attr-content .attr-main').animate({'bottom':'0'},500);
			}
		}
	})

	// 属性窗
	carts = new Vue({
		el: '#carts',
		data: {
			description: 'description',
			price: 'price',
			storenum: 'storenum',
			pro: '',
			default_imgurl: 'default_imgurl',
			count: 1
		}
	})
	$('#app').show()

	function jump_to_shop(){
		location.href = "/weixinpl/common_shop/jiushop/index.php?customer_id=<?php echo $customer_id?>";
	}

	/*开启属性框*/
	function open_arrt(obj){
		$('.list .column').find('li').removeClass('skin-bd skin-color');
		var is_diy_menu = "<?php echo $is_diy_menu;?>";
		var price = "<?php echo $price;?>";
		if( is_diy_menu==1 || price==-1 ){ //从商城自定义链接进入
			showAlertMsg("提示","该商品需要满相应金额才能购买！请先前往商城选购商品！<br>&nbsp;&nbsp;&nbsp;&nbsp;如果你购物车已满足金额条件，请在下单页面将此满赠特价产品加入付款即可","去选购",jump_to_shop);;
			return;
		}

		var parent = $(obj).parents('.list');
		console.log(parent)

		var pid = parent.data('id')
		console.log(pid,$(obj).data('id'))
		var propertyids = parent.data('propertyids')
		var can_not_select = parent.data('can_not_select')
		var eid = $('.screen-list').find('.skin-bd').data('id')
		add_pid = pid;

		if( can_not_select == true ){
			showAlertMsgNoclose('提示','超过商品可选次数',"知道了")
			return false;
		}

		var count = 1;
		if( pro_arr != null ){
			$.each(pro_arr,function(index,e){
				console.log(e)
				if( e[1][0] == pid){
					count = e[1][2];
				}
			})
		}
		$.ajax({
			url:"/mshop/admin/index.php?m=exchange&a=get_product_pro&propertyids="+propertyids+'&pid='+pid+'&eid='+eid,
			async: false,
			dataType: 'json',
			success:function(data){
				console.log(data)
				carts._data.default_imgurl = data.data.default_imgurl
				carts._data.price = data.data.exchange_price
				carts._data.storenum = data.data.storenum
				carts._data.description = data.data.name
				add_price = data.data.exchange_price
				carts._data.pro = data.data.pro
				carts._data.count = count
			}
		});
		$('body').css({'overflow':'hidden','height':'100%','position':'fixed'});
		$('.attr-content').fadeToggle();
		$('.attr-content .attr-main').animate({'bottom':'0'},500);
	}

	//确认
	function confirms(){
		var bool = true;
		var pro = '';
		$('.attr-list .list').each(function(){
			var name = $(this).find('.name').text();
			var pid = $(this).find('.skin-bd').data('id');
			if( typeof(pid) != 'number' ){
				bool = false;
				showAlertMsgNoclose('提示','请选择'+name,"知道了")
				return false;
			}
			if( pro ){
				pro = pro + '_' + pid;
			}else{
				pro = pid;
			}
		})
		add_count = $('#pd-num').val()
		console.log(pro)
		add_pro = pro;
		if( bool ){
			add_product()
		}
	}

	// 添加产品
	function add_product(){
		// pro_arr = localStorage.getItem(local_id);
		console.log(pro_arr)
		pro_info = new Array()
		pro_info_arr = new Array()
		is_add = true
		console.log(pro_arr)
		console.log('add_pid'+add_pid)
		add_exchange_id =  $('.screen-list').find('.skin-bd').data('id')
		// add_exchange_price =  $('[data-id="'+add_pid+'"]').attr('data-exchange_price')
		add_exchange_price =  $('.list[data-id="'+add_pid+'"]').data('exchange_price');  //报障20682
		add_storenum =  $('[data-id="'+add_pid+'"]').data('storenum')
		add_exchange = 1;
		add_live_room_id = '-1';
		add_is_collage_product = '';
		add_act_type = '';
		add_act_id = '';

		var num_per_time =  $('[data-id="'+add_pid+'"]').data('num_per_time')
		var exchange_num =  $('[data-id="'+add_pid+'"]').data('exchange_num')
		var can_not_select =  $('[data-id="'+add_pid+'"]').data('can_not_select')
		var select_exchange_count =  0;
		var select_exchange_pid_count =  0;
		var is_exchange_pid_count =  0;
		var change_index =  0;
		$.each(pro_arr,function(index,e){
			if( e[1][8] == add_exchange_id ){
				if( e[1][0] == add_pid && e[1][1]==add_pro ){
					select_exchange_count += parseInt(add_count);
					select_exchange_pid_count += parseInt(add_count);
					is_exchange_pid_count = e[1][2]
				}else if( e[1][0] == add_pid ){
					select_exchange_count += parseInt(e[1][2]);
					select_exchange_pid_count += parseInt(e[1][2]);
				}else{
					select_exchange_count += parseInt(e[1][2]);
				}
				console.log(select_exchange_count)
			}
		})
		select_exchange_pid_count = parseInt(select_exchange_pid_count) - parseInt(is_exchange_pid_count) + parseInt(add_count)

		$.each(pro_arr,function(index,e){
			if( e[1][0] == add_pid && e[1][1]==add_pro && e[1][8] == add_exchange_id ){
				num = parseInt(num) + parseInt(add_count) - parseInt(e[1][2])
				// e[1][2] = parseInt(add_count);
				is_add = false
				change_index = index;
			}
		})

		console.log(add_pid,add_pro,add_exchange_id)
		if( is_add ){
			select_exchange_count += parseInt(add_count);
		}
		console.log(exchange_num,select_exchange_count,select_exchange_pid_count)
		if( add_storenum != -1 && select_exchange_pid_count > add_storenum ){
			showAlertMsgNoclose('提示','库存不足',"知道了")
			return false
		}
		if( num_per_time != -1 && select_exchange_pid_count > num_per_time ){
			showAlertMsgNoclose('提示','超过商品每次可选数量',"知道了")
			console.log(num_per_time,add_count)
			return false
		}
		if( can_not_select ){
			showAlertMsgNoclose('提示','超过活动每次可选数量',"知道了")
			return false
		}else if( is_add==false ){
			pro_arr[change_index][1][2] = parseInt(add_count);
		}

		if( is_add ){
			add_count = parseInt(add_count);
			pro_info.push(add_pid,add_pro,add_count,'',add_live_room_id,add_is_collage_product,add_act_type,add_act_id,add_exchange_id,1,add_exchange_price);
			pro_info_arr.push('-1',pro_info);
			pro_arr.push(pro_info_arr)
			num = parseInt(num) + parseInt(add_count)
		}
		var total_exchange_prices = 0;
		$.each(pro_arr,function(index,e){
			total_exchange_prices += parseFloat(e[1][10]) * parseFloat(e[1][2]);
		})
		_pl._data.total_price = total_exchange_prices.toFixed(2)
		_pl._data.num = num

		console.log(pro_arr)

		localStorage.setItem(local_id,JSON.stringify(pro_arr));
		save_exchange()

		$('.attr-content .off-btn').click();
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
		// var url = '/weixinpl/mshop/order_form.php';
		// console.log(pro_arr)
		// var value = 'exchange='+JSON.stringify(pro_arr);
		// post(url,value)
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

	$('.full-search .search-text').keypress(function(e) {  
		 
	}); 

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

	/*分类滚动*/
	var list_len = $('.screen-list a').length;
	switch(list_len)
	{
		case 1:
			$('.screen-content').hide();
			break;

		case 2:
			var list_w = w_width/2-20;
			$('.screen-list a').css('min-width',list_w+'px');
			break;

		case 3:
			var list_w = w_width/3-20;
			$('.screen-list a').css('min-width',list_w+'px');
			break;
		default:
			var list_w = w_width/3-20;
			var screen_width = (list_w+20) * list_len;
			$('.screen-list a').css('min-width',list_w+'px');
			$('.screen-list').css('min-width',screen_width + 40 +'px');
	}

	/*分类切换*/
	$('.screen-list a').on('click',function(){
		var i = $(this).index();
		$('.screen-list a').removeClass('skin-color skin-bd').eq(i).addClass('skin-color skin-bd');
	});
	/*产品属性选择*/
	$(document).on("click", ".attr-list .column li", function(){
		var box = $(this).parent('.column');
		box.find('li').removeClass('skin-bd skin-color');
		$(this).addClass('skin-bd skin-color');
	});

	/*加减框*/
	$(document).on("click", ".change-num .reduce", function(){
		var num = $('#pd-num').val();
		if(num <= 1){
			return false;
		};
		num --;
		$('#pd-num').val(num);
	});

	$(document).on("click", ".change-num .add", function(){
		var num = $('#pd-num').val();
		num ++;
		$('#pd-num').val(num);
	});
	function clearNoNum(obj){
		//先把非数字的都替换掉，除了数字
		obj.value = obj.value.replace(/[^\d]/g,"");
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
	};
	/*关闭属性框*/
    var vuy = 1;
	function off_attr(){
        if(vuy == 1){
            vuy = 0;
		$('body').css({'overflow':'auto','position':'inherit'});
		$('.attr-main').animate({'bottom':'-500px'},300);
        $('.attr-content').fadeToggle(300);
        setTimeout("vuy=1",300);
        }
	}

	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	    // 通过下面这个API隐藏右上角按钮
	    WeixinJSBridge.call('hideOptionMenu');
	});
</script>
<script>

</script>
</html>	