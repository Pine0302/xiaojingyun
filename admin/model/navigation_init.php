<?php

class model_navigation_init{
    public $db;

    function __construct()
    {
        $this->db = DB::getInstance();
    }

    /*
     * 查询所有商家customer
     * @param  int    customer_id   商家编号id
     */
     public function select_all_customer(){
        $sql = "select customer_id from weixin_shops where isvalid=true";
        $result = $this->db->getAll($sql);
        return $result;
     }

    /*
     * 导航模板初始化
     * @param  int    customer_id   商家编号id
     */
    public function navigaton_initialize($customer_id){

        //判断是否已经初始化了商城初始模板1
        $select_template1_sql = "select id from ".WSY_SHOP.".navigation_template_setting where isvalid=true and customer_id='".$customer_id."'";
        $select_template1 = $this->db->getOne($select_template1_sql);
        if ($select_template1 > 0) {
            echo "已初始化过脚本！";
            return;
        }
        echo "初始化模板脚本开始运行....(请勿刷新及关闭脚本)</br>";
        //查询是否有导航
        $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
        mysql_select_db(DB_NAME) or die('Could not select database');
        $nav_sql = "select count(id) from ".WSY_SHOP.".navigation_setting_t where isvalid=true and customer_id='".$customer_id."'";
        // echo $nav_sql;
        $nav_res = _mysql_query($nav_sql) or die('nav' . mysql_error());
        $nav = mysql_fetch_array($nav_res);
        if ($nav['0']==0) {
            echo "初始化脚本结束（无导航）";
            return;
        }

        //查询出模板下一个tmp_id号
        $sql_id = "select max(id) from ".WSY_SHOP.".navigation_template_setting";
        $id_sql = _mysql_query($sql_id) or die('111111' . mysql_error());
        $id_res = mysql_fetch_array($id_sql);
        // echo $sql_id;
        // echo $id_res['0'];
        if ($id_res['0']=="") {
            $id = '1';
        }else{
            $id = $id_res['0']+1;
        }

        //查找出旧数据风格
        $style_sql = "select type from ".WSY_SHOP.".navigation_style_setting_t where customer_id='".$customer_id."' and isvalid=true";
        // echo $style_sql;
        $old_style_res = _mysql_query($style_sql);
        $old_style1 = mysql_fetch_array($old_style_res);
        if ($old_style1['0']=="") {
            $old_style ='1';
        }else{
            $old_style = $old_style1['0'];
        }

        //查询出状态为显示的导航按钮设置数据插入新表navigation_icon_setting
        $type_show_sql = "insert into ".WSY_SHOP.".navigation_icon_setting(tmp_id,customer_id,icon_url,page_url,column_id,sort,display,isvalid,createtime,selector_id) select '".$id."' as tmp_id,customer_id,icon_url,page_url,column_id,(@rowNum:=@rowNum+1) as rowNo,display,isvalid,now() as createtime,selector_id from ".WSY_SHOP.".navigation_setting_t,(Select (@rowNum :=0) ) b  where customer_id='".$customer_id."' and isvalid=true and display='1' order by sort DESC";
        //判断风格是否为2和5的，如果风格是2和5的只保留12条数据
        if($old_style == 2 || $old_style == 5){
            $type_show_sql.=" limit 12";
        }else{
            $type_show_sql.=" limit 15";
        }

        $type_show = $this->db->query($type_show_sql) or die('数据更新失败，错误代码40014' . mysql_error());
        echo "---导航按钮设置数据初始化更新";

        //创建商城初始模板1
        $template1_data = array(
                "id"          => $id,
                "customer_id" => $customer_id,
                "name"        => "商城初始模板1",
                "is_shelve"   => 1,
                "isvalid"     => '1',
                "createtime"  => date('Y-m-d H:i:s'),
                "position"    => '1',
                "style"       => $old_style,
            );
        $template1 = $this->db->autoExecute(WSY_SHOP.'.navigation_template_setting',$template1_data, 'insert') or die('创建初始模板1失败，错误代码40034' . mysql_error());
        echo "---创建商城初始模板1</br>";

        //商城初始模板1初始化发布页面
        $funs_sql = "insert into ".WSY_SHOP.".publish_page_management(tmp_id,customer_id,isvalid,page_id,type,funs,createtime) select '".$id."' as tmp_id,customer_id,1 as isvalid,page_id,type,funs,now() as createtime from ".WSY_SHOP.".publish_page_t where customer_id='".$customer_id."' and isvalid=true and type='1' order by id";
        $funs_data = $this->db->query($funs_sql) or die('初始化模板1发布页面失败，错误代码40044' . mysql_error());

        //状态为隐藏的导航按钮，按十五个归类为一个模板 
        $type_hide_sql = "select count(*) from ".WSY_SHOP.".navigation_setting_t where isvalid=true and display='0' and customer_id='".$customer_id."'" ;
        $count_data = $this->db->getOne($type_hide_sql);
        if ($count_data == "") {
            echo "</br>初始化模板脚本运行结束！";
            return;
        }
        if($old_style == 2 || $old_style == 5){
            $num = ceil($count_data/12);
        }else{
            $num = ceil($count_data/15);
        }
        for ($i=1; $i <= $num; $i++) { 
            //更新数据到navigation_icon_setting
            $tmp_num = $id+$i;
            $type_hide_sql = "insert into ".WSY_SHOP.".navigation_icon_setting(tmp_id,customer_id,icon_url,page_url,column_id,sort,display,isvalid,createtime,selector_id) select '".$tmp_num."' as tmp_id,customer_id,icon_url,page_url,column_id,sort,display,isvalid,now() as createtime,selector_id from ".WSY_SHOP.".navigation_setting_t where customer_id='".$customer_id."' and isvalid=true and display='0' order by sort DESC";
            //计算出要查询的数据 
            if($old_style == 2 || $old_style == 5){
                $a = ($i-1)*12;
                $type_hide_sql .= " limit ".$a.",12";
            }else{
                $a = ($i-1)*15;
                $type_hide_sql .= " limit ".$a.",15";
            }
            $type_hide = $this->db->query($type_hide_sql) or die('数据更新失败，错误代码40004' . mysql_error());

            //创建模板商城初始模板2.3.4....
            $template2_data = array(
                    "id"          => $tmp_num,
                    "customer_id" => $customer_id,
                    "name"        => "商城初始模板".$tmp_num,
                    "isvalid"     => '1',
                    "createtime"  => date('Y-m-d H:i:s'),
                    "position"    => '1',
                    "style"       => $old_style,
                );
            $template2 = $this->db->autoExecute(WSY_SHOP.'.navigation_template_setting',$template2_data, 'insert') or die('创建初始模板（其他）失败，错误代码40064' . mysql_error());
            echo "---创建商城初始模板".$tmp_num."</br>";
        }
        echo "</br>初始化模板脚本运行结束！";

    }

