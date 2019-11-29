<?php
header("Content-type: text/html; charset=utf-8");
require('../../../../weixinpl/config.php');
require('../../../../weixinpl/customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$cat_id = $configutil->splash_new($_GET["type_id"]);
$callback = $configutil->splash_new($_GET["callback"]);
/* 判断已选分类是否下架和有效 */
$chk_id = -1;
$query_chk = "select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and id=".$cat_id." and customer_id=".$customer_id;
$result_chk = _mysql_query($query_chk) or die('Query_chk failed:'.mysql_error());
while( $row_chk = mysql_fetch_object($result_chk) ){
    $chk_id = $row_chk -> id;
}
if( $chk_id < 0 ){
    //非有效分类则查一个一级分类
    $query_type_find = "select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=-1 and customer_id=".$customer_id." limit 1";
    $result_type_find = _mysql_query($query_type_find) or die('Query_type_find failed:'.mysql_error());
    while( $row_type_find = mysql_fetch_object($result_type_find) ){
        $cat_id = $row_type_find -> id;
    }
}
/* 判断已选分类是否下架和有效 */
$query="select id,name from weixin_commonshop_products where isvalid=true and isout=false and customer_id=".$customer_id;
$typeson_id=array();
if($cat_id >0){
    /* 查找该分类的所有子分类 start */
    $query_child = "select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$cat_id." and customer_id=".$customer_id;
    $result_child = _mysql_query($query_child) or die('Query_child failed:'.mysql_error());
    while( $row_child = mysql_fetch_object($result_child) ){
        $child_id = $row_child -> id;

        $typeson_id[] = $child_id;

        $query_child2 = "select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$child_id." and customer_id=".$customer_id;
        $result_child2 = _mysql_query($query_child2) or die('Query_child2 failed:'.mysql_error());
        while( $row_child2 = mysql_fetch_object($result_child2) ){
            $child_id2 = $row_child2 -> id;

            $typeson_id[] = $child_id2;

            $query_child3 = "select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$child_id2." and customer_id=".$customer_id;
            $result_child3 = _mysql_query($query_child3) or die('Query_child3 failed:'.mysql_error());
            while( $row_child3 = mysql_fetch_object($result_child3) ){
                $child_id3 = $row_child3 -> id;

                $typeson_id[] = $child_id3;
            }
        }
    }
    /* 查找该分类的所有子分类 end */

    if(empty($typeson_id)){
        $typeson_id=$cat_id;
    }else{
        array_push($typeson_id,$cat_id);
        $typeson_id=implode(',',$typeson_id);
    }
        $query = $query." and (";
        $typeson_id_arr = explode(",",$typeson_id);
        $typeson_id_count = count($typeson_id_arr);
        for($j=0;$j<$typeson_id_count;$j++){
            $o_typeid = $typeson_id_arr[$j];
            if($j==0){
                $query = $query."( LOCATE(',".$o_typeid.",', type_ids)>0)";
                }else{
                $query = $query." or (LOCATE(',".$o_typeid.",', type_ids)>0)";
            }
        }
        $query = $query.")";
        unset($typeson_id);

}
// echo $query;
$str="{pos:".$pos."},{pid:".$pid."},{sort:".$sort."},{detail_id:".$cat_id."}";
$result = _mysql_query($query) or die('Query failed: ' . mysql_error());
$i=0;
while ($row = mysql_fetch_object($result)) {

    $pid = $row->id;
    $pname = mysql_real_escape_string($row->name);
    //$str = $str.",{pid:".$pid.",pname:'".$pname."'}";
    // $pname = str_replace("'"," ",$pname);
    $str = $str.",{pid:".$pid.",pname:'".$pname."'}";
    $res[$i]['pid'] = $pid;
    $res[$i]['pname'] = $pname;
    $i++;
}



 //echo $error;
 // mysql_close($link);
 echo json_encode($res);


?>