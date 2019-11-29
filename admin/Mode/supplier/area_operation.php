<?php
header("Content-type: text/html; charset=utf-8");     
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php'); 

// 数据库操作类
require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/namespace_database.php');
$database = new \Key\DB();

// 连接数据库
$setDB = $database->linkDB(DB_HOST,DB_USER,DB_PWD,DB_NAME);


//ajax更改审核结果

    if($_POST["status"]!=''&&!empty($_POST["user_id"])){
        $data=array();
        if($_POST["status"]==0){
            $query_users="update weixin_commonshop_wholesalers set wholesaler_status='-1'  where user_id=".$_POST["user_id"]."";
            $res=_mysql_query($query_users);
            if($res!=false){
                $data['code'] = '0';
                $data['msg'] = '驳回成功';
            }else{
                $data['code'] = '1';
                $data['msg'] = '参数有误';
            }
        }elseif($_POST["status"]==1){

            $brand_status = '';
            $brand_sql = "select brand_status from weixin_commonshop_brand_supplys where isvalid=true and user_id=".$_POST["user_id"]." ";
            // echo $brand_sql."<br>";
            //获取区域批发商申请状态
            $brand_sql =_mysql_query($brand_sql);
            if($row = mysql_fetch_object($brand_sql)){
                $brand_status = $row->brand_status;
            }
            // var_dump($brand_status);die;
            //该用户没有申请品牌合作商,  将区域批发商信息同步至合作商表，更改区域批发商申请状态
            if($brand_status==''){
                //查询出区域批发商基本信息
                $sql = "select * from weixin_commonshop_wholesalers where isvalid=true and user_id=".$_POST["user_id"]." ";
                $data_wholesalers=_mysql_query($sql);
                $data_wholesalers=mysql_fetch_array($data_wholesalers);

                //同步合作商表
                $query="update weixin_commonshop_applysupplys set user_name='".$data_wholesalers['user_name']."',user_phone='".$data_wholesalers['user_phone']."',id_cards_num='".$data_wholesalers['id_cards_num']."',location_p='".$data_wholesalers['location_p']."',location_c='".$data_wholesalers['location_c']."',location_a='".$data_wholesalers['location_a']."',business_address='".$data_wholesalers['wholesaler_address']."',company_name='".$data_wholesalers['company_name']."',advisory_telephone='".$data_wholesalers['wholesaler_tel']."',business_licence_pic='".$data_wholesalers['wholesaler_business_license']."',id_cards_pic='".$data_wholesalers['id_cards_pic']."',shopName='".$data_wholesalers['wholesaler_name']."' where isvalid=true and user_id=".$_POST["user_id"]." ";
                $res=_mysql_query($query);

                //更改区域批发商申请状态
                $query="update weixin_commonshop_wholesalers set wholesaler_status='".$_POST["status"]."'  where user_id=".$_POST["user_id"]."";
                $res=_mysql_query($query);

                if($res!=false){
                    $data['code'] = '2';
                    $data['msg'] = '审核成功';
                }else{
                    $data['code'] = '3';
                    $data['msg'] = '参数有误';
                }
            }elseif($brand_status==0){
                $data['code'] = '4';
                $data['msg'] = '请先审核品牌合作商';
            }else{//$brand_status==1   该用户已通过区域批发商审核,将品牌合作商信息同步到区域批发商表 以及 合作商表 并更改审批状态

                //查询出区域批发商基本信息
                $sql = "select * from weixin_commonshop_wholesalers where isvalid=true and user_id=".$_POST["user_id"]." ";
                $data_wholesalers=_mysql_query($sql);
                $data_wholesalers=mysql_fetch_array($data_wholesalers);


                //同步合作商表
                $query="update weixin_commonshop_applysupplys set user_name='".$data_wholesalers['user_name']."',user_phone='".$data_wholesalers['user_phone']."',sex='".$data_wholesalers['sex']."',id_cards_num='".$data_wholesalers['id_cards_num']."',location_p='".$data_wholesalers['location_p']."',location_c='".$data_wholesalers['location_c']."',location_a='".$data_wholesalers['location_a']."',business_address='".$data_wholesalers['wholesaler_address']."',company_name='".$data_wholesalers['company_name']."',advisory_telephone='".$data_wholesalers['wholesaler_tel']."',business_licence_pic='".$data_wholesalers['wholesaler_business_license']."',id_cards_pic='".$data_wholesalers['id_cards_pic']."',shopName='".$data_wholesalers['wholesaler_name']."' where isvalid=true and user_id=".$_POST["user_id"]." ";
                $res=_mysql_query($query);

                //将区域批发商信息同步到品牌合作商中
                $query="update weixin_commonshop_brand_supplys set user_name='".$data_wholesalers['user_name']."',user_phone='".$data_wholesalers['user_phone']."',sex='".$data_wholesalers['sex']."',id_cards_num='".$data_wholesalers['id_cards_num']."',location_p='".$data_wholesalers['location_p']."',location_c='".$data_wholesalers['location_c']."',location_a='".$data_wholesalers['location_a']."',brand_address='".$data_wholesalers['wholesaler_address']."',brand_name='".$data_wholesalers['company_name']."',brand_tel='".$data_wholesalers['wholesaler_tel']."',brand_intro='".$data_wholesalers['wholesaler_intro']."',brand_business_license='".$data_wholesalers['wholesaler_business_license']."',id_cards_pic='".$data_wholesalers['id_cards_pic']."',brand_logo='".$data_wholesalers['wholesaler_logo']."',brand_supply_name='".$data_wholesalers['wholesaler_name']."' where isvalid=true and user_id=".$_POST["user_id"]." ";
                $res=_mysql_query($query);

                //更改区域批发商审批状态
                $query="update weixin_commonshop_wholesalers set wholesaler_status='".$_POST["status"]."'  where user_id=".$_POST["user_id"]."";
                $res2=_mysql_query($query);

                if($res!=false&&$res2!=false){
                    $data['code'] = '2';
                    $data['msg'] = '审核成功';
                }else{
                    $data['code'] = '3';
                    $data['msg'] = '参数有误';
                }
            }
        }
        echo json_encode($data);
        exit;
    }



