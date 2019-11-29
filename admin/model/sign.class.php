<?php

class sign{

    public function __construct() {
		// require_once('model/base.php');
        // $this->model_base = new model_base();
    }

     /**
     * 对数组作去空格处理
     * @param $data   传递过来的数据
     * @return mixed
     * @throws HttpException
     */
    public function trim_field($arr)
    {   
        $return = array();

        foreach($arr as $key=>$value){
            if(is_numeric($value) || is_array($value)){
                $return[$key] = $value;
            }else{
                $return[$key] = trim($value); 
            }         
        }      

        return $return; 
    }
    /**
     * 校验数据是否存在(必填项)
     * @param $field  字段
     * @param $data   传递过来的数据
     * @return mixed
     * @throws HttpException
     */
    public function issetRequestData($field,$data)
    {   
        
        $return = array('errcode'=>0, 'errmsg'=>'成功');       

        // 多字段校验
        if (is_array($field)) {
            foreach ($field as $value) {
                if (!in_array($value, array_keys($data))) {
                    $return = array('errcode'=>40001, 'errmsg'=>"{$value} 参数不存在");
                    break;
                }else{
                    if(empty($data[$value])){
                        $return = array('errcode'=>40002, 'errmsg'=>"{$value} 参数不合法");
                        break;
                    }else if($this->sql_check($data[$value])){
                        $return = array('errcode'=>40002, 'errmsg'=>"{$value} 参数不合法");
                        break;
                    }
                }
            }

        }else{
            // 单字段校验
            if (!in_array($field, array_keys($data))) {
                $return = array('errcode'=>40001, 'errmsg'=>"$field 参数不存在");
            }else{
                if(empty($data[$field])){
                    $return = array('errcode'=>40002, 'errmsg'=>"$field 参数不合法");  
                }else if($this->sql_check($data[$field])){

                    $return = array('errcode'=>40002, 'errmsg'=>"$field 参数不合法");
                }
            }
        }  

        return $return; 
    }
    /**
     * sql防注入检测
     * @param $sql_str
     * @author CDR
     * return false 表明通过检测
     */ 
    function sql_check($sql_str) { 
        return preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|\#|union|into|load_file|outfile/', $sql_str); // 进行过滤 
    }

