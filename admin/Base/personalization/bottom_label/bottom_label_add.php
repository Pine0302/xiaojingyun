<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../../weixinpl/config.php');
require('../../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../../../weixinpl/proxy_info.php');
require('../../../../../weixinpl/common/common_ext.php');

_mysql_query("SET NAMES UTF8");

//数组去重
$selected_funs_arr = array_values(array_unique($_POST['links2']));

if(!empty($selected_funs_arr)){
    //先判断是否满足发布个数
    $rcount_num=0;
    $check_num_sql = "select count(1) as rcount_num from bottom_label_setting_t where isvalid=true and customer_id=".$customer_id." and display=1";
    $check_num_label = _mysql_query($check_num_sql) or die('check_num_sql failed_num: ' . mysql_error());
    while ($check_num_row = mysql_fetch_object($check_num_label)) {
        $rcount_num = (int)$check_num_row->rcount_num;
    }

    if(($rcount_num >0 && $rcount_num<2) || $rcount_num>=6){
        echo "<script>alert('底部菜单的显示个数为2-5个，请重新设置再发布！');location.href='./index.php?customer_id=".$customer_id_en."';</script>";
        die();
    }

    $label_set_arr   = array(); // 定义一个标签发布前的id数组(可显示)
    $label_using_arr = array(); // 定义一个标签使用中的id数组
    /*先判断是否有设置底部标签*/
    $check_label_sql = "select id,name,icon_url,icon_url_selected,page_url,column_id,`sort`,display from bottom_label_setting_t where isvalid=true and customer_id=".$customer_id;
    $result_label = _mysql_query($check_label_sql) or die('check_label_sql failed_num: ' . mysql_error());
    while ($row = mysql_fetch_object($result_label)) {
        $label_id    =(int)$row->id;
        $name        =$row->name;
        $icon_url    =$row->icon_url;
        $icon_url_selected =$row->icon_url_selected;
        $page_url    =$row->page_url;
        $column_id   =(int)$row->column_id;
        $sort        =$row->sort;
        $display     =$row->display;

        if($display == 1){
            array_push($label_set_arr,$label_id);
        }
        /*查找使用中的底部标签是否存在，存在则更新，不存在则新增*/
        $using_id = -1;
        $using_sql = "select id from bottom_label_using where customer_id=".$customer_id." and label_id=".$label_id." limit 1";
        $using_result = _mysql_query($using_sql) or die('using_sql failed: ' .  mysql_error());

        while ($using_row = mysql_fetch_object($using_result)) {
            $using_id =$using_row->id;
        }

        $col_funs = "";

        if($column_id >0){
            $col_sql = "select funs from page_column_t where isvalid=true and type=2 and id=".$column_id;
            $col_result = _mysql_query($col_sql) or die('col_sql failed: ' .  mysql_error());

            while ($col_row = mysql_fetch_object($col_result)) {
                $col_funs =$col_row->funs;
            }
        }

        if($using_id > 0){
            $update_using_sql = "update bottom_label_using set name='".$name."',icon_url='".$icon_url."',icon_url_selected='".$icon_url_selected."',page_url='".$page_url."',column_id=".$column_id.",sort=".$sort.",funs='".$col_funs."',createtime=now()";
            if($display ==1){//标签隐藏时,使用表置0
                $update_using_sql .= ",isvalid=true";
            }else{
                $update_using_sql .= ",isvalid=false";
            }
            $update_using_sql .= " where id=".$using_id;

            _mysql_query($update_using_sql) or die('update_using_sql failed: ' .  mysql_error());
        }else{
            if($display == 1){  //显示时才插入
                $insert_using_sql = "insert into bottom_label_using(customer_id,name,icon_url,icon_url_selected,page_url,column_id,sort,label_id,funs,isvalid,createtime) values(".$customer_id.",'".$name."','".$icon_url."','".$icon_url_selected."','".$page_url."',".$column_id.",".$sort.",".$label_id.",'".$col_funs."',true,now())";
                _mysql_query($insert_using_sql) or die('insert_using_sql failed: ' .  mysql_error());
            }
        }
    }
    /*查找正在使用的底部标签*/
    $check_using_sql = "select id,label_id as using_label_id from bottom_label_using where customer_id=".$customer_id." and isvalid=true";
    $ch_using_result = _mysql_query($check_using_sql) or die('check_using_sql failed: ' .  mysql_error());

    while ($ch_using_row = mysql_fetch_object($ch_using_result)) {
        $using_label_id  = (int)$ch_using_row->using_label_id;

        array_push($label_using_arr,$using_label_id);
    }

    $diff_arr = array_diff($label_using_arr,$label_set_arr);

    if(!empty($diff_arr)){
        for($i=0;$i<count($label_using_arr);$i++){
            if($diff_arr[$i]){
                $deal_sql = "update bottom_label_using set isvalid=false where label_id=".$diff_arr[$i]." and customer_id=".$customer_id;
                _mysql_query($deal_sql) or die('deal_sql failed: ' .  $deal_sql);
            }
        }
    }

    /*先把所有发布页面isvalid置0 start*/
    $set_sql = "update publish_page_t set isvalid=false where type=2 and customer_id=".$customer_id;
    _mysql_query($set_sql) or die('set_sql failed: ' .  mysql_error());
    /*先把所有发布页面isvalid置0 end*/

    $insert_sql  = "insert into publish_page_t(customer_id,page_id,isvalid,type,funs,createtime) values";
    $insert_sql2 = "";

    for($i=0;$i<count($selected_funs_arr);$i++){
        /*查找发布页面是否存在，存在则更新，不存在则新增*/
        $publish_id = -1;
        $check_sql = "select id from publish_page_t where customer_id=".$customer_id." and type=2 and funs='{$selected_funs_arr[$i]}' limit 1";
        $check_result = _mysql_query($check_sql) or die('check_sql failed: ' .  mysql_error());

        while ($check_row = mysql_fetch_object($check_result)) {
            $publish_id =  $check_row->id ;
        }

        if($publish_id >0){
            $update_sql = "update publish_page_t set isvalid = true  where customer_id=".$customer_id." and type=2 and funs='{$selected_funs_arr[$i]}'";
            _mysql_query($update_sql) or die('update_sql failed: ' .  mysql_error());
        }else{
            $insert_sql2 .= "(".$customer_id.",".(int)$col_id.",true,2,'".$selected_funs_arr[$i]."',now()),";
        }
    }

    if(!empty($insert_sql2)){
        $insert_sql .= $insert_sql2;
        $insert_sql = rtrim($insert_sql,",");

        _mysql_query($insert_sql) or die('insert_sql failed: ' .  mysql_error());
    }


}else{
    $update2_sql = "update publish_page_t set isvalid = false  where customer_id=".$customer_id." and type=2 ";
    _mysql_query($update2_sql) or die('update2_sql failed: ' .  mysql_error());
}
mysql_close($link);
echo "<script>alert('发布成功!');self.location.href='./index.php?customer_id=".$customer_id_en."';</script>";
?>