// var_dump($_REQUEST);exit;

$op                 = $database->init($_REQUEST['op'],0);
$is_area            = $database->init($_REQUEST['is_area'],0);
$is_area_entrance   = $database->init($_REQUEST['is_area_entrance'],0);
$user_location      = $database->init($_REQUEST['user_location'],0);
$is_prerogative     = $database->init($_REQUEST['is_prerogative'],0);
$level              = $database->init($_REQUEST['level'],0);
$apply_restrict     = $database->init($_REQUEST['apply_restrict'],0);
$brandsupply_detail = $database->init($_REQUEST['brandsupply_detail'],0);
$level              = implode('_', $level);

$name               = $database->init($_REQUEST['name'],0);
$sort               = $database->init($_REQUEST['sort'],0);
$id                 = $database->init($_REQUEST['id'],0);

$type               = $database->init($_REQUEST['type'],0);
$wqid               = $database->init($_REQUEST['wqid'],0);
$str                = $database->init($_REQUEST['str']);

if( $database->init($_REQUEST['user_id']) == null ){    //通过用的是user_id     保存区域用的是supply_id
    $user_id        = $database->init($_REQUEST['supply_id'],0);
}else{
    $user_id        = $database->init($_REQUEST['user_id'],0);
}
// var_dump($user_id);
// exit;

$company_name         = $database->init($_REQUEST['company_name'],0);
$wholesaler_name  = $database->init($_REQUEST['wholesaler_name'],0);
$wholesaler_tel          = $database->init($_REQUEST['wholesaler_tel'],0);
$wholesaler_address      = $database->init($_REQUEST['wholesaler_address']);
$wholesaler_intro        = $database->init($_REQUEST['wholesaler_intro'],0);


