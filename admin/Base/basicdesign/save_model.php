<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../../weixinpl/back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

if ($_GET['opid'] == '') {
    $op = 'save';
} else {
    $opid = $_GET['opid'];
    $op = 'update';
}

$isvalid = 1;  //有效性
$status = 0;  //广告状态
$name = $_POST['name']; //广告名称
$stop_time = $_POST['stop_time']; //广告图停留时间
if ($_POST['screen'] == 'half') { //广告图模式 0：半屏 1：全屏
    $show_type = 0;
} else {
    $show_type = 1;
}

if ($stop_time =='') {
    $stop_time = 1;
}

if ($_POST['time'] == 2) { //广告开始时间-结束时间
    $timelimit_type = 1;
    $start_time = $_POST['begintime'];
    $end_time   = $_POST['endtime'];
} else {
    $timelimit_type = 0;
    $start_time = '0000-00-00 00:00:00';
    $end_time   = '0000-00-00 00:00:00';
}

$uptypes=array('image/jpg', //上传文件类型列表
                'image/jpeg',
                'image/png',
                'image/pjpeg',
                'image/gif',
                'image/bmp',
                'image/x-png');

$max_file_size=10000000; //上传文件大小限制, 单位BYTE
$ii=count($_POST['page']);
$s=0;
for ($i=0;$i<$ii;$i++) {
    $s++;
    $img_link = $_POST["img_link".$s];

    $href = $_POST['href'][$i];

    $select_value[$i] = '#';
    $detail_value[$i] = '#';

    if ($img_link == 1) {

        $page = $_POST["page"][$i];

        // var_dump($_POST);exit;
        $type = substr($_POST['page'][$i],2);
        if ($_POST["page"][$i] == 2) {
            if (empty($_POST['ctype'][$i])) {
                $type = substr($_POST['type'][$i],2);

                $tcount = 0;    //子分类数量
                $query_type = "SELECT count(1) as tcount FROM weixin_commonshop_types WHERE customer_id=".$customer_id." AND parent_id=".$type." AND is_shelves=1 AND isvalid=true";
                $result_type = _mysql_query($query_type) or die('Query_type failed:'.mysql_error());
                while( $row_type = mysql_fetch_object($result_type) ){
                    $tcount = $row_type->tcount;
                }

                if( $tcount > 0 ){
                    $link_url[]="../../mshop/proclass.php?customer_id=".$customer_id_en."&tid=".$type;
                }else{
                    $link_url[]="../../mshop/list.php?customer_id=".$customer_id_en."&tid=".$type;
                }

                $select_value[$i] = $_POST['type'][$i];
            } else {
                $ctype = $_POST['ctype'][$i];
                $select_value[$i] = $_POST['type'][$i];
                $detail_value[$i] = $ctype;
                $link_url[]="../../mshop/product_detail.php?customer_id=".$customer_id_en."&pid=".$ctype;
            }
        } elseif ($_POST["page"][$i] == 10) {
            $zhibo = $_POST['zhibo'][$i];
            $type = substr($_POST['zhibo'][$i],3);
            $select_value[$i] = $zhibo;

            //微视直播系统
            $link_url[] = "../../../weixin/plat/app/index.php/Mshopzhibo/show_room/customer_id/".$customer_id."/room_id/".$type;
        } elseif ($_POST["page"][$i] == 3) {
            //图文
            $query = "SELECT id,website_url FROM weixin_subscribes where customer_id=".$customer_id." and  id=".$type;
            $result = _mysql_query($query) or die('Query failed: ' . mysql_error());
            while ($row = mysql_fetch_object($result)) {
                $website_url = $row->website_url;
                //客户保障16070 将http转为https,本地不改
                //if(strpos($website_url,'https') ==false){
                //    $website_url = str_replace("http","https",$website_url);
                //}
            }
            $pos = strpos($website_url,"?");
            $pos2 = strpos($website_url,"single_id");
            if( $pos2 > 0 ){    //微官网单页链接
                $website_url = $website_url."&C_id=".$customer_id_en;
            }
            $link_url[] = $website_url;
        } elseif ($_POST["page"][$i] == 4) {
            //城市商圈-美食
            $link_url[] = "../../city_area/cater/shop_detail.php?customer_id=".$customer_id_en."&caterer_id=".$type;
        } elseif ($_POST["page"][$i] == 5) {
            //城市商圈-ktv
            $link_url[] = "../../city_area/ktv/shop_detail.php?customer_id=".$customer_id_en."&supply_id=".$type;
        } elseif ($_POST["page"][$i] == 6) {
            //城市商圈-酒店
            $link_url[] = "../../city_area/hotel/shop.php?customer_id=".$customer_id_en."&shop_id=".$type;
        } elseif ($_POST["page"][$i] == 7) {
            //城市商圈-线下商城
            $link_url[] = "../../city_area/shop/supply_store.php?customer_id=".$customer_id_en."&supply_id=".$type;
        } elseif ($_POST["page"][$i] == 8) {
            //商圈行业列表
            switch($type){
                case 0:
                    $link_url[] = "../../city_area/cater/index.php?customer_id=".$customer_id_en;
                    break;
                case 1:
                    $link_url[] = "../../city_area/ktv/index.php?customer_id=".$customer_id_en;
                    break;
                case 2:
                    $link_url[] = "../../city_area/hotel/index.php?customer_id=".$customer_id_en;
                    break;
                case 3:
                    $link_url[] = "../../city_area/shop/index.php?customer_id=".$customer_id_en;
                    break;
                case 4:
                    $link_url[] = "../../city_area/shop/shop_list.php?customer_id=".$customer_id_en;
                    break;
                case 5:
                    $link_url[] = "../../city_area/finance2/loan/loanList.php?customer_id=".$customer_id_en;
                    break;
                case 6:
                    $link_url[] = "../../city_area/finance2/credit/index.php?customer_id=".$customer_id_en;
                    break;
                case 7:
                    $link_url[] = "../../city_area/finance2/insurance/insurance_list.php?customer_id=".$customer_id_en;
                    break;
            }
        } elseif ($_POST["page"][$i] == 9) {
            //品牌供应商
            $link_url[] = "../../mshop/my_store/my_store.php?customer_id=".$customer_id_en."&supplier_id=".$type;
        } else {
            //商城类链接
            switch($type){
                case 6:
                    $link_url[]="../../mshop/list.php?customer_id=".$customer_id_en;
                    break;
                case 2:
                    $link_url[]="../../mshop/list.php?isnew=1&customer_id=".$customer_id_en;
                    break;
                case 3:
                    $link_url[]="../../mshop/list.php?ishot=1&customer_id=".$customer_id_en;
                    break;
                case 4:
                    $link_url[]="../../mshop/order_cart.php?customer_id=".$customer_id_en;
                    break;
                case 7:
                    $link_url[]="../../mshop/class_page.php?customer_id=".$customer_id_en;
                    break;
                case 8:
                    $link_url[]="../../mshop/personal_center.php?customer_id=".$customer_id_en;
                    break;
                case 9:
                    $link_url[]="../../mshop/my_microshop/my_microshop.php?customer_id=".$customer_id_en;
                    break;
                case 5:
                    $link_url[]="../../mshop/snap_up.php?customer_id=".$customer_id_en;
                    break;
                case 33:
                    $link_url[]="../../mshop/wholesalers_list.php?customer_id=".$customer_id_en;
                    break;
                case 10:
                    $link_url[]="../../online/show_online.php?customer_id=".$customer_id_en;
                    break;
                case 11:
                    $link_url[]="../../mshop/package_list.php?customer_id=".$customer_id_en;
                    break;
                case 12:
                    $link_url[]="../../mshop/list.php?isvp=1&customer_id=".$customer_id_en;
                    break;
                case 15:
                    $link_url[]="../../mshop/list.php?isscore=1&customer_id=".$customer_id_en;
                    break;
                case 16:
                    $link_url[]="index.php?customer_id=".$customer_id_en;
                    break;
                case 17:
                    $link_url[]="../../mshop/proclass.php?customer_id=".$customer_id_en;
                    break;
                case 18:
                    $link_url[]="../../mshop/orderlist.php?customer_id=".$customer_id_en;
                    break;
                case 19:
                    $link_url[]="/market/web/collageActivities/product_list_view.php?customer_id=".$customer_id_en."&op=ordinary";
                    break;
                case 20:
                    $link_url[]="/market/web/collageActivities/product_list_view.php?customer_id=".$customer_id_en."&op=popularity";
                    break;
                case 21:
                    $link_url[]="/market/web/promoter_renew/index.php?customer_id=".$customer_id_en;
                    break;
                case 22:
                    $link_url[]="/addons/index.php/micro_broadcast/user/index?customer_id=".$customer_id_en;
                    break;
                case 23:
                    $link_url[]="/addons/index.php/voice_online/Index/index?customer_id=".$customer_id_en;
                    break;
                case 24:
                    $link_url[]="/weixinpl/ticke_check.php?type=flight";
                    break;
                case 25:
                    $link_url[]="/weixinpl/ticke_check.php?type=train";
                    break;
                case 26:
                    $link_url[]="/addons/index.php/f2c/index/personal_center?customer_id=".$customer_id_en;
                    break;
                case 27:
                    $link_url[]="/addons/index.php/ordering_retail/Proxy/proxy_login?customer_id=".$customer_id_en;
                    break;
                case 28:
                    $link_url[]="/addons/index.php/ordering_retail/Proxy/proxy_apply?customer_id=".$customer_id_en;
                    break;
                case 29:
                    $link_url[]="/addons/index.php/ordering_retail/Proxy/personal_center.html?customer_id=".$customer_id_en;
                    break;
                default:
                    $link_url[]="javascript:";
                    break;
            }
        }
        $page = $img_link==2?0:$page;
        $link_type[] = $page;
    } else {
        $page = $img_link==2?0:$page;
        $link_type[] = 0;
        $link_url[] = $href;
    }

    if ($_POST['ctype'][$i] == '---请选择---') {
        $detail_value[$i] = '#';
    }

    $destination_folder = './upload/'; //上传文件路径

    if ($_FILES['file']['name'][0] == '') {
        $_FILES['file']['name'][0] = $_FILES['file_0']['name'];
        $_FILES['file']['type'][0] = $_FILES['file_0']['type'];
        $_FILES['file']['tmp_name'][0] = $_FILES['file_0']['tmp_name'];
        $_FILES['file']['error'][0] = $_FILES['file_0']['error'];
        $_FILES['file']['size'][0] = $_FILES['file_0']['size'];
    }

    if ($_FILES['file']['tmp_name'][$i] == '') {
        $imgurl[$i] = $_POST['img_src'][$i];
        continue;
    }

    $rand1=rand(0,9);
    $rand2=rand(0,9);
    $rand3=rand(0,9);
    $savefile=$savedir.$filename;

    $destination = "";
    if (is_uploaded_file($_FILES['file']['tmp_name'][$i])){//是否存在文件

        $file = $_FILES['file']['tmp_name'];
        if($max_file_size < $_FILES['file']['size'][$i]){//检查文件大小
            echo "<font color='red'>文件太大！</font>";
            exit;
        }

        if(!in_array($_FILES['file']['type'][$i], $uptypes)){//检查文件类型
            echo "<font color='red'>不能上传此类型文件！</font>";
            exit;
        }

        if(!file_exists($destination_folder)){
            mkdir($destination_folder,0777,true);
        }

        $filename=$_FILES['file']['tmp_name'][$i];

        $filenames=date("Ymdhis").$rand1.$rand2.$rand3;

        $destination=$destination_folder.$filenames;

        $overwrite=true;

        if (file_exists($destination) && $overwrite != true){
            echo "<font color='red'>同名文件已经存在了！</a>";
            exit;
        }

        $info = pathinfo($_FILES['file']['name'][$i]);
        $destination = $destination.'.'.$info['extension'];

        if(!_move_uploaded_file ($filename, $destination)){
            echo "<font color='red'>移动文件出错！</a>";
            exit;
        }

        $destination = str_replace("./","",$destination);
        $destination = "/weixinpl/back_newshops/Base/basicdesign/".$destination;
        $imgurl[$i] = $destination;
    }
}

