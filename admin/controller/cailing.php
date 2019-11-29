<?php


class control_cailing extends control_base 
{
	public $model;
	public $model_common;
    public $music_folder;
	function __construct() 
	{
		parent::__construct();
		require_once('model/cailing.php');
		$this->model = new model_cailing();
		require_once('model/common.php');
        $this->model_common = new model_common();
		
		parent::check_login();
        $data['data']=file_get_contents('php://input', true);
		//$data = $_REQUEST['data'];
		$this->parmdata  = json_decode($data['data'],true);
        $customer_id = $this->customer_id;
        require_once($_SERVER['DOCUMENT_ROOT'].'/weixinpl/proxy_info.php');
        $this->music_folder = UPLOAD_IMAGE_PATH .$this->customer_id.'/blessing_music/'.date ( 'Ym' ).'/';     //音频上传文件路径

		
    }

	/*
    版权信息:  秘密信息
    功能描述：彩铃订购——添加彩铃及编辑
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-16
    重要说明：无
     */   
    public function add_color_bell(){
        $data['customer_id'] = $this->customer_id;
        $data['name']        = $_POST['name'];      //彩铃名字
        $data['tip']         = $_POST['tip'];       //彩铃标签
        $data['img_url']     = $_POST['img_url'];   //彩铃图片
        $data['price']       = $_POST['price'];     //价格
        $data['sort']        = $_POST['sort'];      //排序
        $data['music']       = $_POST['music'];      //音乐
        $data['issale']      = $_POST['issale'];
        $data['cailing_id']  = $_POST['cailing_id']?$_POST['cailing_id']:'';
        $data['op']          = $_POST['op']?$_POST['op']:'';
        $p_customer_id       = $data['customer_id'] = $_POST['customer_id'];
        $data['customer_id'] = $customer_id = $this->customer_id;//商家ID
        if ($data['sort'] == '') {
            $data['sort'] = 0;
        }

        if($p_customer_id != $customer_id) {
            return $return=array('errcode' => 403, 'errmsg' => '非法操作', 'data' => '');
        }

        if (empty($data['name']) || empty($data['img_url']) || empty($data['music']) || $data['price'] == '') {
            json_out(array('errcode' => 401,'errmsg'=>'参数丢失','code'=>$data));
        }

        $result = $this->model->insert_color_bell($data);

        if (!$result) {
            json_out(array('errcode' => 400,'errmsg'=>'添加失败','code'=>$result));
        }
        json_out(array('errcode' => 1));
    }   


    /*
    版权信息:  秘密信息
    功能描述：彩铃订购——添加彩铃静态页
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-16
    重要说明：无
     */   
    public function color_bell_static(){
        parent::check_login(); 
        $customer_id = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);
        include('view/cailing/color_bell_static.php');


    }

 /*
    版权信息:  秘密信息
    功能描述：彩铃订购——添加彩铃管理静态页
    开 发 者：linrongdie -- V409
    开发日期： 2018-05-16
    重要说明：无
     */   
    public function color_bell_management(){
        $cailing_name = '';
        if (!empty($_GET['name'])) {
            $cailing_name = $_GET['name'];
        }

        $cailing_issale = '';
        if ($_GET['issale'] != '') {
            $cailing_issale = $_GET['issale'];
        }

        if ($cailing_issale == 2) {
            $cailing_issale = '';
        }
        parent::check_login(); 
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme   = $this->model_common->find_theme($customer_id);
        $data['customer_id']  = $customer_id;
        $data['name']         = $_GET['name']?$_GET['name']:'';  //彩铃名称
        $data['issale']       = $_GET['issale']; //商品状态 2.全部商品 1.上架中 0.下架
        $data['page']         = $_GET['page']?$_GET['page']:'';
        $data['total']         = $_GET['total']?$_GET['total']:'';
        $p_customer_id = $_GET['customer_id']?$_GET['customer_id']:'';
        $result  = $this->model->get_ell_management($data);  //获取全部信息

        $res_select = $result['res_select'];
        $res_count  = $result['res_count'];
        include('view/cailing/color_bell_management.php');

    }