$region                  = $database->init($_REQUEST['region']);
$city_area               = $database->init($_REQUEST['city_area']);

$is_kefu = $configutil->splash_new($_POST["is_kefu"]); //是否开启客服
$kefu_type = $configutil->splash_new($_POST["kefu_type"]); //客服类型
$supply_qq = $configutil->splash_new($_POST["supply_qq"]); //QQ
// $siteid = $configutil->splash_new($_POST["siteid"]); //小能企业号
$xiaoneng = $configutil->splash_new($_POST["xiaoneng"]); //小能客服接待组


$chat  =    array(
'supply_qq'=>$supply_qq,
'xiaoneng'=>$xiaoneng,
);
$chat_json = json_encode($chat);
// var_dump($chat_json);die;

// var_dump($is_kefu);echo '<br>';
// var_dump($kefu_type);echo '<br>';
// var_dump($supply_qq);echo '<br>';
// var_dump($siteid);echo '<br>';
// var_dump($xiaoneng);die;


// var_dump($region);echo '<br>';
// var_dump($city_area);die;
// var_dump($level);exit;
switch ($op) {
    case 1:
        // 更新区域批发设置
        $result = update_area_set($customer_id,$is_area,$user_location,$is_prerogative,$level,$apply_restrict,$brandsupply_detail);
        break;
    case 2:
        // 新建区域批发设置
        $result = save_area_set($customer_id,$is_area,$user_location,$is_prerogative,$level,$apply_restrict,$brandsupply_detail);
        break;

    case 3:
        // 新建类目
        $result = save_category($customer_id,$name,$sort);
        break;
    case 4:
        // 更新类目
        $result = update_category($customer_id,$name,$sort,$id);
        break;
    case 5:
        // 检测类目名重名
        $result = check_name($customer_id,$name,$id);
        echo $result;
        return false;
        break;
    case 6:
        // 删除类目名
        $result = del_category($customer_id,$id);
        echo $result;
        return false;
        break;

    case 7:
        // 驳回||通过区域批发商
        $result = change($customer_id,$user_id,$type,$wqid,$str);
        echo "<script>location.href='area_supply.php'</script>";
        break;
    case 8:
        // 删除区域批发商
        $result = del_area($customer_id,$id,$user_id);
        echo "<script>location.href='area_supply.php'</script>";
        break;
    case 9:
        // 更新区域批发商详情
        $user_id            = $database->init($_REQUEST['supply_id'],0);
        $result = update_area($customer_id,$user_id,$company_name,$wholesaler_name,$wholesaler_tel,$wholesaler_address,$wholesaler_intro,$region,$city_area,$is_kefu,$kefu_type,$chat_json);
        echo "<script>location.href='area_supply.php'</script>";
        // var_dump($result);
        break;
    default:
        # code...
        break;
}
echo json_encode($result);

// 新建区域批发设置
function save_area_set($customer_id,$is_area,$user_location,$is_prerogative,$level,$apply_restrict,$brandsupply_detail){
    global $database;

    $sql = "INSERT into weixin_commonshop_area (customer_id,is_area,user_location,is_prerogative,level,apply_restrict,brandsupply_detail,isvalid,createtime) 
            values('{$customer_id}',{$is_area},'{$user_location}',{$is_prerogative},'{$level}',{$apply_restrict},'{$brandsupply_detail}',true,now())";
    $database->query($sql);
    echo "<script>location.href='area_set.php'</script>";
    return $sql;
}

// 更新区域批发设置
function update_area_set($customer_id,$is_area,$user_location,$is_prerogative,$level,$apply_restrict,$brandsupply_detail){
    global $database;

    $sql = "UPDATE weixin_commonshop_area set is_area={$is_area},user_location='{$user_location}',is_prerogative={$is_prerogative},level='{$level}',apply_restrict={$apply_restrict},brandsupply_detail='{$brandsupply_detail}' where customer_id='{$customer_id}' and isvalid=true";
    $database->query($sql);
    echo "<script>location.href='area_set.php'</script>";
    return $sql;
}

