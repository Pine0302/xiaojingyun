<?php

class control_bottom_label extends control_base
{
    public $model;
    
    function __construct()
    {
        parent::__construct();  
        
        // parent::check_login();

        require_once('model/bottom_label.php');
        $this->model = new model_bottom_label($this->customer_id);
        
        require_once('model/common.php');
        $this->model_common = new model_common();

        $this->user_id = $_SESSION['user_id_'.$this->customer_id];
    }

    //列表页
    public function template_list()
    {
        //获取后台主题颜色
        $pageSize       = 20;
        $theme          = $this->model_common->find_theme($this->customer_id);
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $head           = 9;
        $pageNum        = isset($_GET['pagenum'])?$_GET['pagenum']:1;
        $start          = ($pageNum-1)*$pageSize;
        $condition["id"]    = isset($_GET['template_id'])?$_GET['template_id']:'';
        $condition["name"]  = isset($_GET['name'])?$_GET['name']:'';
        $condition["createtime"]  = isset($_GET['createtime'])?$_GET['createtime']:'';
        $is_shelve      = isset($_GET['status'])?$_GET['status']:'-1';
        if($is_shelve > -1) $condition["is_shelve"] = $is_shelve;
        $count          = $this->model->template_list_count($condition);
        $condition["limit"]  = $start.",".$pageSize;
        $data           = $this->model->template_list_select($condition)['lists'];
        $pageCount      = ceil($count/$pageSize);
        $condition['is_shelve'] = $is_shelve;
        include('view/bottom_label/template_list.php');
    }

    //模版新增页
    public function template_add(){
        $customer_id_en = $this->customer_id_en;
        if(isset($_POST['name'])&&isset($_POST['position'])){
            $length = mb_strlen($_POST['name'],'utf-8');
            if($length > 60 ) json_out(array('errcode'=>401,'errmsg'=>'模板名称必须60个字内'));
            $data['customer_id']=$this->customer_id;
            $data['name']=trim($_POST['name']);
            $data['createtime']=date("Y-m-d H:i:s");
            $data['position']=$_POST['position'];
            $res=$this->model->template_add($data);
            if($res['insertid']){
                //默认添加一个按钮
                $icon['tmp_id']=$res['insertid'];
                $icon['customer_id']=$this->customer_id;
                $icon['icon_url']="/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png";
                $icon['icon_url_selected']="/weixinpl/back_newshops/Common/images/Base/personal_center/gift.png";
                $icon['color']="000000";
                $icon['color_selected']="000000";
                $icon['sort']=1;
                $icon['createtime']=date("Y-m-d H:i:s");
                $this->model->icon_add($icon);
            }
            json_out($res);
        }else{
            $theme = $this->model_common->find_theme($this->customer_id);
            include("view/bottom_label/template_add.php");
        }
    }

    //模版编辑页
    public function template_edit(){
        $customer_id_en = $this->customer_id_en;
        if(isset($_POST['id'])&&isset($_POST['name'])&&isset($_POST['position'])){
            $length = mb_strlen($_POST['name'],'utf-8');
            if($length > 60 ) json_out(array('errcode'=>401,'errmsg'=>'模板名称必须60个字内'));
            $id=$_POST['id'];
            $data['name']=$_POST['name'];
            $data['position'] = $_POST['position'];
            $res=$this->model->template_edit($data,$id);
            json_out($res);
        }
        else if(isset($_GET['id'])){
            $theme = $this->model_common->find_theme($this->customer_id);
            $id=$_GET['id'];
            $template=$this->model->template_select($id)['data'];
            include("view/bottom_label/template_edit.php");
        }
    }

    //删除列表
    public function template_del(){
        if(isset($_POST['tid']) && $_POST['type'] == 3){
            $id=$_POST['tid'];
            $res=$this->model->template_del($id);
            json_out($res);
        }else{
            json_out(array('errcode'=>403,'errmsg'=>'删除失败!'));
        }
    }

    //模版上架 ---在保存时上架
    public function template_shelve($tid = 0){
        $template_shelve = $this->model->template_shelve($this->customer_id,$tid,true);
        return $template_shelve;
    }

    //模版下架
    public function template_off_shelve(){
        $tid  = $_POST['tid']?$_POST['tid']:0;
        $type = $_POST['type'];
        if($type == 2) {
            //下架
            $template_shelve = $this->model->template_shelve($this->customer_id,$tid,0);
            if($template_shelve['errcode'] === 0){
                json_out(array('errcode'=>0,'errmsg'=>'下架成功!'));
            }
        }else{
            json_out(array('errcode'=>403,'errmsg'=>'非法操作!'));
            die();
        }
    }

