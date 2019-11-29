<?php
header("Content-type: text/html; charset=utf-8");
require_once('../../../../../weixinpl/config.php'); //配置
require_once('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require_once('../../../../../weixinpl/back_init.php');//验证customer_id,customer_id解密写在前
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
_mysql_query("SET NAMES UTF8");

require_once('../../../../../weixinpl/proxy_info.php'); //解密加密
require_once('../../../../../weixinpl/common/common_ext.php');

//数组去重
$selected_funs_arr = array_values(array_unique($_POST['links2']));
if(!empty($selected_funs_arr)){
    $navigation_set_arr   = array(); // 定义一个标签发布前的id数组(可显示)
    $navigation_using_arr = array(); // 定义一个标签使用中的id数组
    /*先判断是否有设置底部标签*/
    $rcount_num = 0;
    $check_navigation_sql = "select id,name,icon_url,page_url,column_id,sort,display from navigation_setting_t where isvalid=true and customer_id=".$customer_id;
    $result_navigation = _mysql_query($check_navigation_sql) or die('check_navigation_sql failed_num: ' . mysql_error());
    while ($row = mysql_fetch_object($result_navigation)) {
        $navigation_id    =(int)$row->id;
        $name        =$row->name;
        $icon_url    =$row->icon_url;
        $page_url    =$row->page_url;
        $column_id   =(int)$row->column_id;
        $sort        =$row->sort;
        $display     =$row->display;

        if($display == 1){
            array_push($navigation_set_arr,$navigation_id);
        }

        $column_funs = "";
        if ($column_id > 0){
            $column_sql = "select funs from page_column_t where isvalid=true and id=".$column_id." limit 1";
            $column_result = _mysql_query($column_sql) or die('using_sql failed: ' .  mysql_error());
            while ($column_row = mysql_fetch_object($column_result)) {
                $column_funs =$column_row->funs;
            }
        }

        /*查找使用中的导航是否存在，存在则更新，不存在则新增*/
        $using_id = -1;
        $using_sql = "select id from navigation_using where customer_id=".$customer_id." and navigation_id=".$navigation_id." limit 1";
        $using_result = _mysql_query($using_sql) or die('using_sql failed: ' .  mysql_error());

        while ($using_row = mysql_fetch_object($using_result)) {
            $using_id =$using_row->id;
        }

        if($using_id > 0){
            $update_using_sql = "update navigation_using set name='".$name."',icon_url='".$icon_url."',page_url='".$page_url."',column_id=".$column_id.",sort=".$sort.",createtime=now(),funs='".$column_funs."'";
            if($display ==1){//标签隐藏时,使用表置0
                $update_using_sql .= ",isvalid=true";
            }else{
                $update_using_sql .= ",isvalid=false";
            }
            $update_using_sql .= " where id=".$using_id;

            _mysql_query($update_using_sql) or die('update_using_sql failed: ' .  mysql_error());
        }else{
            if($display == 1){  //显示时才插入
                $insert_using_sql = "insert into navigation_using(customer_id,name,icon_url,page_url,column_id,sort,navigation_id,isvalid,createtime,funs) values(".$customer_id.",'".$name."','".$icon_url."','".$page_url."',".$column_id.",".$sort.",".$navigation_id.",true,now(),'".$column_funs."')";
                _mysql_query($insert_using_sql) or die('insert_using_sql failed: ' .  mysql_error());
            }
        }
    }
    /*查找正在使用的底部标签*/
    $check_using_sql = "select id,navigation_id as using_navigation_id from navigation_using where customer_id=".$customer_id." and isvalid=true";
    $ch_using_result = _mysql_query($check_using_sql) or die('check_using_sql failed: ' .  mysql_error());

    while ($ch_using_row = mysql_fetch_object($ch_using_result)) {
        $using_navigation_id  = (int)$ch_using_row->using_navigation_id;

        array_push($navigation_using_arr,$using_navigation_id);
    }

    $diff_arr = array_diff($navigation_using_arr,$navigation_set_arr);

    if(!empty($diff_arr)){
        for($i=0;$i<count($navigation_using_arr);$i++){
            if($diff_arr[$i]){
                $deal_sql = "update navigation_using set isvalid=false where navigation_id=".$diff_arr[$i]." and customer_id=".$customer_id;
                _mysql_query($deal_sql) or die('deal_sql failed: ' .  $deal_sql);
            }
        }
    }
    // $rcount_num = mysql_num_rows($result_navigation);

    // if($rcount_num <=0){
    // 	echo "<script>alert('没有可显示的底部标签，请先设置再发布！');parent.location.href='setting.php?customer_id=".$customer_id_en."';</script>";
    // 	return;
    // }

    /*先把所有发布页面isvalid置0 start*/
    $set_sql = "update publish_page_t set isvalid=false where type=1 and customer_id=".$customer_id;
    _mysql_query($set_sql) or die('set_sql failed: ' .  mysql_error());
    /*先把所有发布页面isvalid置0 end*/

    $insert_sql  = "insert into publish_page_t(customer_id,page_id,isvalid,type,funs,createtime) values";
    $insert_sql2 = "";
    for($i=0;$i<count($selected_funs_arr);$i++){
        /*查找对应的栏目标志*/
        $sql = "select id from page_column_t where isvalid=true and type=1 and funs='{$selected_funs_arr[$i]}'";
        $result = _mysql_query($sql) or die('sql failed: ' . mysql_error());
        while ($row = mysql_fetch_object($result)) {
            $col_id =  $row->id ;
        }

        /*查找发布页面是否存在，存在则更新，不存在则新增*/
        $publish_id = -1;
        $check_sql = "select id from publish_page_t where customer_id=".$customer_id." and type=1 and funs='{$selected_funs_arr[$i]}' limit 1";
        $check_result = _mysql_query($check_sql) or die('check_sql failed: ' .  mysql_error());

        while ($check_row = mysql_fetch_object($check_result)) {
            $publish_id =  $check_row->id ;
        }

        if($publish_id >0){
            $update_sql = "update publish_page_t set isvalid = true  where customer_id=".$customer_id." and type=1 and funs='{$selected_funs_arr[$i]}'";

            _mysql_query($update_sql) or die('update_sql failed: ' .  mysql_error());
        }else{
            $insert_sql2 .= "(".$customer_id.",".(int)$col_id.",true,1,'".$selected_funs_arr[$i]."',now()),";
        }
    }

    if(!empty($insert_sql2)){
        $insert_sql .= $insert_sql2;
        $insert_sql = rtrim($insert_sql,",");

        _mysql_query($insert_sql) or die('insert_sql failed: ' .  mysql_error());
    }


}else{
    $update2_sql = "update publish_page_t set isvalid = false  where customer_id=".$customer_id." and type=1 ";
    _mysql_query($update2_sql) or die('update2_sql failed: ' .  mysql_error());
}
mysql_close($link);
echo "<script>alert('发布成功');self.location.href='setting.php?customer_id=".$customer_id_en."';</script>";
?>