    /**对数组排序
	 * $array 排序前的数组
	 * return 排序后的数组
	*/
	public function arr_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}

    /**
    * 解析json串
    * @param type $json_str
    * @return type
    */
    function analyJson($json_str) {
        
        $json_str = str_replace('＼＼', '', $json_str);
        $out_arr = array();
        preg_match('/{.*}/', $json_str, $out_arr);

        if (!empty($out_arr)) {
            $result = json_decode($out_arr[0], TRUE);
        } else {
            return FALSE;
        }

        return $result;
    }


    /**
    * 构建回调表单
    * @param     array      $params     [返回数据]
    * @param     string     $action     [回调地址]
    * @return    string     $html       [html表单]
    * Author: Ning
    * 2018-7-30
    */
    public function create_auto_html($params, $action) {
        
        $html = "<form id='pay_form' name='pay_form' action='{$action}' method='post'>";

        foreach ($params as $key => $value) {
            $html .= "<input type='hidden' name='{$key}' value='{$value}' />";
        }

        $html .= "</form><script language='javascript'>document.pay_form.submit();</script>";
        
        return $html;
    }


    /**
    * 参数 数组转签名格式 a=1&b=2&c=3
    * @param     array      $params     [签名参数]
    * @return    string     $string     [签名]
    * Author: Ning
    * 2018-7-30
    */
    public function to_sign_type($params) {

        if (!is_array($params)) {
            return false;
        }

        $params = $this->arr_sort($params);//升序排序

        $string = '';
        foreach ($params as $key => $val) {
            if (is_array($val)) {
                $val = json_encode($val,JSON_UNESCAPED_UNICODE);
            }
            $string .= '&'.$key.'='.$val;//拼接参数
        }

        $string = ltrim($string,"&");//原始参数串

        return $string;
    }


    /**
     * 外部商户
     * 生成签名(公钥加密)
     * @param    string      $params      请求参数
     * @return   string      签名
     */
    public function create_rsa_sign($params) {

        if (!is_array($params)) {
            return ['errcode' => 40018, 'errmsg' => '数据异常' ];
        }

        $params                 = $this->trim_field($params);   //去空格处理

        $check_field            = array('app_id','method','timetamp','version');//验证公共请求参数

        $check_result           = $this->issetRequestData($check_field,$params); //检查参数是否完整

        if ($check_result['errcode'] !== 0) {

            return $check_result;
        }

        $string = $this->to_sign_type($params);//参数数组转参数串
       
        if (isset($_SESSION['file']))
        {
            $certificate = $_SESSION['file'];

        }else{
              //证书路径
            $sql = "SELECT certificate FROM ".WSY_PAY.".pay_config where customer_id='".$params['customer_id']."' and appid ='".$params['app_id']."' and appsecret ='".$params['app_secret']."' LIMIT 1 ";
            $result= _mysql_query($sql);
            while ($row =mysql_fetch_object($result)) {
               $certificate = $row ->certificate;
            }
            $certificate = $_SERVER['DOCUMENT_ROOT'].$certificate;
        }
        //获取公钥
        $key = file_get_contents($certificate);
        $key = openssl_pkey_get_public($key);
        require_once('model/rsa.class.php');//引入rsa方法
        $res_model = new rsa_method();

        $data['data'] = $string;//参数串
        $data['key']  = $key;//公钥
        $data['type'] = 1;//公钥加密
        //生成签名
        $en_sign = $res_model->rsa_encrypt($data);

        if ($en_sign['code'] != '20000') {

            return ['errcode' => 40017, 'errmsg' => '生成签名失败'];
        }
        
        $params['sign'] = $en_sign['data'];
        // unlink($certificate); //删除服务器证书文件

        return $params;
    }

    
    /**
     * 外部商户
     * 验证签名是否正确(公钥验签)
     * @param    array       $params      参数
     * @return   array       参数
     * Author: Ning
     * 2018-7-27
     */
    public function check_rsa_sign($params) {

        if (!is_array($params)) {
            return ['errcode' => 40018, 'errmsg' => '数据异常' ];
        }

        $params                 = $this->trim_field($params);  //去空格处理
        
        $check_field            = array('sign');//验证公共请求参数

        $check_result           = $this->issetRequestData($check_field,$params); //检查参数是否完整

        if ($check_result['errcode'] !== 0) {

            return $check_result;
        }

        require_once('model/rsa.class.php');//引入rsa方法
        $res_model = new rsa_method();
          //证书路径
        $sql = "SELECT certificate FROM ".WSY_PAY.".pay_config where customer_id='".$params['customer_id']."' and appid ='".$params['app_id']."' and appsecret ='".$params['app_secret']."' LIMIT 1 ";
        $result= _mysql_query($sql);
        while ($row =mysql_fetch_object($result)) {
           $certificate = $row ->certificate;
        }

        //获取公钥
        $key = file_get_contents($certificate);
        $key = openssl_pkey_get_public($key);

        $data['data'] = $params['sign'];//签名
        $data['key']  = $key;//公钥
        $data['type'] = 2;//公钥解密
        //解密sign
        $de_sign = $res_model->rsa_decrypt($data);

        if ($de_sign['code'] != '20000') {

            return ['errcode' => 40005, 'errmsg' => '解密异常'];
        }

        $en_sign = '';
        unset($params['sign']);//去掉sign
        
        $en_sign = $this->to_sign_type($params);//参数数组转参数串

        if ($de_sign['data'] != $en_sign) {

            return ['errcode' => 40006, 'errmsg' => '签名错误'];
        }

        $params['sign_success'] = 1;//判断验签结果

        return $params;
    }


    function post_curl($url,$post_data)
    {
        // $headers = ["Content-type: application/json;charset='utf-8'","Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache"];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);         // 要访问的地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);                         // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);    // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');      // 模拟用户使用的浏览器
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);     // 使用自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);             // 自动设置Referer
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);                   // Post提交的数据包
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                   // Post提交的数据包
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);                        // 设置超时限制防止死循环
        //$curl_error = curl_error($ch);
        $json = curl_exec($ch);

        curl_close($ch);
        return $json;
    }


}
