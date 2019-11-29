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
		//加载上传按钮
		/*global_obj.file_upload($('#HomeFileUpload'), $('#home_form input[name=ImgPath]'), $('#home .shop_skin_index_list').eq($('#home_form input[name=no]')).find('.img'));
		for(var i=0;i<5;i++){
			global_obj.file_upload($('#HomeFileUpload_'+i), $('#home_form input[name=ImgPathList\\[\\]]').eq(i), $('#home_form .b_r').eq(i));
		}
		$('.m_lefter a').attr('href', '#').css({'cursor':'default', 'text-decoration':'none'}).click(function(){
			$(this).blur();
			return false;
		});
		$('.m_lefter form').submit(function(){
			return false;
		});*/
		//加载版面内容
		
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
			
			//obj.attr('no', i);
			var contenttype = shop_skin_data[i]['ContentsType'];
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
				
				linktypearr = linktype.split("|*|");
				foreign_idarr= foreign_id.split("|*|");
				
				detail_idarr= detail_id.split("|*|");
				
				var clen = foreign_idarr.length;
				for(m=0;m<clen;m++){
					var sobj= document.getElementById("type_id_1_"+(m+1));
					if(!sobj){
					   continue;
					}
					var options = sobj.options;
					document.getElementById("div_products_1_"+(m+1)).style.display="none";
					if(linktypearr[m]==1 || linktypearr[m]==2){
					   //$("#type_id_2").attr("value",foreign_id);
					   //document.getElementById("div_products_1_"+(m+1)).style.display="none";
					   for(j=0;j<options.length;j++){
					       //链接类型
						   var ov = options[j].value;
						   var ovlen = ov.length;
						   if(ovlen>2){
                              ov = ov.substring(0,ov.length-2);
						   }    
						   if(ov==foreign_idarr[m]){
							   options[j].selected ="selected";
							   //break;
							   if(linktypearr[m]==1){
							       if(foreign_idarr[m]>0){
							           document.getElementById("div_products_1_"+(m+1)).style.display="block";	
									}
							        var d_id=detail_idarr[m];
									
							   //产品详情
								   if(d_id>0){
								        
								 		changeProductType3(foreign_idarr[m],d_id,parseInt(m+1,10)); 
								   }else{
										changeProductType3(foreign_idarr[m],-1,m+1); 
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
					var imgurl = imgurlarr[m];
					if(imgurl){
					//图片赋值
						$("#imgids_1_"+(m+1)).attr("value",imgurl);
						//alert('imgurl========'+imgurl);
						$('#banner_img_'+(m+1)).html('<img src="'+imgurl+'" />');
						$("#a_banner_"+(m+1)).attr("href","defaultset.php?op=del&position="+position+"&b_imgurl="+imgurl);
					}else{ 
					   $('#banner_img_'+(m+1)).html('');
					}
				}
				
			}else if(contenttype==2){
			   var iscate = obj.attr("iscate");
				var imgurl=shop_skin_data[i]['ImgPath'];
				document.getElementById("div_products_2").style.display="none";
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
				var linktype = shop_skin_data[i]['linktype'];
				var foreign_id = shop_skin_data[i]['foreign_id'];
				var detail_id = shop_skin_data[i]['detail_id'];
				var sobj= document.getElementById("type_id_2");
				  
				var options = sobj.options;
				$("#imgurl2").attr("value",shop_skin_data[i]['ImgPath']);
				//链接类型
				if(linktype==1 || linktype==2){
				    
				   for(j=0;j<options.length;j++){
				       var ov = options[j].value;
					   var ovlen = ov.length;
					   var sel_type = 1;
					   var ov_id= -1;
					   if(ovlen>2){
						  ov_id = ov.substring(0,ov.length-2);
						  var ov_index = ov.indexOf("_");
						  sel_type = ov.substring(ov_index+1);
					   }  
					   if(ov==foreign_id){
					     
					       if(linktype==1){
						        options[j].selected ="selected";
						       //产品详情
							   if(foreign_id>0 && sel_type==1){
								   //产品分类才显示出 选择产品，图文不需要
							       document.getElementById("div_products_2").style.display="block";
							   }
							   if(detail_id>0){
							         changeProductType2(foreign_id,detail_id); 
							   }else{
							        changeProductType2(foreign_id,-1); 
							   }
						   }else{
					          options[j].selected ="selected";
						   }
						   break;
					   }
					    else if(ov_id==foreign_id){
					     
					       if(linktype==1){
						        options[j].selected ="selected";
						       //产品详情
							   //产品分类才显示选择产品
							   if(foreign_id>0 && sel_type==1){
							       document.getElementById("div_products_2").style.display="block";
							   }
							   if(detail_id>0){
							         changeProductType2(foreign_id,detail_id); 
							   }else{
							        changeProductType2(foreign_id,-1); 
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
			}else if(contenttype==3){
			   //文字
			   
			   var title = shop_skin_data[i]['Title'];
			   obj.find('.div_typename').html(title);
			   var linktype = shop_skin_data[i]['linktype'];
			   var font_color = shop_skin_data[i]['font_color'];
			   obj.find('.div_typename').css("color","#"+font_color);
			   
				var foreign_id = shop_skin_data[i]['foreign_id'];
				var detail_id = shop_skin_data[i]['detail_id'];
				var sobj= document.getElementById("type_id_3");
				var options = sobj.options;
				//链接类型
				if(linktype==1 || linktype==2){
				    
				   for(j=0;j<options.length;j++){
				      var ov = options[j].value;
					  var ovlen = ov.length;
					   if(ovlen>2){
						  ov = ov.substring(0,ov.length-2);
					   }
					  if(ov==foreign_id){
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
				//var foreign_id = shop_skin_data[i]['foreign_id']; 
				//var detail_id = shop_skin_data[i]['detail_id'];
				var sobj= document.getElementById("type_id_2");
				var options = sobj.options;
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
			
				$("#setimages").show();
				$("#imgurl2").attr("value",shop_skin_data[no]['ImgPath']);
				
				$('#setimages .tips label').html(shop_skin_data[no]['Width']+'*'+shop_skin_data[no]['Height']);
				$("#contenttype").attr("value",2);
				
				var linktype = shop_skin_data[no]['linktype'];
				var foreign_id = shop_skin_data[no]['foreign_id'];
				var detail_id = shop_skin_data[no]['detail_id'];
				var sobj= document.getElementById("type_id_2");
				var options = sobj.options;
				document.getElementById("div_products_2").style.display="none";
				if(linktype==1 || linktype==2){
				   options[0].selected =true;
				   for(j=0;j<options.length;j++){
				      var ov = options[j].value;
					  var ovlen = ov.length;
					  //是图文还是产品分类
					  var sel_type = 1;
					  var ov_id= -1;
					   if(ovlen>2){
						  ov_id = ov.substring(0,ov.length-2);
						  var ov_index = ov.indexOf("_");
						  sel_type = ov.substring(ov_index+1);
					   }
					  
					   if(ov==foreign_id){
					     
					       if(linktype==1){
						        options[j].selected ="selected";
						       //产品详情
							   if(foreign_id>0 && sel_type==1){
								   //产品分类才显示出 选择产品，图文不需要
							       document.getElementById("div_products_2").style.display="block";
							   }
							   if(detail_id>0){
							         changeProductType2(foreign_id,detail_id); 
							   }else{
							        changeProductType2(foreign_id,-1); 
							   }
						   }else{
					          options[j].selected ="selected";
						   }
						   break;
					   }
					    else if(ov_id==foreign_id){
					     
					       if(linktype==1){
						        options[j].selected ="selected";
						       //产品详情
							   //产品分类才显示选择产品
							   if(foreign_id>0 && sel_type==1){
							       document.getElementById("div_products_2").style.display="block";
							   }
							   if(detail_id>0){
							         changeProductType2(foreign_id,detail_id); 
							   }else{
							        changeProductType2(foreign_id,-1); 
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
				$("#a_banner_2_1").attr("href","defaultset.php?op=del_2&position="+no_true);
			
			}else if(contenttype==3){
			   //文字
			    var linktype = shop_skin_data[no]['linktype'];
				var title = shop_skin_data[no]['Title'];
				var font_color = shop_skin_data[no]['font_color'];
			
				document.getElementById("title_3").value=title;
				document.getElementById("font_bg").value=font_color;
				document.getElementById("font_bg").style.backgroundColor ="#"+font_color;
				var foreign_id = shop_skin_data[no]['foreign_id'];
				var detail_id = shop_skin_data[no]['detail_id'];
				var sobj= document.getElementById("type_id_3");
				var options = sobj.options;
				//document.getElementById("div_products_3").style.display="none";
				if(linktype==1 || linktype==2){
				   options[0].selected =true;
				   for(j=0;j<options.length;j++){
				      var ov = options[j].value;
					  var ovlen = ov.length;
					   if(ovlen>2){
						  ov = ov.substring(0,ov.length-2);
					   }
					  
					  if(ov==foreign_id){
					       if(linktype==1){
						        options[j].selected ="selected";
						       //产品详情
							   if(foreign_id>0){
							       document.getElementById("div_products_3").style.display="block";
							   }
							   if(detail_id>0){
							         changeProductType_txt(foreign_id,detail_id); 
							   }else{
							        changeProductType_txt(foreign_id,-1); 
							   }
							   
						   }else{
					          options[j].selected ="selected";
						   }
					   }
					}
				}else{
				   options[0].selected =true;
				}
			   $("#set_title").show();
			}
			
			else if(contenttype==4){
		
				$("#contenttype").attr("value",4);
				
				var linktype = shop_skin_data[no]['linktype'];
				var foreign_id = shop_skin_data[no]['foreign_id'];
				var detail_id = shop_skin_data[no]['detail_id'];
				var sobj= document.getElementById("type_id_2");
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