/*
    版权信息:  秘密信息
    功能描述：彩铃管理——單個删除
    开 发 者：linrongdie_V409
    开发日期： 2018-05-18
    重要说明：无
    返回：  $return['errcode'] = 1/0 成功/失败
            $return['errmsg'] = "删除成功！/删除失败";
     */
    public function del_cailing_shopkeepers(){
        $param['customer_id'] = $this->customer_id;
        $param['id']     = $_GET['id']?$_GET['id']:-1;
        $res = $this->model->del_cailing_shopkeeper($param);        
        json_out($res);
    }

/*
    版权信息:  秘密信息
    功能描述：彩铃管理——批量删除
    开 发 者：linrongdie_V409
    开发日期： 2018-05-18
    重要说明：无
    返回：  $return['errcode'] = 1/0 成功/失败
            $return['errmsg'] = "删除成功！/删除失败";
     */
    public function del_cailing(){
           
            // $return = array('errcode'=>0,'errmsg'=>'success');
           if(!empty($_GET['del_cailing'])){
            if( $_GET['del_cailing'] == "del_cailing" ){
                $del_arr = $_GET["delete_ids"];
                $del_arr = rtrim($del_arr,",");
                // alert($del_arr);
                $return = $this->model->del_cailing($del_arr);      
                // $query = "UPDATE ".WSY_SHOP.".colortone_prod set isvalid=0 where id IN(".$del_arr.")";
                // $result = _mysql_query($query)or die("L132 : query error : ".mysql_error());
                // if($result){

                // }
                echo json_encode($return);
            }
        }
    }