$imgurl = implode('|',$imgurl);
$link_type = implode('|',$link_type);
$select_value = implode('|',$select_value);
$detail_value = implode('|',$detail_value);
$link_url = implode('|',$link_url);

if($op=="save"){
    //插入一个新广告
    $add_ads="insert into weixin_commonshop_ads (customer_id,isvalid,status,name,show_type,timelimit_type,start_time,end_time,createtime) values ('".$customer_id."','".$isvalid."','".$status."','".$name."','".$show_type."','".$timelimit_type."','".$start_time."','".$end_time."',now())";
    $result_add_ads=_mysql_query($add_ads) or die ('add_ads failed' .mysql_error());

    $id = mysql_insert_id();

    $ad_imgs="insert into weixin_commonshop_ad_imgs (customer_id,isvalid,ad_id,show_time,imgurl,link,link_type,select_value,detail_value,createtime) values ('".$customer_id."','".$isvalid."','".$id."','".$stop_time."','".$imgurl."','".$link_url."','".$link_type."','".$select_value."','".$detail_value."',now())";
    $result_ad_imgs=_mysql_query($ad_imgs) or die ('ad_imgs failed' .mysql_error());
    $url = $prorocol_http_host.'/weixinpl/back_newshops/Base/basicdesign/limit_ad.php?customer_id='.$customer_id_en;
    echo "<script language=\"javascript\">window.alert('添加成功');window.location.href='".$url."';</script>";
    // header("Location:".$url);
}

if($op=="update"){
    //更新一个新广告
    $update_ads="update weixin_commonshop_ads set
                name='".$name."',
                show_type='".$show_type."',
                timelimit_type='".$timelimit_type."',
                start_time='".$start_time."',
                end_time='".$end_time."'
                where id=".$opid." and customer_id=".$customer_id;
    $result_update_ads=_mysql_query($update_ads) or die ('update_ads failed' .mysql_error());

    $update_imgs="update weixin_commonshop_ad_imgs set
                show_time='".$stop_time."',
                imgurl='".$imgurl."',
                link='".$link_url."',
                link_type='".$link_type."',
                select_value='".$select_value."',
                detail_value='".$detail_value."'
                where ad_id=".$opid." and customer_id=".$customer_id;
    $result_update_imgs=_mysql_query($update_imgs) or die ('update_imgs failed' .mysql_error());
    echo "<script language=\"javascript\">window.alert('修改成功');window.location.href='".$_SERVER['HTTP_REFERER']."';</script>";
    // header("Location:".$_SERVER['HTTP_REFERER']);
}

?>