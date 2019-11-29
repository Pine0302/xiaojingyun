<?php
 header("Content-type: text/html; charset=utf-8"); 

 require('../../../../../weixinpl/config.php');
 require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
 require('../../../../../weixinpl/back_init.php');
 
 
 
 $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
//$new_baseurl = BaseURL."back_commonshop/";  //11.13隐藏
  require('../../../../../weixinpl/proxy_info.php');  /*fenxiao下链接出错 11.13 by cdr*/
//$new_baseurl ="//". $http_host."/weixinpl/back_commonshop/"; 
   
   /**********4M START************/
    require('../../../../../weixinpl/common/utility_4m.php');

	$shop_4m = new Utiliy_4m_new();
	
	$rearr = $shop_4m->is_4M_new($customer_id);//是否开启4M
	//var_dump($rearr);
	$is_shopgeneral 	 = $rearr[0] ;//是4m分销
	$adminuser_id 		 = $rearr[1] ;//厂家编号
	$is_samelevel 		 = $rearr[2] ;//是否是厂家总店
	$general_template_id = $rearr[3] ;//总店模板编号
	$general_customer_id = $rearr[4] ;//总店商家编号
	$owner_general 		 = $rearr[5] ;//1：厂家总店； 2：代理商总店
	$orgin_adminuser_id  = $rearr[6] ;//自己渠道

	/***********4M END***********/
   
$uptypes=array('image/jpg', //上传文件类型列表
'image/jpeg',
'image/png',
'image/pjpeg',
'image/gif',   
'image/bmp',
'image/x-png');
$max_file_size=1000000; //上传文件大小限制, 单位BYTE
  
//$customer_id = $configutil->splash_new($_GET["customer_id"]); // config 里面已经有customer_id了 ,直接获取为加密后的，注释掉
$template_id = $configutil->splash_new($_GET["template_id"]);
$position=$configutil->splash_new($_POST["position"]);
$contenttype=$configutil->splash_new($_POST["contenttype"]);
$type_id_1_1=-1;
$type_id_1_2=-1;
$type_id_1_3=-1;
$type_id_1_4=-1;
$type_id_1_5=-1;
$type_id_1_6=-1;
$type_id_1_7=-1;
$type_id_1_8=-1;
$type_id_1_9=-1;
$type_id_1_10=-1;


$product_detail_id_1 =-1;
$product_detail_id_2 =-1;
$product_detail_id_3 =-1;
$product_detail_id_4 =-1;
$product_detail_id_5 =-1;
$product_detail_id_6 =-1;
$product_detail_id_7 =-1;
$product_detail_id_8 =-1;
$product_detail_id_9 =-1;
$product_detail_id_10 =-1;

$type_id_2=-1;
$product_detail_id_2 =-1;

$type_id_3=-1;
$product_detail_id_3=-1;


$foreign_id_1_1=-1;
$foreign_id_1_2=-1;
$foreign_id_1_3=-1;
$foreign_id_1_4=-1;
$foreign_id_1_5=-1;

$foreign_id_1_6=-1;
$foreign_id_1_7=-1;
$foreign_id_1_8=-1;
$foreign_id_1_9=-1;
$foreign_id_1_10=-1;

$linktype_id_1_1=-1;
$linktype_id_1_2=-1;
$linktype_id_1_3=-1;
$linktype_id_1_4=-1;
$linktype_id_1_5=-1;
$linktype_id_1_6=-1;
$linktype_id_1_7=-1;
$linktype_id_1_8=-1;
$linktype_id_1_9=-1;
$linktype_id_1_10=-1;



$path_parts=pathinfo($_SERVER['PHP_SELF']); //取得当前路径
//$destination_folder="../../../../../up/";  //上传文件路径
$destination_folder = "../../../../".Base_Upload."Base/personalization/templateimg/"; 

if(!file_exists($destination_folder))
//  mkdir($destination_folder);
mkdir($destination_folder,0777,true);
//$destination_folder = $destination_folder.$customer_id."/";  
//if(!file_exists($destination_folder))
//  mkdir($destination_folder);

//$watermark=1; //是否附加水印(1为加水印,0为不加水印);
//$watertype=1; //水印类型(1为文字,2为图片)
//$waterposition=2; //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);
//$waterstring="www.tt365.org"; //水印字符串
//$waterimg="xplore.gif"; //水印图片
$imgpreview=1; //是否生成预览图(1为生成,0为不生成);
$imgpreviewsize=1/1; //缩略图比例
$destination = "";
//覆盖
$overwrite = true;
$destination1 = "";
$destination2 = "";
$destination3 = "";
$destination4 = "";
$destination5 = "";

$destination6 = "";
$destination7 = "";
$destination8 = "";
$destination9 = "";
$destination10 = "";