function save_category($customer_id,$name,$sort){
    global $database;

    $sql = "INSERT into weixin_commonshop_area_category (customer_id,name,sort,isvalid,createtime) 
            values('{$customer_id}','{$name}','{$sort}',true,now())";
    $database->query($sql);
    return $sql;
}

function update_category($customer_id,$name,$sort,$id){
    global $database;

    $sql = "UPDATE weixin_commonshop_area_category set name='{$name}',sort='{$sort}' where customer_id='{$customer_id}' and isvalid=true and id='{$id}'";
    $database->query($sql);
    return $result;
}

function check_name($customer_id,$name,$id){
    global $database;

    $sql = "SELECT count(1) from weixin_commonshop_area_category where name like '%{$name}%' and id not in({$id}) and customer_id='{$customer_id}' and isvalid=true";
    $result = $database->getField($sql);
    return $result;
}

function del_category($customer_id,$id){
    global $database;

    $count = check_category($customer_id,$id);
    // echo $count;
    if($count <= 0){
        $sql = "UPDATE weixin_commonshop_area_category set isvalid=false where customer_id='{$customer_id}' and isvalid=true and id='{$id}'";
        $database->query($sql);
        $count = 0;
    }
    return $count;
}

function check_category($customer_id,$id){
    global $database;

    $sql = "SELECT count(1) from weixin_commonshop_wholesalers where business_category like '%{$id}%' and isvalid=true and customer_id='{$customer_id}' ";
    $result = $database->getField($sql);
    return $result;
}

function change($customer_id,$user_id,$type,$wqid,$str){
    global $database;

    // $sql = "SELECT user_id from weixin_commonshop_wholesalers where id='{$id}' ";
    // $user_id = $database->getField($sql);
    if( $type==2 ){
        $status = 1;
        $sql = "UPDATE weixin_qrs set status={$status},reason='{$str}' where id='{$wqid}' and customer_id='{$customer_id}'";
        $result = $database->query($sql);

        $types = 3;
        $remark = '驳回申请';
        $sql = "INSERT into weixin_commonshop_wholesaler_logs(customer_id,isvalid,user_id,type,remark,createtime) values('{$customer_id}',true,'{$user_id}','{$types}','{$remark}',now()) ";
    }else{
        $sql = "SELECT wholesaler_name from ".WSY_SHOP.".weixin_commonshop_wholesalers where user_id='{$user_id}' ";
        $result_name = $database->getField($sql);
        $sqls = "UPDATE weixin_commonshop_applysupplys set isarea_supply=true,shopName='".$result_name."' where user_id='{$user_id}' ";
        $result = $database->query($sqls);

        $types = 2;
        $remark = '通过申请';
        $sql = "INSERT into weixin_commonshop_wholesaler_logs(customer_id,isvalid,user_id,type,remark,createtime) values('{$customer_id}',true,'{$user_id}','{$types}','{$remark}',now()) ";
        $result = $database->query($sql);
    }

    $sql = "UPDATE weixin_commonshop_wholesalers set wholesaler_status={$type} where customer_id='{$customer_id}' and isvalid=true and user_id='{$user_id}'";
    // echo $sql;exit;
    $result = $database->query($sql);
    return $result;
}

function del_area($customer_id,$id,$user_id){
    global $database;

    $sql = "UPDATE weixin_commonshop_wholesalers set isvalid=false where customer_id='{$customer_id}' and isvalid=true and id='{$id}'";
    $result = $database->query($sql);

    $sqls = "UPDATE weixin_commonshop_applysupplys set isarea_supply=false where user_id='{$user_id}' ";
    $result = $database->query($sqls);

    return $result;
}

