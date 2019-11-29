<?php


class control_test extends control_base 
{
	var $model;
	var $model_common;
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
//        var_dump($theme);

		
    }

    public function index()
    {   
        $customer_id = $this->customer_id;
        $theme  = $this->model_common->find_theme($customer_id);
        include('view/cailing/test.php');
    }

    //接受上传视频并处理
    public function handleVideo (){
        ini_set('max_input_time', '300');
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '512M');

        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $return_msg["errcode"] = 0;
        $return_msg["errmsg"]  = "success";
        $customer_id = $this->customer_id;

        // Support CORS
        // header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }


        if ( !empty($_REQUEST[ 'debug' ]) ) {
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );
            if ( $random === 0 ) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }

        // header("HTTP/1.0 500 Internal Server Error");
        // exit;


        // 10 minutes execution time
        @set_time_limit(10 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);

        // Settings
        // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $targetDir = $_SERVER['DOCUMENT_ROOT'].'/yundian'."/../video_tmp";
        $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/yundian';

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds


        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir,0755,true);
        }

        // Create target dir
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir,0755,true);
        }

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        // Convert UTF-8
        $fileName  = iconv("UTF-8","gb2312",$fileName);

        $fileTypes = array('mov', 'ogg', 'mp4', 'mpeg4', 'webm','mp3'); // 允许的文件后缀

        $fileParts = pathinfo($_FILES["file"]["name"]);

        if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

        } else {
            die('{"errcode" : 402, "errmsg" : "不合法的文件格式！", "jsonrpc" : "2.0", "error" : {"code": 104, "message": "不合法的文件格式！"}, "id" : "id"}');
        }

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;  //分片文件路径


        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"errcode" : 402, "errmsg" : "上传失败！", "jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }


        // Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            die('{"errcode" : 402, "errmsg" : "上传失败！", "jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"errcode" : 402, "errmsg" : "上传失败！", "jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }

            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"errcode" : 402, "errmsg" : "上传失败！", "jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"errcode" : 402, "errmsg" : "上传失败！", "jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        //更改分片文件名称
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        //更改分片文件名称 End
        
        //插入数据库
        _file_put_contents ('video_tmp.txt', "add====fileName=== ".var_export($fileName,true)." \r\n", FILE_APPEND );
        _file_put_contents ('video_tmp.txt', "add====chunk=== ".var_export($chunk,true)." \r\n", FILE_APPEND );
        $reuslt_add = $this->model->save_video_tmp($fileName,$chunk,$customer_id);  
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
            _file_put_contents ('video_tmp.txt', "====done=======chunk:".$chunk."=====  \r\n", FILE_APPEND );
            for( $index = 0; $index < $chunks; $index++ ) {                 
                $tmp_exits = $this->model->check_video_tmp_exist($fileName,$index,$customer_id);    
                _file_put_contents ('video_tmp.txt', "tmp_exits=======chunks:".$index."== ".var_export($tmp_exits,true)." \r\n", FILE_APPEND ); 
                if(!$tmp_exits){
                    $done = false;
                    break;                      
                }
            }           
        }
        _file_put_contents ('video_tmp.txt', "====done2=== ".var_export($done,true)." \r\n", FILE_APPEND );
        if ( $done ) {
            
            $NewfileName = time().rand(10,1000) . "." . $fileParts['extension'];    //文件命名
            $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $NewfileName;          //文件最终合并上传路径    
            
            _file_put_contents ('video_tmp.txt', "=====uploadPath=".$uploadPath."===========\r\n", FILE_APPEND );
            if (!$out = @fopen($uploadPath, "wb")) {
                $re_clear = $this->model->clear_video_tmp($fileName,$customer_id);
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

        // Return Success JSON-RPC response
        if ($done) {
            $re_success = $this->model->update_success_video($fileName,$customer_id);
            die('{"errcode" : 0, "errmsg" : "上传成功！", "jsonrpc" : "2.0", "result" : null, "id" : "id", "newfileurl" : "'.$value['newfileurl'].'"}');
        }else{
            die('{"errcode" : 0, "errmsg" : "上传成功！", "jsonrpc" : "2.0", "result" : null, "id" : "id"}');
        }
    }


}
