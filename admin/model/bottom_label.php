<?php

class model_bottom_label{
    public $db;

    function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
        $this->db = DB::getInstance();
    }

    /**
     * 模版数目查询
     * @param  array  condition     数据
     * @return int    count         模版总数
     */
    public function template_list_count($condition){
        $sql="select count(id) from ".WSY_SHOP.".bottom_label_template_setting where customer_id='{$this->customer_id}' and isvalid=1";
        if($condition['id']!='') $sql.=" and id='{$condition["id"]}'";
        if($condition['name']!='') $sql.=" and name='{$condition["name"]}'";
        if($condition['createtime']!='') $sql.=" and createtime like '{$condition["createtime"]}%'";
        if($condition['is_shelve']!='') $sql.=" and is_shelve='{$condition["is_shelve"]}'";
        $count = $this->db->getOne($sql);
        return $count;
    }

     /**
     * 单个模版查询
     * @param  int  id              编号
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return array  data         模版详情
     */
    public function template_select($id){
        $sql="select name,is_shelve,createtime,position from ".WSY_SHOP.".bottom_label_template_setting where id='{$id}' and customer_id='{$this->customer_id}' and isvalid=1";
        $data = $this->db->getRow($sql);
        return array("errcode"=>0,"errmsg"=>"查询成功","data"=>$data);
    }


    /**
     * 模版列表查询
     * @param  array  condition     数据
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return array  lists         模版数组详情
     */
    public function template_list_select($condition){
        $sql="select id,name,is_shelve,createtime from ".WSY_SHOP.".bottom_label_template_setting where customer_id='{$this->customer_id}' and isvalid=true ";
        if($condition['id']!='') $sql.=" and id='{$condition["id"]}'";
        if($condition['name']!='') $sql.=" and name='{$condition["name"]}'";
        if($condition['createtime']!='') $sql.=" and createtime like '{$condition["createtime"]}%'";
        if($condition['is_shelve']!='') $sql.=" and is_shelve='{$condition["is_shelve"]}'";
        $sql.=" order by createtime desc limit {$condition['limit']}";
        $data = $this->db->getAll ($sql);
        return array("errcode"=>0,"errmsg"=>"查询成功","lists"=>$data);
    }

    /**
     * 模版增加
     * @param  array  data          数据
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     */
    public function template_add($data){
        $res = $this->db->autoExecute(WSY_SHOP.'.bottom_label_template_setting',$data,'insert');
        if($res){
            $insertid=$this->db->insert_id();
            return array("errcode"=>0,"errmsg"=>"添加成功","insertid"=>$insertid);
        }else{
            return array("errcode"=>400,"errmsg"=>"添加失败","insertid"=>0);
        }
    }

    /**
     * 模版编辑
     * @param  array  data          数据
     * @param  int    id            编号
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     */
    public function template_edit($data,$id){
        $res = $this->db->autoExecute(WSY_SHOP.'.bottom_label_template_setting',$data,'update',"id = '$id' and customer_id = '$this->customer_id'");
        if($res){
            return array("errcode"=>0,"errmsg"=>"保存成功");
        }else{
            return array("errcode"=>400,"errmsg"=>"编辑失败");
        }
    }

    /**
     * 模版删除
     * @param  int    customer_id   商家编号id
     * @param  int    id            编号
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     */
    public function template_del($id){
        $this->db->query("update ".WSY_SHOP.".bottom_label_template_setting set isvalid = false where id='{$id}' and customer_id = '$this->customer_id'");
        return array("errcode"=>0,"errmsg"=>"删除成功");
    }


    /**
     * 模版上下架
     * @param  int    customer_id   商家编号id
     * @param  int    id            编号
     * @param  bool   status        状态
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     */
    public function template_shelve($customer_id,$tid,$status){
        $sql = "update ".WSY_SHOP.".bottom_label_template_setting set is_shelve = $status where customer_id = '{$customer_id}' and isvalid = true and id = '{$tid}'";
        $this->db->query($sql);
        return array('errcode'=>0,'errmsg'=>'ok');
    }

    /**
     * 模版发布模块保存
     * @param  int    customer_id   商家编号id
     * @param  array  condition     数据
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     */
    public function release_save($customer_id,$condition){
        $data   = $condition['data'];
        $tid    = $condition['tid'];
        $time   = date('Y-m-d H:i:s',time());
        //先隐藏当前模板的所有模块
        $sql_start = "update ".WSY_SHOP.".publish_page_management set isvalid = false where customer_id='{$customer_id}' and tmp_id = '{$tid}' and type = 2";
        $result =  $this->db->query($sql_start);
        //数组去重
        $selected_funs_arr = array_values(array_unique($data));     //已选中的模块
        foreach($selected_funs_arr as $v){
            //判断该模块是否已经添加过
            $sql = "select id from ".WSY_SHOP.".publish_page_management where funs = '{$v}' and customer_id='{$customer_id}' and tmp_id = '{$tid}' and type = 2";
            $res = $this->db->getRow($sql);
            if($res){
                $sql2 = "update ".WSY_SHOP.".publish_page_management set isvalid = true where funs = '{$v}' and customer_id='{$customer_id}' and tmp_id = '{$tid}' and type = 2";
            }else{
                $sql2 = "insert into ".WSY_SHOP.".publish_page_management (customer_id,tmp_id,isvalid,type,funs,createtime) values ('{$customer_id}','{$tid}',true,2,'{$v}','{$time}')";
            }
            $res2 = $this->db->query($sql2);
        }
        return array('errcode'=>0,'errmsg'=>'ok');
    }

    /**
     * 模版的按钮数目查询
     * @param  int    condition     条件
     * @return int    count         按钮总数
     */
    public function icon_count($condition){
        $sql="select count(id) from ".WSY_SHOP.".bottom_label_icon_setting where customer_id='{$this->customer_id}' and isvalid=1 and tmp_id='{$condition["tmp_id"]}'";
        $count = $this->db->getOne($sql);
        return $count;
    }

    /**
     * 模版按钮列表查询
     * @param  array  condition     条件
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return array  lists         模版数组详情
     */
    public function icon_list_select($condition){
        $sql="select id,name,icon_url as noimgUrl,icon_url_selected as selimgUrl,page_url as url,color as nocolor,color_selected as selcolor,sort as orderby,selector_id as column_id from ".WSY_SHOP.".bottom_label_icon_setting where customer_id='{$this->customer_id}' and isvalid=1 and tmp_id='{$condition["tmp_id"]}' order by sort,createtime";
        $data = $this->db->getAll ($sql);
        foreach ($data as &$value) {
            if(!empty($value['column_id'])){
                $value['column_title']=substr($value['column_id'],(strripos($value['column_id'],'-')+1));
            }else{
                $value['column_title']='';
            }
			
            if(is_null($value['name'])){
                $value['name'] = null;
            }	
            
        }
        return array("errcode"=>0,"errmsg"=>"查询成功","lists"=>$data);
    }

    /**
     * 按钮增加
     * @param  array  data          数据
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return int insertid         插入记录的id
     */
    public function icon_add($data){
        $res = $this->db->autoExecute(WSY_SHOP.'.bottom_label_icon_setting',$data,'insert');
        if($res){
            $insertid=$this->db->insert_id();
            return array("errcode"=>0,"errmsg"=>"添加成功","insertid"=>$insertid);
        }else{
            return array("errcode"=>400,"errmsg"=>"添加失败","insertid"=>$insertid);
        }
    }

    /**
     * 模版增加
     * @param  array  data          数据
     * @param  int    id            编号
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     */
    public function icon_edit($data,$id){
        $res = $this->db->autoExecute(WSY_SHOP.'.bottom_label_icon_setting',$data,'update',"id = '$id' and customer_id = '$this->customer_id'");
        if($res){
            return array("errcode"=>0,"errmsg"=>"保存成功");
        }else{
            return array("errcode"=>400,"errmsg"=>"保存失败");
        }
    }

    /**
     * 按钮编辑
     * @param  int    id            编号
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     */
    public function icon_del($id){
        $this->db->query("update ".WSY_SHOP.".bottom_label_icon_setting set isvalid = false where id='{$id}' and customer_id = '$this->customer_id'");
        return array("errcode"=>0,"errmsg"=>"删除成功");
    }

    /**
     * 发布页-查询不可发布的模块
     * @param  int    customer_id   商家编号id
     * @param  int    id            编号
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return array  release_list  权限列表array('power'=>'权限功能名称','template_id'=>'模版编号')
     */
    public function release_select($customer_id,$id = 0){
        //查询不可发布的模块（包括已被其他模板关联但未上架的自定义菜单）
        $sql = "SELECT p.funs as power,p.tmp_id as template_id from ".WSY_SHOP.".publish_page_management as p inner join ".WSY_SHOP.".bottom_label_template_setting as n on n.id = p.tmp_id and n.isvalid = true and (n.is_shelve = true or p.funs like 'weixin_commonshop_diy_template_%') where p.customer_id = '{$customer_id}' and n.customer_id = '{$customer_id}' and p.isvalid = true and p.type = 2 and p.tmp_id <> '{$id}'";
        $result = $this->db->getAll($sql);
        $power  = array_column($result, 'power');
        $tmp_id = $id;
        $re     = array('power'=>$power,'tid'=>$tmp_id);
        return array('errcode'=>0,'errmsg'=>'ok','release_list'=>$re);
    }

    /**
     * 发布页-查询该模板已发布的模块
     * @param  int    customer_id   商家编号id
     * @param  int    id            编号
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return array  release_list  权限列表array('power'=>'权限功能名称','template_id'=>'模版编号')
     */
    public function release_selected($customer_id,$id = 0){
        //查询已发布的模块
        $sql = "SELECT funs as power,tmp_id as template_id from ".WSY_SHOP.".publish_page_management where customer_id = '{$customer_id}' and isvalid = true and type = 2 and tmp_id = '{$id}'";
        $result = $this->db->getAll($sql);
        $power  = array_column($result, 'power');
        $tmp_id = $id;
        $re     = array('power'=>$power,'tid'=>$tmp_id);
        return array('errcode'=>0,'errmsg'=>'ok','release_list'=>$re);
    }

    /**
     * 发布页-查询该模板已发布的模块
     * @param  int    customer_id   商家编号id
     * @param  int    funs          关联模块
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return array  tmp_id        底部模板id
     */
    public function re_release_selected($customer_id,$funs){
        $sql = "SELECT ppm.tmp_id from ".WSY_SHOP.".publish_page_management as ppm inner join ".WSY_SHOP.".bottom_label_template_setting as blts  where ppm.customer_id = '{$customer_id}' and ppm.tmp_id=blts.id and ppm.isvalid = true and blts.isvalid = true and ppm.type = 2 and ppm.funs = '{$funs}'";
        $result = $this->db->getOne($sql);
        return array('errcode'=>0,'errmsg'=>'ok','tmp_id'=>$result);
    }

    /**
     * 根据自定义模板id获取关联的底部菜单的按钮
     * @param  int    customer_id   商家编号id
     * @param  int    diy_temid     自定义模板id
     * @return int    errcode       返回状态
     * @return varchar errmsg       返回状态说明
     * @return array  tmp_id        底部模板id
     */
    public function icon_list_by_diy($customer_id,$diy_temid){
        $sql = "SELECT blis.id,blis.name,blis.icon_url as noimgUrl,blis.icon_url_selected as selimgUrl,blis.page_url as url,blis.color as nocolor,blis.color_selected as selcolor,blis.sort as orderby,blis.selector_id as column_id from ".WSY_SHOP.".publish_page_management as ppm inner join ".WSY_SHOP.".bottom_label_template_setting as blts inner join ".WSY_SHOP.".bottom_label_icon_setting as blis  where ppm.customer_id = '{$customer_id}' and ppm.tmp_id=blts.id and ppm.isvalid = true and blts.isvalid = true and blis.isvalid=true and blis.tmp_id=blts.id AND blis.customer_id = '{$customer_id}' and ppm.type = 2 and ppm.funs = 'weixin_commonshop_diy_template_{$diy_temid}' order by blis.sort asc";
        $data = $this->db->getAll($sql);
        return array("errcode"=>0,"errmsg"=>"查询成功","lists"=>$data);
    }

}