function update_area($customer_id,$user_id,$company_name,$wholesaler_name,$wholesaler_tel,$wholesaler_address,$wholesaler_intro,$e_region,$city_area,$is_kefu,$kefu_type,$supply_qq){

    global $database;

    $e_region  = explode(",",$e_region);
    $city_area = explode(",",$city_area);

    $wholesale_areas_p = array();
    $wholesale_areas_c = array();
    $wholesale_areas_d = array();

    $count = count($city_area);
    for($i=0;$i<$count;$i++){
        $single_cd = explode('_',$city_area[$i]);
        $single_c = $single_cd[0];   //市
        $single_d = $single_cd[1];   //区

        // $con0['MergerName'] = array('like','%,'.$single_c.','.$single_d);
        $sql = "SELECT ID,ParentId from address where MergerName like '%{$single_c},{$single_d}%'";
        $address_d = $database->getFields($sql);//查找区的ID 以及市的ID

        if($address_d==null){

            $single_d = substr($single_d,0,-3);

            $sql = "SELECT ID,ParentId from address where (MergerName like '%{$single_c},{$single_d}市%') or (MergerName like '%{$single_c},{$single_d}区%') or (MergerName like '%{$single_c},{$single_d}县%') ";

            $address_d = $database->getFields($sql);//查找区的ID 以及市的ID
        }

        $sql = "SELECT ID,ParentId from address where ID = '{$address_d['ID']}'";
        $address_c = $database->getFields($sql);//根据区的ID查找市的ID

        $sql = "SELECT ID,ParentId from address where ID = '{$address_c['ParentId']}'";
        $address_p = $database->getFields($sql);//根据市的ID查找省的ID


        // $address_d = $m_address->where($con0)->field('ID')->find();
        // $con1['ID'] = $address_d['ID'];
        // $address_c = $m_address->where($con1)->field('ParentId')->find();
        // $con2['ID'] = $address_c['ParentId'];
        // $address_p = $m_address->where($con2)->field('ParentId')->find();

        $wholesale_areas_d[] = $address_d['ID'];   //区的id
        $wholesale_areas_c[] = $address_c['ParentId'];  //市的id
        $wholesale_areas_p[] = $address_p['ParentId'];  //省的id
    }

    // var_dump($wholesale_areas_d,$wholesale_areas_c,$wholesale_areas_p);exit;
    //批发区域处理 END

    /* ----- 批发区域修改 ------*/
    //删除现存的所有区域
    $sql = "UPDATE weixin_commonshop_wholesaler_areas set isvalid = false where customer_id='{$customer_id}' and isvalid=true and user_id='{$user_id}'";
    $database->query($sql);

    //添加新选择的区域
    for($i=0;$i<count($wholesale_areas_p);$i++){
        $sql = "INSERT INTO `weixin_commonshop_wholesaler_areas` (`customer_id`,`user_id`,`isvalid`,`createtime`,`province`,`city`,`district`) VALUES ('{$customer_id}','{$user_id}',true,now(),'{$wholesale_areas_p[$i]}','{$wholesale_areas_c[$i]}','{$wholesale_areas_d[$i]}')";
        // echo $sql;exit;
        $database->query($sql);
    }

    $sql = "UPDATE weixin_commonshop_wholesalers set company_name='{$company_name}',wholesaler_name='{$wholesaler_name}',wholesaler_tel='{$wholesaler_tel}',wholesaler_intro='{$wholesaler_intro}' where customer_id='{$customer_id}' and isvalid=true and user_id='{$user_id}'";
    // $result = $database->query($sql);

    // 同步更新品牌供应商资料
    $sql = "UPDATE weixin_commonshop_brand_supplys set brand_name='{$company_name}',brand_supply_name='{$wholesaler_name}',brand_intro='{$wholesaler_intro}' where customer_id='{$customer_id}' and isvalid=true and user_id='{$user_id}'";
    $result = $database->query($sql);
    // echo $sql;exit;

    $sql="update weixin_commonshop_supply_kefu set is_kefu=".$is_kefu.",kefu_type='".$kefu_type."',supply_qq='".$supply_qq."' where supply_id='".$user_id."' and customer_id=".$customer_id." ";
    // echo $sql;die;
    $result = $database->query($sql);

    return $sql;

}