    //按钮列表页
    public function icon_list(){
        $customer_id_en = $this->customer_id_en;
        $id=$_GET['id'];//模板id
        $condition['tmp_id']=$id;
        $icon_list=$this->model->icon_list_select($condition)['lists'];
        $maxsort=max(array_column($icon_list,'orderby'));//最大排序
        if(!$maxsort) $maxsort=0;
        $icon_list=json_encode($icon_list);
        include("view/bottom_label/icon_list.php");
    }

    //按钮列表新增页
    public function icon_add(){
        $data['tmp_id']=$_POST['tmp_id'];
        $count=$this->model->icon_count($data);
        if($count==5){
            json_out(array('errcode'=>403,'errmsg'=>'添加失败!'));
        }
        $data['name']=$_POST['name'];
        $data['customer_id']=$this->customer_id;
        $data['icon_url']=$_POST['noimgUrl'];
        $data['icon_url_selected']=$_POST['selimgUrl'];
        $data['color']=$_POST['nocolor'];
        $data['color_selected']=$_POST['selcolor'];
        $data['sort']=$_POST['sort'];
        $data['createtime']=date("Y-m-d H:i:s");
        $res=$this->model->icon_add($data);
        json_out($res);
    }

    //按钮列表编辑页
    public function icon_edit(){
        //var_dump($_REQUEST);var_dump($_FILES);die;
        $id=$_POST['id'];
        $data['name']=$_POST['name'];
        $data['color_selected']=$_POST['color_selected'];
        $data['color']=$_POST['color'];
        $data['sort']=$_POST['sort'];
        //上传文件
        $uptypes=array('image/jpg',
        'image/jpeg',
        'image/png',
        'image/pjpeg',
        'image/gif',
        'image/bmp',
        'image/x-png'); 
        $max_file_size=51200; //上传文件大小限制, 单位BYTE
        $destination_folder='../../mshop/'.Base_Upload.'Base/personalization/navigation/';
        if (!is_uploaded_file($_FILES["icon_file_selected"]["tmp_name"])){
            $data['icon_url_selected']=$_POST['icon_url_selected'];
        }else{
            $file = $_FILES["icon_file_selected"];
            if($max_file_size < $file["size"]){
                json_out(array('errcode'=>403,'errmsg'=>'文件太大'));
            }
            if(!in_array($file["type"], $uptypes)){
                json_out(array('errcode'=>403,'errmsg'=>'不能上传此类型文件'));
            }
            if(!file_exists($destination_folder)){
                mkdir($destination_folder,0777,true);
            }
            $pinfo=pathinfo($file["name"]);
            $ftype=$pinfo["extension"];
            $destination = $destination_folder.time().".".$ftype;
            $overwrite=true;
            if (file_exists($destination) && $overwrite != true){
                json_out(array('errcode'=>403,'errmsg'=>'同名文件已经存在了'));
            }
            $filename=$file["tmp_name"];
            if(!_move_uploaded_file($filename, $destination))
            {
                json_out(array('errcode'=>403,'errmsg'=>'移动文件出错'));
            }
            $save_destination = str_replace("../","",$destination);
            $save_destination="/".$save_destination;
            $data['icon_url_selected']=$save_destination;
        }
        $destination_folder='../../mshop/'.Base_Upload.'Base/personalization/navigation/';
        if (!is_uploaded_file($_FILES["icon_file"]["tmp_name"])){
            $data['icon_url']=$_POST['icon_url'];
        }else{
            $file = $_FILES["icon_file"];
            if($max_file_size < $file["size"]){
                json_out(array('errcode'=>403,'errmsg'=>'文件太大'));
            }
            if(!in_array($file["type"], $uptypes)){
                json_out(array('errcode'=>403,'errmsg'=>'不能上传此类型文件'));
            }
            if(!file_exists($destination_folder)){
                mkdir($destination_folder,0777,true);
            }
            $pinfo=pathinfo($file["name"]);
            $ftype=$pinfo["extension"];
            $destination = $destination_folder.(time()+1).".".$ftype;
            $overwrite=true;
            if (file_exists($destination) && $overwrite != true){
                json_out(array('errcode'=>403,'errmsg'=>'同名文件已经存在了'));
            }
            $filename=$file["tmp_name"];
            if(!_move_uploaded_file($filename, $destination))
            {
                json_out(array('errcode'=>403,'errmsg'=>'移动文件出错'));
            }
            $save_destination = str_replace("../","",$destination);
            $save_destination="/".$save_destination;
            $data['icon_url']=$save_destination;
        }
        //栏目链接
        if($_POST['page_url']!=''){
            $data['page_url']=$_POST['page_url'];
        }
        if($_POST['selector_id']!=''){
            $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
            mysql_select_db(DB_NAME) or die('Could not select database');
            include_once('../../mshop/admin/Base/personalization/home_decoration/pink_selector_url.php');
            $data['selector_id']=$_POST['selector_id'];
            $data['page_url'] = pink_selector_url($_POST['selector_id'],$protocol_http_host,$this->customer_id,$this->customer_id_en,$this->user_id)['url'];
        }
        $res=$this->model->icon_edit($data,$id);
        json_out($res);
    }