/*
    版权信息:  秘密信息
    功能描述：彩铃管理——上下架
    开 发 者：linrongdie-V409
    开发日期： 2018-5-19
    重要说明：无
     */
     public function change_isout_get(){
        $data = $_POST;
        $data['customer_id'] = $this->customer_id;
        $res = $this->model->change_isout_get($data);
        json_out($res);
     }


    /*
    版权信息:  秘密信息
    功能描述：彩铃订购——编辑彩铃静态页
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-22
    重要说明：无
     */   
    public function color_ring_editor(){
         parent::check_login(); 
        $customer_id = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);
        $cailing_id = $_GET['id'];
        $result = $this->model->color_ring_editor($cailing_id,$customer_id);   //获取数据表数据
        include('view/cailing/color_ring_editor.php');
    }



    /*
    版权信息:  秘密信息
    功能描述：彩铃订购——文件切片上传
    开 发 者：Zoujunjie -- V397
    开发日期： 2018-05-17
    重要说明：无
     */   
    public function file_slicing(){
        // 确保文件不被缓存（如在iOS设备上发生的那样） 
        header("Access-Control-Allow-origin:*");  
        //header("Access-Control-Allow-Credentials:true");  
        //header('Access-Control-Allow-Headers:x-requested-with,content-type');  
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  
        header("Cache-Control: no-store, no-cache, must-revalidate");  
        header("Cache-Control: post-check=0, pre-check=0", false);  
        header("Pragma: no-cache");  
        $customer_id = $this->customer_id;
        // Support CORS  
        // header("Access-Control-Allow-Origin: *");  
        // 其他CORS头如果有的话…  
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {  
            exit; // 完成飞行前的CORS请求 
        }  
          
          
        if ( !empty($_REQUEST[ 'debug' ]) ) {  
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );  
            if ( $random === 0 ) {  
                header("HTTP/1.0 500 Internal Server Error");  
                exit;  
            }  
        }  
        //  
        //var_dump($_REQUEST);  
        //  
        // header("HTTP/1.0 500 Internal Server Error");  
        // exit;  
          
          
        // 5 minutes execution time  
        @set_time_limit(5 * 60);  

        // $targetDir = $this->destination_folder; //文件临时路径
        // $uploadDir = $this->uploadDir; //文件最终路径
        // $targetDir = 'upload_tmp';  
        // $uploadDir = 'upload';
        $targetDir = $this->music_folder."/../music_tmp";
        $uploadDir = $this->music_folder;
          
        $cleanupTargetDir = true; // 删除旧文件  
        $maxFileAge = 5 * 3600; // 秒的临时文件年龄  
          
          
        // 创建目标DIR  
        if (!file_exists($targetDir)) {
            @mkdir($targetDir,0755,true);
        }  
          
        // 创建目标DIR  
        if (!file_exists($uploadDir)) {  
            @mkdir($uploadDir,0755,true);  
        }  
          
        // 获取文件名 
        if (isset($_REQUEST["name"])) {  
            $fileName = $_REQUEST["name"];  
        } elseif (!empty($_FILES)) {  
            $fileName = $_FILES["file"]["name"];  
        } else {  
            $fileName = uniqid("file_");  
        } 

        //$fileName  = iconv("UTF-8","gb2312",$fileName);

        $fileTypes = array('mp3', 'wmv'); // 允许的文件后缀

        $fileParts = pathinfo($_FILES["file"]["name"]);

        if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

        } else {
            die('{"errcode" : 402, "errmsg" : "不合法的文件格式！", "jsonrpc" : "2.0", "error" : {"code": 104, "message": "不合法的文件格式！"}, "id" : "id"}');
        }
          
        // 可以启用组块  
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;  
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;  //分片文件路径
          
        // 删除旧的临时文件  
        if ($cleanupTargetDir) {  
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {  
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "打开临时目录失败"}, "id" : "id"}');  
            }  
          
            while (($file = readdir($dir)) !== false) {  
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;  
          
                // 如果临时文件是当前文件，则进入下一个文件  
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {  
                    continue;  
                }  
          
                // 删除临时文件，如果它大于最大年龄，而不是当前文件 
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {  
                    @unlink($tmpfilePath);  
                }  
            }  
            closedir($dir);  
        }  
          
          
        // 打开临时文件 
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) { 
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "打开输出流失败"}, "id" : "id"}');  
        }  
          
        if (!empty($_FILES)) {  
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {  
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "移动上传文件失败"}, "id" : "id"}');  
            }  
          
            // 读取二进制输入流并将其附加到临时文件 
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {  
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "打开输入流失败"}, "id" : "id"}');  
            }  
        } else {  
            if (!$in = @fopen("php://input", "rb")) {  
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "打开输入流失败"}, "id" : "id"}');  
            }  
        }  
          
        while ($buff = fread($in, 4096)) {  
            fwrite($out, $buff);  
        }  
          
        @fclose($out);  
        @fclose($in);  

        //更改分片文件名称
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part"); 
        //end
        
        //插入数据库
        _file_put_contents ('music_tmp.txt', "add====fileName=== ".var_export($fileName,true)." \r\n", FILE_APPEND );
        _file_put_contents ('music_tmp.txt', "add====chunk=== ".var_export($chunk,true)." \r\n", FILE_APPEND );
        $reuslt_add = $this->model->save_music_tmp($fileName,$chunk,$customer_id);
        //插入数据库 End 
          
        $index = 0;  
        $done = true;  

        for( $index = 0; $index < $chunks; $index++ ) {  
            if ( !file_exists("{$filePath}_{$index}.part") ) {  
                $done = false;  
                break;  
            }  
        }

        if($done){
            _file_put_contents ('music_tmp.txt', "====done=======chunk:".$chunk."=====  \r\n", FILE_APPEND );
            for( $index = 0; $index < $chunks; $index++ ) {                 
                $tmp_exits = $this->model->check_music_tmp_exist($fileName,$index,$customer_id);    
                _file_put_contents ('music_tmp.txt', "tmp_exits=======chunks:".$index."== ".var_export($tmp_exits,true)." \r\n", FILE_APPEND ); 
                if(!$tmp_exits){
                    $done = false;
                    break;                      
                }
            }           
        }
        _file_put_contents ('music_tmp.txt', "====done2=== ".var_export($done,true)." \r\n", FILE_APPEND );


        if ( $done ) {  

            $NewfileName = time().rand(10,1000) . "." . $fileParts['extension'];    //文件命名
            $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $NewfileName;          //文件最终合并上传路径    
            
            _file_put_contents ('music_tmp.txt', "=====uploadPath=".$uploadPath."===========\r\n", FILE_APPEND );

            if (!$out = @fopen($uploadPath, "wb")) {  
                $re_clear = $this->model->clear_music_tmp($fileName,$customer_id);
                die('{"errcode" : 402, "errmsg" : "上传失败！", "jsonrpc" : "2.0", "error" : {"code": 105, "message": "Failed to open output stream."}, "id" : '.$chunk.'}');  
            }  
          
            if ( flock($out, LOCK_EX) ) {  
                for( $index = 0; $index < $chunks; $index++ ) {  
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {  
                        break;  
                    }  
          
                    while ($buff = fread($in, 4096)) {  
                        fwrite($out, $buff);  
                    }  
          
                    @fclose($in);  
                    @unlink("{$filePath}_{$index}.part");  
                }  
          
                flock($out, LOCK_UN);  
            }  
            @fclose($out);  
        }  

        if(!empty($uploadPath)){
            $new_file_path = str_replace ( UPLOAD_IMAGE_PATH, '', $uploadPath );
            $new_file_path = str_replace ( '//', '/', $new_file_path );
            $value['newfileurl'] = '/resources/'.$new_file_path;
        }

        // 返回成功JSON-RPC响应
        if ($done) {
            $re_success = $this->model->update_success_music($fileName,$customer_id);
            die('{"errcode" : 0, "errmsg" : "上传成功！", "jsonrpc" : "2.0", "result" : null, "id" : "id", "newfileurl" : "'.$value['newfileurl'].'"}');
        }else{
            die('{"errcode" : 0, "errmsg" : "上传成功！", "jsonrpc" : "2.0", "result" : null, "id" : "id"}');
        }

    }

    /*
    版权信息: 秘密信息
    功能描述：彩铃订购——基本设置
    开 发 者：liupeixin
    开发日期：2018-05-18
    重要说明：无
     */
    public function basic_settings(){
        $customer_id = $this->customer_id;
        //查询出主题颜色
        $theme  = $this->model_common->find_theme($customer_id);
        $res = $this->model->get_basic_settings($customer_id);
        include('view/cailing/basic_settings.php');
    }

    /*
    版权信息: 秘密信息
    功能描述：彩铃订购——基本设置保存
    开 发 者：liupeixin
    开发日期：2018-05-18
    重要说明：无
     */
    public function setting_save(){
        $param  = $_POST;
        $param['customer_id']         = $this->customer_id;

        if( $param['phone_check_but'] != 0 && $param['phone_check_but'] != 1 ){
            $res['errmsg'] = "保存失败！";
        }else if( $param['card_show_but'] != 0 && $param['card_show_but'] != 1 ){
            $res['errmsg'] = "保存失败！";
        }else if( $param['card_position'] != 0 && $param['card_position'] != 1 ){
            $res['errmsg'] = "保存失败！";
        }else{
            $res = $this->model->setting_save($param);
        }
        json_out($res);
    }

    /*
    版权信息: 秘密信息
    功能描述：彩铃订购——订单详情
    开 发 者：liupeixin
    开发日期：2018-05-21
    重要说明：无
     */
    public function order_details(){
        $param['customer_id']  = $customer_id = $this->customer_id;
        //console.log($param['customer_id']);
        //查询出主题颜色
        $theme  = $this->model_common->find_theme($customer_id);
        $param['batchcode']    = $_REQUEST['batchcode']?$_REQUEST['batchcode']:'';
        // 数据校验
        if( $param['batchcode'] == ''){
            json_out(['errmsg'=>'订单号不能为空']);
        }
        $res = $this->model->select_order_details($param);
        if ($res['errcode'] == 400) {
            json_out(array('error' => 404,'errmsg' => '订单不存在'));
        }
        include('view/cailing/order_details.php');
    }

  /*
    * 分页
    * linrongdie 
    * 参数： array('search_key'=>['user_id','user_name','identity_id','status','begin_time','end_time'],'page','page_size','is_ajax')
    *        is_ajax 1 ajax请求 0 页面请求,search_key 搜索条件 ，page 当前页数 ，page_size 每页条数
    *
    */
    public function shopkeeper_review_list()
    {
        $post = $_POST;
        extract($post);
        $data['customer_id'] = $customer_id;
        $data['page']        = $page;
        $data['page_size']   = $page_size;
        $theme               = $this->model_common->find_theme($data['customer_id']);
        //判断是否为AJAX请求
        if($is_ajax != 1)
        {
           $identity_arr = $this->model->get_identity($data['customer_id']);
             include('view/cailing/color_bell_management.php');
        }
        else
        {
            //判断数据是否安全
            if(empty($data['customer_id']))
            {
                json_out(array('errcode' => 400,'errmsg'=>'customer_id参数丢失！'));
            }
            if(empty($search_key))
            {
                json_out(array('errcode' => 400,'errmsg'=>'search_key参数丢失！'));
            }
            if(empty($data['page']) || $data['page'] < 1)
            {
                json_out(array('errcode' => 400,'errmsg'=>'page_size有误！'));
            }
            if(empty($data['page_size']) || $data['page_size'] < 1){
                $data['page_size'] = 20;//每页数量
            }
            $data = array_merge($data,$search_key);
            $result = $this->model->shopkeeper_review_list($data);
            json_out($result);
        }
    }



    /**彩铃订单管理列表
    * @author  HMJ-V384
    * @param  
    * @version  2018-05-25
    * @return  
    * @var  
    */    
    function order_list_management(){             
        $customer_id = $this->customer_id;
        $customer_id_en = $this->customer_id_en;
        $theme       = $this->model_common->find_theme($customer_id);

        $param['customer_id']     = $customer_id;
        $param['batchcode']       = $_REQUEST['batchcode']?$_REQUEST['batchcode']:-1;
        $param['status']          = $_REQUEST['status']?$_REQUEST['status']:-1;
        $param['use_phone']       = $_REQUEST['use_phone']?$_REQUEST['use_phone']:-1;
        $param['paystyle']        = $_REQUEST['paystyle']?$_REQUEST['paystyle']:-1;
        $pageNum                  = $_REQUEST['pagenum']?$_REQUEST['pagenum']:1;//当前页
        $param['pageNum']         = $pageNum;//当前页

        $res       = $this->model->order_list_management($param);//获取店主列表
        $data      = $res['order_arr'];
        $pageCount = $res['pageCount'];        
        include("view/cailing/color_bell_order_list.php");
    }


    /**彩铃订单管理列表--订单处理---
    * @author  HMJ-V384
    * @param  type：0确认支付1确认完成2退款3删除4详情5备注 POST
    * @version  2018-05-25
    * @return  
    * @var  
    */    
    function order_deal(){             
        $customer_id    = $this->customer_id;
        $type           = $_POST['type'];
        $id             = $_POST['id'];
        $batchcode      = $_POST['batchcode'];
        $p_customer_id  = $_POST['customer_id'];
        $content        = isset($_POST['content'])?mysql_escape_string($_POST['content']):'';
        if($p_customer_id != $customer_id) {
            return $return=array('errcode' => 403, 'errmsg' => '非法操作', 'data' => '');
        }
        $res = $this->model->order_deal($customer_id,$type,$id,$batchcode,$content);
        json_out($res);
    }

    /*
    版权信息: 秘密信息
    功能描述：彩铃订购——排序
    开 发 者：zoujunjie v397
    开发日期：2018-05-29
    重要说明：无
     */
    public function setting_sort(){
        $data = $_POST;
        $data['customer_id'] = $this->customer_id;
        $res = $this->model->setting_sort($data);
        json_out($res);
    }





}