    //底部菜单模板初始化
    public function bottom_initialize($customer_id){
        //判断底部菜单模板是否已初始化
        $select_bottom_sql = "select id from ".WSY_SHOP.".bottom_label_template_setting where isvalid=true and customer_id='".$customer_id."'";
        // echo $select_bottom_sql;
        $select_bottom = $this->db->getOne($select_bottom_sql);
        if ($select_bottom > 0) {
            echo "</br>已初始化过脚本！";
            return;
        }

        //查询是否有底部菜单
        $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
        mysql_select_db(DB_NAME) or die('Could not select database');
        $nav_sql = "select count(id) from ".WSY_SHOP.".bottom_label_setting_t where isvalid=true and customer_id='".$customer_id."'";
        $nav_res = _mysql_query($nav_sql) or die('nav' . mysql_error());
        $nav = mysql_fetch_array($nav_res);
        if ($nav['0']==0) {
            echo "初始化脚本结束（无底部菜单）";
            return;
        }
        echo "初始化底部菜单模板脚本开始运行....(请勿刷新及关闭脚本)</br>";

        //查询出模板下一个tmp_id号
        $sql_id = "select max(id) from ".WSY_SHOP.".bottom_label_template_setting";
        $id_sql = _mysql_query($sql_id) or die('111111' . mysql_error());
        $id_res = mysql_fetch_array($id_sql);
        if ($id_res['0']=="") {
            $id = '1';
        }else{
            $id = $id_res['0']+1;
        }
        // var_dump($id_res);
        //查询出状态为显示的底部菜单按钮设置数据插入新表bottom_label_icon_setting
        $type_show_sql = "insert into ".WSY_SHOP.".bottom_label_icon_setting(name,tmp_id,customer_id,icon_url,icon_url_selected,page_url,column_id,sort,display,isvalid,createtime,selector_id) select name,'".$id."' as tmp_id,customer_id,icon_url,icon_url_selected,page_url,column_id,(@rowNum:=@rowNum+1) as rowNo,display,isvalid,now() as createtime,selector_id from ".WSY_SHOP.".bottom_label_setting_t,(Select (@rowNum :=0) ) b  where customer_id='".$customer_id."' and isvalid=true and display='1' order by sort DESC limit 5";
        $type_show = $this->db->query($type_show_sql) or die('数据更新失败，错误代码40014' . mysql_error());
        echo "---底部菜单按钮设置数据初始化更新";

        //创建商城初始模板1
        $template1_data = array(
                "id"          => $id,
                "customer_id" => $customer_id,
                "name"        => "商城初始模板1",
                "is_shelve"   => 1,
                "isvalid"     => '1',
                "createtime"  => date('Y-m-d H:i:s'),
                "position"    => '1',
            );
        $template1 = $this->db->autoExecute(WSY_SHOP.'.bottom_label_template_setting',$template1_data, 'insert') or die('创建初始模板1失败，错误代码40034' . mysql_error());
        echo "---创建商城初始模板1</br>";

        //商城初始模板1初始化发布页面
        $funs_sql = "insert into ".WSY_SHOP.".publish_page_management(tmp_id,customer_id,isvalid,page_id,type,funs,createtime) select '".$id."' as tmp_id,customer_id,1 as isvalid,page_id,type,funs,now() as createtime from ".WSY_SHOP.".publish_page_t where customer_id='".$customer_id."' and isvalid=true and type='2' order by id";
        $funs_data = $this->db->query($funs_sql) or die('初始化模板1发布页面失败，错误代码40044' . mysql_error());

        //状态为隐藏的底部菜单按钮，按五个归类为一个模板 
        $type_hide_sql = "select count(*) from ".WSY_SHOP.".bottom_label_setting_t where isvalid=true and display='0' and customer_id='".$customer_id."'" ;
        $count_data = $this->db->getOne($type_hide_sql);
        if ($count_data == "") {
            echo "</br>初始化模板脚本运行结束！";
            return;
        }
        $num = ceil($count_data/5);
        for ($i=1; $i <= $num; $i++) { 
            //更新数据到navigation_icon_setting
            $tmp_num = $id+$i;
            $type_hide_sql = "insert into ".WSY_SHOP.".bottom_label_icon_setting(name,tmp_id,customer_id,icon_url,icon_url_selected,page_url,column_id,sort,display,isvalid,createtime,selector_id) select name,'".$tmp_num."' as tmp_id,customer_id,icon_url,icon_url_selected,page_url,column_id,sort,display,isvalid,now() as createtime,selector_id from ".WSY_SHOP.".bottom_label_setting_t where customer_id='".$customer_id."' and isvalid=true and display='0' order by sort DESC";
            //计算出要查询的数据 
            $a = ($i-1)*5;
            $type_hide_sql .= " limit ".$a.",5";
            $type_hide = $this->db->query($type_hide_sql) or die('数据更新失败，错误代码40004' . mysql_error());

            //创建模板商城初始模板2.3.4....
            $template2_data = array(
                    "id"          => $tmp_num,
                    "customer_id" => $customer_id,
                    "name"        => "商城初始模板".$tmp_num,
                    "isvalid"     => '1',
                    "createtime"  => date('Y-m-d H:i:s'),
                    "position"    => '1',
                );
            $template2 = $this->db->autoExecute(WSY_SHOP.'.bottom_label_template_setting',$template2_data, 'insert') or die('创建初始模板（其他）失败，错误代码40064' . mysql_error());
            echo "---创建商城初始模板".$tmp_num."</br>";
        }
        echo "</br>初始化底部菜单模板脚本运行结束！";

    }