    //按钮删除列表
    public function icon_del(){
        if(isset($_POST['id'])){
            $id=$_POST['id'];
            $res=$this->model->icon_del($id);
            json_out($res);
        }else{
            json_out(array('errcode'=>403,'errmsg'=>'删除失败!'));
        }
    }

    //发布页  模板上架 跳转发布页
    public function template_release(){
        $theme = $this->model_common->find_theme($this->customer_id);
        $tid = isset($_GET['tid'])?$_GET['tid']:0;
        if($_GET['do'] == 'show'){
            if($tid <= 0 || $tid == false) echo "<script>alert('模板id不存在，无法查看');window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list';</script>";
            //所有模块均显示
            $show = 1;
        }else{
            if($tid <= 0 || $tid == false) echo "<script>alert('模板id不存在，无法发布');window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list';</script>";
            //查询除当前模板发布模块
            $release_select = $this->model->release_select($this->customer_id,$tid);
            $funs = $release_select['release_list']['power'];
        }
        //当前模板已选择发布模块
        $release_selected = $this->model->release_selected($this->customer_id,$tid);
        $selected_funs2 = $release_selected['release_list']['power'];
        include("view/bottom_label/template_release.php");
    }

    //发布保存
    public function template_release_edit(){
        $tid  = $_POST['tid']?$_POST['tid']:0;
        $data = $_POST['links2'];
        if($data){
            //验证当前模块是否在其他上架模板中使用
            $check= $this->model->release_select($this->customer_id,$tid);
            $check_array = $check['release_list'];
            foreach($data as $v){
                if(in_array($v,$check_array)){
                    echo "<script>alert('该模块已经在其他模板中发布！不可重复发布！');window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list';</script>";die();
                }
            }
            $condition['tid']   = $tid; //模板id
            $condition['data']  = $data;//数据
            //保存模板发布数据并上架
            $release_save = $this->model->release_save($this->customer_id,$condition);
            $template_shelve = $this->template_shelve($tid);
            if($release_save['errcode'] === 0 && $template_shelve['errcode'] === 0){
                echo "<script>alert('模板上架成功！');window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list';</script>";die();
            }
            echo "<script>alert('模板上架失败！');window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list';</script>";die();
        }
        echo "<script>alert('模板上架成功！');window.location.href='/mshop/admin/index.php?m=bottom_label&a=template_list';</script>";die();
    }

    //模板选择页
    public function bottom_selector_list(){
        $pageSize       = 20;
        $theme          = $this->model_common->find_theme($this->customer_id);
        $customer_id    = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $head           = 9;
        $pageNum        = isset($_GET['pagenum'])?$_GET['pagenum']:1;
        $start          = ($pageNum-1)*$pageSize;
        $condition["id"]    = isset($_GET['template_id'])?$_GET['template_id']:'';
        $condition["name"]  = isset($_GET['name'])?$_GET['name']:'';
        $condition["createtime"]  = isset($_GET['createtime'])?$_GET['createtime']:'';
        $is_shelve      = isset($_GET['status'])?$_GET['status']:'';
        if($is_shelve > -1) $condition["is_shelve"] = $is_shelve;
        $count          = $this->model->template_list_count($condition);
        $condition["limit"]  = $start.",".$pageSize;
        $data           = $this->model->template_list_select($condition)['lists'];
        $pageCount      = ceil($count/$pageSize);
        $condition['is_shelve'] = $is_shelve;
        include("view/bottom_label/bottom_selector_list.php");
    }

    //获得模板的按钮
    public function icon_list_get(){
        $id=$_POST['tmp_id'];//模板id
        $condition['tmp_id']=$id;
        $icon_list=$this->model->icon_list_select($condition)['lists'];
        json_out($icon_list);
    }

    //获取发布某模块的模板id
    public function  re_release_selected(){
        $funs=$_POST['funs'];
        $tmp_id=$this->model->re_release_selected($this->customer_id,$funs)['tmp_id'];
        json_out($tmp_id);
    }

    public function icon_list_by_diy(){
        $diy_temid=$_POST['diy_temid'];
        $data=$this->model->icon_list_by_diy($this->customer_id,$diy_temid)['lists'];
        json_out($data);
    }

}
