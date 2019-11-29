<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../../weixinpl/config.php');
$customer_id = passport_decrypt($customer_id);
require('../../../../weixinpl/back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../../weixinpl/proxy_info.php');
_mysql_query("SET NAMES UTF8");

//ajax更改审核结果

	if($_POST["status"]!=''&&!empty($_POST["user_id"])){
		$data = array();
		if($_POST["status"]==0){
			$query_users="update weixin_commonshop_brand_supplys set brand_status='-1'  where user_id=".$_POST["user_id"]."";
			$res=_mysql_query($query_users);
			if($res!=false){
				$data['code'] = '0';
				$data['msg'] = '驳回成功';
			}else{
				$data['code'] = '1';
				$data['msg'] = '参数有误';
			}
		}elseif($_POST["status"]==1){
			$wholesaler_status = '';
			$wholesaler_sql = "select wholesaler_status from weixin_commonshop_wholesalers where isvalid=true and user_id=".$_POST["user_id"]." "; 
			//获取区域批发商申请状态
			$wholesaler_sql =_mysql_query($wholesaler_sql);
			if($row = mysql_fetch_object($wholesaler_sql)){
				$wholesaler_status = $row->wholesaler_status;
			}
			// var_dump($wholesaler_status);die;

			//该用户没有申请区域批发商,或已申请但状态是审核中的，同步合作商表  并 更改品牌合作商申请状态
			if($wholesaler_status==''||$wholesaler_status==0){
				//查询出品牌合作商基本信息
				$sql = "select * from weixin_commonshop_brand_supplys where isvalid=true and user_id=".$_POST["user_id"]." ";
				$data_brand=_mysql_query($sql);
				$data_brand=mysql_fetch_array($data_brand);

				//同步合作商表
				$query="update weixin_commonshop_applysupplys set user_name='".$data_brand['user_name']."',user_phone='".$data_brand['user_phone']."',sex='".$data_brand['sex']."',id_cards_num='".$data_brand['id_cards_num']."',location_p='".$data_brand['location_p']."',location_c='".$data_brand['location_c']."',location_a='".$data_brand['location_a']."',business_address='".$data_brand['brand_address']."',company_name='".$data_brand['brand_name']."',advisory_telephone='".$data_brand['brand_tel']."',business_licence_pic='".$data_brand['brand_business_license']."',id_cards_pic='".$data_brand['id_cards_pic']."' where isvalid=true and user_id=".$_POST["user_id"]." ";
				$res=_mysql_query($query);

				//改品牌合作商申请状态
				$query="update weixin_commonshop_brand_supplys set brand_status='".$_POST["status"]."',brand_opentime=now()  where user_id=".$_POST["user_id"]."";
				$res=_mysql_query($query);
                
                $applysupplys="update weixin_commonshop_applysupplys set isbrand_supply=true where user_id='".$_POST["user_id"]."' ";
                _mysql_query($applysupplys);//将供应商标识为品牌供应商
                
				$res2 = true;
			}else{ //$wholesaler_status==1  该用户已通过区域批发商审核,将品牌合作商信息同步到区域批发商中

				//查询出品牌合作商基本信息
				$sql = "select * from weixin_commonshop_brand_supplys where isvalid=true and user_id=".$_POST["user_id"]." ";
				$data_brand=_mysql_query($sql);
				$data_brand=mysql_fetch_array($data_brand);

				//同步合作商表
				$query="update weixin_commonshop_applysupplys set user_name='".$data_brand['user_name']."',user_phone='".$data_brand['user_phone']."',sex='".$data_brand['sex']."',id_cards_num='".$data_brand['id_cards_num']."',location_p='".$data_brand['location_p']."',location_c='".$data_brand['location_c']."',location_a='".$data_brand['location_a']."',business_address='".$data_brand['brand_address']."',company_name='".$data_brand['brand_name']."',advisory_telephone='".$data_brand['brand_tel']."',business_licence_pic='".$data_brand['brand_business_license']."',id_cards_pic='".$data_brand['id_cards_pic']."' where isvalid=true and user_id=".$_POST["user_id"]." ";
				$res=_mysql_query($query);


				//将品牌合作商信息同步到区域批发商中
				$query="update weixin_commonshop_wholesalers set user_name='".$data_brand['user_name']."',user_phone='".$data_brand['user_phone']."',sex='".$data_brand['sex']."',id_cards_num='".$data_brand['id_cards_num']."',location_p='".$data_brand['location_p']."',location_c='".$data_brand['location_c']."',location_a='".$data_brand['location_a']."',wholesaler_address='".$data_brand['brand_address']."',company_name='".$data_brand['brand_name']."',wholesaler_tel='".$data_brand['brand_tel']."',wholesaler_intro='".$data_brand['brand_intro']."',wholesaler_business_license='".$data_brand['brand_business_license']."',id_cards_pic='".$data_brand['id_cards_pic']."',wholesaler_logo='".$data_brand['brand_logo']."',qcode_bgimg='".$data_brand['qcode_bgimg']."' where isvalid=true and user_id=".$_POST["user_id"]." ";
					$res=_mysql_query($query);

				//更改审核状态
				$query="update weixin_commonshop_brand_supplys set brand_status='".$_POST["status"]."',brand_opentime=now() where user_id=".$_POST["user_id"]."";
				$res2=_mysql_query($query);
                
                $applysupplys="update weixin_commonshop_applysupplys set isbrand_supply=true where user_id='".$_POST["user_id"]."' ";
                _mysql_query($applysupplys);//将供应商标识为品牌供应商

			}
			if($res!=false&&$res2!=false){
				$data['code'] = '2';
				$data['msg'] = '审核成功';
			}else{
				$data['code'] = '3';
				$data['msg'] = '参数有误';
			}
		}
		echo json_encode($data);
		exit;
	}

