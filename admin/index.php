<?php
	/*
		测试地址 http://127.0.0.1/weixin_platform/trunk/mshop/web/index.php?m=package&a=index
		参数$m: 控制器(module)
		参数$a: 方法(action)
	*/
    require_once '../../weixinpl/config.php';
	require_once '../../weixinpl/customer_id_decrypt.php';
    require_once '../common/mshop_function.php';
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Methods:POST');
    header('Access-Control-Allow-Headers:x-requested-with,content-type');
    if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') //检测到预请求直接退出执行
    {
        exit;
    }

    extract ( $_REQUEST,EXTR_SKIP ); //将数组转为变量
    $app = new App ();
    $app->run ();
	$m = $m ? $m : 'index';
    call_user_func ( array ($app, $m ) );

    class App {
        var $db;
        public function run() {
            $this->init ();
        }

		/*初始化*/
        function init() {
            date_default_timezone_set ( "PRC" );
            ini_set("magic_quotes_runtime", 0);
            header("content-type:text/html; charset=utf-8;");
			$this->site_url =	str_ireplace ( 'mshop/admin/index.php', '',  'http://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['SCRIPT_NAME'])) ;
        }

		/*魔术函数*/
        function __call($fun,$b) {
            extract ( $_REQUEST );
			require ROOT_DIR . 'mshop/admin/controller/base.php';
			$action_file = ROOT_DIR . 'mshop/admin/controller/' . str_replace ( '.', '', $fun ) . ".php";
            if (file_exists ( $action_file )) {
				require ($action_file);
				$classname = 'control_'.$fun;
				$control = new $classname();
            } else {
                exit ( "No controller " . $fun );
            }
			$method = $a;
            // $control = new control_();
            // var_dump($control, $method,method_exists($control, $method),is_callable( array($control,$method) ));exit;
			if (method_exists($control, $method))
			{
				$control->$method();
			} else
			{
				trigger_error('method '.$method.' not found!',E_USER_WARNING); exit;
			}
        }

    }
