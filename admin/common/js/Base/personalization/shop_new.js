/*
Powered by weisanyun.cn
东莞市商二信息科技有限公司
*/

var shop_obj={

	skin_init:function(){
		$('#skin li .item').click(function(){
			if(!confirm('您确定要选择此风格吗？')){return false};
			/*$.post('?', "do_action=shop.skin_mod&SId="+$(this).attr('SId'), function(data){
				if(data.status==1){
					window.location.reload();
				}
			}, 'json');*/
			document.location="fengge.php?customer_id="+customer_id+"&general_customer_id="+general_customer_id+"&adminuser_id="+adminuser_id+"&op=set&template_id="+$(this).attr('SId');
			
		});
	},
	
	home_init:function(){
		for(i=0; i<shop_skin_data.length; i++){
			var obj;
			var position = shop_skin_data[i]['Postion'];
			if(position<10){
			   obj= $("#shop_skin_index div").filter('[rel=edit-t0'+position+']');
			   if(obj.length==0){ 
			     obj= $("#shop_skin_index span").filter('[rel=edit-t0'+position+']');
			   }
			}else{
			   obj=$("#shop_skin_index div").filter('[rel=edit-t'+position+']');
			   if(obj.length==0){
			      obj=$("#shop_skin_index span").filter('[rel=edit-t'+position+']');
			   }
			}
			
			var contenttype = shop_skin_data[i]['ContentsType'];
			var is_4m = shop_skin_data[i]['is_4m'];	// 厂家是否同步过该模板属性
			if(contenttype==1){
			    //轮播图片
				
				var imgurls = shop_skin_data[i]['ImgPath'];
				imgurlarr = imgurls.split("|*|");
				if(imgurlarr[0]){
				   obj.find('.img').html('<img src="'+imgurlarr[0]+'" />');
				}
				var linktype = shop_skin_data[i]['linktype'];
				var foreign_id = shop_skin_data[i]['foreign_id'];
				var detail_id = shop_skin_data[i]['detail_id'];
				var sel_link_type  = shop_skin_data[i]['sel_link_type'];
				var url  = shop_skin_data[i]['Url'];
				linktypearr = linktype.split("|*|");
				foreign_idarr= foreign_id.split("|*|");
				detail_idarr= detail_id.split("|*|");
				sel_link_typearr= sel_link_type.split("|*|");
				urlarr= url.split("|*|");
				
				var clen = imgurlarr.length;	//以轮播图的数量为主
				for(m=0;m<clen;m++){
					var sobj= document.getElementById("type_id_1_"+(m+1));
					if(!sobj){
					   continue;
					}
					$('#sel_link_type_1_'+(m+1)+'_2').next().next().val('');
					if( sel_link_typearr[m] == 2 ){
						$('#sel_link_type_1_'+(m+1)+'_2').attr('checked','checked');
						$('#sel_link_type_1_'+(m+1)+'_2').next().next().val(urlarr[m]);
					} else {
						$('#sel_link_type_1_'+(m+1)+'_1').attr('checked','checked');
					}
					var temp_arr = new Array();
					temp_arr = linktypearr[m].split('-');
					if(temp_arr.length>1){
						$("input[name='selector_title_"+(m+1)+"']").attr("value",temp_arr[temp_arr.length-1]);
					}
					
					var imgurl = imgurlarr[m];
					console.log(imgurl);
					if(imgurl){
					//图片赋值
					
						$("#imgids_1_"+(m+1)).attr("value",imgurl);
						$('#banner_img_'+(m+1)).html('<img src="'+imgurl+'" />');
						$('#banner_img_'+(m+1)).attr('is_4m',is_4m);	//添加4m标识
						$("#a_banner_"+(m+1)).attr("href","defaultset.php?op=del&position="+position+"&b_imgurl="+imgurl);
					}else{ 
					   $('#banner_img_'+(m+1)).html('');
					}
				}
				
			}else if(contenttype==2){
			    var iscate = obj.attr("iscate");
				var imgurl=shop_skin_data[i]['ImgPath'];
			    if(iscate && iscate==1){
				 
				   obj.find('.img').css('background-image',"url("+imgurl+")");
				   $("#a_banner_2_1").attr("href","defaultset.php?op=del_2&position="+position+"&b_imgurl="+imgurl);
				}else{
				    if(template_id==20 || template_id==23){
					   obj.find('.img').html('<img src="'+imgurl+'" width="32" height="25" style="width:32px;" />');
					}else if(template_id==22){
					  // alert(obj.find('.img'));
					   obj.find('.img').html('<img src="'+imgurl+'" width="25.5px"  height="25.5" />');
					}else{
				       obj.find('.img').html('<img src="'+imgurl+'" />');
					}
					$("#a_banner_2_1").attr("href","defaultset.php?op=del_2&position="+position+"&b_imgurl="+imgurl);
				}
				
				var linktype   = shop_skin_data[i]['linktype'];
				var foreign_id = shop_skin_data[i]['foreign_id'];
				var detail_id  = shop_skin_data[i]['detail_id'];
				var sel_link_type  = shop_skin_data[i]['sel_link_type'];
				var url  = shop_skin_data[i]['Url'];
				$("#imgurl2").attr("value",shop_skin_data[i]['ImgPath']);
				$("#temp_img").attr("src",shop_skin_data[i]['ImgPath']);
				$('#sel_link_type_2_2').next().next().val('');
				if( sel_link_type == 2 ){
					$('#sel_link_type_2_2').attr('checked','checked');
					$('#sel_link_type_2_2').next().next().val(url);
				} else {
					$('#sel_link_type_2_1').attr('checked','checked');
				}
				var temp_arr = new Array();
				temp_arr = linktype.split('-');
				if(temp_arr.length>1){
					$('#selector_title').attr("value",temp_arr[temp_arr.length-1]);
				}
			}else if(contenttype==3){
			   //文字
			   $("#contenttype").attr("value",contenttype);
			   var title = shop_skin_data[i]['Title'];
			   obj.find('.div_typename').html(title);
			   var linktype = shop_skin_data[i]['linktype'];
			   var font_color = shop_skin_data[i]['font_color'];
			   obj.find('.div_typename').css("color","#"+font_color);
			   //document.getElementById("old_title").value=title;
				var foreign_id = shop_skin_data[i]['foreign_id'];
				var detail_id = shop_skin_data[i]['detail_id'];
				var sel_link_type  = shop_skin_data[i]['sel_link_type'];
				var url  = shop_skin_data[i]['Url'];
				var sobj= document.getElementById("type_id_3");
				var options = sobj.options;
				$('#sel_link_type_3_2').next().next().val('');
				if( sel_link_type == 2 ){
					$('#sel_link_type_3_2').attr('checked','checked');
					$('#sel_link_type_3_2').next().next().val(url);
				} else {
					$('#sel_link_type_3_1').attr('checked','checked');
				}
				//链接类型
				if(linktype==1 || linktype==2 || linktype==3 || linktype==10 || linktype==11 || linktype==12 || linktype==13 || linktype==50 || linktype==51 || linktype==52 || linktype==53 || linktype==54 || linktype==60 || linktype==61 || linktype==62 || linktype==63 || linktype==64 || linktype==65){
				    
				   for(var j=0;j<options.length;j++){
				      var ov = options[j].value;					  
					  var ovlen = ov.length;
					   var ovtype = 1;
					   if(ov.indexOf('_')!=-1){
						   var ovarr = ov.split('_');
						   ov = ovarr[0];
						   ovtype = ovarr[1];						   
					   }
					   if(ov==foreign_id && ovtype==linktype){						   
						 options[j].selected ="selected";
						 break;
					  }
					}
				}else{
				   options[0].selected =true;
				}
			}
			else if(contenttype==4){
			   var iscate = obj.attr("iscate");
				//var imgurl=shop_skin_data[i]['ImgPath'];
				var video_link = shop_skin_data[i]['Video_link'];
				document.getElementById("div_products_2").style.display="none";
			    obj.find('.div_typevideo').html(' <iframe class="video_class" src="'+video_link+'" frameborder=0 allowfullscreen></iframe>');
				var linktype = shop_skin_data[i]['linktype'];
				var sobj     = document.getElementById("type_id_2");
				var options  = sobj.options;
				//var foreign_id = shop_skin_data[i]['foreign_id']; 
				//var detail_id = shop_skin_data[i]['detail_id'];				
				//$("#imgurl2").attr("value",shop_skin_data[i]['ImgPath']);
				//链接类型
				//链接类型
				
				   options[0].selected =true;
				
				
			}
			
			$("#contenttype").attr("value",contenttype);
		}
		
		
		
		$('.shop_skin_index_list div').after('<div class="mod">&nbsp;</div>');	//追加编辑按钮
		//$('.shop_skin_index_list span').after('<div class="mod">&nbsp;</div>');	//追加编辑按钮
		$('#shop_skin_index .shop_skin_index_list').hover(function(){$(this).find('.mod').show();}, function(){$(this).find('.mod').hide();});
		
		//点击图标切换编辑内容
		$('#shop_skin_index .shop_skin_index_list .mod').click(function(){
		//alert('index====');
		    var parent=$(this).parent();
			var no=parent.attr('no');
			var no_true = no;
			var len = shop_skin_data.length;
			
			$('#SetHomeCurrentBox').remove();
			parent.append("<div id='SetHomeCurrentBox'></div>");
			$('#SetHomeCurrentBox').css({'height':parent.height()-10, 'width':parent.width()-10})
			$("#setbanner, #setimages,#set_title,#set_video_link").hide();
			//alert(shop_skin_data[no]['length']);
			$('.url_select').css('display', shop_skin_data[no]['NeedLink']==1?'block':'none');
			
			var contenttype = shop_skin_data[no]['ContentsType'];
			
			if(contenttype==1){
				$("#setbanner").show();
				$('#home_form #setbanner .tips label').html(shop_skin_data[no]['Width']+'*'+shop_skin_data[no]['Height']);
				var imgpaths = shop_skin_data[no]['ImgPath'];
				dataImgPath = imgpaths.split(",");
				var iwidth = shop_skin_data[no]['Width'];
				var iheight = shop_skin_data[no]['Height'];
				//alert('iw====='+iwidth);
				for(var i=1;i<6;i++){
				   document.getElementById("label_slide_"+i).innerHTML=iwidth+"*"+iheight;
				}
				/*for(var i=0; i<dataImgPath.length; i++){
				alert('==========1');
					/*$('#home_form input[name=ImgPathList\\[\\]]').eq(i).val(dataImgPath[i]);
					$('#home_form input[name=UrlList\\[\\]]').eq(i).val(dataUrl[i]);
					$('#home_form input[name=TitleList\\[\\]]').eq(i).val(dataTitle[i]);
					*/
					/*if(dataImgPath[i].indexOf('//')!=-1){
						var s='';
					}else if(dataImgPath[i].indexOf('/u_file/')!=-1){
						var s=img_domain;
						dataImgPath[i]=dataImgPath[i].replace('/u_file', '');
					}else if(dataImgPath[i].indexOf('/api/')!=-1){
						var s=static_domain;
					}else{
						var s='';
					}*/
					//dataImgPath[i] && $("#home_form .b_r").eq(i).html('<a href="'+s+dataImgPath[i]+'" target="_blank"><img src="'+s+dataImgPath[i]+'" /></a>');
					//if(dataUrl[i]){
					//	$("#home_form select[name=UrlList\\[\\]]").eq(i).find("option[value='"+dataUrl[i]+"']").attr("selected", true);
					//}else{
					//	$("#home_form select[name=UrlList\\[\\]]").eq(i).find("option").eq(0).attr("selected", true);
					//}
				//}
				$("#contenttype").attr("value",1);
			}else if(contenttype==2){
				//var dd;
				$("#setimages").show();
				$("#imgurl2").attr("value",shop_skin_data[no]['ImgPath']);
				$("#temp_img").attr("src",shop_skin_data[no]['ImgPath']);
				$('#setimages .tips label').html(shop_skin_data[no]['Width']+'*'+shop_skin_data[no]['Height']);
				$("#contenttype").attr("value",2);
				
				var linktype   = shop_skin_data[no]['linktype'];
				var foreign_id = shop_skin_data[no]['foreign_id'];
				var detail_id  = shop_skin_data[no]['detail_id'];
				var sel_link_type  = shop_skin_data[no]['sel_link_type'];
				var url  = shop_skin_data[no]['Url'];
				var sobj       = document.getElementById("type_id_2");
				$('#sel_link_type_2_2').next().next().val('');
				if( sel_link_type == 2 ){
					$('#sel_link_type_2_2').attr('checked','checked');
					$('#sel_link_type_2_2').next().next().val(url);
				} else {
					$('#sel_link_type_2_1').attr('checked','checked');
				}
				
				var temp_arr = new Array();
				temp_arr = linktype.split('-');
				if(temp_arr.length>1){
					$('#selector_title').attr("value",temp_arr[temp_arr.length-1]);
				}
				$("#a_banner_2_1").attr("href","defaultset.php?op=del_2&position="+no_true);
			
			}else if(contenttype==3){
			   //文字
			    var linktype   = shop_skin_data[no]['linktype'];
				var title      = shop_skin_data[no]['Title'];
				var font_color = shop_skin_data[no]['font_color'];
				$("#contenttype").attr("value",3);
				document.getElementById("title_3").value=title;
				document.getElementById("old_title").value=title;
				document.getElementById("font_bg").value=font_color;
				document.getElementById("font_bg").style.backgroundColor ="#"+font_color;
				var foreign_id     = shop_skin_data[no]['foreign_id'];
				var detail_id      = shop_skin_data[no]['detail_id'];
				var sel_link_type  = shop_skin_data[no]['sel_link_type'];
				var url  = shop_skin_data[no]['Url'];
				var sobj           = document.getElementById("type_id_3");
				var options        = sobj.options;
				var product_type_3 = document.getElementById('product_type_3');
				product_type_3.style.display = "none";
				document.getElementById("div_products_3").style.display = "none";
				$('#sel_link_type_3_2').next().next().val('');
				if( sel_link_type == 2 ){
					$('#sel_link_type_3_2').attr('checked','checked');
					$('#sel_link_type_3_2').next().next().val(url);
				} else {
					$('#sel_link_type_3_1').attr('checked','checked');
				}
				if(linktype==1 || linktype==2 || linktype==3 || linktype==10 || linktype==11 || linktype==12 || linktype==13 || linktype==50 || linktype==51 || linktype==52 || linktype==53 || linktype==54 || linktype==60  || linktype==61 || linktype==62 || linktype==63 || linktype==64 || linktype==65){
					
				   //options[0].selected =true;
				   for(var j=0;j<options.length;j++){
				      var ov = options[j].value;
					  var ovlen = ov.length;
					 
					   /*if(ovlen>2 && foreign_id > -10){
						  ov = ov.substring(0,ov.length-2);
						  
					   }
					   if(ovlen>2 && foreign_id <-9){ //解决链接页面，id小于-9之后，后台模板不显示链接 1.29 by cdr
						    ov = ov.substring(0,ov.length);
						   
					   }*/
					    
					  var ovtype = 1;
					  if(ov.indexOf('_')!=-1){
						  var ovarr = ov.split('_');
						  ov	 = ovarr[0];
						  ovtype = ovarr[1];
					  }
					  
					   var type_id = -1;
					   if(ov == -40 && linktype==1){	//选择了多级分类
						   var foreign_id_arr = foreign_id.split('_');
						   foreign_id = -40;
						   type_id = foreign_id_arr[0];
					   }
					   if(ov==foreign_id && ovtype==linktype){
					       if(linktype==1){
						        options[j].selected ="selected";
								if(foreign_id == -40){
								   var option_3 = product_type_3.options;
								   var option_3_len = option_3.length;
								   for(var i=0;i<option_3_len;i++){
									   var option_3_arr = option_3[i].value.split('_');
									   var option_3_type = option_3_arr[0];
									   product_type_3.style.display="block";
									   if(type_id == option_3_type){
										   option_3[i].selected = "selected";
										   break;
									   }
								   }
							   }
						       //产品详情
							   if(type_id>0){
							       document.getElementById("div_products_3").style.display="block";
							   }
							   if(detail_id>0){
							         changeProductType_txt(type_id,detail_id); 
							   }else{
							        changeProductType_txt(type_id,-1); 
									
							   }
							   
						   }else{
					          options[j].selected ="selected";
						   }
						   break;
					   }
					}
				}else{
				   options[0].selected =true;
				}
			   $("#set_title").show();
			}
			
			else if(contenttype==4){
		
				$("#contenttype").attr("value",4);
				
				var linktype   = shop_skin_data[no]['linktype'];
				var foreign_id = shop_skin_data[no]['foreign_id'];
				var detail_id  = shop_skin_data[no]['detail_id'];
				var sobj       = document.getElementById("type_id_2");
				document.getElementById("title_4").value=video_link;
				
				var options = sobj.options;
				//document.getElementById("div_products_2").style.display="none";

				options[0].selected =true;
				
				$("#set_video_link").show();
			}
			
			//if(no_true>19){
			 //  $("#position").attr("value",(parseInt(no_true,10)+1));
			//}else{
			   $("#position").attr("value",(parseInt(no,10)+1));
			   $("#contenttype").attr("value",contenttype);
			//}
			$('#home_form input').filter('[name=PId]').val(shop_skin_data[no]['PId'])
			.end().filter('[name=SId]').val(shop_skin_data[no]['SId'])
			.end().filter('[name=ContentsType]').val(shop_skin_data[no]['ContentsType'])
			.end().filter('[name=no]').val(no);
		});
		
		//加载默认内容
		$('#shop_skin_index .shop_skin_index_list .mod').eq(0).click();
	},

	
	products_list_init:function(){
		$('a[href=#search]').click(function(){
			$('form.store').slideUp();
			$('form.search').slideDown();
			return false;
		});
		
		
		$('a[href=#store]').click(function(){
			$('form.search').slideUp();
			$('form.store').slideDown();
			return false;
		});
	},
	
	products_category_init:function(){
		global_obj.file_upload($('#HomeFileUpload'), $('#shop_category_form input[name=ImgPath]'), $('#look'));
		$('#products .category .m_lefter dl').dragsort({
			dragSelector:'dd',
			dragEnd:function(){
				var data=$(this).parent().children('dd').map(function(){
					return $(this).attr('CateId');
				}).get();
				$.get('?m=shop&a=products', {do_action:'shop.products_category_order', sort_order:data.join('|')});
			},
			dragSelectorExclude:'ul, a',
			placeHolderTemplate:'<dd class="placeHolder"></dd>',
			scrollSpeed:5
		});
		
		$('#products .category .m_lefter ul').dragsort({
			dragSelector:'li',
			dragEnd:function(){
				var data=$(this).parent().children('li').map(function(){
					return $(this).attr('CateId');
				}).get();
				$.get('?m=shop&a=products', {do_action:'shop.products_category_order', sort_order:data.join('|')});
			},
			dragSelectorExclude:'a',
			placeHolderTemplate:'<li class="placeHolder"></li>',
			scrollSpeed:5
		});
		
		$('#products .category .m_lefter ul li').hover(function(){
			$(this).children('.opt').show();
		}, function(){
			$(this).children('.opt').hide();
		});
		
		$('#pro-list-type .item').removeClass('item_on').each(function(){
			$(this).click(function(){
				$('#pro-list-type .item').removeClass('item_on');
				$(this).addClass('item_on');
				$('#shop_category_form input[name=ListTypeId]').val($(this).attr('ListTypeId'));
			});
		}).filter('[ListTypeId='+$('#shop_category_form input[name=ListTypeId]').val()+']').addClass('item_on');
		
		$('#shop_category_form').submit(function(){return false;});
		$('#shop_category_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$(this).attr('disabled', true);
			$.post('?', $('#shop_category_form').serialize(), function(data){
				if(data.status==1){
					window.location='?m=shop&a=products&d=category';
				}else{
					//alert(data.msg);
					$('#shop_category_form input:submit').attr('disabled', false);
				}
			}, 'json');
		});
	},
	
	products_property_init:function(){
	
		var ul=$('#frm_pro ul');
		var add_btn=ul.find('img[src*=add]');
		var add_fun=function(){
			add_btn.click(function(){
				ul.append(ul.children('li:last').clone(true));
				ul.children('li').eq(-2).children('img[src*=add]').remove();
				ul.find('li:last input').val('');
			});
		};
		add_fun();
		ul.find('img[src*=del]').click(function(){
			if(ul.children('li').size()>1){
				$(this).parent().remove();
				
				if(ul.find('img[src*=add]').size()==0){
					ul.children('li:last').append(add_btn);
					add_fun();
				}
			}
		});
		
		$('#products .property .m_lefter ul li').hover(function(){
			$(this).children('.opt').show();
		}, function(){
			$(this).children('.opt').hide();
		});
		
		/*$('#frm_pro').submit(function(){return false;});
		$('#frm_pro input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$(this).attr('disabled', true);
			$.post('?', $('#frm_pro').serialize(), function(data){
				if(data.status==1){
					window.location='?m=shop&a=products&d=property';
				}else{
					alert(data.msg);
					$('#frm_pro input:submit').attr('disabled', false);
				}
			}, 'json');
		});*/
	},
	
	shopping_init:function(){
		$('#shopping .shipping .m_lefter dl').dragsort({
			dragSelector:'dd',
			dragEnd:function(){
				var data=$(this).parent().children('dd').map(function(){
					return $(this).attr('SId');
				}).get();
				$.get('?m=shop&a=shopping', {do_action:'shop.shopping_shipping_order', sort_order:data.join('|')});
			},
			dragSelectorExclude:'a',
			placeHolderTemplate:'<dd class="placeHolder"></dd>',
			scrollSpeed:5
		});
		
		$('#shop_address_form').submit(function(){return false;});
		$('#shop_address_form .submit input').click(function(){
			$(this).attr('disabled', true);
			$.post('?', $('#shop_address_form').serialize(), function(data){
				if(data.status==1){
					window.location='?m=shop&a=shopping';
				}else{
					$('#shop_address_form .submit input').attr('disabled', false);
				}
			}, 'json');
		});
		
		$('#shop_shipping_form').submit(function(){return false;});
		$('#shop_shipping_form input:submit').click(function(){
			if(global_obj.check_form($('*[notnull]'))){return false};
			$(this).attr('disabled', true);
			$.post('?', $('#shop_shipping_form').serialize(), function(data){
				if(data.status==1){
					window.location='?m=shop&a=shopping';
				}else{
					alert(data.msg);
					$('#shop_shipping_form input:submit').attr('disabled', false);
				}
			}, 'json');
		});
		
		$('#shop_payment_form').submit(function(){return false;});
		$('#shop_payment_form .submit input').click(function(){
			$(this).attr('disabled', true);
			$.post('?', $('#shop_payment_form').serialize(), function(data){
				if(data.status==1){
					window.location='?m=shop&a=shopping';
				}else{
					$('#shop_payment_form .submit input').attr('disabled', false);
				}
			}, 'json');
		});
	},
	
	orders_init:function(){
		var date_str=new Date();
		$('#search_form input[name=AccTime_S], #search_form input[name=AccTime_E]').omCalendar({
			date:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate(), 00, 00, 00),
			maxDate:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate()),
			showTime:true
		});
		$('#search_form input[name=AccTime_A], #search_form input[name=AccTime_B]').omCalendar({
			date:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate(), 00, 00, 00),
			maxDate:new Date(date_str.getFullYear(), date_str.getMonth(), date_str.getDate()+1),
			showTime:true
		});
		
		/*$('#orders .cp_title #cp_view, #orders .cp_title #cp_mod').click(function(){
			$('#orders .cp_title div').removeClass('cur');
			$(this).addClass('cur');
			
			if($(this).attr('id')=='cp_view'){
				$('#orders_mod_form .cp_item_view').show();
				$('#orders_mod_form .cp_item_mod').hide();
			}else{
				$('#orders_mod_form .cp_item_view').hide();
				$('#orders_mod_form .cp_item_mod').show();
			}
		});*/
		//$('#orders_mod_form').submit(function(){$('#orders_mod_form .submit .sub').attr('disabled', true);});
		//$('#orders_mod_form .cp_item_mod .back').click(function(){$('#orders .cp_title #cp_view').click();});
		
		var change_is_read=function(){
			/*$('#order_list tr[IsRead=0]').off().click(function(){
				var o=$(this);
				$.get('?', 'do_action=shop.orders_set_read&OrderId='+o.attr('OrderId'), function(data){
					if(data.ret==1){
						o.removeClass('is_not_read').off();
					}
				}, 'json');
			});*/
		};
		
		var refer_time=10;
		var refer_left_time=0;
		var refer_ing=false;
		var auto_refer=function(){
			if($('#auto_refer').is(':checked')){
				if(refer_left_time<refer_time){
					$('#search_form .refer label').html('<span><strong>'+(refer_time-refer_left_time)+'</strong></span>秒后自动刷新');
					refer_left_time++;
				}else if(refer_ing==false){
					refer_ing=true; 
					$('#search_form .refer label').html('数据拉取中..');
					document.location="order.php?customer_id="+customer_id+"&isauto=1";
					//document.location.reload();
					/*$.get('?', 'do_action=shop.orders_is_not_read', function(data){
						refer_ing=false;
						refer_left_time=0;
						if(data.ret==1){
							var have_new_order=false;
							var html='';
							for(var i=0; i<data.msg.length; i++){
								if($('#order_list tr[OrderId='+data.msg[i]['OrderId']+']').size()==0){	//订单号不在列表中
									have_new_order=true;
									html+='<tr class="is_not_read" IsRead="0" OrderId="'+data.msg[i]['OrderId']+'">';
										html+='<td nowrap="nowrap">新订单</td>';
										html+='<td nowrap="nowrap">'+data.msg[i]['OId']+'</td>';
										html+='<td>'+data.msg[i]['Name']+'</td>';
										html+='<td nowrap="nowrap">￥'+data.msg[i]['Price']+'</td>';
										NeedShipping && (html+='<td nowrap="nowrap">'+data.msg[i]['Shipping']+'</td>');
										html+='<td nowrap="nowrap">'+orders_status[data.msg[i]['OrderStatus']]+'</td>';
										html+='<td nowrap="nowrap">￥'+data.msg[i]['OrderTime']+'</td>';
										html+='<td nowrap="nowrap" class="last"><a href="?m=shop&a=orders&d=view&OrderId='+data.msg[i]['OrderId']+'"><img src="'+static_domain+'/member/images/ico/view.gif" align="absmiddle" alt="修改" /></a><a href="?m=shop&a=orders&do_action=shop.orders_del&OrderId='+data.msg[i]['OrderId']+'" title="删除" onClick="if(!confirm(\'删除后不可恢复，继续吗？\')){return false};"><img src="'+static_domain+'/member/images/ico/del.gif" align="absmiddle" /></a></td>';
									html+='</tr>';
								}
							}
							if(have_new_order){
								$('#search_form div label').html('<span>数据拉取成功</span>');
								$('#order_list tbody').prepend(html);
								change_is_read();
								$('body').prepend('<bgsound src="'+static_domain+'/member/images/shop/tips.mp3" autostart="true" loop="1">');
							}else{
								$('#search_form div label').html('<span>没有新的订单</span>');
							}
						}else{
							$('#search_form div label').html('<span>数据拉取失败</span>');
						}
					}, 'json');*/
				}
			}else{
				$('#search_form .refer label').html('自动刷新订单');
				refer_left_time=0;
				refer_ing=false;
			}
			setTimeout(auto_refer, 1000);
		};
		auto_refer();
		change_is_read();
	}

	
}