$supply_qq = '';
$xiaoneng = '';

$user_id = $configutil->splash_new($_POST["user_id"]);
$brand_name = $configutil->splash_new($_POST["brand_name"]);//公司名
$brand_tel = $configutil->splash_new($_POST["brand_tel"]);//公司电话
$brand_address = $configutil->splash_new($_POST["brand_address"]);//公司地址
$brand_intro = $configutil->splash_new(nl2br($_POST["brand_intro"]));//公司简介
$brand_supply_name = $configutil->splash_new($_POST["brand_supply_name"]); //品牌供应商名称
$brand_supply_name = $configutil->splash_new($_POST["brand_supply_name"]); //品牌供应商名称

$supply_id = $configutil->splash_new($_GET["supply_id"]); //品牌供应商ID
$is_kefu = $configutil->splash_new($_POST["is_kefu"]); //是否开启客服
$kefu_type = $configutil->splash_new($_POST["kefu_type"]); //客服类型
$supply_qq = $configutil->splash_new($_POST["supply_qq"]); //QQ
$siteid = $configutil->splash_new($_POST["siteid"]); //小能企业号
$xiaoneng = $configutil->splash_new($_POST["xiaoneng"]); //小能客服接待组
/* echo $supply_qq.'<br>';
echo $xiaoneng.'<br>'; */

$chat  =    array(
'supply_qq'=>$supply_qq,
'xiaoneng'=>$xiaoneng,
);
$chat_json = json_encode($chat); 

	$save_brand="update weixin_commonshop_brand_supplys set brand_name='".$brand_name."',brand_tel='".$brand_tel."',brand_address='".$brand_address."',brand_supply_name='".$brand_supply_name."',brand_intro='".$brand_intro."' where user_id='".$user_id."' and customer_id=".$customer_id." ";
	$result=_mysql_query($save_brand) or die('Query1 failed: ' . mysql_error());

	//同步保存区域批发商资料
    $save_wholesalers="update weixin_commonshop_wholesalers set company_name='".$brand_name."',wholesaler_name='".$brand_supply_name."',wholesaler_intro='".$brand_intro."' where user_id='".$user_id."' and customer_id=".$customer_id." ";
    $result=_mysql_query($save_wholesalers) or die('Query1 failed: ' . mysql_error());

	$sql="update weixin_commonshop_supply_kefu set is_kefu=".$is_kefu.",kefu_type='".$kefu_type."',supply_qq='".$chat_json."' where supply_id='".$supply_id."' and customer_id=".$customer_id." ";
			
	$result2=_mysql_query($sql) or die('Query2 failed: ' . mysql_error());
	//echo $sql; die;


mysql_close($link);
echo "<script>location.href='brand_supply.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
 
?>