$index_bg = $configutil->splash_new($_POST["index_bg"]);

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{	
	if($contenttype==4){
			//保存视频
	    $video_link = $configutil->splash_new($_POST["video_link"]);
	    
	   //$query="update weixin_commonshop_template_imgs set imgurl='".$destination."',url='".$url."',linktype=".$linktype.",foreign_id=".$foreign_id." where template_id=".$template_id." and position='".$position."'";
		$query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
		//echo $query."<br/>";
		$result = _mysql_query($query) or die('L146 Query failed: ' . mysql_error());
		$ti_id=-1;
		while ($row = mysql_fetch_object($result)) {
		   $ti_id = $row->id;
		}
		/*$query_font="select id from weixin_commonshop_type_font where isvalid=true and template_id=".$template_id." and font_id=".$ti_id." and customer_id=".$customer_id;	
		$result_font = _mysql_query($query_font) or die('Query failed query_font: ' . mysql_error());
		$co_id=-1;
		while ($row_font = mysql_fetch_object($result_font)) {
		   $co_id = $row_font->id;
		}*/
		
		$type_id_3 = $configutil->splash_new($_POST["type_id_3"]);
		$product_detail_id_3 = -1;
		$url="";
		
		
		$linktype=1;			
		$foreign_id=$type_id_3;
		if($ti_id>0){
			$query="update weixin_commonshop_template_item_imgs set detail_id=".$product_detail_id_3.", video_link='".$video_link."',foreign_id='".$foreign_id."'  where id=".$ti_id;
			//echo $query;
			_mysql_query($query);
			
		}else{
			$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,video_link,detail_id) values(".$template_id.",'','".$position."','".$url."','".$linktype."','".$foreign_id."',true,now(),".$customer_id.",'".$video_link."','".$product_detail_id_3."')";
			_mysql_query($query);
		}
		
	}


    //保存单页图片
	if($contenttype==3){
	   //保存文字
	    $title = $configutil->splash_new($_POST["title"]);
	    $font_color = $configutil->splash_new($_POST["font_bg"]);
		$ti_id=-1;
	   //$query="update weixin_commonshop_template_imgs set imgurl='".$destination."',url='".$url."',linktype=".$linktype.",foreign_id=".$foreign_id." where template_id=".$template_id." and position='".$position."'";
	   
		$query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
		//echo $query."<br/>";
		$result = _mysql_query($query) or die('L188 Query failed: ' . mysql_error());
		
		while ($row = mysql_fetch_object($result)) {
		  $ti_id = $row->id;
		}
		
		
		$query_font="select id from weixin_commonshop_type_font where isvalid=true and template_id=".$template_id." and font_id=".$ti_id." and customer_id=".$customer_id;	
		$result_font = _mysql_query($query_font) or die('Query failed query_font: ' . mysql_error());
		$co_id=-1;
		while ($row_font = mysql_fetch_object($result_font)) {
		   $co_id = $row_font->id;
		}
		
		$type_id_3 = $configutil->splash_new($_POST["type_id_3"]);
		$product_detail_id_3 = -1;
		$url="";
		$linktype=1;
		if($type_id_3>0 or $type_id_3 == -40){   
			$typestrarr= explode("_",$type_id_3);
			$type_id_3 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($type_id_3 == -40){
				$linktype = 1;
				$product_types_3 = '';
				if(!empty($_POST["product_type_3"])){
					$product_types_3 = $configutil->splash_new($_POST["product_type_3"]);
				}
				$product_types_3_arr = explode("_",$product_types_3);
				$product_types_3 = $product_types_3_arr[0];
			    $product_detail_id_3 = $configutil->splash_new($_POST["product_detail_id_3"]);
				if($product_detail_id_3>0){
					    
					$url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_3;
					 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_3;
					$result3 = _mysql_query($query3) or die('L225 Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_3."&tname=".$typename;
				}
				$type_id_3 = $product_types_3;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_3;
				$result = _mysql_query($query) or die('L236 Query failed: ' . mysql_error());
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_3;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_3;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_3;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_3;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_3 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_3;
					}
				}		
				//优惠券跳转 End
		}else{
		   switch($type_id_3){
			   case -6:
				  $url="../../mshop/list.php?customer_id=".$customer_id_en;
				  break;
			   case -2:
				  $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				  break;
			   case -3:
				  $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				  break;
			   case -4:
				  $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				  break;
			   case -7:
				  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
				  break;
			   case -8:
				  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				  break;
			   case -9:
				  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
				  break;
			   case -5:
				  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
				  break;
			   case -10:
				  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
				  break;
			  case -11:
				  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
				  break;
				case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
				case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
				case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;
				case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
				case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
				case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
		   }
		}
		
		//$linktype=1;			
		$foreign_id=$type_id_3;
		
		if($ti_id>0){
			$query="update weixin_commonshop_template_item_imgs set detail_id=".$product_detail_id_3.", title='".$title."',linktype=".$linktype.",url='".$url."',foreign_id='".$foreign_id."'  where id=".$ti_id;
			
			//echo $query;
			
			_mysql_query($query);
			
			if($co_id>0){
				$query_color="update weixin_commonshop_type_font set font_color='".$font_color."' where font_id=".$ti_id." and template_id=".$template_id;
				
				_mysql_query($query_color) or die('Query failed query_color1: ' . mysql_error());
				//echo $query_color;return;
			}else{
				$query_color="insert into weixin_commonshop_type_font(customer_id,template_id,font_id,font_color,isvalid) values(".$customer_id.",".$template_id.",".$ti_id.",'".$font_color."',1)";
				//echo $query_color;
				
				_mysql_query($query_color) or die('Query failed query_color2: ' . mysql_error());
				//return;
			}
			
		}else{
			
			
			$query="INSERT into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,title,detail_id) values(".$template_id.",'','".$position."','".$url."','".$linktype."','".$foreign_id."',true,now(),".$customer_id.",'".$title."','".$product_detail_id_3."')";
			$result2=_mysql_query($query) or die ('query' .mysql_error());
			
			//_mysql_query($query);
			$getID=mysql_insert_id();
			//echo $getID;

			$query_color1="INSERT into weixin_commonshop_type_font(customer_id,template_id,font_id,font_color,isvalid) values(".$customer_id.",".$template_id.",".$getID.",'".$font_color."',1)";
			$result=_mysql_query($query_color1) or die ('query_color1' .mysql_error());
			
		}   
		
	}else if($contenttype==2){
		if (!is_uploaded_file($_FILES["upfile2"]["tmp_name"]))
		//是否存在文件
		{
	
		    $type_id_2 = $configutil->splash_new($_POST["type_id_2"]);
			
		    $url="";
			$linktype=1;
            if($type_id_2>0 or $type_id_2 == -40){
			    $typestrarr= explode("_",$type_id_2);
				$type_id_2 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($type_id_2 == -40){
					 $linktype = 1;
					 $product_types_2 = '';
					 if(!empty($_POST["product_type_2"])){
						 $product_types_2 = $configutil->splash_new($_POST["product_type_2"]);
					 }
					 $product_types_2_arr = explode("_",$product_types_2);
					 $product_types_2 = $product_types_2_arr[0];
				     $product_detail_id_2 = $configutil->splash_new($_POST["product_detail_id_2"]);        
                     if($product_detail_id_2>0){
					    
						 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_2;
						$result3 = _mysql_query($query3) or die('L411 Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_2."&tname=".$typename;
					}
					$type_id_2 = $product_types_2;
					
				}else if($linktype==2){
				   //图文
				    $query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_2;
				    $result = _mysql_query($query) or die('L423 Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&customer_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?customer_id=".$customer_id_en;
					}
					$url = $website_url;
					
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_2;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_2;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End
					
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_2 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_2;
					}
				}		
				//优惠券跳转 End
			}else{
			   switch($type_id_2){
			       case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 		
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				   case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;
				   case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
			
		    $destination = $configutil->splash_new($_POST["imgurl2"]);
			
		    //$linktype=1;			
			$foreign_id=$type_id_2;
			
			//$query="update weixin_commonshop_template_imgs set imgurl='".$destination."',url='".$url."',linktype=".$linktype.",foreign_id=".$foreign_id." where template_id=".$template_id." and position='".$position."'";
		    $query="select id from weixin_commonshop_template_item_imgs where  template_id=".$template_id." and isvalid=true and position=".$position." and customer_id=".$customer_id;	
			//echo $query."<br/>";
			$result = _mysql_query($query) or die('L541 Query failed: ' . mysql_error());
			$ti_id=-1;
			
			while ($row = mysql_fetch_object($result)) {
			   $ti_id = $row->id;
			}
			
			if($ti_id>0){
			    $query="update weixin_commonshop_template_item_imgs set createtime=now(),detail_id=".$product_detail_id_2.", imgurl='".$destination."',url='".$url."',linktype='".$linktype."',foreign_id='".$foreign_id."' where id=".$ti_id;
				_mysql_query($query);
			}else{
				$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,detail_id) values(".$template_id.",'".$destination."','".$position."','".$url."','".$linktype."','".$foreign_id."',true,now(),".$customer_id.",".$product_detail_id_2.")";
				_mysql_query($query);
				$ti_id = mysql_insert_id();
			}
			//4M同步下级商家图片
			if($is_shopgeneral == 1 && $is_samelevel == 1){
				$shop_4m->Fac_update_template_item($customer_id,$template_id,$ti_id,$destination,'','','',$position);
			}
			
		}else{
			$file = $_FILES["upfile2"];
			if($max_file_size < $file["size"])
			//检查文件大小
			{
				echo "<font color='red'>文件太大！</font>";
				exit;
			}
			if(!in_array($file["type"], $uptypes))
			//检查文件类型
			{
			  echo "<font color='red'>不能上传此类型文件！</font>";
			  exit;
			}
			if(!file_exists($destination_folder))
			  // mkdir($destination_folder);
				mkdir($destination_folder,0777,true);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination = $destination_folder.time().".".$ftype;
			  if (file_exists($destination) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!_move_uploaded_file ($filename, $destination))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			  $save_destination = str_replace("../","",$destination);
//			 $destination= "/weixinpl/".$save_destination;
			 $destination= "/mshop/".$save_destination;

			  
			  //$pinfo=pathinfo($destination);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			
            $type_id_2 = $configutil->splash_new($_POST["type_id_2"]);
            $url="";
			$linktype=1;
            if($type_id_2>0 or $type_id_2 == -40){
			    $typestrarr= explode("_",$type_id_2);
				$type_id_2 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($type_id_2 == -40){
					$linktype = 1;
					$product_types_2 = '';
					 if(!empty($_POST["product_type_2"])){
						 $product_types_2 = $configutil->splash_new($_POST["product_type_2"]);
					 }
					 $product_types_2_arr = explode("_",$product_types_2);
					 $product_types_2 = $product_types_2_arr[0];
					 $product_detail_id_2 = $configutil->splash_new($_POST["product_detail_id_2"]);        
                     if($product_detail_id_2>0){
					    
						 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_2;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_2;
						$result3 = _mysql_query($query3) or die('L630 Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_2."&tname=".$typename;
					}
					$type_id_2 = $product_types_2;
				}else if($linktype==2){
				   //图文
				    $query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_2;
				    $result = _mysql_query($query) or die('L641 Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&customer_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?customer_id=".$customer_id_en;
					}
					$url = $website_url;
					
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_2;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_2;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_2;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_2 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_2;
					}
				}		
				//优惠券跳转 End
			}else{
			   switch($type_id_2){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
				    case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					 case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
			//默认为链接到类型。以后可以链接到其他的功能模块
           // echo $url;		
			$foreign_id=$type_id_2;
			
			$query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
			//echo $query."<br/>";
			$result = _mysql_query($query) or die('L755 Query failed: ' . mysql_error());
			$ti_id=-1;
			while ($row = mysql_fetch_object($result)) {
			   $ti_id = $row->id;
			}
			if($ti_id>0){
			    $query="update weixin_commonshop_template_item_imgs set detail_id=".$product_detail_id_2.", imgurl='".$new_baseurl.$destination."',url='".$url."',linktype=".$linktype.",foreign_id='".$foreign_id."' where id=".$ti_id;
				//echo $query;
				_mysql_query($query);
			}else{
				$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,detail_id) values(".$template_id.",'".$new_baseurl.$destination."','".$position."','".$url."',".$linktype.",'".$foreign_id."',true,now(),".$customer_id.",".$product_detail_id_2.")";
				_mysql_query($query);
				$ti_id = mysql_insert_id();
			}
			
			//4M同步下级商家图片
			if($is_shopgeneral == 1 && $is_samelevel == 1){
				$shop_4m->Fac_update_template_item($customer_id,$template_id,$ti_id,$destination,'','','',$position);
			}
	  }
  }else{
    //轮播图片
	$destination_folder = $destination_folder."banner/";  
    if(!file_exists($destination_folder))
       // mkdir($destination_folder);
		mkdir($destination_folder,0777,true);
	 $lun_imgurls="";
	 $lun_urls="";
	 $lun_linktypes="";
	 $lun_foreign_ids="";
	 $lun_detail_ids="";
	 if (!is_uploaded_file($_FILES["upfile1_1"]["tmp_name"]))
	 {
	      $destination1 = $configutil->splash_new($_POST["imgids_1_1"]);
		  $type_id_1_1 = $configutil->splash_new($_POST["type_id_1_1"]);
		  $url="";
		  $linktype=1;
		  $product_detail_id_1_1 =-1;
		  if($type_id_1_1>0 or $type_id_1_1 == -40){
			
			$typestrarr= explode("_",$type_id_1_1);
			$type_id_1_1 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($type_id_1_1 == -40){
				 $linktype = 1;
				 $product_types_1_1 = '';
				 if(!empty($_POST["product_type_1_1"])){
					 $product_types_1_1 = $configutil->splash_new($_POST["product_type_1_1"]);
				 }
				 $product_types_1_1_arr = explode("_",$product_types_1_1);
				 $product_types_1_1 = $product_types_1_1_arr[0];
			     $product_detail_id_1_1 = $configutil->splash_new($_POST["product_detail_id_1_1"]);        
				 if($product_detail_id_1_1>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_1;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_1;
					$result3 = _mysql_query($query3) or die('L813 Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_1."&tname=".$typename;
				}
				$type_id_1_1 = $product_types_1_1;
				
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_1;
				$result = _mysql_query($query) or die('L825 Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_1;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_1;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_1;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_1;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
				//商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
				//商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_1 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_1;
					}
				}		
			//优惠券跳转 End
		  }else{
			   switch($type_id_1_1){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
					  break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en;   
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
					case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break; 
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_1;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		  $lun_imgurls = $lun_imgurls.$destination1."|*|";
		  
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_1."|*|";
	 }else{
			$file = $_FILES["upfile1_1"];
			if($max_file_size < $file["size"])
			//检查文件大小
			{
				echo "<font color='red'>文件太大！</font>";
				exit;
			}
			if(!in_array($file["type"], $uptypes))
			//检查文件类型
			{
			  echo "<font color='red'>不能上传此类型文件！</font>";
			  exit;
			}
			if(!file_exists($destination_folder))
			  // mkdir($destination_folder);
				mkdir($destination_folder,0777,true);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  
			  $destination1 = $destination_folder.time()."1.".$ftype;
			  if (file_exists($destination1) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!_move_uploaded_file ($filename, $destination1))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			 
			 $save_destination = str_replace("../","",$destination1);
//			 $destination1= "/weixinpl/".$save_destination;
			 $destination1= "/mshop/".$save_destination;

			  $linktype=1;
			  $type_id_1_1 = $configutil->splash_new($_POST["type_id_1_1"]);
			  $product_detail_id_1_1 =-1;
              $url="";
              if($type_id_1_1>0 or $type_id_1_1 == -40){
			    
				$typestrarr= explode("_",$type_id_1_1);
				$type_id_1_1 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($type_id_1_1 == -40){
					 $linktype = 1;
					 $product_types_1_1 = '';
					 if(!empty($_POST["product_type_1_1"])){
						 $product_types_1_1 = $configutil->splash_new($_POST["product_type_1_1"]);
					 }
					 $product_types_1_1_arr = explode("_",$product_types_1_1);
					 $product_types_1_1 = $product_types_1_1_arr[0];
					$product_detail_id_1_1 = $configutil->splash_new($_POST["product_detail_id_1_1"]);        
					 if($product_detail_id_1_1>0){
						
						 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_1;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_1;
						$result3 = _mysql_query($query3) or die('L1008 Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_1."&tname=".$typename;
					}
					$type_id_1_1 = $product_types_1_1;
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_1;
					$result = _mysql_query($query) or die('L1019 Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&customer_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?customer_id=".$customer_id_en;
					}
					$url = $website_url;
					
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_1;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_1;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_1;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_1;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_1 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_1;
					}
				}		
				//优惠券跳转 End
			 }else{
			   switch($type_id_1_1){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en;   
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
					case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
			 
			 $foreign_id=$type_id_1_1;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			//默认为链接到类型。以后可以链接到其他的功能模块
			
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination1."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_1."|*|";
	  }
	  if (!is_uploaded_file($_FILES["upfile1_2"]["tmp_name"]))
	  {
	  
	      $destination2 = $configutil->splash_new($_POST["imgids_1_2"]);
		  $type_id_1_2 = $configutil->splash_new($_POST["type_id_1_2"]);
		  $url="";
		  $linktype=1;
		  $product_detail_id_1_2 = -1;
		  if($type_id_1_2>0 or $type_id_1_2 == -40){
			$typestrarr= explode("_",$type_id_1_2);
			$type_id_1_2 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($type_id_1_2 == -40){
				 $linktype = 1;
				 $product_types_1_2 = '';
				 if(!empty($_POST["product_type_1_2"])){
					 $product_types_1_2 = $configutil->splash_new($_POST["product_type_1_2"]);
				 }
				 $product_types_1_2_arr = explode("_",$product_types_1_2);
				 $product_types_1_2 = $product_types_1_2_arr[0];
				 $product_detail_id_1_2 = $configutil->splash_new($_POST["product_detail_id_1_2"]);        
				 if($product_detail_id_1_2>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_2;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_2;
					$result3 = _mysql_query($query3) or die('L1166 Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="list.php?customer_id=".$customer_id."&tid=".$product_types_1_2."&tname=".$typename;
				}
				$type_id_1_2 = $product_types_1_2;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_2;
				$result = _mysql_query($query) or die('L1177 Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_2;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_2;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_2;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_2;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
				//商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
				//商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End		
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_2 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_2;
					}
				}		
			//优惠券跳转 End
		  }else{
			   switch($type_id_1_2){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break; 
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_2;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination2."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_2."|*|";
		  
	  }else{
			$file = $_FILES["upfile1_2"];
			if($max_file_size < $file["size"])
			//检查文件大小
			{
				echo "<font color='red'>文件太大！</font>";
				exit;
			}
			if(!in_array($file["type"], $uptypes))
			//检查文件类型
			{
			  echo "<font color='red'>不能上传此类型文件！</font>";
			  exit;
			}
			if(!file_exists($destination_folder))
			 //  mkdir($destination_folder);
				mkdir($destination_folder,0777,true);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination2 = $destination_folder.time()."2.".$ftype;
			  if (file_exists($destination2) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!_move_uploaded_file ($filename, $destination2))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			  $save_destination = str_replace("../","",$destination2);
//			 $destination2= "/weixinpl/".$save_destination;
			 $destination2= "/mshop/".$save_destination;

			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			 $linktype=1;
			 $type_id_1_2 = $configutil->splash_new($_POST["type_id_1_2"]);
              $url="";
			  $product_detail_id_1_2=-1;
              if($type_id_1_2>0 or $type_id_1_2 == -40){
			    $typestrarr= explode("_",$type_id_1_2);
				$type_id_1_2 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($type_id_1_2 == -40){
					 $linktype = 1;
					 $product_types_1_2 = '';
					 if(!empty($_POST["product_type_1_2"])){
						 $product_types_1_2 = $configutil->splash_new($_POST["product_type_1_2"]);
					 }
					 $product_types_1_2_arr = explode("_",$product_types_1_2);
					 $product_types_1_2 = $product_types_1_2_arr[0];
					$product_detail_id_1_2 = $configutil->splash_new($_POST["product_detail_id_1_2"]);        
					 if($product_detail_id_1_2>0){
						
						 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_2;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_2;
						$result3 = _mysql_query($query3) or die('L1365 Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_2."&tname=".$typename;
					}
					$type_id_1_2 = $product_types_1_2;
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_2;
					$result = _mysql_query($query) or die('L1376 Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&customer_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?customer_id=".$customer_id_en;
					}
					$url = $website_url;
					
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_2;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_2;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_2;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_2;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End	
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_2 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_2;
					}
				}		
				//优惠券跳转 End
			 }else{
			   switch($type_id_1_2){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
				  case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;	
				  case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
			 			
			 $foreign_id=$type_id_1_2;
			 $lun_urls=$lun_urls.$url."|*|";
		
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			//默认为链接到类型。以后可以链接到其他的功能模块
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination2."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_2."|*|";
			 
	  }
	  if (!is_uploaded_file($_FILES["upfile1_3"]["tmp_name"]))
	  {
	      $destination3 = $configutil->splash_new($_POST["imgids_1_3"]);
		  $type_id_1_3 = $configutil->splash_new($_POST["type_id_1_3"]);
		  
          $product_detail_id_1_3=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_3>0 or $type_id_1_3 == -40){
			$typestrarr= explode("_",$type_id_1_3);
			$type_id_1_3 = $typestrarr[0];
			$linktype=$typestrarr[1];
			

			if($type_id_1_3 == -40){
				 $linktype = 1;
				 $product_types_1_3 = '';
				 if(!empty($_POST["product_type_1_3"])){
					 $product_types_1_3 = $configutil->splash_new($_POST["product_type_1_3"]);
				 }
				 $product_types_1_3_arr = explode("_",$product_types_1_3);
				 $product_types_1_3 = $product_types_1_3_arr[0];
				 $product_detail_id_1_3 = $configutil->splash_new($_POST["product_detail_id_1_3"]);        
				 if($product_detail_id_1_3>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_3;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_3;
					$result3 = _mysql_query($query3) or die('L1525 Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_3."&tname=".$typename;
				}
				$type_id_1_3 = $product_types_1_3;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_3;
				$result = _mysql_query($query) or die('L1536 Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_3;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_3;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_3;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_3;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_3 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_3;
					}
				}		
				//优惠券跳转 End
		  }else{
			   switch($type_id_1_3){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;		
				   case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_3;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination3."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_3."|*|";
		   
  	  }else{
			$file = $_FILES["upfile1_3"];
			if($max_file_size < $file["size"])
			//检查文件大小
			{
				echo "<font color='red'>文件太大！</font>";
				exit;
			}
			if(!in_array($file["type"], $uptypes))
			//检查文件类型
			{
			  echo "<font color='red'>不能上传此类型文件！</font>";
			  exit;
			}
			if(!file_exists($destination_folder))
			 //  mkdir($destination_folder);
				mkdir($destination_folder,0777,true);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination3 = $destination_folder.time()."3.".$ftype;
			  if (file_exists($destination3) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!_move_uploaded_file ($filename, $destination3))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			  
			  $save_destination = str_replace("../","",$destination3);
//			 $destination3= "/weixinpl/".$save_destination;
			 $destination3= "/mshop/".$save_destination;

			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			  $type_id_1_3 = $configutil->splash_new($_POST["type_id_1_3"]);
			  $product_detail_id_1_3=-1;
              $url="";
			  $linktype=1;	
              if($type_id_1_3>0 or $type_id_1_3 == -40){
			    $typestrarr= explode("_",$type_id_1_3);
				$type_id_1_3 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($type_id_1_3 == -40){
					 $linktype = 1;
					 $product_types_1_3 = '';
					 if(!empty($_POST["product_type_1_3"])){
						 $product_types_1_3 = $configutil->splash_new($_POST["product_type_1_3"]);
					 }
					 $product_types_1_3_arr = explode("_",$product_types_1_3);
					 $product_types_1_3 = $product_types_1_3_arr[0];
					 $product_detail_id_1_3 = $configutil->splash_new($_POST["product_detail_id_1_3"]);        
					 if($product_detail_id_1_3>0){
						
						 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_3;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_3;
						$result3 = _mysql_query($query3) or die('L1725 Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_3."&tname=".$typename;
					}
					$type_id_1_3 = $product_types_1_3;
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_3;
					$result = _mysql_query($query) or die('L1736 Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&customer_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?customer_id=".$customer_id_en;
					}
					$url = $website_url;
					
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_3;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_3;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_3;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_3;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_3 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_3;
					}
				}		
				//优惠券跳转 End
			 }else{
			   switch($type_id_1_3){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
			 		
			 $foreign_id=$type_id_1_3;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			 
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination3."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_3."|*|";
	  }
	  if (!is_uploaded_file($_FILES["upfile1_4"]["tmp_name"]))
	  {
	      $destination4 = $configutil->splash_new($_POST["imgids_1_4"]);
		  $type_id_1_4 = $configutil->splash_new($_POST["type_id_1_4"]);
		  $product_detail_id_1_4=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_4>0 or $type_id_1_4 == -40){
			$typestrarr= explode("_",$type_id_1_4);
			$type_id_1_4 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($type_id_1_4 == -40){
				 $linktype = 1;
				 $product_types_1_4 = '';
				 if(!empty($_POST["product_type_1_4"])){
					 $product_types_1_4 = $configutil->splash_new($_POST["product_type_1_4"]);
				 }
				 $product_types_1_4_arr = explode("_",$product_types_1_4);
				 $product_types_1_4 = $product_types_1_4_arr[0];
				$product_detail_id_1_4 = $configutil->splash_new($_POST["product_detail_id_1_4"]);        
				 if($product_detail_id_1_4>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_4;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_4;
					$result3 = _mysql_query($query3) or die('L1881 Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_4."&tname=".$typename;
				}
				$type_id_1_4 = $product_types_1_4;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_4;
				$result = _mysql_query($query) or die('L1892 Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_4;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_4;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_4;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_4;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
				//商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
				//商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_4 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_4;
					}
				}		
				//优惠券跳转 End
		  }else{
			   switch($type_id_1_4){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_4;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination4."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_4."|*|";
		   
	  }else{
			$file = $_FILES["upfile1_4"];
			if($max_file_size < $file["size"])
			//检查文件大小
			{
				echo "<font color='red'>文件太大！</font>";
				exit;
			}
			if(!in_array($file["type"], $uptypes))
			//检查文件类型
			{
			  echo "<font color='red'>不能上传此类型文件！</font>";
			  exit;
			}
			if(!file_exists($destination_folder))
			 //  mkdir($destination_folder);
				mkdir($destination_folder,0777,true);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination4 = $destination_folder.time()."4.".$ftype;
			  if (file_exists($destination4) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!_move_uploaded_file ($filename, $destination4))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			  $save_destination = str_replace("../","",$destination4);
//			 $destination4= "/weixinpl/".$save_destination;
			 $destination4= "/mshop/".$save_destination;

			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			 
			 $type_id_1_4 = $configutil->splash_new($_POST["type_id_1_4"]);
			 $product_detail_id_1_4=-1;
              $url="";
			  $linktype=1;
              if($type_id_1_4>0 or $type_id_1_4 == -40){
			    $typestrarr= explode("_",$type_id_1_4);
				$type_id_1_4 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($type_id_1_4 == -40){
					$linktype = 1;
					 $product_types_1_4 = '';
					 if(!empty($_POST["product_type_1_4"])){
						 $product_types_1_4 = $configutil->splash_new($_POST["product_type_1_4"]);
					 }
					 $product_types_1_4_arr = explode("_",$product_types_1_4);
					 $product_types_1_4 = $product_types_1_4_arr[0];
					 $product_detail_id_1_4 = $configutil->splash_new($_POST["product_detail_id_1_4"]);        
					 if($product_detail_id_1_4>0){
						
						 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_4;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_4;
						$result3 = _mysql_query($query3) or die('L2081 Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_4."&tname=".$typename;
					}
					$type_id_1_4 = $product_types_1_4;
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_4;
					$result = _mysql_query($query) or die('L2092 Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&customer_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?customer_id=".$customer_id_en;
					}
					$url = $website_url;
					
					//城市商圈-店铺跳转
				}else if($linktype==10){
				   //城市商圈-美食				    
					$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_4;
				}else if($linktype==11){
				   //城市商圈-KTV				    
					$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_4;
				}else if($linktype==12){
				   //城市商圈-酒店			    
					$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_4;
				}else if($linktype==13){
				   //城市商圈-线下商城				    
					$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_4;
					//城市商圈-店铺跳转End
					
					//城市商圈-行业跳转
				}else if($linktype==50){
				   //商圈行业列表-美食				    
					$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
				}else if($linktype==51){
				   //商圈行业列表-KTV				    
					$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
				}else if($linktype==52){
				   //商圈行业列表-酒店				    
					$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
				}else if($linktype==53){
				   //商圈行业列表-线下商城-首页
					$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
				}else if($linktype==54){
				   //商圈行业列表-线下商城-商家列表
					$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
				}
					//城市商圈-行业跳转End
				//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_4 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_4;
					}
				}		
				//优惠券跳转 End
			 }else{
			   switch($type_id_1_4){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				  case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				  case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break; 
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break; 
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break; 
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
			 			
			 $foreign_id=$type_id_1_4;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			 
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination4."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_4."|*|";
	  }
	  
	  if (!is_uploaded_file($_FILES["upfile1_5"]["tmp_name"]))
	  {
	      $destination5 = $configutil->splash_new($_POST["imgids_1_5"]);
		  $type_id_1_5 = $configutil->splash_new($_POST["type_id_1_5"]);
		  $product_detail_id_1_5=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_5>0 or $type_id_1_5 == -40){
			$typestrarr= explode("_",$type_id_1_5);
			$type_id_1_5 = $typestrarr[0];
			$linktype=$typestrarr[1];
			if($type_id_1_5 == -40){
				$linktype = 1;
				$product_types_1_5 = '';
				if(!empty($_POST["product_type_1_5"])){
					$product_types_1_5 = $configutil->splash_new($_POST["product_type_1_5"]);
				}
				$product_types_1_5_arr = explode("_",$product_types_1_5);
				$product_types_1_5 = $product_types_1_5_arr[0];
				 $product_detail_id_1_5 = $configutil->splash_new($_POST["product_detail_id_1_5"]);        
				 if($product_detail_id_1_5>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_5;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_5;
					$result3 = _mysql_query($query3) or die('L2238 Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_5."&tname=".$typename;
				 }
				 $type_id_1_5 = $product_types_1_5;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_5;
				$result = _mysql_query($query) or die('L2249 Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_5;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_5;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_5;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_5;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
				//商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
				//商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_5 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_5;
					}
				}		
				//优惠券跳转 End
		  }else{
			   switch($type_id_1_5){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break; 
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_5;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		
		  $lun_imgurls = $lun_imgurls.$destination5."|*|";
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_5."|*|";
		   
	  }else{
			$file = $_FILES["upfile1_5"];
			if($max_file_size < $file["size"])
			//检查文件大小
			{
				echo "<font color='red'>文件太大！</font>";
				exit;
			}
			if(!in_array($file["type"], $uptypes))
			//检查文件类型
			{
			  echo "<font color='red'>不能上传此类型文件！</font>";
			  exit;
			}
			if(!file_exists($destination_folder))
			  // mkdir($destination_folder);
				mkdir($destination_folder,0777,true);

			  $filename=$file["tmp_name"];

			  $image_size = getimagesize($filename);

			  $pinfo=pathinfo($file["name"]);

			  $ftype=$pinfo["extension"];
			  $destination5 = $destination_folder.time()."5.".$ftype;
			  if (file_exists($destination5) && $overwrite != true)
			  {
				 echo "<font color='red'>同名文件已经存在了！</font>";
				 exit;
			   }
			  if(!_move_uploaded_file ($filename, $destination5))
			  {
				 echo "<font color='red'>移动文件出错！</font>";
				 exit;
			  }
			  $save_destination = str_replace("../","",$destination5);
//			 $destination5= "/weixinpl/".$save_destination;
			 $destination5= "/mshop/".$save_destination;

			 // $pinfo=pathinfo($destination1);
			  //$fname=$pinfo["basename"];
		//   echo " <font color=red>成功上传,鼠标移动到地址栏自动复制</font><br><table width=\"348\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" class=\"table_decoration\" align=\"center\"><tr><td><input type=\"checkbox\" id=\"fmt\" onclick=\"select_format()\"/>图片UBB代码<br/><div id=\"site\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."\"/>
		  //  </td></tr></table></div><div id=\"sited\" style=\"display:none\"><table border=\"0\"><tr><td valign=\"top\">文件地址:</td><td><input type=\"text\" onclick=\"sendtof(this.value)\" onmouseover=\"oCopy(this)\" style=font-size=9pt;color:blue size=\"44\" value=\"[img]//".$_SERVER['SERVER_NAME'].$path_parts["dirname"]."/".$destination_folder.$fname."[/img]\"/></td></tr></table></div></td></tr></table>";
			  //echo " 宽度:".$image_size[0];
			 // echo " 长度:".$image_size[1];
			 
			 $type_id_1_5 = $configutil->splash_new($_POST["type_id_1_5"]);
			 $product_detail_id_1_5=-1;
              $url="";
			  $linktype=1;
              if($type_id_1_5>0 or $type_id_1_5 == -40){
			    $typestrarr= explode("_",$type_id_1_5);
				$type_id_1_5 = $typestrarr[0];
				$linktype=$typestrarr[1];
				if($type_id_1_5 == -40){
					$linktype = 1;
					$product_types_1_5 = '';
					if(!empty($_POST["product_type_1_5"])){
						$product_types_1_5 = $configutil->splash_new($_POST["product_type_1_5"]);
					}
					$product_types_1_5_arr = explode("_",$product_types_1_5);
					$product_types_1_5 = $product_types_1_5_arr[0];
					$product_detail_id_1_5 = $configutil->splash_new($_POST["product_detail_id_1_5"]);        
					 if($product_detail_id_1_5>0){
						
						 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_5;
					 
					 }else{					
						$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_5;
						$result3 = _mysql_query($query3) or die('L2438 Query failed: ' . mysql_error());
						$typename="";
						while ($row3 = mysql_fetch_object($result3)) {
						   $typename = $row3->name;
						}
						$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_5."&tname=".$typename;
					 }
					 $type_id_1_5 = $product_types_1_5;
				}else if($linktype==2){
				   //图文
					$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_5;
					$result = _mysql_query($query) or die('L2449 Query failed: ' . mysql_error());
					$website_url="";
					while ($row = mysql_fetch_object($result)) {
					   $website_url = $row->website_url;
					}
					$pos = strpos($website_url,"?"); 
					if($pos>0){
					   $website_url = $website_url."&customer_id=".$customer_id_en;
					}else{
					   $website_url = $website_url."?customer_id=".$customer_id_en;
					}
					$url = $website_url;
					
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_5;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_5;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_5;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_5;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
			   //商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
			   //商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_5 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_5;
					}
				}		
				//优惠券跳转 End	
			 }else{
			   switch($type_id_1_5){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					  break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
			 			
			 $foreign_id=$type_id_1_5;
			 
			 $lun_urls=$lun_urls.$url."|*|";
			 $lun_linktypes=$lun_linktypes.$linktype."|*|";
			 $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
			 
			 $lun_imgurls = $lun_imgurls.$new_baseurl.$destination5."|*|";
			 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_5."|*|";
	  }
	 /* if (!empty($_POST["type_id_1_6"]))
	  {
		  $type_id_1_6 = $configutil->splash_new($_POST["type_id_1_6"]);
		  $product_detail_id_1_6=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_6>0 or $type_id_1_6 == -40){
			$typestrarr= explode("_",$type_id_1_6);
			$type_id_1_6 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($type_id_1_6 == -40){
				$linktype = 1;
				$product_types_1_6 = '';
				if(!empty($_POST["product_type_1_6"])){
					$product_types_1_6 = $configutil->splash_new($_POST["product_type_1_6"]);
				}
				$product_types_1_6_arr = explode("_",$product_types_1_6);
				$product_types_1_6 = $product_types_1_6_arr[0];
				$product_detail_id_1_6 = $configutil->splash_new($_POST["product_detail_id_1_6"]);        
				 if($product_detail_id_1_6>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_6;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_6;
					$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_6."&tname=".$typename;
				 }
				 $type_id_1_6 = $product_types_1_6;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_6;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_6;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_6;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_6;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_6;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
			   //商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
			   //商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_6 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_6;
					}
				}		
				//优惠券跳转 End
		  }else{
			   switch($type_id_1_6){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break; 
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_6;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		  
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_6."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			 $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  
	  if (!empty($_POST["type_id_1_7"]))
	  {
	      $type_id_1_7 = $configutil->splash_new($_POST["type_id_1_7"]);
		  
          $product_detail_id_1_7=-1;
		  
		  $url="";
		  $linktype=1;
		  if($type_id_1_7>0 or $type_id_1_7 == -40){
			$typestrarr= explode("_",$type_id_1_7);
			$type_id_1_7 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($type_id_1_7 == -40){
				$linktype = 1;
				$product_types_1_7 = '';
				if(!empty($_POST["product_type_1_7"])){
					$product_types_1_7 = $configutil->splash_new($_POST["product_type_1_7"]);
				}
				$product_types_1_7_arr = explode("_",$product_types_1_7);
				$product_types_1_7 = $product_types_1_7_arr[0];
				$product_detail_id_1_7 = $configutil->splash_new($_POST["product_detail_id_1_7"]);        
				 if($product_detail_id_1_7>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_7;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_7;
					$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_7."&tname=".$typename;
				 }
				 $type_id_1_7 = $product_types_1_7;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_7;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_7;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_7;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_7;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_7;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
			   //商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
			   //商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_7 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_7;
					}
				}		
				//优惠券跳转 End
		  }else{
			   switch($type_id_1_7){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en;   
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_7;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		$lun_detail_ids=$lun_detail_ids.$product_detail_id_1_7."|*|";
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			  $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  
	  
	  if (!empty($_POST["type_id_1_8"]))
	  {
		  $type_id_1_8 = $configutil->splash_new($_POST["type_id_1_8"]);
		  $product_detail_id_1_8=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_8>0 or $type_id_1_8 == -40){
			$typestrarr= explode("_",$type_id_1_8);
			$type_id_1_8 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($type_id_1_8 == -40){
				$linktype = 1;
				$product_types_1_8 = '';
				if(!empty($_POST["product_type_1_8"])){
					$product_types_1_8 = $configutil->splash_new($_POST["product_type_1_8"]);
				}
				$product_types_1_8_arr = explode("_",$product_types_1_8);
				$product_types_1_8 = $product_types_1_8_arr[0];
				$product_detail_id_1_8 = $configutil->splash_new($_POST["product_detail_id_1_8"]);        
				 if($product_detail_id_1_8>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_8;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_8;
					$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_8."&tname=".$typename;
				 }
				 $type_id_1_8 = $product_types_1_8;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_8;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_8;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_8;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_8;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_8;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
			   //商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
			   //商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_8 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_8;
					}
				}		
			//优惠券跳转 End
		  }else{
			   switch($type_id_1_8){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_8;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		 $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_8."|*|";
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			  $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  if (!empty($_POST["type_id_1_9"]))
	  {
		  $type_id_1_9 = $configutil->splash_new($_POST["type_id_1_9"]);
		  
          $product_detail_id_1_9=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_9>0 or $type_id_1_9 == -40){
			$typestrarr= explode("_",$type_id_1_9);
			$type_id_1_9 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($type_id_1_9 == -40){
				$linktype = 1;
				$product_types_1_9 = '';
				if(!empty($_POST["product_type_1_9"])){
					$product_types_1_9 = $configutil->splash_new($_POST["product_type_1_9"]);
				}
				$product_types_1_9_arr = explode("_",$product_types_1_9);
				$product_types_1_9 = $product_types_1_9_arr[0];
				 $product_detail_id_1_9 = $configutil->splash_new($_POST["product_detail_id_1_9"]);        
				 if($product_detail_id_1_9>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_9;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_9;
					$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_9."&tname=".$typename;
				 }
				 $type_id_1_9 = $product_types_1_9;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_9;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_9;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_9;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_9;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_9;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
			   //商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
			   //商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_9 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_9;
					}
				}		
				//优惠券跳转 End
		  }else{
			   switch($type_id_1_9){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break; 
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_9;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_9."|*|";
		   
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			 $lun_detail_ids=$lun_detail_ids."|*|";
	  }
	  
	  
	  if (!empty($_POST["type_id_1_10"]))
	  {
	      $type_id_1_10 = $configutil->splash_new($_POST["type_id_1_10"]);
		  $product_detail_id_1_10=-1;
		  $url="";
		  $linktype=1;
		  if($type_id_1_10>0 or $type_id_1_10 == -40){
			$typestrarr= explode("_",$type_id_1_10);
			$type_id_1_10 = $typestrarr[0];
			$linktype=$typestrarr[1];
			
			if($type_id_1_10 == -40){
				$linktype = 1;
				$product_types_1_10 = '';
				if(!empty($_POST["product_type_1_10"])){
					$product_types_1_10 = $configutil->splash_new($_POST["product_type_1_10"]);
				}
				$product_types_1_10_arr = explode("_",$product_types_1_10);
				$product_types_1_10 = $product_types_1_10_arr[0];
				$product_detail_id_1_10 = $configutil->splash_new($_POST["product_detail_id_1_10"]);        
				 if($product_detail_id_1_10>0){
					
					 $url="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$product_detail_id_1_10;
				 
				 }else{					
					$query3="select name from weixin_commonshop_types where isvalid=true and id=".$product_types_1_10;
					$result3 = _mysql_query($query3) or die('Query failed: ' . mysql_error());
					$typename="";
					while ($row3 = mysql_fetch_object($result3)) {
					   $typename = $row3->name;
					}
					$url="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$product_types_1_10."&tname=".$typename;
				 }
				 $type_id_1_10 = $product_types_1_10;
			}else if($linktype==2){
			   //图文
				$query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type_id_1_10;
				$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
				$website_url="";
				while ($row = mysql_fetch_object($result)) {
				   $website_url = $row->website_url;
				}
				$pos = strpos($website_url,"?"); 
				if($pos>0){
				   $website_url = $website_url."&customer_id=".$customer_id_en;
				}else{
				   $website_url = $website_url."?customer_id=".$customer_id_en;
				}
				$url = $website_url;
				
				//城市商圈-店铺跳转
			}else if($linktype==10){
			   //城市商圈-美食				    
				$url = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type_id_1_10;
			}else if($linktype==11){
			   //城市商圈-KTV				    
				$url = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_10;
			}else if($linktype==12){
			   //城市商圈-酒店			    
				$url = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type_id_1_10;
			}else if($linktype==13){
			   //城市商圈-线下商城				    
				$url = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type_id_1_10;
				//城市商圈-店铺跳转End
				
				//城市商圈-行业跳转
			}else if($linktype==50){
			   //商圈行业列表-美食				    
				$url = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
			}else if($linktype==51){
			   //商圈行业列表-KTV				    
				$url = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
			}else if($linktype==52){
			   //商圈行业列表-酒店				    
				$url = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
			}else if($linktype==53){
			   //商圈行业列表-线下商城-首页
				$url = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
			}else if($linktype==54){
			   //商圈行业列表-线下商城-商家列表
				$url = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
			}
				//城市商圈-行业跳转End
			//优惠券跳转 Start		
				else if($linktype==60){	
					$url="../../mshop/coupon.php?customer_id=".$customer_id_en;
					if($type_id_1_10 != '1all'){		//1all以便通过>0判断	
						$url.= '&cp_id='.$type_id_1_10;
					}
				}		
				//优惠券跳转 End	
		  }else{
			   switch($type_id_1_10){
			        case -6:
				      $url="../../mshop/list.php?customer_id=".$customer_id_en;
				      break;
				   case -2:
				      $url="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
				      break;
				   case -3:
				      $url="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
				      break;
				   case -4:
				      $url="../../mshop/order_cart.php?customer_id=".$customer_id_en;
				      break;
				   case -7:
					  $url="../../mshop/class_page.php?customer_id=".$customer_id_en;
					  break;
				   case -8:
					  $url="../../mshop/personal_center.php?customer_id=".$customer_id_en;
				      break;
				   case -9:
					  $url="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
					   break;
				   case -5:
					  $url="../../mshop/snap_up.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -10:
					  $url="../../online/show_online.php?customer_id=".$customer_id_en; 	  
					  break;	
				  case -11:
					  $url="../../mshop/package_list.php?customer_id=".$customer_id_en; 	  
					  break;
					case -12:
					  $url="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en; 	  
					  break;  
					case -13:
					  $url="../../back_newshops/MarkPro/Rec_funnytest/index.php?customer_id=".$customer_id_en; 	  
					  break;
				   case -14:
					  $url="../../back_newshops/MarkPro/ruanwen/index.php?customer_id=".$customer_id_en; 	  
					  break;
					case -15:
					  $url="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;	  
					  break;  
					case -16:
					  $url="index.php?customer_id=".$customer_id_en;	  
					  break;
					case -17:
					  $url="../../mshop/proclass.php?customer_id=".$customer_id_en;	  
					  break;  
					case -18:
					  $url="../../mshop/orderlist.php?customer_id=".$customer_id_en;	  
					  break; 
			   }
			}
		  			
		  $foreign_id=$type_id_1_10;
			 
		  $lun_urls=$lun_urls.$url."|*|";
		  $lun_linktypes=$lun_linktypes.$linktype."|*|";
		  $lun_foreign_ids=$lun_foreign_ids.$foreign_id."|*|";
		//默认为链接到类型。以后可以链接到其他的功能模块
		  $lun_detail_ids=$lun_detail_ids.$product_detail_id_1_10."|*|";
	  }else{
	         $lun_urls=$lun_urls."|*|";
			 $lun_linktypes=$lun_linktypes."|*|";
			 $lun_foreign_ids=$lun_foreign_ids."|*|";
			  $lun_detail_ids=$lun_detail_ids."|*|";
	  }*/
	  
	  //var_dump($lun_foreign_ids);
	  if($lun_imgurls!=""){
		  
		  $lun_imgurls = substr($lun_imgurls,0,count($lun_imgurls)-4);
		  $lun_urls = substr($lun_urls,0,count($lun_urls)-4);
		  $lun_foreign_ids = substr($lun_foreign_ids,0,count($lun_foreign_ids)-4);	
		  $lun_linktypes = substr($lun_linktypes,0,count($lun_linktypes)-4);
	
		  $query="select id from weixin_commonshop_template_item_imgs where isvalid=true and template_id=".$template_id." and position=".$position." and customer_id=".$customer_id;	
		  $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
		  $ti_id=-1;
		  while ($row = mysql_fetch_object($result)) {
		     $ti_id = $row->id;
		  }
		  if($ti_id>0){
			 $query="update weixin_commonshop_template_item_imgs set imgurl='".$lun_imgurls."',url='".$lun_urls."',linktype='".$lun_linktypes."',foreign_id='".$lun_foreign_ids."',detail_id='".$lun_detail_ids."' where id=".$ti_id;			 
			 _mysql_query($query);
			
		  }else{
			$query="insert into weixin_commonshop_template_item_imgs(template_id,imgurl,position,url,linktype,foreign_id,isvalid,createtime,customer_id,detail_id) values(".$template_id.",'".$lun_imgurls."','".$position."','".$lun_urls."','".$lun_linktypes."','".$lun_foreign_ids."',true,now(),".$customer_id.",'".$lun_detail_ids."')";
			
			_mysql_query($query);
			$ti_id = mysql_insert_id();
		  }
			//echo $query .'<br>';
		  //4M同步下级商家图片
			if($is_shopgeneral == 1 && $is_samelevel == 1){
				$shop_4m->Fac_update_template_item($customer_id,$template_id,$ti_id,$lun_imgurls,$lun_urls,$lun_foreign_ids,$lun_linktypes,$position);
			}
	  }
  }
  
	
  

 $sql="update weixin_commonshops set index_bg='".$index_bg."' where isvalid=true and customer_id=".$customer_id;
 _mysql_query($sql);
 $error =mysql_error();
 mysql_close($link);
echo "<script>location.href='defaultset.php?customer_id=".$customer_id_en."';</script>";
 }
?>