    //自定义模板初始化
    public function custom_initialize($customer_id){

        $link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
        mysql_select_db(DB_NAME) or die('Could not select database');
        echo "初始化自定义底部菜单模板脚本开始运行....(请勿刷新及关闭脚本)</br>";
        $createtime = date('Y-m-d H:i:s');

        $page_sql ="select count(id) from ".WSY_SHOP.".publish_page_management where funs like 'weixin_commonshop_diy_template%' and customer_id='".$customer_id."' and isvalid=true";
        $page_res =  _mysql_query($page_sql) or die('page_sql' . mysql_error());
        $page = mysql_fetch_array($page_res);
        if ($page[0]!=0) {
            echo "已初始化过数据！";
            return;
        }

        //查询出有多少自定义模板
        $num_sql = "select count(id) from ".WSY_SHOP.".weixin_commonshop_diy_template where customer_id='".$customer_id."' and content!=-1 and isvalid=true";
        // echo $num_sql;
        $num_res = _mysql_query($num_sql) or die('2222222' . mysql_error());
        $num = mysql_fetch_array($num_res);
        if($num[0]==0){
            echo "初始化脚本结束（无自定义底部菜单）";
            return;
        }

        //查询出模板下一个tmp_id号
        $sql_id = "select max(id) from ".WSY_SHOP.".bottom_label_template_setting";
        $id_res = _mysql_query($sql_id) or die('111111' . mysql_error());
        $id_res2 = mysql_fetch_array($id_res);
        if ($id_res2['0']=="") {
            $id='1';
        }else{
            $id=$id_res2['0']+1;
        }
        // var_dump($id_res2);

        //将自定义模板插入新表
        $sql_template = "insert into ".WSY_SHOP.".bottom_label_template_setting(id,customer_id,name,is_shelve,isvalid,createtime,position) values";
        $sql_bottom = "insert into ".WSY_SHOP.".bottom_label_icon_setting(name,tmp_id,customer_id,icon_url,icon_url_selected,page_url,color,color_selected,sort,display,isvalid,createtime,selector_id) values";
        $sql_management = "insert into ".WSY_SHOP.".publish_page_management(tmp_id,customer_id,page_id,isvalid,type,funs,createtime) values";
        for($i=0;$i<$num[0];$i++){
            $sql2 = "select id,content,is_open,name from ".WSY_SHOP.".weixin_commonshop_diy_template where customer_id='".$customer_id."' and isvalid=true and content!=-1 limit ".$i.",1";
            // echo $sql2;
            $res = _mysql_query($sql2) or die('2222222' . mysql_error());
            $result = mysql_fetch_array($res);
            if($result['content']!=""){
            //查询出对应的底部菜单栏信息
            $content = ltrim(rtrim($result['content'],","),",");
            $sql3 = "select title,imgurl,link,color,select_value,type from ".WSY_SHOP.".weixin_commonshop_diy_template_content where customer_id=".$customer_id." and isvalid=true and diy_temid='".$result['id']."' and diy_tem_contid in (".$content.")";
            $res3 = $this->db->getAll($sql3) or die('111111' . mysql_error());
            $num2= count($res3);
            for($a=0;$a<$num2;$a++){
                $result3 =array();
                if($res3[$a]['type'] == '6'){
                    $result3['title'] =  $res3[$a]['title'];
                    $result3['imgurl'] =  $res3[$a]['imgurl'];
                    $result3['link'] =  $res3[$a]['link'];
                    $result3['color'] =  $res3[$a]['color'];
                    $result3['select_value'] =  $res3[$a]['select_value'];
                    break;
                }
            }

            if ($result3['title']!="") {
                //拼接创建模板语句
                $tmp_id = $id+$i;
                $sql_template .="('".$tmp_id."','".$customer_id."','".$result['name']."','".$result['is_open']."','1','".$createtime."','1'),";

                $result3['title'] = rtrim($result3['title'],"|");
                $result3['imgurl'] = rtrim($result3['imgurl'],"|");
                $result3['link'] = rtrim($result3['link'],"|");
                $result3['color'] = rtrim($result3['color'],"|");
                $result3['select_value'] = rtrim($result3['select_value'],"|");

                $title   = explode("|", $result3['title']);
                $imgurl  = explode("|", $result3['imgurl']);
                $link    = explode("|", $result3['link']);
                $color   = explode("|", $result3['color']);
                $select_value   = explode("|", $result3['select_value']);
                //拼接按钮数据语句  
                for($t=0;$t<count($title);$t++){
                    $sql_bottom.="('".$title[$t]."','".$tmp_id."','".$customer_id."','".$imgurl[$t]."','".$imgurl[$t]."','".$link[$t]."','".$color[$t]."','".$color[$t]."','".$t."','1','1','".$createtime."','".$select_value[$t]."'),";
                    
                }
            
            echo "---创建自定义初始模板".$i."</br>";

            //拼接发布页语句
            $funs = "weixin_commonshop_diy_template_".$result['id'];
            $sql_management .= "('".$tmp_id."','".$customer_id."','0','1','2','".$funs."','".$createtime."'),";
        }
        }
        }
        $sql_template    = rtrim($sql_template,",");
        $sql_bottom      = rtrim($sql_bottom,",");
        $sql_management  = rtrim($sql_management,",");
        if (substr($sql_template,-6)!="values") {
            _mysql_query($sql_template) or die('4444444' . mysql_error());
        }
        if (substr($sql_bottom,-6)!="values") {
            _mysql_query($sql_bottom) or die('555555' . mysql_error());
        }
        if (substr($sql_management,-6)!="values") {
            _mysql_query($sql_management) or die('6666666' . mysql_error());
        }
        // echo "</br>----".$sql_template;
        // echo "</br>----".$sql_bottom;
        // echo "</br>----".$sql_management;
        echo "</br>初始化底部菜单模板脚本运行结束！";
        mysql_close($link);
    }
}
 ?>