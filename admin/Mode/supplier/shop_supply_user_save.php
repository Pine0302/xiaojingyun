<?php 
  header("Content-type: text/html; charset=utf-8"); 
  require('../../../../weixinpl/config.php');
  $customer_id = passport_decrypt($customer_id);
  require('../../../../weixinpl/back_init.php');
  $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
   mysql_select_db(DB_NAME) or die('Could not select database');
   _mysql_query("SET NAMES UTF8");
   require('../../../../weixinpl/proxy_info.php');

    //搜索开关
   $is_search ='';
   $is_search =$_POST['is_search'];
   // 检测开关
   $is_check ='';
   $is_check =$_POST['is_check'];
    //ajax更改审核结果
    if($_POST["status"]!=''&&!empty($_POST["user_id"])){
        $data=array();
        if($_POST["status"]==0){
            $query_users="update weixin_commonshop_applysupplys set status='-1' where user_id=".$_POST["user_id"]." ";
            $res=_mysql_query($query_users);

            var_dump($_POST["user_id"]);die;
            $query_users="select id from weixin_qr_infos where isvalid=true and foreign_id=".$_POST["user_id"]." ";
            $res=_mysql_query($query_users);
	        while ($row = mysql_fetch_object($res)) {
	           $wqi_id = $res->id;
	        } 

            $query_users="update weixin_qrs set reason='".$_POST["reason"]."' where id=".$wqi_id." ";
            $res=_mysql_query($query_users);

            if($res!=false){
                $data['code'] = '0';
                $data['msg'] = '驳回成功';
            }else{
                $data['code'] = '1';
                $data['msg'] = '参数有误';
            }
        }elseif($_POST["status"]==1){
            $query_users="update weixin_commonshop_applysupplys set status='".$_POST["status"]."'  where user_id=".$_POST["user_id"]."";
            $res=_mysql_query($query_users);
            if($res!=false){
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

    //搜索商城用户信息
    if(($_POST["promoters_id"]!=''||$_POST["promoters_name"]!='')&&$is_search==true){

        $data=array();
        $i=0;

        $query="select wu.name,p.user_id from weixin_users wu inner join wsy_pub.promoters p on wu.id=p.user_id where p.isvalid=true";
    

        if ($_POST["promoters_id"]!='') {
            $query .= " and wu.id ='".$_POST["promoters_id"]."'";
        }

        if ($_POST["promoters_name"]!='') {
            $query .= " and wu.name like '%".$_POST["promoters_name"]."%'";
        }
        $res=_mysql_query($query);
        $result=_mysql_query($query);


        $result=mysql_fetch_object($result);

        if($result){
            while ($row = mysql_fetch_object($res)){
                $data[$i]['name'] = $row->name.':'.$row->user_id;
                $data[$i]['user_id'] = $row->user_id;
                $i++;
            }
            $restult['errcode'] = '0';
            $restult['info'] = $data;
        }else{
            $restult['errcode'] = '1';
            $restult['msg'] = '用户不存在';
        }
        echo json_encode($restult);
        exit;
    }


    //检测用户是否已将注册合作商
    if($_POST["check_uid"]!=''&&$is_check==true){

        $data=array();
        $i=0;

        $query="select user_id from weixin_commonshop_applysupplys where isvalid=true and user_id='".$_POST["check_uid"]."'";

    
        $res=_mysql_query($query);

        while ($row = mysql_fetch_object($res)){
            $user_id = $row->user_id;
        }
        if($user_id){
            $restult['errcode'] = '1';
            $restult['msg'] = '该用户已申请合作商';
        }else{

            $restult['errcode'] = '0';
        }
        echo json_encode($restult);
        exit;
    }

    $keyid = -1;
    $name ="";
    $password ="";
    if(!empty($_POST["keyid"])){
        $keyid = $configutil->splash_new($_POST["keyid"]);
    }

    $user_id  = $configutil->splash_new($_POST['userName']);

    // 用户名
    $user_name = $configutil->splash_new($_POST['username']); 
    //真实姓名
    $true_name = $configutil->splash_new($_POST['name']); 

    $password = $configutil->splash_new($_POST['password']); 
    $sex = $configutil->splash_new($_POST['sex']);
    $user_phone = $configutil->splash_new($_POST['phone']);
    // 需要修改
    $location_p = $configutil->splash_new($_POST['location_p']);
    $location_c = $configutil->splash_new($_POST['location_c']);
    $location_a = $configutil->splash_new($_POST['location_a']);

    $business_address=$configutil->splash_new($_POST['business_address']);

    $id_cards_num = $configutil->splash_new($_POST['idcard_num']);
    $company_name = $configutil->splash_new($_POST['company_name']);
    $apply_way    = $configutil->splash_new($_POST['apply_way']);
  
    $logo_img = "";
    //获取身份证图片

    //获取数组长度

    // 全部删除走idcard_pic, 不改变就提交走idcard_pic_ex,  改一个
    $idcard_length=count($_FILES['idcard_pic']['name']);
    foreach ($_FILES['idcard_pic']['name'] as $i => $v){
        if(!empty($_FILES['idcard_pic']['name'][$i])){
            $rand1=rand(0,9);
            $rand2=rand(0,9);
            $rand3=rand(0,9);
            $filename=date("Ymdhis").$rand1.$rand2.$rand3;

            $filetype=substr($_FILES['idcard_pic']['name'][$i], strrpos($_FILES['idcard_pic']['name'][$i], "."),strlen($_FILES['idcard_pic']['name'][$i])-strrpos($_FILES['idcard_pic']['name'][$i], "."));
            $filetype=strtolower($filetype);

            if(($filetype!='.png')&&($filetype!='.jpg')&&($filetype!='.gif')){
                    echo "<script>alert('文件类型或地址错误');</script>";
                    echo "<script>history.back(-1);</script>";
                    exit ;
                }
            $savedir='../../../'.Base_Upload.'Mode/supplier/';
            $filename=$filename.$filetype;

            if(!is_dir($savedir)){
                mkdir($savedir,0777,true);
            }

            $savefile[$i]=$savedir.$filename;
            if (!_move_uploaded_file($_FILES['idcard_pic']['tmp_name'][$i], $savefile[$i])){
                echo 'back1';
                echo "<script>history.back(-1);</script>";
                exit;
            }

            $idcard_dir[$i]=$savefile[$i];
            $idcard_dir[$i] = str_replace("../","",$idcard_dir[$i]);
            $idcard_dir[$i] = "/mshop/".$idcard_dir[$i];
        }else{
            $idcard_dir[$i]=$configutil->splash_new($_POST['idcard_pic_ex'][$i]);
        } 
    }


    //  将存有两张图片路径的数组转化成字符串,用|连接两张图片的地址
        $idcard_savefile=implode('|', $idcard_dir);
// }
    //获取身份证图片end

    //获取营业执照图片

    //获取营业执照图片长度
    $business_length=count($_FILES['business_licence_pic']['name']);
    foreach ($_FILES['business_licence_pic']['name'] as $i => $v) {
    // for ($i=0; $i <$business_length ; $i++) {
        if(!empty($_FILES['business_licence_pic']['name'][$i])){
            $rand1=rand(0,9);
            $rand2=rand(0,9);
            $rand3=rand(0,9);
            $filename=date("Ymdhis").$rand1.$rand2.$rand3;

            $filetype=substr($_FILES['business_licence_pic']['name'][$i], strrpos($_FILES['business_licence_pic']['name'][$i], "."),strlen($_FILES['business_licence_pic']['name'][$i])-strrpos($_FILES['business_licence_pic']['name'][$i], "."));
            $filetype=strtolower($filetype);
            if(($filetype!='.png')&&($filetype!='.jpg')&&($filetype!='.gif')){
                    echo "<script>alert('文件类型或地址错误');</script>";
                    echo "<script>history.back(-1);</script>";
                    exit ;
                }
            $savedir='../../../'.Base_Upload.'Mode/supplier/';
            $filename=$filename.$filetype;

            if(!is_dir($savedir)){
                mkdir($savedir,0777,true);
            }

            $savefile[$i]=$savedir.$filename;

            if (!_move_uploaded_file($_FILES['business_licence_pic']['tmp_name'][$i], $savefile[$i])){
                echo 'back2';
                // echo "<script>history.back(-1);</script>";
                exit;
            }

            $business_dir[$i] = $savefile[$i];
            $business_dir[$i] = str_replace("../","",$business_dir[$i]);
            $business_dir[$i] = "/mshop/".$business_dir[$i];
        } else{
            $business_dir[$i]=$configutil->splash_new($_POST['business_licence_pic_ex'][$i]);
        } 
    }
    
    //第二次循环 用|连接两张图片的地址
    $business_savefile=implode('|', $business_dir);
    //获取营业执照图片end

    if($keyid>0){
        // 用户名(自填 weixin_users表)   密码
        // 推广员表 密码 password
        // apply表 正式姓名、手机号码、性别、 地址 、详细地址、身份证号、公司名称、身份证营业执照相片
        // apply.    wu.  p.
        //weixin_users
        $query_users="update weixin_users set name='".$user_name."',pwd='".$password."',sex='".$sex."', weixin_headimgurl='".$logo_img."',phone='".$phone."'  where isvalid=true and id=".$keyid."";
        _mysql_query($query_users) or die('Query_users failed: ' . mysql_error());  
        //echo $query_users."<br>"; 

        // wsy_pub promoters
        $query_users="update promoters set pwd='".$password."' where isvalid=true and user_id=".$keyid."";
        _mysql_query($query_users) or die('Query_users failed: ' . mysql_error());  


        //修改的选项
        $change_item_sql == '';
		if ($true_name != '') $change_item_sql = " user_name='".$true_name."',";
		if($user_phone != '')$change_item_sql .= " user_phone='".$user_phone."',";
		if($location_p != '')$change_item_sql .= " location_p='".$location_p."',";
		if($location_c != '')$change_item_sql .= " location_c='".$location_c."',";
		if($location_a != '')$change_item_sql .= " location_a='".$location_a."',";
		if($business_address != '')$change_item_sql .= " business_address='".$business_address."',";
		if($id_cards_num != '')$change_item_sql .= " id_cards_num='".$id_cards_num."',";
		if($idcard_savefile != '')$change_item_sql .= " id_cards_pic='".$idcard_savefile."',";
		if($business_savefile != '')$change_item_sql .= " business_licence_pic='".$business_savefile."',";
		if($company_name != '')$change_item_sql .= " company_name='".$company_name."',";
		

        // weixin_commonshop_applysupplys
        $query_users="update weixin_commonshop_applysupplys set ".$change_item_sql." sex='".$sex."' where isvalid=true and user_id=".$keyid."";
        _mysql_query($query_users) or die('Query_users failed: ' . mysql_error());      

        //查询 微商城 会员卡卡号
        /*
        $card_id=-1;
        $query_card="SELECT shop_card_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 1";
        $result_card = _mysql_query($query_card) or die('Query_card failed: ' . mysql_error());
        while ($row_card = mysql_fetch_object($result_card)) {
           $card_id = $row_card->shop_card_id;
        }   
        
        //weixin_card_members
        if($card_id>0){
            $query_card="update weixin_card_members set name='".$name."',sex='".$sex."' ,phone='".$phone."'  where user_id=".$keyid." and card_id=".$card_id;
            _mysql_query($query_card) or die('Query_card failed: ' . mysql_error());    
        }*/
        
        //echo $query_card."<br>";           
  
    }else{

        $weixin_fromuser=generate_random(24); //随机24号码

        if($user_id==''){
        //weixin_users   ifl
        $query_user="insert into weixin_users (name,createtime,customer_id,weixin_headimgurl,type,phone,isvalid,pwd,sex,weixin_fromuser)values('".$user_name."',now(),".$customer_id.",'".$logo_img."',5,'".$phone."',true,'".$password."','".$sex."','".$weixin_fromuser."')";
        _mysql_query($query_user) or die('Query_user failed: ' . mysql_error()); 
            $user_id = mysql_insert_id();

        $query_promoter="insert into promoters (user_id,createtime,customer_id,parent_id,status,isAgent,isvalid,pwd)values('".$user_id."',now(),".$customer_id.",-1,1,3,true,'".$password."')";
        _mysql_query($query_promoter) or die('Query_promoter failed: ' . mysql_error());


                //weixin_qr_infos    scene_id???      ifl
        $query_qr_infos="insert into weixin_qr_infos (foreign_id,customer_id,isvalid,type,scene_id,user_type,obj_id)values('".$user_id."',".$customer_id.",1,1,1,1,1)";
        _mysql_query($query_qr_infos) or die('Query_qr_infos failed: ' . mysql_error()); 
        //echo $query_qr_infos."<br>";
        $qr_info_id = mysql_insert_id();
        //echo $qr_info_id."<br>";  

        //weixin_qrs     
        $query_qrs="insert into weixin_qrs(expire_seconds,action_name,qr_info_id,isvalid,createtime,customer_id,type,imgurl_qr,reward_score,reward_money,status,apptype)values(-1,'QR_LIMIT_SCENE',".$qr_info_id.",true,now(),".$customer_id.",1,'',0,0,1,0)";
        //echo $query_qrs."<br>";       
        _mysql_query($query_qrs) or die('Query_qrs failed: ' . mysql_error());  

        }
        //echo $query_user."<br>";  

        //echo $user_id."<br>";                     
        
        //promoters   ifl
 
        //echo $query_promoter."<br>";                              
        
        $query = "select deposit from weixin_commonshop_supplys where isvalid=true and customer_id=".$customer_id;
        $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $deposit=$row->deposit;
        }

        $query_applysupply="insert into weixin_commonshop_applysupplys (user_id,user_name,user_phone,sex,location_p,location_c,location_a,business_address,id_cards_num,company_name,id_cards_pic,business_licence_pic,apply_way,createtime,isvalid,supply_money,deposit,status,shopName)values('".$user_id."','".$true_name."','".$user_phone."','".$sex."','".$location_p."','".$location_c."','".$location_a."','".$business_address."','".$id_cards_num."','".$company_name."','".$idcard_savefile."','".$business_savefile."','".$apply_way."',now(),1,0,'".$deposit."',1,'{$name}')";
        _mysql_query($query_applysupply) or die('Query_applysupply failed: ' . mysql_error()); 
        //echo $query_applysupply."<br>";                       
        $query_users="update promoters set pwd='".$password."',isAgent = 3 where isvalid=true and user_id=".$user_id."";
        _mysql_query($query_users) or die('Query_users failed: ' . mysql_error());  


        
        //V8.0商城取消添加供应商是添加会员卡
        //查询 微商城 会员卡卡号 
        //$card_id=-1;
        //$query_card="SELECT shop_card_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 1";
        //$result_card = _mysql_query($query_card) or die('Query_card failed: ' . mysql_error());
        //while ($row_card = mysql_fetch_object($result_card)) {
        //   $card_id = $row_card->shop_card_id;
        //} 

        //weixin_card_members 
        //$query_card_members="insert into weixin_card_members (card_id,name,phone,isvalid,createtime,sex,user_id)values('".$card_id."','".$name."','".$phone."',true,now(),".$sex.",".$user_id.")";
        //_mysql_query($query_card_members) or die('Query_card_members failed: ' . mysql_error()); 
        //echo $query_card_members."<br>";                  

        //weixin_commonshop_supply_pc 
        $query_supply_pc="insert into weixin_commonshop_supply_pc (customer_id,isvalid,createtime,user_id)values('".$customer_id."',true,now(),".$user_id.")";
        _mysql_query($query_supply_pc) or die('Query_supply_pc failed: ' . mysql_error()); 
        //echo $query_supply_pc."<br>";     
            
    }
     

echo "<script>location.href='shop_supply_user.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";

//生成随即字符串
function generate_random( $length = 6 ) {  
// 密码字符集，可任意添加你需要的字符  
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';  
    $random ='';  
    for ( $i = 0; $i < $length; $i++ )  
    {  
    // 这里提供两种字符获取方式  
    // 第一种是使用 substr 截取$chars中的任意一位字符；  
    // 第二种是取字符数组 $chars 的任意元素  
    // $random .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
    $random .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
    }  
    return $random;  
} 

?>